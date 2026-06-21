# BoilerCo UK — High-Performance Marketing Template + Design Copier

A modern, conversion-optimised marketing website template for a UK boiler / heating
company, built as **plain static HTML/CSS/JS** so it loads instantly and targets
**100/100 Lighthouse** (Performance, Accessibility, Best Practices, SEO) — especially
on mobile. Ships with a browser-based **Design Copier** tool that extracts the
colours, fonts and feel from any website's source and re-skins the template live.

> Brand is a neutral placeholder ("BoilerCo UK") so the template is fully reusable —
> swap the name, phone number, colours and copy to launch a real site.

## What's inside

```
.
├── index.html                  # The homepage (all conversion sections)
├── thank-you.html              # Post-submission page
├── css/styles.css              # Full stylesheet (mobile-first, system fonts)
├── js/main.js                  # ~2KB: mobile nav + multi-step lead form (deferred)
├── robots.txt, sitemap.xml     # SEO basics
├── docs/
│   └── CONVERSION-PSYCHOLOGY.md # Why every section exists (the persuasion plan)
└── tools/design-copier/        # Paste a site's source → copy its design
    ├── index.html
    └── copier.js
```

## Run it locally

No build step. Just serve the folder:

```bash
# any static server works
python3 -m http.server 8080
# then open http://localhost:8080/                      (the website)
#           http://localhost:8080/tools/design-copier/  (the design tool)
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

`tools/design-copier/` is a self-contained, **100% in-browser** app (nothing is
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
   `css/styles.css` to re-skin the whole site.

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
- [ ] Swap brand colours via the Design Copier or `:root` in `css/styles.css`.
- [ ] Update services, pricing tiers and FAQ copy.
- [ ] Wire the lead form to a real handler (replace the `action="thank-you.html"`
      with your CRM/Formspree/Netlify Forms endpoint) and add analytics events.
- [ ] Replace placeholder testimonials & stats with real, verifiable ones.
- [ ] Set real `canonical`, Open Graph image, and `sitemap.xml` URLs.
- [ ] Add a privacy policy & finance/regulatory disclaimers for the UK market.
```
