<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/*
|--------------------------------------------------------------------------
| Web Routes — static marketing site
|--------------------------------------------------------------------------
|
| The marketing site is plain static HTML/CSS/JS living in public/. On
| Laravel Cloud the web server serves those files directly (fast, great
| PageSpeed). These routes only cover the "directory" URLs that don't map
| to a physical file — chiefly "/" and the tool/page index URLs — plus a
| catch-all so any /foo resolves to public/foo.html or public/foo/index.html.
|
*/

/** Serve a file from public/ with a sensible cache header. */
$serve = function (string $relative): BinaryFileResponse {
    $path = public_path($relative);
    abort_unless(is_file($path), 404);

    return response()
        ->file($path, ['Cache-Control' => 'public, max-age=600']);
};

Route::get('/', fn () => $serve('index.html'))->name('home');
Route::get('/thank-you', fn () => $serve('thank-you.html'));
Route::get('/tools/design-copier', fn () => $serve('tools/design-copier/index.html'));

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
