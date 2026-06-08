<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanRepayment;
use App\Models\LoanCosigner;
use App\Models\LoanCollateral;
use App\Models\Member;
use App\Models\CalendarYear;
use App\Models\Collection;
use App\Models\CollectionFund;
use App\Services\LoanService;
use Illuminate\Support\Facades\DB;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class LoanController extends Controller
{
    use DataTableTrait;

    public function __construct()
    {
        $this->middleware('permission:view_loans')->only(['index', 'show']);
        $this->middleware('permission:apply_loans')->only(['create', 'store', 'apply', 'submitApplication']);
        $this->middleware('permission:approve_loans')->only([
            'approve', 'reject',
            'approveTreasurer', 'approveSecretary', 'approveChairman',
            'rejectTreasurer', 'rejectSecretary', 'rejectChairman',
            'approveCosigner', 'rejectCosigner',
        ]);
        $this->middleware('permission:disburse_loans')->only(['disburseForm', 'disburse']);
        $this->middleware('permission:manage_loan_repayments')->only(['recordPayment', 'markSkipped']);
        $this->middleware('permission:edit_loans')->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        $tab = $request->get('tab', 'all');

        $query = Loan::with(['member', 'loanType'])->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        switch ($tab) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'disbursed':
                $query->whereIn('status', ['disbursed', 'repaying']);
                break;
            case 'completed':
                $query->where('status', 'completed');
                break;
            case 'defaulted':
                $query->where('status', 'defaulted');
                break;
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $loans = $query->latest()->paginate(20);
        $loanTypes = LoanType::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();

        $summaryStats = [
            'total_pending' => Loan::where('group_id', $groupId)->where('status', 'pending')->count(),
            'total_disbursed' => Loan::where('group_id', $groupId)->whereIn('status', ['disbursed', 'repaying'])->count(),
            'total_completed' => Loan::where('group_id', $groupId)->where('status', 'completed')->count(),
            'total_defaulted' => Loan::where('group_id', $groupId)->where('status', 'defaulted')->count(),
        ];

        return view('loans.index', compact('loans', 'loanTypes', 'members', 'calendarYears', 'tab', 'summaryStats'));
    }

    public function data(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $calendarYearId = session('current_calendar_year_id');
        $tab = $request->input('tab', 'all');

        $query = Loan::with(['member', 'loanType'])->where('group_id', $groupId);
        if ($calendarYearId) $query->where('calendar_year_id', $calendarYearId);

        switch ($tab) {
            case 'pending': $query->where('status', 'pending'); break;
            case 'disbursed': $query->whereIn('status', ['disbursed', 'repaying']); break;
            case 'completed': $query->where('status', 'completed'); break;
            case 'defaulted': $query->where('status', 'defaulted'); break;
        }

        $result = $this->processDataTable($request, $query, []);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $memberName = $item->member ? e($item->member->first_name . ' ' . $item->member->last_name) : '<span class="text-muted">-</span>';
            $approvalStage = $item->approvalStageLabel();
            $statusBadge = match($item->status) {
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'approved' => '<span class="badge bg-info">Approved</span>',
                'disbursed' => '<span class="badge bg-primary">Disbursed</span>',
                'repaying' => '<span class="badge bg-success">Repaying</span>',
                'completed' => '<span class="badge bg-success">Completed</span>',
                'defaulted' => '<span class="badge bg-danger">Defaulted</span>',
                'rejected' => '<span class="badge bg-secondary">Rejected</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . route('loans.show', $item->id) . '" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>';
            if ($item->status === 'pending' && $item->readyForDisbursement()) {
                $actions .= '<a href="' . route('loans.disburse.form', $item->id) . '" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Disburse"><i class="bi bi-cash"></i></a>';
            }
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'member' => $memberName,
                'amount' => '<strong>' . number_format($item->loan_amount, 2) . ' TZS</strong>',
                'type' => $item->loanType ? '<span class="badge bg-light text-dark">' . e($item->loanType->name) . '</span>' : '-',
                'rate' => $item->interest_rate . '% <small class="text-muted">' . ($item->rate_type ?? 'flat') . '</small>',
                'status' => $statusBadge . '<br><small class="text-muted">' . e($approvalStage) . '</small>',
                'date' => '<small>' . ($item->application_date ? \Carbon\Carbon::parse($item->application_date)->format('M d, Y') : '-') . '</small>',
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    /**
     * Show the Record Loan form (admin-initiated with cosigners)
     */
    public function create()
    {
        $groupId = session('current_group_id');
        $loanTypes = LoanType::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();

        return view('loans.create', compact('loanTypes', 'members', 'calendarYears'));
    }

    /**
     * Store a recorded loan (admin-initiated)
     */
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
            'loan_type_id' => 'required|exists:loan_types,id',
            'loan_amount' => 'required|numeric|min:1000',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'rate_type' => 'nullable|in:flat,reducing_balance,simple',
            'loan_term_months' => 'required|integer|min:1|max:60',
            'application_date' => 'required|date',
            'first_installment_date' => 'nullable|date|after_or_equal:application_date',
            'reason' => 'nullable|string',
            'cosigner_ids' => 'nullable|array',
            'cosigner_ids.*' => 'exists:members,id',
        ]);

        $validated['calendar_year_id'] = $calendarYearId;
        $validated['source'] = 'recorded';

        $loan = LoanService::createLoanApplication($validated, $groupId);

        // Attach cosigners
        if (!empty($validated['cosigner_ids'])) {
            foreach ($validated['cosigner_ids'] as $cosignerId) {
                // Skip if cosigner is the borrower
                if ($cosignerId == $validated['member_id']) continue;
                LoanCosigner::create([
                    'loan_id' => $loan->id,
                    'member_id' => $cosignerId,
                ]);
            }
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan recorded successfully. Loan number: ' . $loan->loan_number);
    }

    /**
     * Show the Apply Loan form (member-initiated with collateral)
     */
    public function apply()
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        $loanTypes = LoanType::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();

        // Get Hisa fund ID for eligibility calculations
        $hisaFund = CollectionFund::where('group_id', $groupId)->where('slug', 'hisa')->first();
        $hisaFundId = $hisaFund?->id;

        // Build member eligibility data
        $memberEligibility = [];
        foreach ($members as $member) {
            $monthsSinceJoin = $member->join_date ? Carbon::parse($member->join_date)->diffInMonths(now()) : 0;
            $hisaTotal = $hisaFundId
                ? Collection::where('member_id', $member->id)->where('collection_fund_id', $hisaFundId)->when($calendarYearId, fn($q) => $q->where('calendar_year_id', $calendarYearId))->sum('amount')
                : 0;
            $memberEligibility[$member->id] = [
                'months_since_join' => (int) $monthsSinceJoin,
                'hisa_total' => (float) $hisaTotal,
                'is_active' => $member->status === 'active',
            ];
        }

        return view('loans.apply', compact('loanTypes', 'members', 'memberEligibility'));
    }

    /**
     * Submit a loan application (member-initiated with collateral)
     */
    public function submitApplication(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        if (!$calendarYearId) {
            $calendarYear = CalendarYear::where('group_id', $groupId)->where('is_current', true)->first();
            $calendarYearId = $calendarYear?->id;
        }

        // Validate basic fields first
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'loan_amount' => 'required|numeric|min:1000',
            'loan_term_months' => 'required|integer|min:1|max:60',
            'application_date' => 'required|date',
            'reason' => 'required|string',
            'cosigner_ids' => 'required|array|min:1',
            'cosigner_ids.*' => 'exists:members,id|different:member_id',
            'collaterals' => 'required|array|min:1',
            'collaterals.*.description' => 'required|string|max:500',
            'collaterals.*.estimated_value' => 'required|numeric|min:0',
            'collaterals.*.document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Load loan type and check eligibility
        $loanType = LoanType::where('group_id', $groupId)->findOrFail($validated['loan_type_id']);
        $member = Member::where('group_id', $groupId)->findOrFail($validated['member_id']);

        if ($loanType->uses_eligibility_rules) {
            $eligibilityErrors = $this->checkEligibility($loanType, $member, $validated['loan_amount'], $calendarYearId);
            if (!empty($eligibilityErrors)) {
                return redirect()->back()->withInput()->withErrors(['eligibility' => implode(' ', $eligibilityErrors)]);
            }
        }

        $validated['calendar_year_id'] = $calendarYearId;
        $validated['source'] = 'applied';

        $loan = LoanService::createLoanApplication($validated, $groupId);

        // Attach cosigners
        foreach ($validated['cosigner_ids'] as $cosignerId) {
            if ($cosignerId == $validated['member_id']) continue;
            LoanCosigner::create([
                'loan_id' => $loan->id,
                'member_id' => $cosignerId,
            ]);
        }

        // Store multiple collaterals
        foreach ($validated['collaterals'] as $index => $collateralData) {
            $documentPath = null;
            $fileKey = "collaterals.{$index}.document";
            if ($request->hasFile($fileKey)) {
                $documentPath = $request->file($fileKey)->store('collaterals', 'public');
            }

            LoanCollateral::create([
                'loan_id' => $loan->id,
                'description' => $collateralData['description'],
                'estimated_value' => $collateralData['estimated_value'],
                'document_path' => $documentPath,
            ]);
        }

        return redirect()->route('loans.index', ['tab' => 'pending'])
            ->with('success', 'Loan application submitted successfully. Awaiting cosigner approval. Loan number: ' . $loan->loan_number);
    }

    /**
     * Check if a member meets the loan type eligibility rules.
     * Returns array of error messages (empty if eligible).
     */
    protected function checkEligibility(LoanType $loanType, Member $member, float $loanAmount, ?int $calendarYearId): array
    {
        $errors = [];

        // Membership period check
        if ($loanType->min_membership_months !== null && $loanType->min_membership_months > 0) {
            $monthsSinceJoin = $member->join_date ? Carbon::parse($member->join_date)->diffInMonths(now()) : 0;
            if ($monthsSinceJoin < $loanType->min_membership_months) {
                $errors[] = "Membership period too short. Required: {$loanType->min_membership_months} months, Current: " . (int) $monthsSinceJoin . " months.";
            }
        }

        // Active status check
        if ($loanType->requires_active_status && $member->status !== 'active') {
            $errors[] = "Member must have active status to apply for this loan type.";
        }

        // Hisa multiplier check
        if ($loanType->max_loan_hisa_multiplier !== null && $loanType->max_loan_hisa_multiplier > 0) {
            $hisaFund = CollectionFund::where('group_id', $member->group_id)->where('slug', 'hisa')->first();
            $hisaTotal = $hisaFund
                ? Collection::where('member_id', $member->id)->where('collection_fund_id', $hisaFund->id)->when($calendarYearId, fn($q) => $q->where('calendar_year_id', $calendarYearId))->sum('amount')
                : 0;
            $maxLoan = $hisaTotal * $loanType->max_loan_hisa_multiplier;
            if ($loanAmount > $maxLoan) {
                $errors[] = "Loan amount exceeds Hisa-based limit. Max allowed: " . number_format($maxLoan, 0) . " TZS (Hisa: " . number_format($hisaTotal, 0) . " TZS x {$loanType->max_loan_hisa_multiplier}). Requested: " . number_format($loanAmount, 0) . " TZS.";
            }
        }

        // Max loan amount check
        if ($loanType->max_loan_amount !== null && $loanAmount > $loanType->max_loan_amount) {
            $errors[] = "Loan amount exceeds maximum of " . number_format($loanType->max_loan_amount, 0) . " TZS for this loan type.";
        }

        return $errors;
    }

    public function show(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load(['member', 'loanType', 'repayments', 'approver', 'disburser', 'calendarYear', 'cosigners.member', 'collaterals', 'treasurerApprover', 'secretaryApprover', 'chairmanApprover']);

        return view('loans.show', compact('loan'));
    }

    /**
     * Legacy single-step approve (for backward compatibility, now does chairman approval)
     */
    public function approve(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $loan->update([
            'status' => 'approved',
            'chairman_approved_by' => auth()->id(),
            'chairman_approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved successfully.');
    }

    public function reject(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $loan->update([
            'status' => 'rejected',
            'approval_notes' => $request->rejection_reason,
        ]);

        return redirect()->route('loans.index', ['tab' => 'pending'])
            ->with('success', 'Loan rejected.');
    }

    // ==================== COSIGNER APPROVAL ====================

    public function approveCosigner(Loan $loan, LoanCosigner $cosigner)
    {
        $this->authorizeAccess($loan);

        if ($cosigner->loan_id !== $loan->id) {
            abort(403, 'Cosigner does not belong to this loan.');
        }

        $cosigner->approve();

        // Check if all cosigners have approved
        if ($loan->fresh()->allCosignersApproved()) {
            $loan->update(['cosigners_approved' => true]);
        }

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Cosigner approval recorded.');
    }

    public function rejectCosigner(Request $request, Loan $loan, LoanCosigner $cosigner)
    {
        $this->authorizeAccess($loan);

        if ($cosigner->loan_id !== $loan->id) {
            abort(403, 'Cosigner does not belong to this loan.');
        }

        $cosigner->reject($request->input('notes'));
        $loan->update(['status' => 'rejected']);

        return redirect()->route('loans.show', $loan)
            ->with('error', 'Loan rejected by cosigner.');
    }

    // ==================== TREASURER APPROVAL ====================

    public function approveTreasurer(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->allCosignersApproved()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'All cosigners must approve before treasurer can approve.');
        }

        $loan->update([
            'treasurer_approved_by' => auth()->id(),
            'treasurer_approved_at' => now(),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved by Treasurer.');
    }

    public function rejectTreasurer(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        $loan->update([
            'status' => 'rejected',
            'approval_notes' => ($loan->approval_notes ? $loan->approval_notes . "\n" : '') . 'Rejected by Treasurer: ' . $request->input('reason', 'No reason provided'),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('error', 'Loan rejected by Treasurer.');
    }

    // ==================== SECRETARY APPROVAL ====================

    public function approveSecretary(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->treasurerApproved()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Treasurer must approve before Secretary can approve.');
        }

        $loan->update([
            'secretary_approved_by' => auth()->id(),
            'secretary_approved_at' => now(),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan approved by Secretary.');
    }

    public function rejectSecretary(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        $loan->update([
            'status' => 'rejected',
            'approval_notes' => ($loan->approval_notes ? $loan->approval_notes . "\n" : '') . 'Rejected by Secretary: ' . $request->input('reason', 'No reason provided'),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('error', 'Loan rejected by Secretary.');
    }

    // ==================== CHAIRMAN APPROVAL ====================

    public function approveChairman(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->secretaryApproved()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Secretary must approve before Chairman can approve.');
        }

        $loan->update([
            'status' => 'approved',
            'chairman_approved_by' => auth()->id(),
            'chairman_approved_at' => now(),
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan fully approved by Chairman. Ready for disbursement.');
    }

    public function rejectChairman(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        $loan->update([
            'status' => 'rejected',
            'approval_notes' => ($loan->approval_notes ? $loan->approval_notes . "\n" : '') . 'Rejected by Chairman: ' . $request->input('reason', 'No reason provided'),
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('error', 'Loan rejected by Chairman.');
    }

    // ==================== DISBURSEMENT ====================

    public function disburseForm(Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->readyForDisbursement()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Loan is not yet fully approved. ' . $loan->approvalStageLabel());
        }

        return view('loans.disburse', compact('loan'));
    }

    public function disburse(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->readyForDisbursement()) {
            return redirect()->route('loans.show', $loan)
                ->with('error', 'Loan is not yet fully approved. All cosigners and officers must approve first.');
        }

        $validated = $request->validate([
            'disbursement_date' => 'required|date',
            'first_installment_date' => 'required|date|after_or_equal:disbursement_date',
            'notes' => 'nullable|string',
        ]);

        $validated['disbursed_by'] = auth()->id();

        LoanService::disburseLoan($loan, $validated);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Loan disbursed successfully. Repayment schedule has been generated.');
    }

    // ==================== REPAYMENTS ====================

    public function recordPayment(Request $request, LoanRepayment $repayment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_channel' => 'required|in:bank,mobile_money,cash,selcom,other',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['recorded_by'] = auth()->id();

        $repayment->markAsPaid(
            $validated['amount'],
            $validated['payment_channel'],
            $validated['transaction_reference']
        );

        return redirect()->back()->with('success', 'Payment recorded successfully.');
    }

    public function markSkipped(LoanRepayment $repayment)
    {
        $repayment->markAsSkipped();

        return redirect()->back()->with('success', 'Installment marked as skipped.');
    }

    // ==================== EDIT / UPDATE / DESTROY ====================

    public function edit(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $groupId = session('current_group_id');
        $loanTypes = LoanType::where('group_id', $groupId)->where('is_active', true)->get();
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $calendarYears = CalendarYear::where('group_id', $groupId)->get();

        return view('loans.edit', compact('loan', 'loanTypes', 'members', 'calendarYears'));
    }

    public function update(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (in_array($loan->status, ['disbursed', 'repaying', 'completed'])) {
            return redirect()->back()->with('error', 'Cannot edit a disbursed or completed loan.');
        }

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'loan_amount' => 'required|numeric|min:1000',
            'loan_term_months' => 'required|integer|min:1|max:60',
            'application_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        $loanType = LoanType::findOrFail($validated['loan_type_id']);
        $principal = $validated['loan_amount'];
        $rate = $loanType->rate_percentage;
        $term = $validated['loan_term_months'];

        if ($loanType->rate_type === 'reducing_balance') {
            $emi = LoanService::calculateEMI($principal, $rate, $term);
            $totalRepayment = round($emi * $term, 2);
            $totalInterest = round($totalRepayment - $principal, 2);
        } else {
            $totalInterest = LoanService::calculateFlatInterest($principal, $rate, $term);
            $totalRepayment = LoanService::calculateTotalRepayment($principal, $totalInterest);
            $emi = round($totalRepayment / $term, 2);
        }

        $loan->update(array_merge($validated, [
            'interest_rate' => $rate,
            'rate_type' => $loanType->rate_type,
            'total_interest_amount' => $totalInterest,
            'total_repayment_amount' => $totalRepayment,
            'monthly_installment' => $emi,
            'amount_remaining' => $totalRepayment,
        ]));

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        $this->authorizeAccess($loan);

        if (in_array($loan->status, ['disbursed', 'repaying', 'completed'])) {
            return redirect()->back()->with('error', 'Cannot delete a disbursed or completed loan.');
        }

        $loan->cosigners()->delete();
        $loan->collaterals()->delete();
        $loan->repayments()->delete();
        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }

    protected function authorizeAccess(Loan $loan)
    {
        $groupId = session('current_group_id');
        if ($loan->group_id != $groupId && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have access to this loan.');
        }
    }
}
