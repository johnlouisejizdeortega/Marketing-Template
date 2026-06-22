# Conversion Psychology Plan — BoilerCo UK Template

This template isn't just "pretty" — every section is deliberately placed to move a
cold visitor toward one action: **requesting a fixed-price quote**. Below is the
reasoning, mapped to the principles it uses and where it lives in `site.html`.

## The single conversion goal
> Get the visitor to submit the quote form (or tap call) — ideally within 60 seconds,
> before they bounce to a competitor.

Everything else (services, pricing, reviews) exists only to remove the reasons a
person says "not yet."

## The buyer's mindset (UK boiler shopper)
A boiler purchase is **high-anxiety, low-frequency, high-cost**. The visitor is usually:
- **Cold/broken-boiler urgent** → wants speed & reassurance, or
- **Researching/planning** → wants a fair, transparent price and trust.

So the two emotional levers are **fear/urgency** ("no heating") and **trust/fairness**
("am I being ripped off?"). The page services both.

## Principle-by-principle map

| Principle | How it's used | Where |
|---|---|---|
| **Above-the-fold conversion** | Lead-capture form sits in the hero, visible without scrolling — the #1 driver of form completions. | `.hero__card` |
| **Friction reduction** | Multi-step form (3 small steps) feels easier than one long form. First step is a single tap; commitment grows gradually (*foot-in-the-door*). Auto-advances on selection. | `#leadForm`, `js/main.js` |
| **Social proof** | "Rated 4.9/5 by 2,384 homeowners" badge in hero; Trustpilot/Google ratings; 3 verified testimonials with names + locations. | hero trust pill, `#reviews` |
| **Authority** | "Gas Safe registered", accredited installer logos (Worcester Bosch, Vaillant…). Reduces "are they legit?" fear. | trust bar, FAQ |
| **Risk reversal** | "Fixed price, no hidden costs", "no-obligation", "cancel anytime", up-to-12-yr warranty, "we never share your details". Removes purchase risk. | hero bullets, pricing, form note |
| **Urgency / loss aversion** | "A warm home by tomorrow", "next-day fitting", "Don't get left in the cold", "24/7 emergency". Taps the cold-house fear without being sleazy. | hero, stats, final CTA |
| **Anchoring & decoy** | 3-tier pricing with the middle "Comfort" plan visually featured ("Most popular"). The £2,995 Premium anchors high so £2,295 feels reasonable. | `#pricing` |
| **Cognitive ease** | "Warm in 3 easy steps" turns a scary job into a simple, predictable process. | `#how` |
| **Reciprocity** | Free quote, free advice, smart thermostat "included" — give value first. | hero, pricing |
| **Objection handling** | FAQ pre-answers the exact doubts that stop a click: speed, fixed price, qualifications, finance, warranty. | `#faq` |
| **Payment-pain reduction** | "0% APR finance from ~£18/month" reframes a £2,000 cost as a small monthly one. | pricing, FAQ |
| **Mobile click-to-call** | Sticky bottom bar with "Call now" + "Free quote" — captures urgent mobile users who won't fill a form. Huge for trades. | `.call-bar` |
| **Visual hierarchy / colour psychology** | Calm blue = trust/reliability (heating engineers); warm orange = "heat" + a high-contrast CTA colour used *only* for actions, so the eye always finds the next step. | `css/styles.css` tokens |

## Page flow (the persuasion funnel)
1. **Hero** — promise + trust + the form (capture the ready-to-act).
2. **Trust bar** — "these are real, accredited pros" (lower the guard).
3. **Services** — "yes, they do exactly what I need."
4. **How it works** — "this will be easy, not stressful."
5. **Stats band** — scale & credibility (30,000+ installed).
6. **Pricing** — transparency kills the "rip-off" fear; anchoring guides choice.
7. **Reviews** — "people like me were happy."
8. **FAQ** — mop up remaining objections.
9. **Final CTA** — last emotional nudge for scrollers who didn't convert yet.

## Measurement (recommended next steps)
- Track form **step-drop-off** (which step loses people).
- A/B test hero headline & CTA copy ("Get my fixed price" vs "Check my price").
- Add analytics events on: form start, each step, submit, and `tel:` taps.
- Heatmap the hero to confirm the form is seen.
- Test sticky call-bar copy and the featured pricing tier.

## Performance *is* conversion
Mobile bounce rises sharply after ~3s load. This template inlines critical CSS,
uses **zero web-font requests** (system stack), inline SVG icons (no image
requests), deferred JS and lazy patterns — targeting 100/100 Lighthouse so the
psychology above actually gets a chance to work. Speed and persuasion compound.
