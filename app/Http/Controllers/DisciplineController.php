<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discipline;
use App\Models\Member;
use App\Models\CalendarYear;
use App\Traits\DataTableTrait;

class DisciplineController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        return $this->renderDisciplineView(null);
    }

    public function fines()
    {
        return $this->renderDisciplineView('fine');
    }

    public function penalties()
    {
        return $this->renderDisciplineView('penalty');
    }

    protected function renderDisciplineView(?string $type)
    {
        $groupId = session('current_group_id');
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $currentCalendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();
        $disciplineType = $type; // 'fine', 'penalty', or null (all)

        // Build query for summary stats scoped by group and optional type
        $summaryQuery = Discipline::where('group_id', $groupId);
        if ($type && in_array($type, ['fine', 'penalty'])) {
            $summaryQuery->where('type', $type);
        }

        $pendingCount = (clone $summaryQuery)->where('status', 'pending')->count();
        $pendingAmount = (clone $summaryQuery)->where('status', 'pending')->sum('amount') ?? 0;

        $paidCount = (clone $summaryQuery)->where('status', 'paid')->count();
        $paidAmount = (clone $summaryQuery)->where('status', 'paid')->sum('paid_amount') ?? 0;

        $skippedCount = (clone $summaryQuery)->where('status', 'skipped')->count();
        $skippedAmount = (clone $summaryQuery)->where('status', 'skipped')->sum('amount') ?? 0;

        $totalOutstanding = (clone $summaryQuery)->where('status', 'pending')
            ->selectRaw('SUM(amount - paid_amount) as total')
            ->value('total') ?? 0;

        $summary = compact('pendingCount', 'pendingAmount', 'paidCount', 'paidAmount', 'skippedCount', 'skippedAmount', 'totalOutstanding');

        return view('disciplines.index', compact('members', 'currentCalendarYear', 'disciplineType', 'summary'));
    }

    public function data(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $status = $request->input('status');
        $type = $request->input('type');

        $query = Discipline::with(['member'])->where('group_id', $groupId);

        if ($status) $query->where('status', $status);
        if ($type && in_array($type, ['fine', 'penalty'])) $query->where('type', $type);

        $result = $this->processDataTable($request, $query, ['reason']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $memberName = $item->member ? e($item->member->first_name . ' ' . $item->member->last_name) : '<span class="text-muted">-</span>';
            $statusBadge = match($item->status) {
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'paid' => '<span class="badge bg-success">Paid</span>',
                'skipped' => '<span class="badge bg-secondary">Skipped</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $balance = $item->amount - $item->paid_amount;
            $balanceText = $balance > 0
                ? '<span class="text-danger">' . number_format($balance, 2) . ' due</span>'
                : '<span class="text-success">Settled</span>';

            $actions = '<div class="btn-group btn-group-sm">';
            if ($item->status === 'pending') {
                $actions .= '<button type="button" class="btn btn-outline-success btn-record-payment" data-id="' . $item->id . '" data-amount="' . $item->amount . '" data-paid="' . $item->paid_amount . '" data-bs-toggle="tooltip" title="Record Payment"><i class="bi bi-cash"></i></button>';
                $actions .= '<form method="POST" action="' . route('disciplines.skip', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Skip this fine?\')">' . csrf_field() . '<button type="submit" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Skip"><i class="bi bi-skip-forward"></i></button></form>';
            }
            $actions .= '<form method="POST" action="' . route('disciplines.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete this record?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'member' => $memberName,
                'type' => '<span class="badge bg-light text-dark">' . ucfirst(str_replace('_', ' ', $item->fine_type)) . '</span>',
                'amount' => '<strong class="text-danger">' . number_format($item->amount, 2) . ' TZS</strong>',
                'paid' => '<span class="text-success">' . number_format($item->paid_amount, 2) . '</span>',
                'balance' => $balanceText,
                'month' => '<small>' . e($item->month) . ' ' . $item->year . '</small>',
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|in:fine,penalty',
            'fine_type' => 'required|in:late_payment,absence,late_contribution,misconduct,violation,damage,other',
            'month' => 'required|string|in:January,February,March,April,May,June,July,August,September,October,November,December',
            'year' => 'required|integer|min:2000|max:2100',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string',
        ]);

        Discipline::create([
            'group_id' => $groupId,
            'calendar_year_id' => $calendarYearId,
            'recorded_by' => auth()->id(),
            ...$validated,
            'status' => 'pending',
        ]);

        // Redirect to the appropriate type-specific page
        $redirectRoute = $validated['type'] === 'penalty' ? 'disciplines.penalties' : 'disciplines.fines';
        return redirect()->route($redirectRoute)->with('success', ucfirst($validated['type']) . ' assigned successfully.');
    }

    public function recordPayment(Request $request, Discipline $discipline)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
        ]);

        $newPaid = $discipline->paid_amount + $validated['paid_amount'];
        $status = $newPaid >= $discipline->amount ? 'paid' : 'pending';

        $discipline->update([
            'paid_amount' => $newPaid,
            'status' => $status,
            'payment_date' => $status === 'paid' ? now() : $discipline->payment_date,
        ]);

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    public function skip(Discipline $discipline)
    {
        $discipline->update(['status' => 'skipped']);
        return redirect()->back()->with('success', 'Fine/Penalty marked as skipped.');
    }

    public function destroy(Discipline $discipline)
    {
        $discipline->delete();
        return redirect()->back()->with('success', 'Record deleted.');
    }
}
