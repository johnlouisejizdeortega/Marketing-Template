# BoilerCo UK — High-Performance Marketing Template + Design Copier

A modern, conversion-optimised marketing website for a UK boiler / heating company,
built as **plain static HTML/CSS/JS** (so it loads instantly and targets **100/100
Lighthouse** on mobile) and wrapped in a **Laravel 13 app** for one-click deployment
to **[Laravel Cloud](https://cloud.laravel.com)**. Ships with a browser-based
**Design Copier** tool that extracts the colours, fonts and feel from any website's
source and re-skins the template live.

> Brand is a neutral placeholder ("BoilerCo UK") so the template is fully reusable —
> swap the name, phone number, colours and copy to launch a real site.

## What's inside

```
.
├── public/                     # Web root — the static marketing site
│   ├── index.html              #   homepage (all conversion sections)
│   ├── thank-you.html          #   post-submission page
│   ├── css/styles.css          #   full stylesheet (mobile-first, system fonts)
│   ├── js/main.js              #   ~2KB: mobile nav + multi-step lead form
│   ├── tools/design-copier/    #   paste a site's source → copy its design
│   ├── robots.txt, sitemap.xml #   SEO basics
│   └── index.php               #   Laravel front controller
├── routes/web.php              # Serves the static pages (/, /thank-you, fallback)
├── app/ bootstrap/ config/ …   # Standard Laravel 13 skeleton
├── docs/
│   ├── CONVERSION-PSYCHOLOGY.md # Why every section exists (the persuasion plan)
│   └── DEPLOY-LARAVEL-CLOUD.md  # Step-by-step Laravel Cloud deployment
├── composer.json / composer.lock
└── .env.example                # No-DB defaults (cookie/file/sync drivers)
```

The marketing site is fully self-contained in `public/` — the Laravel layer exists
only to deploy it on Laravel Cloud and to give the directory URLs (`/`, `/thank-you`)
clean routing. Static assets are served directly by the web server (no PHP), which
is what preserves the PageSpeed score.

## Deploy to Laravel Cloud

See **[docs/DEPLOY-LARAVEL-CLOUD.md](docs/DEPLOY-LARAVEL-CLOUD.md)** for the full
walkthrough. In short: connect the repo, choose **no database**, set the env vars
from `.env.example` (generate `APP_KEY`), remove `migrate` from the deploy command,
and deploy. No Node/Vite build runs (there's no `package.json`).

## Run it locally

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve            # http://127.0.0.1:8000
```

Or serve just the static site with any static server (it's all in `public/`):

```bash
php -S localhost:8000 -t public      # or: python3 -m http.server -d public 8080
# http://localhost:8000/                      (the website)
# http://localhost:8000/tools/design-copier/  (the design tool)
```

## How the 100-PSI performance is achieved

- **Critical CSS inlined** in `<head>`; the full stylesheet is preloaded
  non-blocking (`rel=preload … onload`) with a `<noscript>` fallback.
- **Zero web-font requests** — a native system font stack (`system-ui …`).
- **Inline SVG icons** — no icon fonts, no image HTTP requests, no layout shift.
- **Deferred JS** (~2KB), everything degrades gracefully without it.
- **No frameworks, no trackers** by default — minimal main-thread work.
- Explicit colours/sizes to avoid Cumulative Layout Shift (CLS ≈ 0).
- Semantic HTML, labelled controls, skip link, focus styles, `prefers-reduced-motion`
  → strong Accessibility + Best-Practices/SEO scores.

> Tip: test with Lighthouse in Chrome DevTools (mobile preset) or PageSpeed Insights.
> Real-world scores also depend on hosting — serve over HTTPS with gzip/brotli and a
> CDN (e.g. Netlify, Cloudflare Pages, GitHub Pages) for best results.

## Conversion design

Every section is placed to drive one action — **a fixed-price quote request**.
The full rationale (social proof, anchoring, risk reversal, urgency, the multi-step
form, the sticky mobile call bar, colour psychology, etc.) is documented in
[`docs/CONVERSION-PSYCHOLOGY.md`](docs/CONVERSION-PSYCHOLOGY.md).

## The Design Copier tool

`public/tools/design-copier/` is a self-contained, **100% in-browser** app (nothing is
uploaded). Workflow:

1. Open a site you like → `Ctrl/Cmd+U` (View Source) → select all → copy.
   *(Even pasting a chunk of HTML or a `<style>` block works.)*
2. Paste it into the textarea and hit **Extract & apply design**.
3. It scans every colour, renders the source in a hidden sandboxed frame to read
   computed styles, then derives semantic tokens: **brand, accent, headings, body,
   background, borders + font family**.
4. The boiler template **re-themes live** in the preview (desktop/mobile toggle).
5. Fine-tune any token with the colour pickers, then **Copy CSS** / **Download
   theme.css** — paste the generated `:root { … }` block at the top of
   `public/css/styles.css` to re-skin the whole site.

Because the template is driven entirely by CSS custom properties
(`--color-brand`, `--color-accent`, `--font`, …), one token block restyles everything.

### Notes & limits
- Externally-hosted stylesheets/fonts referenced by the pasted source may be blocked
  by the browser (CORS) or load slowly; the tool still extracts from inline `<style>`
  blocks and `style=""` attributes and from a raw colour-frequency scan, so it
  degrades gracefully.
- It copies **design language** (palette, type, feel) — not someone's exact layout
  or content. Use it for inspiration and respect others' copyright/trademarks.

## Make it yours (checklist)

- [ ] Replace `BoilerCo UK`, phone `0800 000 0000`, and email everywhere.
- [ ] Swap brand colours via the Design Copier or `:root` in `public/css/styles.css`.
- [ ] Update services, pricing tiers and FAQ copy.
- [ ] Wire the lead form to a real handler (replace the `action="thank-you.html"`
      with your CRM/Formspree/Netlify Forms endpoint) and add analytics events.
- [ ] Replace placeholder testimonials & stats with real, verifiable ones.
- [ ] Set real `canonical`, Open Graph image, and `sitemap.xml` URLs.
- [ ] Add a privacy policy & finance/regulatory disclaimers for the UK market.
