<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\CollectionFund;
use App\Models\CalendarYear;
use App\Models\LoanType;
use App\Models\Group;
use App\Models\GroupSetting;
use Illuminate\Support\Facades\DB;
use App\Traits\DataTableTrait;

class SettingsController extends Controller
{
    use DataTableTrait;
    public function __construct()
    {
        $this->middleware('permission:manage_subscription_plans')->only(['subscriptionPlans', 'storePlan', 'updatePlan', 'destroyPlan']);
        $this->middleware('permission:manage_collection_funds')->only(['collectionFunds', 'storeFund', 'updateFund', 'destroyFund']);
        $this->middleware('permission:manage_calendar_years')->only(['calendarYears', 'storeCalendarYear', 'updateCalendarYear', 'destroyCalendarYear']);
        $this->middleware('permission:manage_loan_types')->only(['loanTypes', 'storeLoanType', 'updateLoanType', 'destroyLoanType']);
        $this->middleware('permission:manage_loan_types')->only(['withdrawalSettings', 'updateWithdrawalSettings']);
    }

    // Subscription Plans (Super Admin)
    public function subscriptionPlans()
    {
        $plans = SubscriptionPlan::orderBy('display_order')->get();
        return view('settings.subscription_plans', compact('plans'));
    }

    public function subscriptionPlansData(Request $request)
    {
        $query = SubscriptionPlan::query();
        $result = $this->processDataTable($request, $query, ['name', 'slug', 'description']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = $item->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
            $features = isset($item->features) && is_array($item->features) ? implode(', ', array_slice($item->features, 0, 3)) : '-';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editPlanModal' . $item->id . '" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>';
            $actions .= '<form method="POST" action="' . route('settings.subscription_plans.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->name) . '</strong><br><small class="text-muted">' . e($item->slug) . '</small>',
                'price' => '<strong class="text-primary">' . number_format($item->price, 0) . ' TZS</strong><br><small class="text-muted">' . ucfirst($item->billing_cycle) . '</small>',
                'duration' => $item->duration_days . ' days',
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        SubscriptionPlan::create($validated);

        return redirect()->route('settings.subscription_plans')->with('success', 'Subscription plan created.');
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,annually',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $plan->update($validated);

        return redirect()->route('settings.subscription_plans')->with('success', 'Subscription plan updated.');
    }

    public function destroyPlan(SubscriptionPlan $plan)
    {
        if ($plan->groups()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete plan with active subscriptions.');
        }
        $plan->delete();
        return redirect()->route('settings.subscription_plans')->with('success', 'Subscription plan deleted.');
    }

    // Collection Funds (Group Admin)
    public function collectionFunds()
    {
        $groupId = session('current_group_id');
        $funds = CollectionFund::where('group_id', $groupId)->orderBy('display_order')->get();
        return view('settings.collection_funds', compact('funds'));
    }

    public function collectionFundsData(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        $query = CollectionFund::where('group_id', $groupId);
        $result = $this->processDataTable($request, $query, ['name', 'slug', 'fund_type']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = $item->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
            $mandatoryBadge = $item->is_mandatory ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-light text-dark">No</span>';
            $typeBadge = '<span class="badge bg-light text-dark">' . ucfirst(str_replace('_', ' ', $item->fund_type)) . '</span>';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editFundModal' . $item->id . '" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>';
            $actions .= '<form method="POST" action="' . route('settings.collection_funds.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->name) . '</strong><br><small class="text-muted">' . e($item->slug) . '</small>',
                'fund_type' => $typeBadge,
                'is_mandatory' => $mandatoryBadge,
                'default_amount' => $item->default_amount ? number_format($item->default_amount, 0) . ' TZS' : '-',
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storeFund(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fund_type' => 'required|in:savings,contribution,fee,fine,project,other',
            'is_mandatory' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $validated['group_id'] = $groupId;
        $validated['is_mandatory'] = $request->boolean('is_mandatory', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        CollectionFund::create($validated);

        return redirect()->route('settings.collection_funds')->with('success', 'Collection fund created.');
    }

    public function updateFund(Request $request, CollectionFund $fund)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fund_type' => 'required|in:savings,contribution,fee,fine,project,other',
            'is_mandatory' => 'boolean',
            'default_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        $validated['is_mandatory'] = $request->boolean('is_mandatory', false);
        $validated['is_active'] = $request->boolean('is_active', true);
        $fund->update($validated);

        return redirect()->route('settings.collection_funds')->with('success', 'Collection fund updated.');
    }

    public function destroyFund(CollectionFund $fund)
    {
        if ($fund->collections()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete fund with recorded collections.');
        }
        $fund->delete();
        return redirect()->route('settings.collection_funds')->with('success', 'Collection fund deleted.');
    }

    // Calendar Years (Group Admin)
    public function calendarYears()
    {
        $groupId = session('current_group_id');
        $calendarYears = CalendarYear::where('group_id', $groupId)->orderBy('year', 'desc')->get();
        return view('settings.calendar_years', compact('calendarYears'));
    }

    public function calendarYearsData(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        $query = CalendarYear::where('group_id', $groupId);
        $result = $this->processDataTable($request, $query, ['name', 'year']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = match($item->status) {
                'active' => '<span class="badge bg-success">Active</span>',
                'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                'closed' => '<span class="badge bg-dark">Closed</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $currentBadge = $item->is_current ? '<span class="badge bg-primary">Current</span>' : '';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editYearModal' . $item->id . '" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>';
            $actions .= '<form method="POST" action="' . route('settings.calendar_years.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->name) . '</strong><br><small class="text-muted">Year ' . e($item->year) . '</small>',
                'period' => '<small>' . \Carbon\Carbon::parse($item->start_date)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($item->end_date)->format('M d, Y') . '</small>',
                'status' => $statusBadge . ' ' . $currentBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storeCalendarYear(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:4',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,closed',
            'is_current' => 'boolean',
        ]);

        $validated['group_id'] = $groupId;
        $isCurrent = $request->boolean('is_current', false);
        $validated['is_current'] = $isCurrent;

        $calendarYear = CalendarYear::create($validated);

        if ($isCurrent) {
            CalendarYear::where('group_id', $groupId)
                ->where('id', '!=', $calendarYear->id)
                ->update(['is_current' => false]);
        }

        return redirect()->route('settings.calendar_years')->with('success', 'Calendar year created.');
    }

    public function updateCalendarYear(Request $request, CalendarYear $calendarYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:4',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,closed',
            'is_current' => 'boolean',
        ]);

        $isCurrent = $request->boolean('is_current', false);
        $validated['is_current'] = $isCurrent;

        $calendarYear->update($validated);

        if ($isCurrent) {
            CalendarYear::where('group_id', $calendarYear->group_id)
                ->where('id', '!=', $calendarYear->id)
                ->update(['is_current' => false]);
        }

        return redirect()->route('settings.calendar_years')->with('success', 'Calendar year updated.');
    }

    public function destroyCalendarYear(CalendarYear $calendarYear)
    {
        if ($calendarYear->collections()->count() > 0 || $calendarYear->loans()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete calendar year with recorded data.');
        }
        $calendarYear->delete();
        return redirect()->route('settings.calendar_years')->with('success', 'Calendar year deleted.');
    }

    // Loan Types (Group Admin)
    public function loanTypes()
    {
        $groupId = session('current_group_id');
        $loanTypes = LoanType::where('group_id', $groupId)->get();
        return view('settings.loan_types', compact('loanTypes'));
    }

    public function loanTypesData(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        $query = LoanType::where('group_id', $groupId);
        $result = $this->processDataTable($request, $query, ['name', 'description']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = $item->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
            $rateTypeBadge = '<span class="badge bg-light text-dark">' . ucfirst(str_replace('_', ' ', $item->rate_type)) . '</span>';
            $eligibilityBadge = $item->uses_eligibility_rules ? '<span class="badge bg-info">Rules</span>' : '<span class="badge bg-light text-dark">None</span>';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editLoanTypeModal' . $item->id . '" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></button>';
            $actions .= '<form method="POST" action="' . route('settings.loan_types.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->name) . '</strong><br><small class="text-muted">' . e($item->description ?? '-') . '</small>',
                'rate' => '<strong>' . $item->rate_percentage . '%</strong> ' . $rateTypeBadge,
                'processing_fee' => $item->processing_fee . '%',
                'eligibility' => $eligibilityBadge,
                'term' => $item->max_loan_term_months . ' months',
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storeLoanType(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rate_type' => 'required|in:flat,reducing_balance,simple',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'processing_fee' => 'nullable|numeric|min:0|max:100',
            'uses_eligibility_rules' => 'boolean',
            'min_membership_months' => 'nullable|integer|min:0',
            'requires_active_status' => 'boolean',
            'max_loan_hisa_multiplier' => 'nullable|numeric|min:0',
            'max_loan_term_months' => 'required|integer|min:1|max:120',
            'max_loan_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['group_id'] = $groupId;
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['uses_eligibility_rules'] = $request->boolean('uses_eligibility_rules', false);
        $validated['requires_active_status'] = $request->boolean('requires_active_status', true);

        LoanType::create($validated);

        return redirect()->route('settings.loan_types')->with('success', 'Loan type created.');
    }

    public function updateLoanType(Request $request, LoanType $loanType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rate_type' => 'required|in:flat,reducing_balance,simple',
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'processing_fee' => 'nullable|numeric|min:0|max:100',
            'uses_eligibility_rules' => 'boolean',
            'min_membership_months' => 'nullable|integer|min:0',
            'requires_active_status' => 'boolean',
            'max_loan_hisa_multiplier' => 'nullable|numeric|min:0',
            'max_loan_term_months' => 'required|integer|min:1|max:120',
            'max_loan_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['uses_eligibility_rules'] = $request->boolean('uses_eligibility_rules', false);
        $validated['requires_active_status'] = $request->boolean('requires_active_status', true);
        $loanType->update($validated);

        return redirect()->route('settings.loan_types')->with('success', 'Loan type updated.');
    }

    public function destroyLoanType(LoanType $loanType)
    {
        if ($loanType->loans()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete loan type with existing loans.');
        }
        $loanType->delete();
        return redirect()->route('settings.loan_types')->with('success', 'Loan type deleted.');
    }

    // ==================== Hisa Withdrawal Settings ====================

    public function withdrawalSettings()
    {
        $groupId = session('current_group_id');
        $withdrawalPercent = GroupSetting::getValue($groupId, 'hisa_withdrawal_percent', 100);
        $withdrawalDeductionPercent = GroupSetting::getValue($groupId, 'hisa_withdrawal_deduction_percent', 0);

        return view('settings.withdrawal', compact('withdrawalPercent', 'withdrawalDeductionPercent'));
    }

    public function updateWithdrawalSettings(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'hisa_withdrawal_percent' => 'required|numeric|min:0|max:100',
            'hisa_withdrawal_deduction_percent' => 'required|numeric|min:0|max:100',
        ]);

        GroupSetting::setValue(
            $groupId,
            'hisa_withdrawal_percent',
            $validated['hisa_withdrawal_percent'],
            'decimal',
            'Hisa Withdrawal Percentage',
            'Percentage of total Hisa contributions a member can withdraw when leaving the group'
        );

        GroupSetting::setValue(
            $groupId,
            'hisa_withdrawal_deduction_percent',
            $validated['hisa_withdrawal_deduction_percent'],
            'decimal',
            'Hisa Withdrawal Deduction Percentage',
            'Percentage deducted from total Hisa contributions when a member withdraws from the group'
        );

        return redirect()->route('settings.withdrawal')->with('success', 'Withdrawal settings updated.');
    }
}
