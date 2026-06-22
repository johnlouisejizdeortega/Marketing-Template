@extends('layouts.dashboard')

@section('title', 'Generate')
@section('heading', 'Generate & theme')

@section('content')
  <p class="dash-lead">
    Paste the full HTML source of any web page below. The tool extracts its colour
    palette and typography, previews the result, and gives you CSS variables to drop
    into <code>public/css/styles.css</code>.
  </p>

  <div class="frame-wrap">
    <iframe class="frame" src="/tools/design-copier" title="Design Copier" loading="lazy"></iframe>
  </div>

  <p class="form-note" style="margin-top:1rem">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/></svg>
    Runs entirely in your browser — nothing is uploaded.
  </p>
@endsection
