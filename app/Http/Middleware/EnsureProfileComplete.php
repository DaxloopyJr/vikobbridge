<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin() && !$user->profile_completed) {
            if (!$request->is('profile*') && !$request->is('logout')) {
                return redirect()->route('profile.wizard')
                    ->with('warning', 'Please complete your profile before proceeding.');
            }
        }

        return $next($request);
    }
}
