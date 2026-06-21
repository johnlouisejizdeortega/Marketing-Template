/* BoilerCo UK — minimal progressive-enhancement JS (deferred, ~2KB).
   Everything degrades gracefully if JS is disabled. */
(function () {
  'use strict';

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

  /* ---- Multi-step lead form -------------------------------------------- */
  var form = document.getElementById('leadForm');
  if (form) {
    var steps = Array.prototype.slice.call(form.querySelectorAll('.lead-form__step'));
    var progress = document.querySelector('[data-progress]');
    var btnNext = form.querySelector('[data-next]');
    var btnPrev = form.querySelector('[data-prev]');
    var btnSubmit = form.querySelector('[data-submit]');
    var current = 0;

    function render() {
      steps.forEach(function (s, i) { s.classList.toggle('active', i === current); });
      if (progress) progress.style.width = ((current + 1) / steps.length * 100) + '%';
      btnPrev.hidden = current === 0;
      var last = current === steps.length - 1;
      btnNext.hidden = last;
      btnSubmit.hidden = !last;
      var firstField = steps[current].querySelector('input, select');
      if (firstField && current > 0) firstField.focus();
    }

    function validStep() {
      var required = steps[current].querySelectorAll('[required]');
      for (var i = 0; i < required.length; i++) {
        if (required[i].type === 'radio') {
          if (!form.querySelector('input[name="' + required[i].name + '"]:checked')) {
            required[i].closest('fieldset').classList.add('shake');
            return false;
          }
        } else if (!required[i].value.trim()) {
          required[i].focus();
          return false;
        }
      }
      return true;
    }

    btnNext.addEventListener('click', function () {
      if (!validStep()) return;
      if (current < steps.length - 1) { current++; render(); }
    });
    btnPrev.addEventListener('click', function () {
      if (current > 0) { current--; render(); }
    });
    // Auto-advance step 1 when a service is picked (reduces friction)
    form.querySelectorAll('input[name="service"]').forEach(function (input) {
      input.addEventListener('change', function () {
        if (current === 0) { setTimeout(function () { current = 1; render(); }, 180); }
      });
    });

    render();
  }
})();
