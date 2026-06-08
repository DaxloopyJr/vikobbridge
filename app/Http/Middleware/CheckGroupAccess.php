<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Group;

class CheckGroupAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $groupId = $request->route('group') ?? $request->input('group_id') ?? session('current_group_id');

        if (!$groupId) {
            abort(403, 'No group specified.');
        }

        $group = Group::find($groupId);

        if (!$group) {
            abort(404, 'Group not found.');
        }

        if (!$user->canAccessGroup($group)) {
            abort(403, 'You do not have access to this group.');
        }

        if ($group->status !== 'active' && !$user->isSuperAdmin()) {
            if ($group->isTrialExpired()) {
                abort(403, 'Your trial period has expired. Please subscribe to continue.');
            }
            if ($group->isSubscriptionExpired()) {
                abort(403, 'Your subscription has expired. Please renew to continue.');
            }
        }

        session(['current_group_id' => $group->id]);

        return $next($request);
    }
}
