<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Member;
use App\Models\Collection;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Expenditure;
use App\Models\CalendarYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $groupId = session('current_group_id');
        if (!$groupId) {
            return redirect()->route('landing')->with('error', 'No group selected.');
        }

        $group = Group::findOrFail($groupId);
        $calendarYearId = session('current_calendar_year_id');
        
        if (!$calendarYearId) {
            $currentCalendarYear = $group->currentCalendarYear;
            $calendarYearId = $currentCalendarYear?->id;
        }

        $totalMembers = $group->members()->count();
        $activeMembers = $group->members()->where('status', 'active')->count();
        $inactiveMembers = $totalMembers - $activeMembers;

        $collectionsQuery = $group->collections();
        if ($calendarYearId) {
            $collectionsQuery->where('calendar_year_id', $calendarYearId);
        }
        $totalCollections = $collectionsQuery->sum('amount');

        $collectionsByFund = DB::table('collections')
            ->join('collection_funds', 'collections.collection_fund_id', '=', 'collection_funds.id')
            ->where('collections.group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('collections.calendar_year_id', $calendarYearId);
            })
            ->select('collection_funds.name', 'collection_funds.slug', DB::raw('SUM(collections.amount) as total'))
            ->groupBy('collection_funds.id', 'collection_funds.name', 'collection_funds.slug')
            ->get();

        $loansQuery = $group->loans();
        if ($calendarYearId) {
            $loansQuery->where('calendar_year_id', $calendarYearId);
        }
        $totalLoansProvided = $loansQuery->whereIn('status', ['disbursed', 'repaying', 'completed', 'defaulted'])->count();
        $totalLoanAmountDisbursed = $loansQuery->whereIn('status', ['disbursed', 'repaying', 'completed', 'defaulted'])->sum('loan_amount');

        $expendituresQuery = $group->expenditures();
        if ($calendarYearId) {
            $expendituresQuery->where('calendar_year_id', $calendarYearId);
        }
        $totalExpenditure = $expendituresQuery->sum('amount');

        $loanDefaulters = $group->loans()
            ->whereIn('status', ['defaulted', 'repaying'])
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->count();

        $totalDefaultedAmount = $group->loans()
            ->whereIn('status', ['defaulted', 'repaying'])
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->sum('total_defaulted_amount');

        $recentCollections = $group->collections()
            ->with(['member', 'collectionFund'])
            ->latest()
            ->limit(10)
            ->get();

        $recentLoans = $group->loans()
            ->with(['member'])
            ->latest()
            ->limit(10)
            ->get();

        $recentMembers = $group->members()
            ->latest()
            ->limit(10)
            ->get();

        $monthlyCollections = DB::table('collections')
            ->select(DB::raw('MONTH(payment_date) as month'), DB::raw('SUM(amount) as total'))
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->whereYear('payment_date', date('Y'))
            ->groupBy(DB::raw('MONTH(payment_date)'))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $monthlyLoans = DB::table('loans')
            ->select(DB::raw('MONTH(disbursement_date) as month'), DB::raw('SUM(loan_amount) as total'))
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->whereYear('disbursement_date', date('Y'))
            ->groupBy(DB::raw('MONTH(disbursement_date)'))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $monthlyExpenditures = DB::table('expenditures')
            ->select(DB::raw('MONTH(expense_date) as month'), DB::raw('SUM(amount) as total'))
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->whereYear('expense_date', date('Y'))
            ->groupBy(DB::raw('MONTH(expense_date)'))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $calendarYears = $group->calendarYears()->get();
        $groupStatus = [
            'days_remaining' => $group->daysUntilExpiry(),
            'is_trial' => $group->isOnTrial(),
            'trial_expired' => $group->isTrialExpired(),
            'subscription_expired' => $group->isSubscriptionExpired(),
        ];

        return view('dashboard.index', compact(
            'group', 'totalMembers', 'activeMembers', 'inactiveMembers',
            'totalCollections', 'collectionsByFund', 'totalLoansProvided',
            'totalLoanAmountDisbursed', 'totalExpenditure', 'loanDefaulters',
            'totalDefaultedAmount', 'recentCollections', 'recentLoans',
            'recentMembers', 'monthlyCollections', 'monthlyLoans',
            'monthlyExpenditures', 'calendarYears', 'calendarYearId',
            'groupStatus'
        ));
    }

    public function adminDashboard()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            abort(403);
        }

        $totalGroups = Group::count();
        $activeGroups = Group::where('status', 'active')->count();
        $pendingGroups = Group::where('status', 'pending')->count();
        $expiredGroups = Group::where('status', 'expired')->orWhere(function ($q) {
            $q->whereNotNull('subscription_end_date')->where('subscription_end_date', '<', now());
        })->count();

        $currentYearRevenue = Payment::whereYear('paid_at', date('Y'))
            ->where('status', 'completed')
            ->sum('amount');

        $currentMonthRevenue = Payment::whereYear('paid_at', date('Y'))
            ->whereMonth('paid_at', date('m'))
            ->where('status', 'completed')
            ->sum('amount');

        $totalMembers = Member::count();
        $totalActiveMembers = Member::where('status', 'active')->count();

        $monthlyRevenue = Payment::select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('paid_at', date('Y'))
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(paid_at)'))
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $groupsByPlan = Group::select('subscription_plans.name', DB::raw('COUNT(*) as total'))
            ->join('subscription_plans', 'groups.subscription_plan_id', '=', 'subscription_plans.id')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        $recentGroups = Group::with(['chairman', 'subscriptionPlan'])
            ->latest()
            ->limit(10)
            ->get();

        $recentPayments = Payment::with(['group', 'subscriptionPlan'])
            ->where('status', 'completed')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact(
            'totalGroups', 'activeGroups', 'pendingGroups', 'expiredGroups',
            'currentYearRevenue', 'currentMonthRevenue', 'totalMembers',
            'totalActiveMembers', 'monthlyRevenue', 'groupsByPlan',
            'recentGroups', 'recentPayments'
        ));
    }

    public function setCalendarYear(Request $request)
    {
        $request->validate([
            'calendar_year_id' => 'required|exists:calendar_years,id',
        ]);

        session(['current_calendar_year_id' => $request->calendar_year_id]);

        return redirect()->back()->with('success', 'Calendar year updated successfully.');
    }

    public function setGroup(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        session(['current_group_id' => $request->group_id]);
        session()->forget('current_calendar_year_id');

        return redirect()->route('dashboard')->with('success', 'Group switched successfully.');
    }
}
