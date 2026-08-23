/**
 * Trip Kailash header
 *
 * Two jobs: decide whether the header is transparent or solid, and run the
 * mobile panel.
 *
 * The transparent state is decided by watching the hero rather than by a
 * scroll offset. A fixed number would be wrong the moment the hero changes
 * height, which it does between the homepage, a package page and a phone.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var header = document.getElementById('tk-header');
  if (!header) return;

  /* ---- transparent over a hero, solid past it ------------------------- */

  var hero = document.querySelector('.tk-hero, .tk-package-hero');

  function setSolid(solid) {
    header.classList.toggle('is-solid', solid);
  }

  if (hero && 'IntersectionObserver' in window) {
    /* Flip as the last sliver of the hero leaves, so the change lands with
       the reader rather than a moment after them. */
    new IntersectionObserver(function (entries) {
      setSolid(!entries[0].isIntersecting);
    }, { rootMargin: '-72px 0px 0px 0px', threshold: 0 }).observe(hero);
  } else {
    setSolid(true);
  }

  /* ---- the mobile panel ------------------------------------------------ */

  var toggle = document.querySelector('.tk-mobile-menu-toggle');
  var panel = document.getElementById('tk-mobile-nav');

  if (!toggle || !panel) return;

  function close() {
    toggle.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
    document.body.classList.remove('tk-scroll-locked');
  }

  function open() {
    toggle.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
    document.body.classList.add('tk-scroll-locked');
  }

  toggle.addEventListener('click', function () {
    if (toggle.getAttribute('aria-expanded') === 'true') close();
    else open();
  });

  /* Escape closes it and returns focus to the button, which is where the
     reader was before they opened it. */
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
      close();
      toggle.focus();
    }
  });

  /* Following a link inside the panel should close it. Without this, an
     in-page anchor scrolls the page behind a menu that is still covering it. */
  panel.addEventListener('click', function (event) {
    if (event.target.closest('a')) close();
  });

  /* Widening past the breakpoint leaves the panel open and the body locked
     otherwise, with no visible way to undo either. */
  var wide = window.matchMedia('(min-width: 901px)');

  function onWide() {
    if (wide.matches) close();
  }

  if (wide.addEventListener) wide.addEventListener('change', onWide);
  else if (wide.addListener) wide.addListener(onWide);
})();
