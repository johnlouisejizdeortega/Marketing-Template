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
├── public/                     # Web root
│   ├── site.html               #   marketing website (shown only via Preview)
│   ├── thank-you.html          #   post-submission page
│   ├── css/styles.css          #   marketing-site stylesheet
│   ├── css/dashboard.css       #   modern dashboard + login UI (self-contained)
│   ├── js/main.js              #   ~2KB: mobile nav + multi-step lead form
│   ├── js/dashboard.js         #   PageSpeed/SEO tools (client-side PSI API)
│   ├── tools/design-copier/    #   paste a site's source → copy its design
│   ├── robots.txt, sitemap.xml #   SEO basics
│   └── index.php               #   Laravel front controller
├── routes/web.php              # Login-first routing + dashboard + gated preview
├── resources/views/            # Blade: login + dashboard (Generate/PageSpeed/SEO/Preview)
├── app/Http/Middleware/        # Password-only session gate (no DB)
├── config/dashboard.php        # The single dashboard password (env)
├── docs/
│   ├── CONVERSION-PSYCHOLOGY.md # Why every section exists (the persuasion plan)
│   └── DEPLOY-LARAVEL-CLOUD.md  # Step-by-step Laravel Cloud deployment
├── composer.json / composer.lock
└── .env.example                # Defaults (no database — cookie sessions)
```

The app is **login-first**: `/` redirects to the login screen (or the dashboard
once signed in). The marketing website is private and only viewable through the
in-dashboard **Preview**. Static assets are served directly by the web server
(no PHP). **No database is used** — auth is a single password + cookie sessions.

## Deploy to Laravel Cloud

See **[docs/DEPLOY-LARAVEL-CLOUD.md](docs/DEPLOY-LARAVEL-CLOUD.md)** for the full
walkthrough. In short: connect the repo, choose **no database**, set the env vars
from `.env.example` (generate `APP_KEY`, set `DASHBOARD_PASSWORD`), and deploy. No
migrations, no Node/Vite build.

## Run it locally

```bash
composer install
cp .env.example .env
php artisan key:generate
# set a password in .env:  DASHBOARD_PASSWORD=your-strong-password
php artisan serve            # http://127.0.0.1:8000  → redirects to /login
```

No database, no migrations — it just runs.

## Admin dashboard

A private, login-gated control panel lives at **`/login`** → **`/dashboard`**.
Auth is a **single shared password** read from env `DASHBOARD_PASSWORD` (no users
table, no database — the session lives in an encrypted cookie). The login + UI are
a modern, minimalist, self-contained design (`public/css/dashboard.css`). Tools:

- **Generate** — the Design Copier, embedded: paste a page's source to extract
  its colours/type and get CSS variables for `public/css/styles.css`.
- **PageSpeed** — runs Google PageSpeed Insights for any URL and shows the four
  category scores + Core Web Vitals. Calls Google directly from the browser.
- **SEO** — a Lighthouse-based on-page SEO checklist (pass / warn / fail).
- **Preview site** — a private, framed preview of `public/site.html` in
  desktop / tablet / mobile widths.

Set the password in `.env` (`DASHBOARD_PASSWORD=your-strong-password`) or, in production,
in the Laravel Cloud **Environment** settings. Change it any time by editing that
one variable — no migrations or seeding.

> The PageSpeed/SEO tools optionally take a free
> [PSI API key](https://developers.google.com/speed/docs/insights/v5/get-started)
> (pasted in the UI, stored only in your browser) to avoid Google's rate limits.

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
The full rationale (social proof, anchoring, risk reversal, urgency, the quote
modal, the sticky mobile call bar, colour psychology, etc.) is documented in
[`docs/CONVERSION-PSYCHOLOGY.md`](docs/CONVERSION-PSYCHOLOGY.md).

## Quote form (GoHighLevel)

The hero CTA and every "Get a quote / Free quote / Choose plan" button open an
accessible modal (`<dialog id="quoteModal">`) that contains your **GoHighLevel
(HighLevel) form**. The form is **lazy-loaded on first open**, so the third-party
iframe never slows the initial page load or hurts your PageSpeed score.

**To go live:**
1. In HighLevel: **Sites → Forms → (your form) → Integrate → Embed** and copy the
   iframe `src` (it ends in your form ID).
2. Open `public/js/main.js` and set:
   ```js
   var GHL_FORM_URL = 'https://api.leadconnectorhq.com/widget/form/YOUR_FORM_ID';
   ```
   Also update the `<noscript>` fallback link in `public/site.html` to the same URL.
3. (Optional) Prefer to paste HighLevel's full embed snippet? Drop it straight into
   `<div id="ghlMount">` in `public/site.html` and delete the iframe-injection block
   in `main.js`.

HighLevel's `form_embed.js` auto-resizes the iframe to the form's height. Submissions,
confirmation messages and redirects are configured inside HighLevel — you can point its
post-submit redirect at `/thank-you` if you want to reuse the included thank-you page.

### Links to your other system features
The nav, mobile menu and footer include **placeholder links** to other parts of your
stack — **Customer login**, **Book a call** and **Live chat / help** — using
`https://your-domain.example.com/...` URLs. Search for `your-domain.example.com` and
replace them with your real portal / calendar / chat URLs (each is also flagged with a
`<!-- TODO -->` comment in `public/site.html`).

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
- [ ] Connect your **GoHighLevel form** — set `GHL_FORM_URL` in `public/js/main.js`
      (see "Quote form" below). Add analytics events on modal open if you like.
- [ ] Replace the **placeholder feature URLs** (`https://your-domain.example.com/...`
      for login / book-a-call / live chat) in the nav, mobile menu and footer.
- [ ] Replace placeholder testimonials & stats with real, verifiable ones.
- [ ] Set real `canonical`, Open Graph image, and `sitemap.xml` URLs.
- [ ] Add a privacy policy & finance/regulatory disclaimers for the UK market.
