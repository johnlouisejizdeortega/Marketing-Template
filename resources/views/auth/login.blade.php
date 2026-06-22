@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
  <div class="auth-card">
    <div class="auth-logo" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none"><path d="M6 12.5l3.5 3.5L18 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>

    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-sub">Enter your password to open the control panel.</p>

    @if ($errors->any())
      <div class="auth-alert" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2 1 21h22L12 2zm0 6 7 12H5l7-12zm-1 4v3h2v-3h-2zm0 4v2h2v-2h-2z"/></svg>
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
      @csrf
      <label class="field">
        <span class="field__label">Password</span>
        <span class="pw">
          <input type="password" name="password" id="password" autocomplete="current-password"
                 required autofocus
                 class="field__input @error('password') field__input--error @enderror">
          <button type="button" class="pw__toggle" aria-label="Show password"
                  onclick="(function(b){var i=document.getElementById('password');var s=i.type==='password';i.type=s?'text':'password';b.setAttribute('aria-label',s?'Hide password':'Show password');b.firstElementChild.style.opacity=s?'.5':'1';})(this)">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>
          </button>
        </span>
      </label>

      <button type="submit" class="btn btn--primary btn--block">Sign in</button>
    </form>

    <p class="auth-foot">Private area — authorised staff only.</p>
  </div>
@endsection
