/**
 * Trip Kailash scroll-scrubbed hero
 *
 * Scroll through the pinned hero maps 0 to 1 and drives the video's time, so
 * the page settles exactly as the footage reaches its composed ending.
 *
 * Every rule in here either created the polish or prevented a bug that ships
 * silently. The four that matter most:
 *
 *   The video is fetched as a Blob rather than played from its URL. Many
 *   hosts lack HTTP Range support, and without it every seek clamps to zero,
 *   so scrubbing works perfectly on localhost and does nothing on the live
 *   site. A Blob works everywhere.
 *
 *   Seeks are gated. Writing currentTime while a previous seek is in flight
 *   piles them up, and that is the whole difference between smooth and choppy
 *   in Chrome.
 *
 *   The displayed time is eased toward the target, normalised by frame delta,
 *   so a 120Hz screen converges at the same rate as a 60Hz one and the site
 *   does not feel different per machine.
 *
 *   The five static-hero gates are live. Deciding once at load leaves a blank
 *   multi-viewport hero the moment a tablet rotates or reduced motion is
 *   switched off mid-session.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var hero = document.getElementById('hero');
  if (!hero || !hero.classList.contains('tk-hero--scrub')) return;

  var stage = hero.querySelector('.tk-hero__stage');
  var pin = hero.querySelector('.tk-hero__pin');
  var video = hero.querySelector('.tk-hero__video');
  var poster = hero.querySelector('.tk-hero__poster');
  var ring = hero.querySelector('.tk-hero__ring');
  var bandEls = [].slice.call(hero.querySelectorAll('.tk-band'));

  if (!stage || !pin || !video) return;

  var VIDEO_URL = hero.getAttribute('data-video');
  var POSTER_URL = hero.getAttribute('data-poster');
  var VIDEO_BYTES = parseInt(hero.getAttribute('data-bytes'), 10) || 0;

  /* These five strings are duplicated in assets/css/home.css, in this order.
     They must stay character for character identical, or one side hides what
     the other is still loading. */
  var GATES = [
    '(max-width: 720px)',
    '(orientation: portrait) and (max-width: 1024px)',
    '(orientation: portrait) and (pointer: coarse)',
    '(orientation: landscape) and (pointer: coarse) and (max-height: 560px)',
    '(prefers-reduced-motion: reduce)'
  ];

  /* Held in a variable rather than created inline: unreferenced MediaQueryList
     objects have historically been collected along with their listeners. */
  var MQLS = GATES.map(function (q) { return window.matchMedia(q); });

  var bands = bandEls.map(function (el) {
    return {
      el: el,
      from: parseFloat(el.getAttribute('data-from')) || 0,
      to: parseFloat(el.getAttribute('data-to')) || 1,
      opacity: -1,   // last written value, so the DOM is only touched on change
      k: -1
    };
  });

  var scrubOn = false;
  var started = false;
  var heroOnScreen = true;
  var target = 0;
  var shown = 0;
  var rafId = null;
  var lastTick = 0;
  var seekBusy = false;
  var pendingTime = null;

  function clamp(v, lo, hi) { return v < lo ? lo : (v > hi ? hi : v); }

  function smoothstep(p, e0, e1) {
    var t = clamp((p - e0) / (e1 - e0), 0, 1);
    return t * t * (3 - 2 * t);
  }

  /* Progress through the pinned region, 0 at the top, 1 when the stage has
     been held for its full height. */
  function heroProgress() {
    var span = pin.offsetHeight - window.innerHeight;
    if (span <= 0) return 0;
    return clamp((window.scrollY - pin.offsetTop) / span, 0, 1);
  }

  /* ---- loading ------------------------------------------------------- */

  function setRing(fraction) {
    if (!ring) return;
    ring.style.setProperty('--tk-ring', String(Math.round(126 * (1 - fraction))));
  }

  /* A stalled stream must never leave a ring sitting there forever. Swap it
     for an honest scroll cue and let the still-image hero carry the page. */
  function failVideo() {
    if (ring) ring.style.display = 'none';
    hero.classList.add('is-video-failed');
  }

  function loadBlob() {
    var controller = new AbortController();
    var watchdog = window.setTimeout(function () { controller.abort(); }, 20000);

    return fetch(VIDEO_URL, { signal: controller.signal })
      .then(function (response) {
        if (!response.ok || !response.body) throw new Error('hero video unavailable');

        var total = Number(response.headers.get('Content-Length')) || VIDEO_BYTES;
        var reader = response.body.getReader();
        var chunks = [];
        var received = 0;
        var lastPaint = 0;

        function pump() {
          return reader.read().then(function (result) {
            if (result.done) return chunks;

            /* Re-arm on every chunk. The watchdog is measuring stalls, not
               total download time: a slow connection is fine, a dead one is
               not. */
            window.clearTimeout(watchdog);
            watchdog = window.setTimeout(function () { controller.abort(); }, 20000);

            chunks.push(result.value);
            received += result.value.length;

            if (total) {
              var fraction = Math.min(1, received / total);
              var now = performance.now();
              /* Throttled, but the terminal write always lands so the ring
                 actually completes rather than stopping at 97 percent. */
              if (now - lastPaint > 100 || fraction === 1) {
                lastPaint = now;
                setRing(fraction);
              }
            }

            return pump();
          });
        }

        return pump();
      })
      .then(function (chunks) {
        window.clearTimeout(watchdog);
        setRing(1);

        video.src = URL.createObjectURL(new Blob(chunks, { type: 'video/mp4' }));
        video.load();

        video.addEventListener('canplay', function () {
          hero.classList.add('is-video-ready');
          onScroll();          // land on wherever the reader already is
        }, { once: true });
      });
  }

  /* The poster wins the bandwidth race by design: paint it first, and only
     then put the video on the wire. A visitor should never look at an empty
     stage while a Blob downloads. */
  function initOnce() {
    if (started) return;
    started = true;

    var begin = function () { loadBlob().catch(failVideo); };

    if (!POSTER_URL) { begin(); return; }

    var img = new Image();
    img.onload = begin;
    img.onerror = begin;
    img.src = POSTER_URL;

    /* Safety: a poster that never resolves must not hold the video hostage. */
    window.setTimeout(begin, 4000);
  }

  /* ---- seeking ------------------------------------------------------- */

  function requestSeek(time) {
    if (!video.duration || isNaN(time)) return;

    if (seekBusy) { pendingTime = time; return; }   // coalesce to newest only

    seekBusy = true;
    video.currentTime = time;
  }

  video.addEventListener('seeked', function () {
    seekBusy = false;

    if (pendingTime !== null) {
      var next = pendingTime;
      pendingTime = null;
      requestSeek(next);
    }
  });

  /* The deadlock escape. A seek that errors never fires seeked, and without
     this the gate stays busy forever and scrubbing freezes mid-scroll. */
  video.addEventListener('error', function () {
    seekBusy = false;
    pendingTime = null;
    failVideo();
  });

  /* ---- captions ------------------------------------------------------ */

  /* Every write here is delta-gated. Touching the DOM every frame is the
     other half of choppy, and a band whose opacity has not changed does not
     need to be told so sixty times a second. */
  function updateBands(p) {
    for (var i = 0; i < bands.length; i++) {
      var band = bands[i];
      var a = band.from;
      var b = band.to;

      /* Ramps in progress units, so pacing holds whatever the hero height is.
         The plateau is most of the band; the ramps are trim. */
      var ramp = Math.min(0.02, (b - a) / 3);

      /* The first band opens already settled and the last one never fades
         out, so the journey starts and ends with the words at rest. */
      var fadeIn = (i === 0) ? 1 : smoothstep(p, a, a + ramp);
      var fadeOut = (i === bands.length - 1) ? 1 : (1 - smoothstep(p, b - ramp, b));
      var opacity = Math.round(fadeIn * fadeOut * 1000) / 1000;

      if (opacity !== band.opacity) {
        band.opacity = opacity;
        band.el.style.opacity = String(opacity);
        /* Out of the way for the pointer once invisible, so the settle band's
           buttons are not blocked by a band that is no longer on screen. */
        band.el.style.visibility = opacity < 0.01 ? 'hidden' : 'visible';
      }

      /* Assembly progress, settling early in the band and holding for the
         long plateau. Drives the per-band scrim and the entrances. */
      var k = clamp((p - a) / (ramp || 0.02), 0, 1);
      k = Math.round(k * 100) / 100;

      if (k !== band.k) {
        band.k = k;
        band.el.style.setProperty('--k', String(k));
      }
    }
  }

  /* ---- the drive loop ------------------------------------------------ */

  function tick(now) {
    var dt = Math.min(100, now - (lastTick || now));
    lastTick = now;

    /* Frame-rate independent easing. A flat per-frame constant converges
       twice as fast on a 120Hz screen as on a 60Hz one, and the site feels
       like a different site per machine. */
    var k = 0.16;
    shown += (target - shown) * (1 - Math.pow(1 - k, dt / 16.667));

    if (Math.abs(target - shown) < 0.0005) {
      shown = target;
      rafId = null;
      lastTick = 0;          // converged: the loop rests rather than free-runs
    } else {
      rafId = window.requestAnimationFrame(tick);
    }

    if (video.duration) requestSeek(shown * video.duration);
    updateBands(shown);
  }

  function onScroll() {
    target = heroProgress();
    if (rafId === null && heroOnScreen) rafId = window.requestAnimationFrame(tick);
  }

  /* The loop must not run while the hero is scrolled past. A rAF loop that
     free-runs from page load wastes battery and marks the build as amateur. */
  if ('IntersectionObserver' in window) {
    new IntersectionObserver(function (entries) {
      heroOnScreen = entries[0].isIntersecting;
      if (heroOnScreen && scrubOn) onScroll();
    }, { threshold: 0 }).observe(pin);
  }

  /* ---- the live gate ------------------------------------------------- */

  function enableScrub() {
    if (scrubOn) return;
    scrubOn = true;

    initOnce();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);

    /* Reset the caches, or bands keep whatever value they were pinned at. */
    bands.forEach(function (band) { band.opacity = -1; band.k = -1; });

    updateBands(heroProgress());
    onScroll();   // re-seek to where the reader already is, not to frame zero
  }

  function disableScrub() {
    if (!scrubOn) return;
    scrubOn = false;

    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);

    if (rafId !== null) {
      window.cancelAnimationFrame(rafId);
      rafId = null;
    }
  }

  function applyHeroMode() {
    var gated = MQLS.some(function (mql) { return mql.matches; });
    if (gated) disableScrub(); else enableScrub();
  }

  MQLS.forEach(function (mql) {
    if (mql.addEventListener) mql.addEventListener('change', applyHeroMode);
    else if (mql.addListener) mql.addListener(applyHeroMode);
  });

  applyHeroMode();
})();
