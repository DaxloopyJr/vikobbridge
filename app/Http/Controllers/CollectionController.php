<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\CollectionFund;
use App\Models\Member;
use App\Models\CalendarYear;
use Illuminate\Support\Facades\DB;
use App\Traits\DataTableTrait;
use App\Models\Discipline;

class CollectionController extends Controller
{
    use DataTableTrait;
    public function __construct()
    {
        $this->middleware('permission:view_collections')->only(['index', 'show']);
        $this->middleware('permission:create_collections')->only(['create', 'store']);
        $this->middleware('permission:edit_collections')->only(['edit', 'update']);
        $this->middleware('permission:delete_collections')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');
        
        $query = Collection::with(['member', 'collectionFund', 'calendarYear'])
            ->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('fund_id')) {
            $query->where('collection_fund_id', $request->fund_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $collections = $query->latest()->paginate(25);
        $funds = CollectionFund::where('group_id', $groupId)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();

        $fundTotals = DB::table('collections')
            ->join('collection_funds', 'collections.collection_fund_id', '=', 'collection_funds.id')
            ->where('collections.group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('collections.calendar_year_id', $calendarYearId);
            })
            ->select('collection_funds.name', 'collection_funds.slug', DB::raw('SUM(collections.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('collection_funds.id', 'collection_funds.name', 'collection_funds.slug')
            ->get();

        $activeFund = $request->input('fund_id', 'all');
        $currentCalendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();

        return view('collections.index', compact('collections', 'funds', 'members', 'calendarYears', 'fundTotals', 'activeFund', 'currentCalendarYear'));
    }

    public function data(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $calendarYearId = session('current_calendar_year_id');
        $query = Collection::with(['member', 'collectionFund'])->where('group_id', $groupId);
        if ($calendarYearId) $query->where('calendar_year_id', $calendarYearId);
        if ($request->filled('fund_id')) $query->where('collection_fund_id', $request->fund_id);

        $result = $this->processDataTable($request, $query, []);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $memberName = $item->member ? e($item->member->first_name . ' ' . $item->member->last_name) : '<span class="text-muted">-</span>';
            $fundName = $item->collectionFund ? '<span class="badge bg-light text-dark">' . e($item->collectionFund->name) . '</span>' : '<span class="text-muted">-</span>';
            $channelBadge = match($item->payment_channel) {
                'cash' => '<span class="badge bg-success">Cash</span>',
                'bank' => '<span class="badge bg-primary">Bank</span>',
                'mobile_money' => '<span class="badge bg-info">Mobile</span>',
                'selcom' => '<span class="badge bg-warning text-dark">Selcom</span>',
                default => '<span class="badge bg-secondary">' . ucfirst($item->payment_channel) . '</span>',
            };
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . route('collections.edit', $item->id) . '" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>';
            $actions .= '<form method="POST" action="' . route('collections.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'member' => $memberName,
                'fund' => $fundName,
                'amount' => '<strong class="text-success">' . number_format($item->amount, 2) . ' TZS</strong>',
                'month' => $item->month ? '<span class="badge bg-light text-dark">' . e($item->month) . '</span>' : '<span class="text-muted">-</span>',
                'payment_date' => $item->payment_date ? '<small>' . \Carbon\Carbon::parse($item->payment_date)->format('M d, Y') . '</small>' : '<small class="text-muted">-</small>',
                'channel' => $channelBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function create()
    {
        $groupId = session('current_group_id');
        $funds = CollectionFund::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();
        $currentCalendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();

        return view('collections.create', compact('funds', 'members', 'calendarYears', 'currentCalendarYear'));
    }

    public function store(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        if (!$calendarYearId) {
            $calendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();
            $calendarYearId = $calendarYear?->id;
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'collection_fund_id' => 'required|exists:collection_funds,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'year' => 'required|integer|min:2000|max:2100',
            'is_new_calendar_year' => 'nullable|boolean',
            'balance_carried_forward' => 'nullable|numeric|min:0',
            'payment_channel' => 'required|in:bank,mobile_money,cash,selcom,other',
            'payment_control_number' => 'nullable|string|max:255',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['group_id'] = $groupId;
        $validated['calendar_year_id'] = $calendarYearId;
        $validated['recorded_by'] = auth()->id();
        $validated['source'] = 'manual';
        $validated['is_new_calendar_year'] = $request->boolean('is_new_calendar_year', false);

        $collection = Collection::create($validated);

        // If the collection fund is "faini" (fine) or "penalty", update the discipline record
        $collectionFund = CollectionFund::find($validated['collection_fund_id']);
        if ($collectionFund && in_array(strtolower($collectionFund->slug), ['faini', 'fine', 'penalty'])) {
            $this->updateDisciplinePayment(
                $groupId,
                $validated['member_id'],
                $validated['month'],
                $validated['year'],
                $validated['amount']
            );
        }

        return redirect()->route('collections.index')->with('success', 'Collection recorded successfully.');
    }

    public function show(Collection $collection)
    {
        $this->authorizeAccess($collection);
        return view('collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $this->authorizeAccess($collection);
        $groupId = session('current_group_id');
        $funds = CollectionFund::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $currentCalendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();

        return view('collections.edit', compact('collection', 'funds', 'members', 'currentCalendarYear'));
    }

    public function update(Request $request, Collection $collection)
    {
        $this->authorizeAccess($collection);

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'collection_fund_id' => 'required|exists:collection_funds,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'year' => 'required|integer|min:2000|max:2100',
            'is_new_calendar_year' => 'nullable|boolean',
            'balance_carried_forward' => 'nullable|numeric|min:0',
            'payment_channel' => 'required|in:bank,mobile_money,cash,selcom,other',
            'payment_control_number' => 'nullable|string|max:255',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_new_calendar_year'] = $request->boolean('is_new_calendar_year', false);

        $collection->update($validated);

        return redirect()->route('collections.index')->with('success', 'Collection updated successfully.');
    }

    /**
     * Store a fine or penalty as a collection record
     */
    public function storeFine(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'fine_type' => 'required|in:late_payment,absence,late_contribution,misconduct,violation,damage,other',
            'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'payment_date' => 'required|date',
            'payment_channel' => 'required|in:bank,mobile_money,cash,selcom,other',
        ]);

        // Find the "faini" (fines) collection fund for this group
        $fineFund = CollectionFund::where('group_id', $groupId)
            ->where(function ($q) {
                $q->where('slug', 'faini')
                  ->orWhere('slug', 'fine')
                  ->orWhere('fund_type', 'fine');
            })
            ->first();

        if (!$fineFund) {
            return redirect()->route('collections.index')
                ->with('error', 'No fine/penalty collection fund found. Please create one in Settings > Collection Funds.');
        }

        Collection::create([
            'group_id' => $groupId,
            'member_id' => $validated['member_id'],
            'collection_fund_id' => $fineFund->id,
            'calendar_year_id' => $calendarYearId,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'payment_channel' => $validated['payment_channel'],
            'notes' => '[' . ucfirst(str_replace('_', ' ', $validated['fine_type'])) . '] ' . $validated['reason'],
            'recorded_by' => auth()->id(),
            'source' => 'fine',
        ]);

        return redirect()->route('collections.index')
            ->with('success', 'Fine/Penalty assigned successfully to member.');
    }

    public function destroy(Collection $collection)
    {
        $this->authorizeAccess($collection);
        $collection->delete();

        return redirect()->route('collections.index')->with('success', 'Collection deleted successfully.');
    }

    public function bulkCreate(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        if (!$calendarYearId) {
            $calendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();
            $calendarYearId = $calendarYear?->id;
        }

        $validated = $request->validate([
            'entries' => 'required|array',
            'entries.*.member_id' => 'required|exists:members,id',
            'entries.*.collection_fund_id' => 'required|exists:collection_funds,id',
            'entries.*.amount' => 'required|numeric|min:0.01',
            'entries.*.payment_date' => 'required|date',
        ]);

        $records = [];
        foreach ($validated['entries'] as $entry) {
            $records[] = array_merge($entry, [
                'group_id' => $groupId,
                'calendar_year_id' => $calendarYearId,
                'recorded_by' => auth()->id(),
                'payment_channel' => 'cash',
                'source' => 'manual',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Collection::insert($records);

        return redirect()->route('collections.index')->with('success', count($records) . ' collections recorded successfully.');
    }

    protected function authorizeAccess(Collection $collection)
    {
        $groupId = session('current_group_id');
        if ($collection->group_id != $groupId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have access to this collection.');
        }
    }

    /**
     * Update discipline record when a fine/penalty collection is recorded.
     * Matches by group_id, member_id, month, and year.
     */
    protected function updateDisciplinePayment(int $groupId, int $memberId, string $month, int $year, float $amount): void
    {
        $discipline = Discipline::where('group_id', $groupId)
            ->where('member_id', $memberId)
            ->where('month', $month)
            ->where('year', $year)
            ->whereIn('status', ['pending', 'paid'])
            ->orderBy('id', 'desc')
            ->first();

        if ($discipline) {
            $newPaidAmount = $discipline->paid_amount + $amount;
            $newStatus = $newPaidAmount >= $discipline->amount ? 'paid' : 'pending';

            $updateData = [
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
            ];

            // Set payment_date when fully paid
            if ($newStatus === 'paid') {
                $updateData['payment_date'] = now();
            }

            $discipline->update($updateData);
        }
    }
}
