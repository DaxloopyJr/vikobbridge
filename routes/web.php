<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExpenditureController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\GroupRoleController;
use App\Http\Controllers\DisciplineController;

/*
|--------------------------------------------------------------------------
| Landing Pages (Public)
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::get('/about', [LandingPageController::class, 'about'])->name('about');
Route::get('/contact', [LandingPageController::class, 'contact'])->name('contact');
Route::post('/contact', [LandingPageController::class, 'submitContact'])->name('contact.submit');
Route::get('/plans', [LandingPageController::class, 'plans'])->name('plans');

/*
|--------------------------------------------------------------------------
| Location API Routes (Public - for registration forms)
|--------------------------------------------------------------------------
*/
Route::prefix('api/locations')->group(function () {
    Route::get('/regions', [LocationController::class, 'regions'])->name('api.locations.regions');
    Route::get('/regions/search', [LocationController::class, 'searchRegions'])->name('api.locations.regions.search');
    Route::get('/districts/search', [LocationController::class, 'searchDistricts'])->name('api.locations.districts.search');
    Route::get('/wards/search', [LocationController::class, 'searchWards'])->name('api.locations.wards.search');
    Route::get('/villages/search', [LocationController::class, 'searchVillages'])->name('api.locations.villages.search');
    Route::get('/regions/{region}/districts', [LocationController::class, 'districtsByRegion'])->name('api.locations.districts');
    Route::get('/districts/{district}/wards', [LocationController::class, 'wardsByDistrict'])->name('api.locations.wards');
    Route::get('/wards/{ward}/villages', [LocationController::class, 'villagesByWard'])->name('api.locations.villages');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Payment Callback Routes (Public)
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Profile Wizard (First-time setup)
    Route::get('/profile/wizard', [ProfileController::class, 'wizard'])->name('profile.wizard');
    Route::post('/profile/wizard', [ProfileController::class, 'storeWizard'])->name('profile.wizard.store');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/calendar-year', [DashboardController::class, 'setCalendarYear'])->name('dashboard.calendar-year');
    Route::post('/dashboard/group', [DashboardController::class, 'setGroup'])->name('dashboard.group');

    // Subscription / Payment
    Route::get('/subscription/expired', [PaymentController::class, 'expired'])->name('subscription.expired');
    Route::get('/subscription/pay', [PaymentController::class, 'pay'])->name('subscription.pay');
    Route::get('/subscription/control-number', [PaymentController::class, 'controlNumber'])->name('subscription.control-number');
    Route::get('/subscription/history', [PaymentController::class, 'history'])->name('subscription.history');

    // Group Management (Super Admin)
    Route::middleware(['permission:manage_groups'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/data', [GroupController::class, 'data'])->name('groups.data');
        Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
        Route::post('/groups/{group}/approve', [GroupController::class, 'approve'])->name('groups.approve');
        Route::post('/groups/{group}/suspend', [GroupController::class, 'suspend'])->name('groups.suspend');
        Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::get('/groups/renewals', [GroupController::class, 'renewals'])->name('groups.renewals');
        Route::post('/groups/{group}/notify', [GroupController::class, 'sendNotification'])->name('groups.notify');
    });

    // Member Management
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_members'])->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/data', [MemberController::class, 'data'])->name('members.data');
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/members/{member}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
        Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');
    });

    // Collections Management
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_collections'])->group(function () {
        Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
        Route::get('/collections/data', [CollectionController::class, 'data'])->name('collections.data');
        Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
        Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
        Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
        Route::get('/collections/{collection}/edit', [CollectionController::class, 'edit'])->name('collections.edit');
        Route::put('/collections/{collection}', [CollectionController::class, 'update'])->name('collections.update');
        Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
        Route::post('/collections/bulk', [CollectionController::class, 'bulkCreate'])->name('collections.bulk');
        Route::post('/collections/fines', [CollectionController::class, 'storeFine'])->name('collections.fines.store');
    });

    // Disciplines — Fines & Penalties (separate from collections)
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_collections'])->prefix('disciplines')->group(function () {
        Route::get('/', [DisciplineController::class, 'index'])->name('disciplines.index');
        Route::get('/fines', [DisciplineController::class, 'fines'])->name('disciplines.fines');
        Route::get('/penalties', [DisciplineController::class, 'penalties'])->name('disciplines.penalties');
        Route::get('/data', [DisciplineController::class, 'data'])->name('disciplines.data');
        Route::post('/', [DisciplineController::class, 'store'])->name('disciplines.store');
        Route::post('/{discipline}/pay', [DisciplineController::class, 'recordPayment'])->name('disciplines.pay');
        Route::post('/{discipline}/skip', [DisciplineController::class, 'skip'])->name('disciplines.skip');
        Route::delete('/{discipline}', [DisciplineController::class, 'destroy'])->name('disciplines.destroy');
    });

    // Loan Management
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_loans'])->group(function () {
        Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/data', [LoanController::class, 'data'])->name('loans.data');

        // Record Loan (admin-initiated)
        Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');

        // Apply Loan (member-initiated with collateral)
        Route::get('/loans/apply', [LoanController::class, 'apply'])->name('loans.apply');
        Route::post('/loans/apply', [LoanController::class, 'submitApplication'])->name('loans.apply.submit');

        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        Route::get('/loans/{loan}/edit', [LoanController::class, 'edit'])->name('loans.edit');
        Route::put('/loans/{loan}', [LoanController::class, 'update'])->name('loans.update');
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');

        // Legacy approval
        Route::post('/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');

        // Cosigner approval
        Route::post('/loans/{loan}/cosigners/{cosigner}/approve', [LoanController::class, 'approveCosigner'])->name('loans.cosigner.approve');
        Route::post('/loans/{loan}/cosigners/{cosigner}/reject', [LoanController::class, 'rejectCosigner'])->name('loans.cosigner.reject');

        // Treasurer approval
        Route::post('/loans/{loan}/treasurer/approve', [LoanController::class, 'approveTreasurer'])->name('loans.treasurer.approve');
        Route::post('/loans/{loan}/treasurer/reject', [LoanController::class, 'rejectTreasurer'])->name('loans.treasurer.reject');

        // Secretary approval
        Route::post('/loans/{loan}/secretary/approve', [LoanController::class, 'approveSecretary'])->name('loans.secretary.approve');
        Route::post('/loans/{loan}/secretary/reject', [LoanController::class, 'rejectSecretary'])->name('loans.secretary.reject');

        // Chairman approval
        Route::post('/loans/{loan}/chairman/approve', [LoanController::class, 'approveChairman'])->name('loans.chairman.approve');
        Route::post('/loans/{loan}/chairman/reject', [LoanController::class, 'rejectChairman'])->name('loans.chairman.reject');

        // Disbursement
        Route::get('/loans/{loan}/disburse', [LoanController::class, 'disburseForm'])->name('loans.disburse.form');
        Route::post('/loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');

        // Repayments
        Route::post('/loan-repayments/{repayment}/pay', [LoanController::class, 'recordPayment'])->name('loan-repayments.pay');
        Route::post('/loan-repayments/{repayment}/skip', [LoanController::class, 'markSkipped'])->name('loan-repayments.skip');
    });

    // Expenditure Management
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_expenditures'])->group(function () {
        Route::get('/expenditures', [ExpenditureController::class, 'index'])->name('expenditures.index');
        Route::get('/expenditures/data', [ExpenditureController::class, 'data'])->name('expenditures.data');
        Route::get('/expenditures/create', [ExpenditureController::class, 'create'])->name('expenditures.create');
        Route::post('/expenditures', [ExpenditureController::class, 'store'])->name('expenditures.store');
        Route::get('/expenditures/{expenditure}', [ExpenditureController::class, 'show'])->name('expenditures.show');
        Route::get('/expenditures/{expenditure}/edit', [ExpenditureController::class, 'edit'])->name('expenditures.edit');
        Route::put('/expenditures/{expenditure}', [ExpenditureController::class, 'update'])->name('expenditures.update');
        Route::delete('/expenditures/{expenditure}', [ExpenditureController::class, 'destroy'])->name('expenditures.destroy');
        Route::post('/expenditures/{expenditure}/approve', [ExpenditureController::class, 'approve'])->name('expenditures.approve');
        Route::post('/expenditures/{expenditure}/reject', [ExpenditureController::class, 'reject'])->name('expenditures.reject');
    });

    // Reports - Admin
    Route::middleware(['permission:view_admin_reports'])->prefix('reports/admin')->group(function () {
        Route::get('/revenue', [ReportController::class, 'adminRevenue'])->name('reports.admin.revenue');
        Route::get('/subscriptions', [ReportController::class, 'adminSubscriptions'])->name('reports.admin.subscriptions');
    });

    // Reports - Group
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_group_reports'])->prefix('reports')->group(function () {
        Route::get('/collections', [ReportController::class, 'monthlyCollections'])->name('reports.collections');
        Route::get('/loans', [ReportController::class, 'loansReport'])->name('reports.loans');
        Route::get('/year-end', [ReportController::class, 'yearEndReport'])->name('reports.year-end');
        Route::get('/expenditure', [ReportController::class, 'expenditureReport'])->name('reports.expenditure');
        Route::get('/financial-statement', [ReportController::class, 'financialStatement'])->name('reports.financial-statement');
        Route::get('/export/{type}', [ReportController::class, 'exportPdf'])->name('reports.export');
    });

    // User Management - System (super admin only)
    Route::middleware(['permission:manage_system_users'])->prefix('users')->group(function () {
        Route::get('/system', [UserController::class, 'systemUsers'])->name('users.system');
        Route::get('/system/data', [UserController::class, 'systemUsersData'])->name('users.system.data');
        Route::post('/system', [UserController::class, 'storeSystemUser'])->name('users.system.store');
        Route::get('/system/{user}/edit', [UserController::class, 'editSystemUser'])->name('users.system.edit');
        Route::put('/system/{user}', [UserController::class, 'updateSystemUser'])->name('users.system.update');
        Route::delete('/system/{user}', [UserController::class, 'destroySystemUser'])->name('users.system.destroy');
    });

    // User Management - Group
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:manage_group_users'])->prefix('users')->group(function () {
        Route::get('/group', [UserController::class, 'groupUsers'])->name('users.group');
        Route::get('/group/data', [UserController::class, 'groupUsersData'])->name('users.group.data');
        Route::post('/group', [UserController::class, 'storeGroupUser'])->name('users.group.store');
        Route::put('/group/{user}', [UserController::class, 'updateGroupUser'])->name('users.group.update');
        Route::delete('/group/{user}', [UserController::class, 'destroyGroupUser'])->name('users.group.destroy');
    });

    // Activity Logs
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_activity_logs'])->get('/activity-logs', [UserController::class, 'activityLogs'])->name('activity-logs');
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:view_activity_logs'])->get('/activity-logs/data', [UserController::class, 'activityLogsData'])->name('activity-logs.data');

    // Group Roles & Permissions Management
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:assign_roles'])->prefix('users')->group(function () {
        Route::get('/roles', [GroupRoleController::class, 'index'])->name('users.roles');
        Route::put('/roles/{role}', [GroupRoleController::class, 'update'])->name('users.roles.update');
        Route::get('/roles/{role}/permissions', [GroupRoleController::class, 'getRolePermissions'])->name('users.roles.permissions');
        Route::post('/roles/assign-user', [GroupRoleController::class, 'assignRoleToUser'])->name('users.roles.assign');
    });

    // Settings - Subscription Plans (super admin only)
    Route::middleware(['permission:manage_subscription_plans'])->prefix('settings')->group(function () {
        Route::get('/subscription-plans', [SettingsController::class, 'subscriptionPlans'])->name('settings.subscription_plans');
        Route::get('/subscription-plans/data', [SettingsController::class, 'subscriptionPlansData'])->name('settings.subscription_plans.data');
        Route::post('/subscription-plans', [SettingsController::class, 'storePlan'])->name('settings.subscription_plans.store');
        Route::put('/subscription-plans/{plan}', [SettingsController::class, 'updatePlan'])->name('settings.subscription_plans.update');
        Route::delete('/subscription-plans/{plan}', [SettingsController::class, 'destroyPlan'])->name('settings.subscription_plans.destroy');
    });

    // Settings - Group Settings (uses groupperm for group context)
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:manage_collection_funds'])->prefix('settings')->group(function () {
        Route::get('/collection-funds', [SettingsController::class, 'collectionFunds'])->name('settings.collection_funds');
        Route::get('/collection-funds/data', [SettingsController::class, 'collectionFundsData'])->name('settings.collection_funds.data');
        Route::post('/collection-funds', [SettingsController::class, 'storeFund'])->name('settings.collection_funds.store');
        Route::put('/collection-funds/{fund}', [SettingsController::class, 'updateFund'])->name('settings.collection_funds.update');
        Route::delete('/collection-funds/{fund}', [SettingsController::class, 'destroyFund'])->name('settings.collection_funds.destroy');
    });

    Route::middleware(['App\Http\Middleware\CheckGroupPermission:manage_calendar_years'])->prefix('settings')->group(function () {
        Route::get('/calendar-years', [SettingsController::class, 'calendarYears'])->name('settings.calendar_years');
        Route::get('/calendar-years/data', [SettingsController::class, 'calendarYearsData'])->name('settings.calendar_years.data');
        Route::post('/calendar-years', [SettingsController::class, 'storeCalendarYear'])->name('settings.calendar_years.store');
        Route::put('/calendar-years/{calendarYear}', [SettingsController::class, 'updateCalendarYear'])->name('settings.calendar_years.update');
        Route::delete('/calendar-years/{calendarYear}', [SettingsController::class, 'destroyCalendarYear'])->name('settings.calendar_years.destroy');
    });

    Route::middleware(['App\Http\Middleware\CheckGroupPermission:manage_loan_types'])->prefix('settings')->group(function () {
        Route::get('/loan-types', [SettingsController::class, 'loanTypes'])->name('settings.loan_types');
        Route::get('/loan-types/data', [SettingsController::class, 'loanTypesData'])->name('settings.loan_types.data');
        Route::post('/loan-types', [SettingsController::class, 'storeLoanType'])->name('settings.loan_types.store');
        Route::put('/loan-types/{loanType}', [SettingsController::class, 'updateLoanType'])->name('settings.loan_types.update');
        Route::delete('/loan-types/{loanType}', [SettingsController::class, 'destroyLoanType'])->name('settings.loan_types.destroy');
    });

    // Hisa Withdrawal Settings (Group Admin)
    Route::middleware(['App\Http\Middleware\CheckGroupPermission:manage_loan_types'])->prefix('settings')->group(function () {
        Route::get('/withdrawal', [SettingsController::class, 'withdrawalSettings'])->name('settings.withdrawal');
        Route::put('/withdrawal', [SettingsController::class, 'updateWithdrawalSettings'])->name('settings.withdrawal.update');
    });
});
