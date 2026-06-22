@extends('layouts.dashboard')

@section('title', 'Overview')
@section('heading', 'Control panel')

@section('content')
  <p class="dash-lead">Welcome back. Pick a tool to get started.</p>

  <div class="grid grid--3 dash-cards">
    <a class="card dash-card" href="{{ route('dashboard.generate') }}">
      <div class="card__icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
      </div>
      <h3>Generate</h3>
      <p>Paste a page's source, extract its colours &amp; type, and apply the look to this site.</p>
      <span class="card__link">Open tool &rarr;</span>
    </a>

    <a class="card dash-card" href="{{ route('dashboard.psi') }}">
      <div class="card__icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.77 7.23l.01-.01-3.72-3.72L15 4.56l2.11 2.11c-.94.36-1.61 1.26-1.61 2.33a2.5 2.5 0 0 0 5 0c0-.36-.08-.7-.22-1.01zM12 3C7.03 3 3 7.03 3 12s4.03 9 9 9 9-4.03 9-9c0-1.05-.18-2.06-.52-3l-1.6 1.6c.08.46.12.92.12 1.4 0 3.86-3.14 7-7 7s-7-3.14-7-7 3.14-7 7-7c1.93 0 3.68.79 4.95 2.05L17 5.78V4h-1.78l-1.27 1.27A8.95 8.95 0 0 0 12 3zm0 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
      </div>
      <h3>PageSpeed</h3>
      <p>Run Google PageSpeed Insights for any URL and track Core Web Vitals.</p>
      <span class="card__link">Run a test &rarr;</span>
    </a>

    <a class="card dash-card" href="{{ route('dashboard.seo') }}">
      <div class="card__icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0A4.5 4.5 0 1 1 14 9.5 4.49 4.49 0 0 1 9.5 14z"/></svg>
      </div>
      <h3>SEO</h3>
      <p>Audit on-page SEO with Lighthouse checks and a pass / warn / fail checklist.</p>
      <span class="card__link">Audit a page &rarr;</span>
    </a>
  </div>
@endsection
