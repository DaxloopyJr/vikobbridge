<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Group;
use App\Models\Collection;
use App\Models\CollectionFund;
use App\Models\CalendarYear;
use Illuminate\Support\Facades\Storage;
use App\Traits\DataTableTrait;

class MemberController extends Controller
{
    use DataTableTrait;
    public function __construct()
    {
        $this->middleware('permission:view_members')->only(['index', 'show']);
        $this->middleware('permission:create_members')->only(['create', 'store']);
        $this->middleware('permission:edit_members')->only(['edit', 'update']);
        $this->middleware('permission:delete_members')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $groupId = session('current_group_id');
        $query = Member::with(['group'])->where('group_id', $groupId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->latest()->paginate(20);
        $group = Group::find($groupId);

        return view('members.index', compact('members', 'group'));
    }

    public function data(Request $request)
    {
        $groupId = $this->currentGroupId();

        if (!$groupId) {
            return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }

        $query = Member::where('group_id', $groupId);

        $result = $this->processDataTable($request, $query, ['first_name', 'last_name', 'phone_number', 'member_number']);

        $result['data'] = collect($result['data'])->map(function ($item) use ($groupId) {
            $statusBadge = match($item->status) {
                'active' => '<span class="badge bg-success">Active</span>',
                'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                'suspended' => '<span class="badge bg-warning text-dark">Suspended</span>',
                'deceased' => '<span class="badge bg-dark">Deceased</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . route('members.show', $item->id) . '" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>';
            $actions .= '<a href="' . route('members.edit', $item->id) . '" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>';
            $actions .= '<form method="POST" action="' . route('members.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete this member?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'member_number' => '<code>' . e($item->member_number) . '</code>',
                'name' => '<strong>' . e($item->first_name . ' ' . $item->last_name) . '</strong>',
                'phone_number' => e($item->phone_number ?? '-'),
                'gender' => '<span class="badge bg-light text-dark">' . ucfirst($item->gender) . '</span>',
                'status' => $statusBadge,
                'join_date' => $item->join_date ? '<small>' . \Carbon\Carbon::parse($item->join_date)->format('M d, Y') . '</small>' : '<small class="text-muted">-</small>',
                'actions' => $actions,
            ];
        })->toArray();

        return response()->json($result);
    }

    public function create()
    {
        $groupId = session('current_group_id');
        $group = Group::find($groupId);
        return view('members.create', compact('group'));
    }

    public function store(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'profile_picture' => 'nullable|image|max:2048',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'ward_id' => 'nullable|exists:wards,id',
            'village_id' => 'nullable|exists:villages,id',
            'region' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'cell_leader_name' => 'nullable|string|max:255',
            'cell_leader_phone' => 'nullable|string|max:20',
            'lg_chairperson_name' => 'nullable|string|max:255',
            'lg_chairperson_phone' => 'nullable|string|max:20',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:20',
            'guarantor_relationship' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_phone' => 'nullable|string|max:20',
            'spouse_occupation' => 'nullable|string|max:255',
            'dependents' => 'nullable|array',
            'dependents.*.full_name' => 'required_with:dependents|string',
            'dependents.*.phone' => 'nullable|string',
            'dependents.*.relationship' => 'required_with:dependents|string',
            'inheritors' => 'nullable|array',
            'inheritors.*.full_name' => 'required_with:inheritors|string',
            'inheritors.*.phone' => 'nullable|string',
            'inheritors.*.relationship' => 'required_with:inheritors|string',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        $validated['group_id'] = $groupId;

        $memberCount = Member::where('group_id', $groupId)->count();
        $validated['member_number'] = 'M' . str_pad($memberCount + 1, 4, '0', STR_PAD_LEFT);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('members/' . $groupId, 'public');
            $validated['profile_picture'] = $path;
        }

        if (isset($validated['dependents'])) {
            $validated['dependents'] = array_values($validated['dependents']);
        }
        if (isset($validated['inheritors'])) {
            $validated['inheritors'] = array_values($validated['inheritors']);
        }

        $member = Member::create($validated);

        return redirect()->route('members.index')->with('success', 'Member registered successfully.');
    }

    public function show(Member $member)
    {
        $this->authorizeAccess($member);
        
        $calendarYearId = session('current_calendar_year_id');
        $group = Group::find(session('current_group_id'));
        
        $collections = $member->collections()
            ->with('collectionFund')
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->latest()
            ->paginate(10);

        $loans = $member->loans()
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->latest()
            ->get();

        $collectionFunds = CollectionFund::where('group_id', $member->group_id)->get();
        $fundTotals = [];
        foreach ($collectionFunds as $fund) {
            $fundTotals[$fund->slug] = $member->collections()
                ->where('collection_fund_id', $fund->id)
                ->when($calendarYearId, function ($q) use ($calendarYearId) {
                    return $q->where('calendar_year_id', $calendarYearId);
                })
                ->sum('amount');
        }

        return view('members.show', compact('member', 'collections', 'loans', 'collectionFunds', 'fundTotals', 'group'));
    }

    public function edit(Member $member)
    {
        $this->authorizeAccess($member);
        $group = Group::find(session('current_group_id'));
        return view('members.edit', compact('member', 'group'));
    }

    public function update(Request $request, Member $member)
    {
        $this->authorizeAccess($member);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'profile_picture' => 'nullable|image|max:2048',
            'region_id' => 'nullable|exists:regions,id',
            'district_id' => 'nullable|exists:districts,id',
            'ward_id' => 'nullable|exists:wards,id',
            'village_id' => 'nullable|exists:villages,id',
            'region' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'ward' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'cell_leader_name' => 'nullable|string|max:255',
            'cell_leader_phone' => 'nullable|string|max:20',
            'lg_chairperson_name' => 'nullable|string|max:255',
            'lg_chairperson_phone' => 'nullable|string|max:20',
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_phone' => 'nullable|string|max:20',
            'guarantor_relationship' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_phone' => 'nullable|string|max:20',
            'spouse_occupation' => 'nullable|string|max:255',
            'dependents' => 'nullable|array',
            'inheritors' => 'nullable|array',
            'status' => 'required|in:active,inactive,suspended,deceased',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($member->profile_picture) {
                Storage::disk('public')->delete($member->profile_picture);
            }
            $path = $request->file('profile_picture')->store('members/' . $member->group_id, 'public');
            $validated['profile_picture'] = $path;
        }

        if (isset($validated['dependents'])) {
            $validated['dependents'] = array_values($validated['dependents']);
        }
        if (isset($validated['inheritors'])) {
            $validated['inheritors'] = array_values($validated['inheritors']);
        }

        $member->update($validated);

        return redirect()->route('members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->authorizeAccess($member);

        if ($member->profile_picture) {
            Storage::disk('public')->delete($member->profile_picture);
        }

        $member->delete();

        return redirect()->route('members.index')->with('success', 'Member deleted successfully.');
    }

    protected function authorizeAccess(Member $member)
    {
        $groupId = session('current_group_id');
        if ($member->group_id != $groupId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have access to this member.');
        }
    }
}
