@extends('layouts.dashboard')

@section('title', 'SEO')
@section('heading', 'SEO audit')

@section('content')
  <div class="tool" data-tool="seo">
    <p class="dash-lead">
      Audit a page's on-page SEO using Lighthouse checks (titles, meta description,
      crawlability, structured data and more). Results come straight from Google to
      your browser.
    </p>

    <form class="tool-bar" data-run novalidate>
      <input type="url" name="url" class="field__input tool-bar__url"
             placeholder="https://example.com" required>
      <div class="seg" role="group" aria-label="Strategy">
        <label class="seg__opt"><input type="radio" name="strategy" value="mobile" checked><span>Mobile</span></label>
        <label class="seg__opt"><input type="radio" name="strategy" value="desktop"><span>Desktop</span></label>
      </div>
      <button type="submit" class="btn btn--primary">Run audit</button>
    </form>

    <details class="tool-key">
      <summary>API key (optional)</summary>
      <input type="text" name="apikey" class="field__input" placeholder="Paste your PSI API key" autocomplete="off">
    </details>

    <div class="tool-status" data-status hidden></div>

    <div class="tool-results" data-results hidden>
      <section class="psi-gauges" data-gauges aria-label="SEO score"></section>
      <h2 class="dash-h2">Checklist</h2>
      <ul class="seo-list" data-checklist></ul>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}" defer></script>
@endpush
