@extends('layouts.dashboard')

@section('title', 'PageSpeed')
@section('heading', 'PageSpeed Insights')

@section('content')
  <div class="tool" data-tool="psi">
    <p class="dash-lead">
      Run Google PageSpeed Insights for any URL. Results come straight from Google to
      your browser. Add a free
      <a href="https://developers.google.com/speed/docs/insights/v5/get-started" target="_blank" rel="noopener">PSI API key</a>
      to avoid rate limits — it is stored only in this browser.
    </p>

    <form class="tool-bar" data-run novalidate>
      <input type="url" name="url" class="field__input tool-bar__url"
             placeholder="https://example.com" required>
      <div class="seg" role="group" aria-label="Strategy">
        <label class="seg__opt"><input type="radio" name="strategy" value="mobile" checked><span>Mobile</span></label>
        <label class="seg__opt"><input type="radio" name="strategy" value="desktop"><span>Desktop</span></label>
      </div>
      <button type="submit" class="btn btn--primary">Run test</button>
    </form>

    <details class="tool-key">
      <summary>API key (optional)</summary>
      <input type="text" name="apikey" class="field__input" placeholder="Paste your PSI API key" autocomplete="off">
    </details>

    <div class="tool-status" data-status hidden></div>

    <div class="tool-results" data-results hidden>
      <section class="psi-gauges" data-gauges aria-label="Category scores"></section>
      <h2 class="psi-h2">Core Web Vitals &amp; lab metrics</h2>
      <section class="psi-metrics" data-metrics></section>
      <h2 class="psi-h2">Top opportunities</h2>
      <ul class="psi-opps" data-opps></ul>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}" defer></script>
@endpush
