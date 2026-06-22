<!doctype html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Sign in') — {{ config('app.name') }}</title>
<meta name="robots" content="noindex">
<meta name="theme-color" content="#0b5cab">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Cpath fill='%23ff7a18' d='M16 2c2 5-3 6-3 11a3 3 0 0 0 6 0c3 3 4 6 4 9a7 7 0 1 1-14 0c0-7 7-9 7-20z'/%3E%3C/svg%3E">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="auth-body">
<main class="auth-shell">
  <div class="auth-card hero__card">
    <a class="brand auth-brand" href="/">
      <svg class="flame" viewBox="0 0 32 32" aria-hidden="true"><path fill="#ff7a18" d="M16 2c2 5-3 6-3 11a3 3 0 0 0 6 0c3 3 4 6 4 9a7 7 0 1 1-14 0c0-7 7-9 7-20z"/></svg>
      BoilerCo<span style="color:#0b5cab">UK</span>
    </a>
    @yield('content')
  </div>
  <p class="auth-foot"><a href="/">&larr; Back to website</a></p>
</main>
</body>
</html>
