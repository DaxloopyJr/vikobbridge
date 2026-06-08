<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('en');

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $currentGroup = null;
                $currentCalendarYear = null;
                $userGroups = collect();

                if ($user->isSuperAdmin()) {
                    $userGroups = Group::where('status', 'active')->get();
                    $currentGroupId = session('current_group_id');
                    if ($currentGroupId) {
                        $currentGroup = $userGroups->firstWhere('id', $currentGroupId);
                    }
                } else {
                    $userGroups = $user->groups()->where('groups.status', 'active')->get();
                    $currentGroupId = session('current_group_id');
                    if ($currentGroupId) {
                        $currentGroup = $userGroups->firstWhere('id', $currentGroupId);
                    }
                    if (!$currentGroup && $userGroups->isNotEmpty()) {
                        $currentGroup = $userGroups->first();
                        session(['current_group_id' => $currentGroup->id]);
                    }
                }

                if ($currentGroup) {
                    $currentCalendarYear = $currentGroup->currentCalendarYear;
                    $calendarYearId = session('current_calendar_year_id');
                    if ($calendarYearId) {
                        $currentCalendarYear = $currentGroup->calendarYears()
                            ->where('id', $calendarYearId)
                            ->first() ?? $currentCalendarYear;
                    }
                }

                $view->with([
                    'authUser' => $user,
                    'currentGroup' => $currentGroup,
                    'currentCalendarYear' => $currentCalendarYear,
                    'userGroups' => $userGroups,
                ]);
            }
        });
    }
}
