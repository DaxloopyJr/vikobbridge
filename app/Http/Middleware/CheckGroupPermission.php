<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class CheckGroupPermission
{
    /**
     * Handle an incoming request.
     * Checks permission via Spatie first (for super-admins), then falls back to group_user pivot role.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check 1: Spatie direct permission (for users with roles in model_has_roles)
        if ($user->can($permission)) {
            return $next($request);
        }

        // Check 2: Group context permission via group_user pivot table
        $groupId = session('current_group_id');
        if ($groupId && $user->hasGroupPermission($permission, $groupId)) {
            return $next($request);
        }

        // Check 3: Try any of the user's groups if no current_group_id
        if (!$groupId) {
            $primaryGroup = $user->primaryGroup();
            if ($primaryGroup && $user->hasGroupPermission($permission, $primaryGroup->id)) {
                // Set the session for future requests
                session(['current_group_id' => $primaryGroup->id]);
                return $next($request);
            }
        }

        // Denied
        abort(403, 'User does not have the right permissions.');
    }
}
