<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $group = null;
        if (session()->has('current_group_id')) {
            $group = \App\Models\Group::find(session('current_group_id'));
        } elseif ($user && $user->groups()->count() > 0) {
            $group = $user->primaryGroup()->first() ?? $user->groups()->first();
        }

        if ($group) {
            if ($group->isTrialExpired()) {
                if (!$request->is('payment*') && !$request->is('subscription*') && !$request->is('logout') && !$request->is('plans')) {
                    return redirect()->route('subscription.expired')
                        ->with('error', 'Your trial period has expired. Please subscribe to continue using the system.');
                }
            }

            if ($group->isSubscriptionExpired() && $group->payment_status === 'paid') {
                if (!$request->is('payment*') && !$request->is('subscription*') && !$request->is('logout') && !$request->is('plans')) {
                    return redirect()->route('subscription.expired')
                        ->with('error', 'Your subscription has expired. Please renew to continue.');
                }
            }
        }

        return $next($request);
    }
}
