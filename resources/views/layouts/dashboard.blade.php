<!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
<meta name="robots" content="noindex">
<meta name="theme-color" content="#4f46e5">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='6' fill='%234f46e5'/%3E%3Cpath d='M7 12.5l3 3 7-7' stroke='white' stroke-width='2.2' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="dash-body">
<div class="dash">
  <aside class="dash-side" id="dashSide">
    <a class="dash-brand" href="{{ route('dashboard') }}">
      <span class="dash-brand__mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><path d="M6 12.5l3.5 3.5L18 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </span>
      Control&nbsp;Panel
    </a>

    @php $r = Route::currentRouteName(); @endphp
    <nav class="dash-nav" aria-label="Dashboard">
      <a href="{{ route('dashboard') }}" class="dash-nav__link @if($r==='dashboard') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Overview
      </a>

      <span class="dash-nav__sep">Tools</span>
      <a href="{{ route('dashboard.generate') }}" class="dash-nav__link @if($r==='dashboard.generate') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
        Generate
      </a>
      <a href="{{ route('dashboard.psi') }}" class="dash-nav__link @if($r==='dashboard.psi') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9 9-4.03 9-9c0-1.05-.18-2.06-.52-3l-1.6 1.6c.08.46.12.92.12 1.4 0 3.86-3.14 7-7 7s-7-3.14-7-7 3.14-7 7-7c1.93 0 3.68.79 4.95 2.05L17 5.78V4h3v3h-1.78A8.95 8.95 0 0 0 12 3zm0 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
        PageSpeed
      </a>
      <a href="{{ route('dashboard.seo') }}" class="dash-nav__link @if($r==='dashboard.seo') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.49 4.49 0 0 1 9.5 14z"/></svg>
        SEO
      </a>

      <span class="dash-nav__sep">Site</span>
      <a href="{{ route('dashboard.preview') }}" class="dash-nav__link @if($r==='dashboard.preview') is-active @endif">
        <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>
        Preview site
      </a>
    </nav>

    <div class="dash-foot">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dash-nav__link dash-nav__link--btn">
          <svg class="ico" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8v-2H4z"/></svg>
          Sign out
        </button>
      </form>
    </div>
  </aside>

  <div class="dash-main">
    <header class="dash-top">
      <button class="dash-burger" type="button" aria-label="Toggle menu" onclick="document.getElementById('dashSide').classList.toggle('open')">
        <span></span><span></span><span></span>
      </button>
      <h1 class="dash-top__title">@yield('heading', 'Dashboard')</h1>
      <div class="dash-top__right">
        <span class="dash-pill"><span class="dash-pill__dot"></span> Admin</span>
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
