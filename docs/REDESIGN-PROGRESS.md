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
- [x] **2.5** Content migration over MCP. **Done for everything answerable.**

      Written to the live site on 23 Aug 2026:

      | Package | Change |
      |---|---|
      | Kailash Mansarovar | Grading Easy to **Challenging**. A 52 km kora over the Dolma La at 5,630 m is not Easy, and the old grading was the one field a buyer uses to decide whether they can physically go. |
      | Kedarnath Chardham | Duration set to **12 days**, matching the title. The spec block said 14 and also said the destination was "Kailash Region", so both look inherited from the Kailash page it was duplicated from. Grading raised to Challenging at 5,636 m. |
      | Haleshi | Duration set to **5 days**, matching the spec. The 8-day figure came from a tagline copied word for word from Muktinath, which genuinely is 8 days. Retitled **Haleshi Maratika**. |
      | Gosaikunda Helicopter | Grading Moderate to Easy. It was described as a "5-Day Moderate trek" while being sold as a helicopter ride. |
      | All seven | short_pitch written from the design package, departure_type set, best months, lead time notes, altitude and fitness notes. |

      **Still open, flagged in the data itself:**

      - The Gosaikunda helicopter package is 5 days at $350, identical to the
        Gosaikunda trek. One of those figures is almost certainly inherited
        from the page it was duplicated from. A note is stored on the post in
        `style_note_todo`.
      - Taxonomy terms could not be assigned. Tradition, region and style are
        registered by the theme, which is not deployed yet, so the terms do not
        exist on the server. **This is the first thing to do after deploying**,
        and it is what fixes Muktinath being filed under Shiva when it is
        Vaishnava and Buddhist.
      - The four new packages (Muktinath-only, Pathibhara, Manakamana, Kailash
        Charan Sparsha) are not created. They need real durations, prices and
        itineraries, which are the operator's to supply.

## Phase 3 — Homepage

- [x] **3.1** `front-page.php` skeleton and section scaffolding.
- [x] **3.2** Hero markup, caption bands, designed static hero.
- [x] **3.3** `hero-scrub.js`: Blob fetch with ring, lerp, gated seeks, five live gates.
- [x] **3.4** Hero legibility: four-layer scrim system, worst-frame tuning.
- [x] **3.5** Tradition doors with three drawn geometries.
- [x] **3.6** The parikrama gallery, pinned horizontal with centre-proximity falloff.
- [x] **3.7** The kora ring, the one interactive moment.
- [x] **3.8** Departures, confirmation steps, lineage, guide cards.
- [x] **3.9** "Check us before you pay us" verification section.
- [x] **3.10** The parikrama line, the signature scroll-drawn SVG.

## Phase 4 — Package template

- [x] **4.1** `single-pilgrimage_package.php` driven by fields.
- [x] **4.2** Sticky reserve panel with the money explainer and trust cluster.
- [x] **4.3** Spec strip, itinerary accordion, inclusions, season bar.
- [x] **4.4** `TouristTrip` schema generated from fields.
      Files: `inc/schema.php`

## Phase 5 — Catalogue

- [x] **5.1** Move `archive-pilgrimage_package.php` to theme root and rebuild.
- [x] **5.2** Package card component. Kills the dark title band from the review shots.
- [x] **5.3** Sacred Paths page with tradition filtering.

## Phase 6 — Shell

- [x] **6.1** Header: transparent over hero, About and Contact in nav.
- [x] **6.2** Footer on night ground with the verification line.
- [x] **6.3** Mobile nav and the WhatsApp bar, phones only.

## Phase 7 — Ship

- [x] **7.1** Lint, copy gate, version bump.
- [~] **7.2** Upload to Hostinger, purge LiteSpeed. **Needs a human.**

      The Royal MCP connector can write content, fields, terms and menus, but
      it cannot write theme PHP, CSS or JS. There is no automated deploy
      either; CI only syntax-checks. So the files reach the server one of two
      ways:

      **Either** pull the branch on the server, if the theme directory is a git
      checkout:

      ```
      cd wp-content/themes/Trip-Kailash
      git fetch origin && git checkout redesign/parikrama
      ```

      **Or** upload through hPanel File Manager or SFTP into
      `wp-content/themes/Trip-Kailash/`, replacing the whole directory. The
      capitalisation matters: WordPress resolves the theme by directory name
      and the host is case-sensitive.

      Then purge LiteSpeed, or the old CSS keeps being served.

      Before uploading, take a backup. Two things change the moment the files
      land: `front-page.php` takes the homepage off Elementor, and
      `single-pilgrimage_package.php` takes over the package pages. Both are
      reversible by putting the old theme back, but not by clicking undo.

- [ ] **7.3** Self-test checklist and speed receipts on the live site.
      Runs after 7.2. `bin/copy-gate.sh`, `bin/check-hero-gates.py`, the flick
      test on the hero beat map, the worst-frame legibility audit, and curl
      timings for the speed receipts.

---

## Held back deliberately

- **Live deploy** waits for explicit go-ahead. Theme files reach the server by upload, and
  a half-built theme on production breaks the site.
- **Blockers** from `docs/design-package.md` section 10 gate the sections that need them.
  Placeholders are marked `[NUMBER]` in the templates and must not ship as-is.

---

## Deploy day, 24 Aug 2026

The theme uploaded fine, but the homepage still rendered the old Elementor
layout. Colours and fonts changed, nothing else did.

**Cause.** The homepage (page 11) had Elementor's own page template assigned,
`elementor_header_footer`, stored in `_wp_page_template`. Elementor hooks
`template_include` late and returns its own template, which beats
`front-page.php` outright. The theme was loading, the enqueue conditionals
were matching, `home.css` was on the page. The template just never ran.

**Fix.** Set page 11's `_wp_page_template` to `default`. No code change.

**Worth knowing:** if the homepage ever reverts, check that field first.
Editing the page in Elementor can set it back.

With that done the homepage renders the new design, and the tradition doors
appeared once terms were assigned, since the theme was finally on the server
to register the taxonomy.

### Also found on deploy day

A real bug in `assets/js/package.js`. Group pricing tiers are the per-person
rates by party size and the headline `price_from` is the marketing figure,
normally the LARGEST group's rate. Gosaikunda runs $650 for one pilgrim down
to $350 for ten. The old logic started at the headline price and only ever
moved lower, so it quoted a solo pilgrim the ten-person rate. Fixed, and the
per-person rate is now disclosed whenever it differs from the headline in
either direction.

Gosaikunda's max altitude corrected from 4,600 m to 4,380 m. 4,380 is the
lake, which is where this trek actually tops out; 4,600 is the Laurebina pass,
which this itinerary does not cross.

### Still open

- Itineraries for Haleshi Maratika, Shiva Divine Yatra, Kedarnath and the
  Gosaikunda helicopter package. Muktinath, Gosaikunda and Kailash are done.
- Customizer credentials, which is why the verification section does not
  render yet.
- The Gosaikunda helicopter duration and price question.


## The deploy is correct and the site still looks unchanged

There are THREE caches in front of this site, not one. Purging LiteSpeed
alone does nothing to the other two.

1. **LiteSpeed** (the WordPress plugin) — purged from the WP admin bar.
2. **hcdn, Hostinger's own edge CDN** — purged from hPanel, a completely
   separate control. Responses carry `Server: hcdn` and
   `x-hcdn-cache-status: HIT`, and the edge node is regional, so two people
   in different countries can be served different versions of the same page.
3. **The visitor's browser**, which honours `stale-while-revalidate` on
   navigation.

### How to tell which one is lying to you

    curl -sSI https://tripkailash.com/ | grep -iE 'x-hcdn-cache-status|age:'

`HIT` with a non-zero `Age` means you are looking at the edge cache, not the
site. To see what the server actually renders, use a URL nobody has requested
before, which is a different cache key all the way down:

    curl -sS 'https://tripkailash.com/?bust=12345' | grep -o '<h1[^>]*>[^<]*'

If those two disagree, the deploy worked and a cache is stale. Purge hcdn in
hPanel.

### Why this was worth a whole debugging session

`stale-while-revalidate=86400` let every layer serve a day-old copy of the
HTML while the CSS on that page updated normally, because assets are fetched
by URL and `?ver=` had changed. The result renders the OLD template with the
NEW design tokens, which presents as "only the colours and fonts changed".

Every check a developer runs defeats it. curl has no cache entry, a hard
refresh bypasses the browser layer, and any `?query=` URL is a different key
at the edge. All three return the correct page while the person reporting the
bug keeps seeing the old one.

That is now capped at a minute, and `bin/check-cache-headers.py` fails the
build if it goes back above five.
