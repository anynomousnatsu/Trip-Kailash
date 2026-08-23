# Redesign progress

Branch: `redesign/parikrama`. Source of truth for the design: `docs/design-package.md`.

**How to resume.** Find the first unchecked box, read its "Files" line, and continue there.
Every chunk is one commit, so `git log --oneline` shows exactly where the work stopped.

Status key: `[ ]` not started · `[~]` in progress · `[x]` done and committed

---

## Phase 1 — Foundation

- [x] **1.1** Design tokens. Replace the old palette with sandalwood/brass/night.
      Files: `assets/css/variables.css`
- [x] **1.2** Self-hosted fonts. Cinzel 700/900, Karla 400/500, Noto Serif Devanagari 500/700.
      Files: `assets/fonts/`, `assets/css/fonts.css`
- [x] **1.3** Motion system. Easing tokens, reveal states, two-way reduced motion.
      Files: `assets/css/motion.css`
- [x] **1.4** Reveal JS. IntersectionObserver, staggered children, retired delays, tab pause.
      Files: `assets/js/reveal.js`
- [x] **1.5** Environment layer. Fixed brass bloom, vermilion wash, feTurbulence grain.
      Files: `assets/css/base.css`
- [x] **1.6** Enqueue wiring. New handles into the existing dependency chain.
      Files: `inc/enqueue.php`

## Phase 2 — Data model

- [x] **2.1** Tradition, Region and Style taxonomies alongside the existing `deity`.
      Files: `inc/taxonomies.php`
- [x] **2.2** Package fields: pricing, trip facts, departures, content.
      Files: `inc/custom-post-types.php`
- [x] **2.3** Admin UI for the new fields, including itinerary and departure repeaters.
      Files: `inc/custom-post-types.php`, `assets/js/admin-repeater.js`
- [x] **2.4** `?deity=` to `?tradition=` migration with 301s.
      Files: `inc/seo.php`
- [~] **2.5** Content migration over MCP: fix the drift, rename Haleshi, add missing packages.
      Files: none, live content. **Blocked on facts only the operator has.**

      Drift confirmed against the live site on 23 Aug 2026:

      | Package | Live value | Problem |
      |---|---|---|
      | Kailash Mansarovar | GRADING Easy, 5,630 m | Easy is wrong for a 52 km kora over the Dolma La |
      | Kedarnath Chardham | Title 12 Days, spec 14 Days | Contradicts itself |
      | Kedarnath Chardham | DESTINATION Kailash Region | It is in Uttarakhand |
      | Haleshi Darshan | Tagline 8-Day, spec 5 Days | Contradicts itself |
      | Haleshi + Muktinath | Both "Through Fire, Water, and the Sky-Realms of Liberation" | Word-for-word identical tagline |
      | Gosaikunda Helicopter | "5-Day Moderate trek" | It is a helicopter ride |

      Page weights measured: 88 KB to 120 KB of stored markup per package.

      Needed from the operator before this runs: the true grading for Kailash,
      the true duration for Kedarnath and Haleshi, and confirmation that
      renaming Haleshi Darshan to Haleshi Maratika is wanted, since that
      changes a published URL and needs a 301.

## Phase 3 — Homepage

- [x] **3.1** `front-page.php` skeleton and section scaffolding.
- [x] **3.2** Hero markup, caption bands, designed static hero.
- [x] **3.3** `hero-scrub.js`: Blob fetch with ring, lerp, gated seeks, five live gates.
- [x] **3.4** Hero legibility: four-layer scrim system, worst-frame tuning.
- [x] **3.5** Tradition doors with three drawn geometries.
- [x] **3.6** The parikrama gallery, pinned horizontal with centre-proximity falloff.
- [x] **3.7** The kora ring, the one interactive moment.
- [x] **3.8** Departures, confirmation steps, lineage, guide cards.
- [ ] **3.9** "Check us before you pay us" verification section.
- [ ] **3.10** The parikrama line, the signature scroll-drawn SVG.

## Phase 4 — Package template

- [ ] **4.1** `single-pilgrimage_package.php` driven by fields.
- [ ] **4.2** Sticky reserve panel with the money explainer and trust cluster.
- [ ] **4.3** Spec strip, itinerary accordion, inclusions, season bar.
- [ ] **4.4** `TouristTrip` schema generated from fields.
      Files: `inc/schema.php`

## Phase 5 — Catalogue

- [ ] **5.1** Move `archive-pilgrimage_package.php` to theme root and rebuild.
- [ ] **5.2** Package card component. Kills the dark title band from the review shots.
- [ ] **5.3** Sacred Paths page with tradition filtering.

## Phase 6 — Shell

- [ ] **6.1** Header: transparent over hero, About and Contact in nav.
- [ ] **6.2** Footer on night ground with the verification line.
- [ ] **6.3** Mobile nav and the WhatsApp bar, phones only.

## Phase 7 — Ship

- [ ] **7.1** Lint, copy gate, version bump.
- [ ] **7.2** Upload to Hostinger, purge LiteSpeed.
- [ ] **7.3** Self-test checklist and speed receipts on the live site.

---

## Held back deliberately

- **Live deploy** waits for explicit go-ahead. Theme files reach the server by upload, and
  a half-built theme on production breaks the site.
- **Blockers** from `docs/design-package.md` section 10 gate the sections that need them.
  Placeholders are marked `[NUMBER]` in the templates and must not ship as-is.
