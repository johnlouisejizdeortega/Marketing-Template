<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * The private control-panel dashboard. Every action here is behind the
 * `auth` middleware (see routes/web.php). The PageSpeed and SEO tools run
 * entirely client-side (browser -> Google PageSpeed Insights API), so these
 * methods only render Blade shells; no server-side outbound calls are made.
 */
class DashboardController extends Controller
{
    /** Landing page: overview cards for the three tools. */
    public function index(): View
    {
        return view('dashboard.index');
    }

    /** Generate: embeds the existing client-side Design Copier tool. */
    public function generate(): View
    {
        return view('dashboard.generate');
    }

    /** PageSpeed / Core Web Vitals dashboard. */
    public function psi(): View
    {
        return view('dashboard.psi');
    }

    /** On-page SEO audit dashboard. */
    public function seo(): View
    {
        return view('dashboard.seo');
    }
}
