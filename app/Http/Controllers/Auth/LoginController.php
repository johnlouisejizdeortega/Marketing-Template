<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Password-only authentication for the private admin dashboard.
 *
 * A single shared password is read from config('dashboard.password')
 * (env DASHBOARD_PASSWORD). There is no users table and no database — a
 * session flag ("dashboard_authed") tracks the logged-in state, and the
 * cookie session driver keeps it stateless.
 */
class LoginController extends Controller
{
    /** Show the login form. */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Verify the password and start a session. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $expected = (string) config('dashboard.password');
        $provided = (string) $request->input('password');

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'password' => 'Incorrect password.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();
        $request->session()->put('dashboard_authed', true);

        return redirect()->intended(route('dashboard'));
    }

    /** Sign out and clear the session. */
    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('dashboard_authed');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Throttle to 5 attempts/minute per IP. */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'password' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return 'login|'.$request->ip();
    }
}
