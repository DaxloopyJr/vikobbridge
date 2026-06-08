<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expenditure;
use App\Models\CalendarYear;
use Illuminate\Support\Facades\Storage;
use App\Traits\DataTableTrait;

class ExpenditureController extends Controller
{
    use DataTableTrait;
    public function __construct()
    {
        $this->middleware('permission:view_expenditures')->only(['index', 'show']);
        $this->middleware('permission:create_expenditures')->only(['create', 'store']);
        $this->middleware('permission:edit_expenditures')->only(['edit', 'update']);
        $this->middleware('permission:delete_expenditures')->only(['destroy']);
        $this->middleware('permission:approve_expenditures')->only(['approve', 'reject']);
    }

    public function index(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        $query = Expenditure::with(['recorder', 'approver'])->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenditures = $query->latest()->paginate(20);
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();

        return view('expenditures.index', compact('expenditures', 'calendarYears'));
    }

    public function data(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $calendarYearId = session('current_calendar_year_id');
        $query = Expenditure::with(['recorder'])->where('group_id', $groupId);
        if ($calendarYearId) $query->where('calendar_year_id', $calendarYearId);

        $result = $this->processDataTable($request, $query, ['description', 'category']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = match($item->status) {
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'approved' => '<span class="badge bg-success">Approved</span>',
                'rejected' => '<span class="badge bg-danger">Rejected</span>',
                'paid' => '<span class="badge bg-info">Paid</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $actions = '<div class="btn-group btn-group-sm">';
            if ($item->status === 'pending') {
                $actions .= '<form method="POST" action="' . route('expenditures.approve', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Approve?\')">' . csrf_field() . '<button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Approve"><i class="bi bi-check-lg"></i></button></form>';
                $actions .= '<form method="POST" action="' . route('expenditures.reject', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Reject?\')">' . csrf_field() . '<button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Reject"><i class="bi bi-x-lg"></i></button></form>';
            }
            $actions .= '<form method="POST" action="' . route('expenditures.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'description' => '<strong>' . e($item->description ?? '-') . '</strong><br><small class="text-muted">' . e($item->category ?? '-') . '</small>',
                'amount' => '<strong class="text-danger">' . number_format($item->amount, 2) . ' TZS</strong>',
                'status' => $statusBadge,
                'expense_date' => $item->expense_date ? '<small>' . \Carbon\Carbon::parse($item->expense_date)->format('M d, Y') . '</small>' : '<small class="text-muted">-</small>',
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function create()
    {
        $groupId = session('current_group_id');
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();
        return view('expenditures.create', compact('calendarYears'));
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
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'receipt_attachment' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
        ]);

        $validated['group_id'] = $groupId;
        $validated['calendar_year_id'] = $calendarYearId;
        $validated['recorded_by'] = auth()->id();
        $validated['expense_number'] = Expenditure::generateExpenseNumber();
        $validated['status'] = 'pending';

        if ($request->hasFile('receipt_attachment')) {
            $path = $request->file('receipt_attachment')->store('receipts/' . $groupId, 'public');
            $validated['receipt_attachment'] = $path;
        }

        Expenditure::create($validated);

        return redirect()->route('expenditures.index')->with('success', 'Expenditure recorded successfully.');
    }

    public function show(Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);
        return view('expenditures.show', compact('expenditure'));
    }

    public function approve(Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);

        $expenditure->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('expenditures.index')->with('success', 'Expenditure approved.');
    }

    public function reject(Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);

        $expenditure->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return redirect()->route('expenditures.index')->with('success', 'Expenditure rejected.');
    }

    public function edit(Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);
        $groupId = session('current_group_id');
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();
        return view('expenditures.edit', compact('expenditure', 'calendarYears'));
    }

    public function update(Request $request, Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);

        if ($expenditure->status === 'approved') {
            return redirect()->back()->with('error', 'Cannot edit an approved expenditure.');
        }

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'receipt_attachment' => 'nullable|file|max:5120',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_attachment')) {
            if ($expenditure->receipt_attachment) {
                Storage::disk('public')->delete($expenditure->receipt_attachment);
            }
            $path = $request->file('receipt_attachment')->store('receipts/' . $expenditure->group_id, 'public');
            $validated['receipt_attachment'] = $path;
        }

        $expenditure->update($validated);

        return redirect()->route('expenditures.index')->with('success', 'Expenditure updated successfully.');
    }

    public function destroy(Expenditure $expenditure)
    {
        $this->authorizeAccess($expenditure);

        if ($expenditure->receipt_attachment) {
            Storage::disk('public')->delete($expenditure->receipt_attachment);
        }

        $expenditure->delete();
        return redirect()->route('expenditures.index')->with('success', 'Expenditure deleted successfully.');
    }

    protected function authorizeAccess(Expenditure $expenditure)
    {
        $groupId = session('current_group_id');
        if ($expenditure->group_id != $groupId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have access to this expenditure.');
        }
    }
}
