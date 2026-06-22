<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/*
|--------------------------------------------------------------------------
| Web Routes — private admin dashboard + (gated) marketing site preview
|--------------------------------------------------------------------------
|
| The whole site is private: landing on "/" sends you to the login screen
| (or the dashboard once signed in). The marketing template is no longer
| served at the root — it lives at public/site.html and is only reachable,
| gated, via the in-dashboard Preview. Auth is a single shared password
| (config/dashboard.php) with cookie sessions, so there is NO database.
|
*/

/** Serve a file from public/ with a sensible cache header. */
$serve = function (string $relative): BinaryFileResponse {
    $path = public_path($relative);
    abort_unless(is_file($path), 404);

    return response()
        ->file($path, ['Cache-Control' => 'public, max-age=600']);
};

// Landing: login-first. Never shows the marketing site directly.
Route::get('/', fn () => redirect(session('dashboard_authed') ? '/dashboard' : '/login'))
    ->name('home');

Route::get('/thank-you', fn () => $serve('thank-you.html'));
Route::get('/tools/design-copier', fn () => $serve('tools/design-copier/index.html'));

Route::middleware('dash.guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('dash.auth')->group(function () use ($serve) {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/generate', [DashboardController::class, 'generate'])->name('dashboard.generate');
    Route::get('/dashboard/psi', [DashboardController::class, 'psi'])->name('dashboard.psi');
    Route::get('/dashboard/seo', [DashboardController::class, 'seo'])->name('dashboard.seo');
    Route::get('/dashboard/preview', [DashboardController::class, 'preview'])->name('dashboard.preview');

    // Gated marketing-site preview target (used by the Preview iframe).
    Route::get('/site', fn () => $serve('site.html'))->name('site');
});

// Catch-all: map /some/path to public/some/path.html or .../index.html.
// Real asset files (with a dot, e.g. .css/.js) are served directly by the
// web server, so they never reach this fallback.
Route::fallback(function () use ($serve) {
    $uri = trim(request()->path(), '/');

    foreach (["$uri.html", "$uri/index.html"] as $candidate) {
        if (!str_contains($candidate, '..') && is_file(public_path($candidate))) {
            return $serve($candidate);
        }
    }

    abort(404);
});
