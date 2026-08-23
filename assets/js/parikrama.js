/**
 * Trip Kailash pinned temple gallery
 *
 * Vertical scroll drives the track horizontally while the section is pinned.
 * Whichever temple is nearest the viewport centre becomes active: full scale,
 * in focus, and its significance expands. Everything else blurs, dims and
 * shrinks IN PROPORTION to its distance from centre, so the falloff is
 * continuous rather than a hard on-off toggle. The proportional part is what
 * makes it read as depth of field rather than as a state change.
 *
 * One function serves both modes. Below 900px the pinning is off and the
 * track is a native swipe, but "which card is nearest the middle of the
 * screen" is the same question either way, so the same measurement answers it.
 *
 * Scroll handlers are passive and rAF-throttled, and every style write is
 * delta-gated: a card whose blur has not changed is not told about it sixty
 * times a second.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var pin = document.getElementById('tk-parikrama-pin');
  var track = document.getElementById('tk-parikrama-track');

  if (!pin || !track) return;

  var temples = [].slice.call(track.children);
  if (!temples.length) return;

  var REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)');
  var PINNED = window.matchMedia('(min-width: 901px)');

  var ticking = false;
  var activeIndex = -1;

  /* Last written values per temple, so the DOM is only touched on change. */
  var state = temples.map(function () {
    return { blur: -1, opacity: -1, scale: -1 };
  });

  function isPinned() {
    return PINNED.matches && !REDUCE.matches;
  }

  function clamp(v, lo, hi) { return v < lo ? lo : (v > hi ? hi : v); }

  function paint() {
    ticking = false;

    /* Drive the track horizontally from vertical progress through the pin. */
    if (isPinned()) {
      var span = pin.offsetHeight - window.innerHeight;
      var progress = span > 0
        ? clamp((window.scrollY - pin.offsetTop) / span, 0, 1)
        : 0;
      var distance = track.scrollWidth - window.innerWidth;

      if (distance > 0) {
        track.style.transform = 'translate3d(' + (-progress * distance).toFixed(1) + 'px,0,0)';
      }
    }

    var middle = window.innerWidth / 2;
    var nearest = 0;
    var nearestDistance = Infinity;

    for (var i = 0; i < temples.length; i++) {
      var rect = temples[i].getBoundingClientRect();
      var offset = Math.abs(rect.left + rect.width / 2 - middle);

      if (offset < nearestDistance) {
        nearestDistance = offset;
        nearest = i;
      }

      /* Normalised distance from centre, 0 at the middle and 1 half a
         viewport away. The three falloffs below are the design package's
         numbers: blur 0 to 4.5px, opacity 1 to 0.4, scale 1 to 0.86. */
      var n = Math.min(1, offset / (window.innerWidth * 0.5));

      var blur = Math.round(n * 4.5 * 100) / 100;
      var opacity = Math.round((1 - n * 0.6) * 100) / 100;
      var scale = Math.round((1 - n * 0.14) * 1000) / 1000;

      var previous = state[i];

      if (blur !== previous.blur) {
        previous.blur = blur;
        temples[i].style.filter = blur > 0.02 ? 'blur(' + blur + 'px)' : '';
      }

      if (opacity !== previous.opacity) {
        previous.opacity = opacity;
        temples[i].style.opacity = String(opacity);
      }

      if (scale !== previous.scale) {
        previous.scale = scale;
        temples[i].style.transform = 'scale(' + scale + ')';
      }
    }

    if (nearest !== activeIndex) {
      if (activeIndex > -1) temples[activeIndex].classList.remove('is-active');
      temples[nearest].classList.add('is-active');
      activeIndex = nearest;
    }
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(paint);
  }

  /* Everything is shown at rest, and the drives never start. */
  function pinToFinalStates() {
    temples.forEach(function (temple, i) {
      temple.style.filter = '';
      temple.style.opacity = '';
      temple.style.transform = '';
      temple.classList.toggle('is-active', i === 0);
      state[i] = { blur: -1, opacity: -1, scale: -1 };
    });
    track.style.transform = '';
    activeIndex = 0;
  }

  function arm() {
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    track.addEventListener('scroll', onScroll, { passive: true });
    paint();
  }

  function disarm() {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onScroll);
    track.removeEventListener('scroll', onScroll);
  }

  /* Reduced motion, live and in both directions. Flipping it on mid-session
     stops the drives and shows everything; flipping it back off re-arms them
     rather than leaving the gallery pinned at its final state. */
  function applyMode() {
    disarm();

    if (REDUCE.matches) {
      pinToFinalStates();
      return;
    }

    arm();
  }

  [REDUCE, PINNED].forEach(function (mql) {
    if (mql.addEventListener) mql.addEventListener('change', applyMode);
    else if (mql.addListener) mql.addListener(applyMode);
  });

  applyMode();
})();
