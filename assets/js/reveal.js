/**
 * Trip Kailash reveal and motion driver
 *
 * Drives the classes that assets/css/motion.css styles:
 *   .is-in    an element has arrived, one shot
 *   .is-done  a stagger container has finished, so its delays can retire
 *   .is-near  a section is on screen, so its whisper loop may run
 *
 * Reduced motion is handled live and in BOTH directions. Switching it on
 * mid-session pins every element to its final state and stops the observers;
 * switching it back off re-arms them instead of leaving the pins behind.
 * Re-arming one half and leaving the other pinned is the half-fix that looks
 * finished and is not.
 *
 * No dependencies. Runs at defer time, so the DOM is already parsed.
 */
(function () {
  'use strict';

  var REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)');
  var STAGGER_STEP = 70;     // must match motion.css
  var STAGGER_MAX = 420;
  var REVEAL_MS = 700;       // must match --dur-slow

  var revealObserver = null;
  var nearObserver = null;

  function each(list, fn) {
    Array.prototype.forEach.call(list, fn);
  }

  /* ---- stagger ------------------------------------------------------ */

  /* Retire the delays once the last child has landed. Without this every
     hover on the later siblings lags by its stagger for the life of the
     page. */
  function scheduleDone(container) {
    var kids = container.querySelectorAll(':scope > .tk-rv');
    var last = Math.min((kids.length - 1) * STAGGER_STEP, STAGGER_MAX);
    window.setTimeout(function () {
      container.classList.add('is-done');
    }, last + REVEAL_MS + 60);
  }

  /* ---- observers ---------------------------------------------------- */

  function armReveal() {
    if (revealObserver || !('IntersectionObserver' in window)) return;

    revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        el.classList.add('is-in');
        if (el.classList.contains('tk-stagger')) scheduleDone(el);
        revealObserver.unobserve(el);
      });
    }, {
      threshold: 0.12,
      // Fire a little before the element is fully in view, so the entrance
      // is already underway by the time the reader looks at it.
      rootMargin: '0px 0px -8% 0px'
    });

    each(document.querySelectorAll('.tk-rv, .tk-rule, .tk-stagger'), function (el) {
      if (el.classList.contains('is-in')) return;
      revealObserver.observe(el);
    });
  }

  /* Two-way, and deliberately never unobserved: a whisper loop should stop
     again when its section leaves the screen. */
  function armNear() {
    if (nearObserver || !('IntersectionObserver' in window)) return;

    nearObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        entry.target.classList.toggle('is-near', entry.isIntersecting);
      });
    }, { threshold: 0 });

    each(document.querySelectorAll('.tk-glow, .tk-float'), function (el) {
      nearObserver.observe(el);
    });
  }

  function disarm() {
    if (revealObserver) { revealObserver.disconnect(); revealObserver = null; }
    if (nearObserver) { nearObserver.disconnect(); nearObserver = null; }
  }

  /* ---- reduced motion, both directions ------------------------------ */

  function pinToFinalStates() {
    disarm();
    document.body.classList.add('tk-motion-off');
    each(document.querySelectorAll('.tk-rv, .tk-rule'), function (el) {
      el.classList.add('is-in');
    });
    each(document.querySelectorAll('.tk-stagger'), function (el) {
      el.classList.add('is-in', 'is-done');
    });
    // Whisper loops stop rather than freeze mid-cycle.
    each(document.querySelectorAll('.tk-glow, .tk-float'), function (el) {
      el.classList.remove('is-near');
    });
  }

  /* Reduced motion gets SOFT motion, not no motion.
   *
   * Pinning everything to its final state was more than the setting asks for.
   * What it is about is vestibular triggers: parallax, scroll-jacking, large
   * travel. A short opacity fade is none of those, and removing it made the
   * whole page look inert to anyone who has the OS toggle on, which is a lot
   * of people who never chose it deliberately.
   *
   * So the reveal observer still runs and things still arrive. They arrive by
   * becoming visible instead of by moving. The looping whispers stay off,
   * because a glow that never settles is the actual thing being asked about.
   */
  function applyMotion() {
    disarm();

    document.body.classList.remove('tk-motion-off');
    document.body.classList.toggle('tk-motion-soft', REDUCE.matches);

    armReveal();

    if (!REDUCE.matches) {
      armNear();
    } else {
      each(document.querySelectorAll('.tk-glow, .tk-float'), function (el) {
        el.classList.remove('is-near');
      });
    }
  }

  /* Older Safari only has the deprecated addListener. */
  if (REDUCE.addEventListener) {
    REDUCE.addEventListener('change', applyMotion);
  } else if (REDUCE.addListener) {
    REDUCE.addListener(applyMotion);
  }

  /* ---- hidden tabs --------------------------------------------------- */

  document.addEventListener('visibilitychange', function () {
    document.body.classList.toggle('tk-paused', document.hidden);
  });

  /* ---- no IntersectionObserver -------------------------------------- */

  /* The page must be complete without JS help. If the API is missing, show
     everything rather than leaving the whole page at opacity 0. */
  if (!('IntersectionObserver' in window)) {
    pinToFinalStates();
    return;
  }

  applyMotion();
})();
