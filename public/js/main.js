/* BoilerCo UK — minimal progressive-enhancement JS (deferred, ~2KB).
   Mobile nav + a lazy-loaded GoHighLevel quote form modal.
   Everything degrades gracefully if JS is disabled. */
(function () {
  'use strict';

  /* =======================================================================
     GoHighLevel form
     -----------------------------------------------------------------------
     1) Set GHL_FORM_URL to your form's embed URL. In HighLevel:
        Sites → Forms → (your form) → Integrate → Embed → copy the iframe
        "src" (it looks like the example below, ending in your form ID).
     2) That's it — the form loads only when the visitor opens the modal,
        so it never slows down the initial page load / PageSpeed score.
     (Alternatively, paste your full iframe embed straight into the
      #ghlMount element in index.html and delete the injection below.)
     ======================================================================= */
  var GHL_FORM_URL = 'https://api.leadconnectorhq.com/widget/form/REPLACE_WITH_YOUR_FORM_ID';
  var GHL_EMBED_JS = 'https://link.msgsndr.com/js/form_embed.js';

  /* ---- Mobile menu toggle ---------------------------------------------- */
  var toggle = document.querySelector('.nav__toggle');
  var menu = document.getElementById('mobile-menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!open));
      menu.classList.toggle('open', !open);
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) {
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('open');
      }
    });
  }

  /* ---- Quote modal (lazy GHL form) ------------------------------------- */
  var modal = document.getElementById('quoteModal');
  var mount = document.getElementById('ghlMount');
  var lastFocus = null;
  var loaded = false;

  function loadGhlForm() {
    if (loaded || !mount) return;
    loaded = true;

    var iframe = document.createElement('iframe');
    iframe.src = GHL_FORM_URL;
    iframe.title = 'Quote request form';
    iframe.setAttribute('scrolling', 'no');
    iframe.style.cssText = 'width:100%;border:0;min-height:560px;display:block';
    iframe.addEventListener('load', function () {
      var loading = mount.querySelector('[data-quote-loading]');
      if (loading) loading.remove();
    });
    mount.appendChild(iframe);

    // GHL's embed script auto-resizes the iframe to the form's height.
    var s = document.createElement('script');
    s.src = GHL_EMBED_JS;
    s.defer = true;
    document.body.appendChild(s);
  }

  function openModal() {
    if (!modal) return;
    lastFocus = document.activeElement;
    loadGhlForm();
    if (typeof modal.showModal === 'function') {
      if (!modal.open) modal.showModal();
    } else {
      modal.setAttribute('open', '');
    }
    document.body.style.overflow = 'hidden';
    var closeBtn = modal.querySelector('[data-close-quote]');
    if (closeBtn) closeBtn.focus();
  }

  function closeModal() {
    if (!modal) return;
    if (typeof modal.close === 'function' && modal.open) {
      modal.close();
    } else {
      modal.removeAttribute('open');
    }
    document.body.style.overflow = '';
    if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
  }

  // Open from any [data-open-quote] button or any link pointing at #quote.
  document.addEventListener('click', function (e) {
    var opener = e.target.closest('[data-open-quote], a[href="#quote"]');
    if (opener) { e.preventDefault(); openModal(); return; }
    if (e.target.closest('[data-close-quote]')) { e.preventDefault(); closeModal(); }
  });

  if (modal) {
    // Click on the backdrop (the dialog element itself) closes it.
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });
    // Reset scroll lock whether closed via Esc (cancel) or programmatically.
    modal.addEventListener('close', function () { document.body.style.overflow = ''; });
  }
})();
