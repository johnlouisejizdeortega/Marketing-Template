<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the dashboard behind the simple password session flag set by
 * LoginController. No database or auth guard is involved.
 */
class EnsureDashboardAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('dashboard_authed')) {
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
