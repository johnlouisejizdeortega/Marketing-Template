<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep already-signed-in users away from the login screen.
 */
class RedirectIfDashboardAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('dashboard_authed')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
