<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Member;
use App\Models\Collection;
use App\Models\CollectionFund;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Expenditure;
use App\Models\Payment;
use App\Models\CalendarYear;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_admin_reports')->only(['adminRevenue', 'adminSubscriptions']);
        $this->middleware('permission:view_group_reports')->only(['monthlyCollections', 'loansReport', 'yearEndReport', 'expenditureReport', 'financialStatement']);
    }

    public function adminRevenue(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $revenueQuery = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        $totalRevenue = $revenueQuery->sum('amount');
        $totalTransactions = $revenueQuery->count();

        $monthlyBreakdown = Payment::select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $planBreakdown = Payment::select(
                'subscription_plans.name',
                DB::raw('SUM(payments.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->join('subscription_plans', 'payments.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('payments.status', 'completed')
            ->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        $recentPayments = Payment::with(['group', 'subscriptionPlan'])
            ->where('status', 'completed')
            ->whereBetween('paid_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()
            ->paginate(50);

        return view('reports.admin_revenue', compact(
            'totalRevenue', 'totalTransactions', 'monthlyBreakdown',
            'planBreakdown', 'recentPayments', 'dateFrom', 'dateTo'
        ));
    }

    public function adminSubscriptions(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfYear()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $groups = Group::with(['chairman', 'subscriptionPlan', 'payments'])
            ->whereBetween('subscription_date', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->latest()
            ->paginate(50);

        $statusSummary = Group::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $planSummary = Group::select('subscription_plans.name', DB::raw('COUNT(*) as count'))
            ->join('subscription_plans', 'groups.subscription_plan_id', '=', 'subscription_plans.id')
            ->groupBy('subscription_plans.id', 'subscription_plans.name')
            ->get();

        return view('reports.admin_subscriptions', compact('groups', 'statusSummary', 'planSummary', 'dateFrom', 'dateTo'));
    }

    public function monthlyCollections(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Collection::with(['member', 'collectionFund'])->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('payment_date', [$dateFrom, $dateTo]);
        }

        $collections = $query->latest()->get();

        $fundSummary = DB::table('collections')
            ->join('collection_funds', 'collections.collection_fund_id', '=', 'collection_funds.id')
            ->where('collections.group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('collections.calendar_year_id', $calendarYearId);
            })
            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                return $q->whereBetween('collections.payment_date', [$dateFrom, $dateTo]);
            })
            ->select('collection_funds.name', 'collection_funds.slug', DB::raw('SUM(collections.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('collection_funds.id', 'collection_funds.name', 'collection_funds.slug')
            ->get();

        $monthlySummary = DB::table('collections')
            ->select(DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $memberSummary = DB::table('collections')
            ->join('members', 'collections.member_id', '=', 'members.id')
            ->where('collections.group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('collections.calendar_year_id', $calendarYearId);
            })
            ->select(
                'members.first_name', 'members.middle_name', 'members.last_name',
                DB::raw('SUM(collections.amount) as total')
            )
            ->groupBy('members.id', 'members.first_name', 'members.middle_name', 'members.last_name')
            ->orderByDesc('total')
            ->get();

        return view('reports.monthly_collections', compact('collections', 'fundSummary', 'monthlySummary', 'memberSummary'));
    }

    public function loansReport(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Loan::with(['member', 'loanType'])->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('application_date', [$dateFrom, $dateTo]);
        }

        $loans = $query->latest()->get();

        $statusSummary = DB::table('loans')
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(loan_amount) as total'))
            ->groupBy('status')
            ->get();

        $totalApplied = $loans->sum('loan_amount');
        $totalDisbursed = $loans->whereIn('status', ['disbursed', 'repaying', 'completed', 'defaulted'])->sum('loan_amount');
        $totalRepaid = $loans->sum('amount_paid');
        $totalDefaulted = $loans->where('status', 'defaulted')->sum('amount_remaining');

        return view('reports.loans_report', compact(
            'loans', 'statusSummary', 'totalApplied', 'totalDisbursed',
            'totalRepaid', 'totalDefaulted'
        ));
    }

    public function yearEndReport(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        if (!$calendarYearId) {
            return redirect()->back()->with('error', 'Please select a calendar year first.');
        }

        $group = Group::find($groupId);
        $calendarYear = CalendarYear::find($calendarYearId);

        $fundTotals = DB::table('collections')
            ->join('collection_funds', 'collections.collection_fund_id', '=', 'collection_funds.id')
            ->where('collections.group_id', $groupId)
            ->where('collections.calendar_year_id', $calendarYearId)
            ->select('collection_funds.name', 'collection_funds.slug', DB::raw('SUM(collections.amount) as total'))
            ->groupBy('collection_funds.id', 'collection_funds.name', 'collection_funds.slug')
            ->get();

        $totalCollections = $fundTotals->sum('total');
        $hisaTotal = $fundTotals->where('slug', 'hisa')->first()?->total ?? 0;
        $jamiiTotal = $fundTotals->where('slug', 'jamii')->first()?->total ?? 0;
        $totalProfit = $fundTotals->whereNotIn('slug', ['hisa', 'jamii'])->sum('total');

        $totalExpenditure = Expenditure::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->sum('amount');

        $totalLoansApplied = Loan::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->sum('loan_amount');

        $totalLoansDisbursed = Loan::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->whereIn('status', ['disbursed', 'repaying', 'completed', 'defaulted'])
            ->sum('loan_amount');

        $totalLoansRepaid = Loan::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->sum('amount_paid');

        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $memberData = [];

        foreach ($members as $member) {
            $memberCollections = [];
            foreach ($fundTotals as $fund) {
                $memberCollections[$fund->slug] = Collection::where('member_id', $member->id)
                    ->where('collection_fund_id', CollectionFund::where('slug', $fund->slug)->where('group_id', $groupId)->value('id'))
                    ->where('calendar_year_id', $calendarYearId)
                    ->sum('amount');
            }

            $memberLoans = Loan::where('member_id', $member->id)
                ->where('calendar_year_id', $calendarYearId)
                ->sum('loan_amount');

            $memberShares = $memberCollections['hisa'] ?? 0;
            $dividend = $totalCollections > 0 ? ($memberShares / max(1, $fundTotals->where('slug', 'hisa')->first()?->total ?? 1)) * $totalProfit : 0;

            $memberData[] = [
                'member' => $member,
                'collections' => $memberCollections,
                'total_loans' => $memberLoans,
                'dividend' => $dividend,
            ];
        }

        return view('reports.year_end', compact(
            'group', 'calendarYear', 'fundTotals', 'totalCollections',
            'totalProfit', 'totalExpenditure', 'totalLoansApplied',
            'totalLoansDisbursed', 'totalLoansRepaid', 'memberData'
        ));
    }

    public function expenditureReport(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Expenditure::with(['recorder'])->where('group_id', $groupId);

        if ($calendarYearId) {
            $query->where('calendar_year_id', $calendarYearId);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('expense_date', [$dateFrom, $dateTo]);
        }

        $expenditures = $query->latest()->get();

        $categorySummary = DB::table('expenditures')
            ->where('group_id', $groupId)
            ->when($calendarYearId, function ($q) use ($calendarYearId) {
                return $q->where('calendar_year_id', $calendarYearId);
            })
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        $totalExpenditure = $expenditures->sum('amount');

        return view('reports.expenditure', compact('expenditures', 'categorySummary', 'totalExpenditure'));
    }

    public function financialStatement(Request $request)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');

        if (!$calendarYearId) {
            return redirect()->back()->with('error', 'Please select a calendar year first.');
        }

        $group = Group::find($groupId);
        $calendarYear = CalendarYear::find($calendarYearId);

        $fundTotals = DB::table('collections')
            ->join('collection_funds', 'collections.collection_fund_id', '=', 'collection_funds.id')
            ->where('collections.group_id', $groupId)
            ->where('collections.calendar_year_id', $calendarYearId)
            ->select('collection_funds.name', 'collection_funds.slug', DB::raw('SUM(collections.amount) as total'))
            ->groupBy('collection_funds.id', 'collection_funds.name', 'collection_funds.slug')
            ->get();

        $totalIncome = $fundTotals->sum('total');
        $totalExpenditure = Expenditure::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->sum('amount');

        $netPosition = $totalIncome - $totalExpenditure;

        $totalAssets = $totalIncome;
        $totalLiabilities = Loan::where('group_id', $groupId)
            ->where('calendar_year_id', $calendarYearId)
            ->whereIn('status', ['disbursed', 'repaying'])
            ->sum('amount_remaining');

        return view('reports.financial_statement', compact(
            'group', 'calendarYear', 'fundTotals', 'totalIncome',
            'totalExpenditure', 'netPosition', 'totalAssets', 'totalLiabilities'
        ));
    }

    public function exportPdf(Request $request, $type)
    {
        $groupId = session('current_group_id');
        $calendarYearId = session('current_calendar_year_id');
        $group = Group::find($groupId);

        $data = compact('group', 'calendarYearId');
        $view = "reports.pdf.{$type}";

        $pdf = PDF::loadView($view, $data);
        return $pdf->download("{$type}_report_" . date('Y-m-d') . ".pdf");
    }
}
