/* ==========================================================================
   Dashboard tools — PageSpeed Insights + SEO audit (client-side).
   Talks directly to Google's PSI API from the browser, so no server-side
   network access or stored secret is required. An optional API key is kept
   in localStorage on this device only.
   ========================================================================== */
(function () {
  'use strict';

  var root = document.querySelector('.tool[data-tool]');
  if (!root) return;

  var TOOL = root.getAttribute('data-tool'); // 'psi' | 'seo'
  var ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';
  var KEY_STORE = 'psiApiKey';

  var form = root.querySelector('[data-run]');
  var urlInput = form.querySelector('input[name="url"]');
  var keyInput = root.querySelector('input[name="apikey"]');
  var statusEl = root.querySelector('[data-status]');
  var resultsEl = root.querySelector('[data-results]');

  // Sensible default + persisted API key.
  if (!urlInput.value) urlInput.value = window.location.origin + '/';
  try {
    var saved = localStorage.getItem(KEY_STORE);
    if (saved && keyInput) keyInput.value = saved;
  } catch (e) { /* storage may be blocked */ }
  if (keyInput) {
    keyInput.addEventListener('change', function () {
      try { localStorage.setItem(KEY_STORE, keyInput.value.trim()); } catch (e) {}
    });
  }

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  // Strip the markdown link syntax Lighthouse uses in descriptions.
  function plain(s) {
    return String(s || '').replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
  }

  function band(scorePct) {
    if (scorePct >= 90) return 'good';
    if (scorePct >= 50) return 'avg';
    return 'poor';
  }

  function setStatus(msg, isError) {
    if (!msg) { statusEl.hidden = true; statusEl.textContent = ''; return; }
    statusEl.hidden = false;
    statusEl.textContent = msg;
    statusEl.classList.toggle('is-error', !!isError);
  }

  function gauge(label, scorePct) {
    var b = band(scorePct);
    var deg = Math.round((scorePct / 100) * 360);
    return '' +
      '<div class="gauge gauge--' + b + '">' +
        '<div class="gauge__ring" style="--deg:' + deg + 'deg">' +
          '<span class="gauge__num">' + scorePct + '</span>' +
        '</div>' +
        '<span class="gauge__label">' + esc(label) + '</span>' +
      '</div>';
  }

  function categories(lr) {
    return lr && lr.categories ? lr.categories : {};
  }

  function pct(cat) {
    return cat && typeof cat.score === 'number' ? Math.round(cat.score * 100) : null;
  }

  function renderPsi(lr) {
    var cats = categories(lr);
    var order = [
      ['performance', 'Performance'],
      ['accessibility', 'Accessibility'],
      ['best-practices', 'Best Practices'],
      ['seo', 'SEO']
    ];
    var gauges = root.querySelector('[data-gauges]');
    gauges.innerHTML = order.map(function (o) {
      var p = pct(cats[o[0]]);
      return p == null ? '' : gauge(o[1], p);
    }).join('');

    var audits = lr.audits || {};
    var metricIds = [
      ['first-contentful-paint', 'First Contentful Paint'],
      ['largest-contentful-paint', 'Largest Contentful Paint'],
      ['total-blocking-time', 'Total Blocking Time'],
      ['cumulative-layout-shift', 'Cumulative Layout Shift'],
      ['speed-index', 'Speed Index'],
      ['interactive', 'Time to Interactive']
    ];
    var metricsEl = root.querySelector('[data-metrics]');
    metricsEl.innerHTML = metricIds.map(function (m) {
      var a = audits[m[0]];
      if (!a || a.displayValue == null) return '';
      var b = typeof a.score === 'number' ? band(a.score * 100) : 'avg';
      return '<div class="metric metric--' + b + '">' +
        '<span class="metric__val">' + esc(a.displayValue) + '</span>' +
        '<span class="metric__label">' + esc(m[1]) + '</span></div>';
    }).join('');

    var opps = Object.keys(audits).map(function (k) { return audits[k]; })
      .filter(function (a) {
        return a.details && a.details.type === 'opportunity' &&
               typeof a.score === 'number' && a.score < 1 &&
               a.details.overallSavingsMs > 0;
      })
      .sort(function (a, b) { return b.details.overallSavingsMs - a.details.overallSavingsMs; })
      .slice(0, 8);
    var oppsEl = root.querySelector('[data-opps]');
    oppsEl.innerHTML = opps.length
      ? opps.map(function (a) {
          var s = (a.details.overallSavingsMs / 1000).toFixed(2);
          return '<li class="psi-opp"><span class="psi-opp__t">' + esc(a.title) + '</span>' +
            '<span class="psi-opp__s">~' + s + ' s</span></li>';
        }).join('')
      : '<li class="psi-opp psi-opp--none">No major opportunities — nice work.</li>';
  }

  function renderSeo(lr) {
    var cats = categories(lr);
    var p = pct(cats.seo);
    root.querySelector('[data-gauges]').innerHTML = p == null ? '' : gauge('SEO', p);

    var audits = lr.audits || {};
    var refs = (cats.seo && cats.seo.auditRefs) || [];
    var rows = refs.map(function (ref) { return audits[ref.id]; })
      .filter(Boolean)
      .filter(function (a) { return a.scoreDisplayMode !== 'manual' && a.scoreDisplayMode !== 'notApplicable'; });

    function rank(a) {
      if (a.scoreDisplayMode === 'informative') return 2;
      if (a.score === 1) return 3;
      if (typeof a.score === 'number' && a.score > 0) return 1;
      return 0; // failing
    }
    rows.sort(function (a, b) { return rank(a) - rank(b); });

    var listEl = root.querySelector('[data-checklist]');
    listEl.innerHTML = rows.map(function (a) {
      var cls, icon;
      if (a.scoreDisplayMode === 'informative') { cls = 'info'; icon = 'i'; }
      else if (a.score === 1) { cls = 'pass'; icon = '✓'; }
      else if (typeof a.score === 'number' && a.score > 0) { cls = 'warn'; icon = '!'; }
      else { cls = 'fail'; icon = '✕'; }
      return '<li class="seo-item seo-item--' + cls + '">' +
        '<span class="seo-item__icon">' + icon + '</span>' +
        '<div><span class="seo-item__title">' + esc(a.title) + '</span>' +
        '<span class="seo-item__desc">' + esc(plain(a.description)) + '</span></div></li>';
    }).join('');
  }

  function run(e) {
    e.preventDefault();
    var url = urlInput.value.trim();
    if (!url) { setStatus('Enter a URL to test.', true); return; }
    if (!/^https?:\/\//i.test(url)) { url = 'https://' + url; urlInput.value = url; }

    var strategy = (form.querySelector('input[name="strategy"]:checked') || {}).value || 'mobile';
    var cats = TOOL === 'seo'
      ? ['seo']
      : ['performance', 'accessibility', 'best-practices', 'seo'];

    var qs = new URLSearchParams();
    qs.set('url', url);
    qs.set('strategy', strategy);
    cats.forEach(function (c) { qs.append('category', c); });
    var key = keyInput ? keyInput.value.trim() : '';
    if (key) qs.set('key', key);

    resultsEl.hidden = true;
    setStatus('Running ' + (TOOL === 'seo' ? 'audit' : 'test') + ' — this can take 20–40 seconds…');
    var btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;

    fetch(ENDPOINT + '?' + qs.toString())
      .then(function (res) {
        return res.json().then(function (data) {
          if (!res.ok) {
            var msg = (data && data.error && data.error.message) || ('Request failed (' + res.status + ')');
            throw new Error(msg);
          }
          return data;
        });
      })
      .then(function (data) {
        var lr = data.lighthouseResult;
        if (!lr) throw new Error('No Lighthouse result returned.');
        if (TOOL === 'seo') renderSeo(lr); else renderPsi(lr);
        setStatus('');
        resultsEl.hidden = false;
        resultsEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      })
      .catch(function (err) {
        setStatus(err.message || 'Something went wrong. Try again, or add an API key.', true);
      })
      .finally(function () {
        if (btn) btn.disabled = false;
      });
  }

  form.addEventListener('submit', run);
})();
