<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminCan
{
    /**
     * Allow the request only if the admin-guard user has the given feature
     * permission. The "admin" role always passes.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('admin')->user();
        abort_unless($user, 403);

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        try {
            if ($user->hasPermissionTo($permission, 'web')) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            // Unknown permission -> treated as denied.
        }

        abort(403);
    }
}
