@extends('layouts.dashboard')

@section('title', 'Preview')
@section('heading', 'Site preview')

@section('content')
  <p class="dash-lead">A live, private preview of your marketing website. Switch widths to check how it responds.</p>

  <div class="preview-bar">
    <div class="seg" role="group" aria-label="Preview width">
      <label class="seg__opt"><input type="radio" name="pw" value="desktop" checked onchange="document.getElementById('previewStage').dataset.w='desktop'"><span>Desktop</span></label>
      <label class="seg__opt"><input type="radio" name="pw" value="tablet" onchange="document.getElementById('previewStage').dataset.w='tablet'"><span>Tablet</span></label>
      <label class="seg__opt"><input type="radio" name="pw" value="mobile" onchange="document.getElementById('previewStage').dataset.w='mobile'"><span>Mobile</span></label>
    </div>
    <span class="spacer"></span>
    <a class="btn btn--ghost" href="{{ route('site') }}" target="_blank" rel="noopener">
      Open in new tab
      <svg class="ico" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M14 3v2h3.59l-9.3 9.29 1.42 1.42L19 6.41V10h2V3h-7zM5 5h6V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6h-2v6H5V5z"/></svg>
    </a>
  </div>

  <div class="preview-stage" id="previewStage" data-w="desktop">
    <div class="frame-wrap">
      <iframe class="frame" src="{{ route('site') }}" title="Marketing site preview" loading="lazy"></iframe>
    </div>
  </div>
@endsection
