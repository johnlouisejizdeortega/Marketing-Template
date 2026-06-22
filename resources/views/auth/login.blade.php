@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
  <h1 class="auth-title">Admin sign in</h1>
  <p class="auth-sub">Access the site control panel.</p>

  @if ($errors->any())
    <div class="auth-alert" role="alert">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
    @csrf

    <label class="field">
      <span class="field__label">Email address</span>
      <input type="email" name="email" value="{{ old('email') }}"
             autocomplete="username" required autofocus
             class="field__input @error('email') field__input--error @enderror">
    </label>

    <label class="field">
      <span class="field__label">Password</span>
      <input type="password" name="password" autocomplete="current-password" required
             class="field__input @error('password') field__input--error @enderror">
    </label>

    <label class="field-check">
      <input type="checkbox" name="remember" value="1">
      <span>Keep me signed in</span>
    </label>

    <button type="submit" class="btn btn--primary btn--block btn--lg">Sign in</button>
  </form>

  <p class="form-note" style="margin-top:1.25rem">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1a5 5 0 0 0-5 5v3H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1V6a5 5 0 0 0-5-5zm3 8H9V6a3 3 0 0 1 6 0z"/></svg>
    Private area — authorised staff only.
  </p>
@endsection
