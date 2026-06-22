<!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
<meta name="robots" content="noindex">
<meta name="theme-color" content="#0b5cab">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cpath fill='%23ff7a18' d='M16 2c2 5-3 6-3 11a3 3 0 0 0 6 0c3 3 4 6 4 9a7 7 0 1 1-14 0c0-7 7-9 7-20z'/%3E%3C/svg%3E">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="dash-body">
<div class="dash">
  <aside class="dash-side" id="dashSide">
    <a class="brand dash-brand" href="{{ route('dashboard') }}">
      <svg class="flame" viewBox="0 0 32 32" aria-hidden="true"><path fill="#ff7a18" d="M16 2c2 5-3 6-3 11a3 3 0 0 0 6 0c3 3 4 6 4 9a7 7 0 1 1-14 0c0-7 7-9 7-20z"/></svg>
      BoilerCo<span style="color:#0b5cab">UK</span>
    </a>

    @php $r = Route::currentRouteName(); @endphp
    <nav class="dash-nav" aria-label="Dashboard">
      <a href="{{ route('dashboard') }}" class="dash-nav__link @if($r==='dashboard') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Overview
      </a>
      <a href="{{ route('dashboard.generate') }}" class="dash-nav__link @if($r==='dashboard.generate') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        Generate
      </a>
      <a href="{{ route('dashboard.psi') }}" class="dash-nav__link @if($r==='dashboard.psi') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33a2.5 2.5 0 0 0 5 0c0-.36-.08-.7-.22-1.01zM12 3C7.03 3 3 7.03 3 12s4.03 9 9 9 9-4.03 9-9c0-1.05-.18-2.06-.52-3l-1.6 1.6c.08.46.12.92.12 1.4 0 3.86-3.14 7-7 7s-7-3.14-7-7 3.14-7 7-7c1.93 0 3.68.79 4.95 2.05L17 5.78V4h-1.78l-1.27 1.27A8.95 8.95 0 0 0 12 3zm0 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
        PageSpeed
      </a>
      <a href="{{ route('dashboard.seo') }}" class="dash-nav__link @if($r==='dashboard.seo') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.49 4.49 0 0 1 9.5 14z"/></svg>
        SEO
      </a>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="dash-logout">
      @csrf
      <button type="submit" class="dash-nav__link dash-nav__link--btn">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8v-2H4z"/></svg>
        Sign out
      </button>
    </form>
  </aside>

  <div class="dash-main">
    <header class="dash-top">
      <button class="dash-burger" type="button" aria-label="Toggle menu" onclick="document.getElementById('dashSide').classList.toggle('open')">
        <span></span><span></span><span></span>
      </button>
      <h1 class="dash-top__title">@yield('heading', 'Dashboard')</h1>
      <div class="dash-top__right">
        <a class="dash-top__link" href="/" target="_blank" rel="noopener">View site &nearr;</a>
        <span class="dash-top__user">{{ auth()->user()->name }}</span>
      </div>
    </header>

    <main class="dash-content">
      @yield('content')
    </main>
  </div>
</div>
@stack('scripts')
</body>
</html>
