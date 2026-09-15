<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Allow only staff authenticated on the separate "admin" guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('admin')->check()) {
            return redirect()->guest(route('admin.login'));
        }

        $user = Auth::guard('admin')->user();

        abort_unless($user && $user->hasAnyRole(['admin', 'agent']), 403);

        return $next($request);
    }
}
