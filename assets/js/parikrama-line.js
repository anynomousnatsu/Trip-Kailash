/**
 * Trip Kailash parikrama line
 *
 * The signature element: one brass hairline running the length of the page,
 * drawn by scroll, with a step marker at each section. It is what makes the
 * homepage read as one continuous walk rather than as stacked sections, and
 * it is the ornament from the design system doing real work instead of
 * sitting there.
 *
 * It also navigates. Each marker is a real link to its section, so the thing
 * that makes the page feel like a journey is also the fastest way to move
 * around it. Decoration that earns its keep twice.
 *
 * Built by JS rather than shipped in the markup, because a page with no
 * JavaScript should not carry a dead ornament, and because the markers have
 * to be derived from whichever sections actually rendered: doors, verify and
 * guides all remove themselves when they have nothing to show.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var main = document.querySelector('.tk-home');
  if (!main) return;

  var REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)');
  var WIDE = window.matchMedia('(min-width: 1200px)');

  var sections = [].slice.call(main.querySelectorAll('section[id]'));
  if (sections.length < 3) return;

  var rail = document.createElement('nav');
  rail.className = 'tk-line';
  rail.setAttribute('aria-label', 'Sections of this page');

  var track = document.createElement('span');
  track.className = 'tk-line__track';

  var drawn = document.createElement('span');
  drawn.className = 'tk-line__drawn';

  rail.appendChild(track);
  rail.appendChild(drawn);

  var marks = sections.map(function (section) {
    var link = document.createElement('a');
    link.className = 'tk-line__mark';
    link.href = '#' + section.id;

    /* The heading each section is labelled by, so the marker announces
       something meaningful rather than a slug. */
    var labelId = section.getAttribute('aria-labelledby');
    var labelEl = labelId ? document.getElementById(labelId) : null;
    var label = labelEl ? labelEl.textContent.trim() : section.id;

    link.setAttribute('aria-label', label);

    var dot = document.createElement('span');
    dot.className = 'tk-line__dot';
    link.appendChild(dot);

    var name = document.createElement('span');
    name.className = 'tk-line__name';
    name.textContent = label;
    link.appendChild(name);

    rail.appendChild(link);
    return { el: link, section: section };
  });

  main.appendChild(rail);

  var ticking = false;
  var shownProgress = -1;
  var shownActive = -1;

  function place() {
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;

    marks.forEach(function (mark) {
      var top = mark.section.offsetTop;
      var fraction = docHeight > 0 ? Math.min(1, top / docHeight) : 0;
      mark.fraction = fraction;
      mark.el.style.top = (fraction * 100).toFixed(2) + '%';
    });
  }

  function paint() {
    ticking = false;

    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var progress = docHeight > 0 ? Math.min(1, Math.max(0, window.scrollY / docHeight)) : 0;
    var rounded = Math.round(progress * 1000) / 1000;

    if (rounded !== shownProgress) {
      shownProgress = rounded;
      drawn.style.transform = 'scaleY(' + rounded + ')';
    }

    /* The active marker is the last one the reader has reached. */
    var active = 0;
    for (var i = 0; i < marks.length; i++) {
      if (progress >= marks[i].fraction - 0.01) active = i;
    }

    if (active !== shownActive) {
      if (shownActive > -1) marks[shownActive].el.classList.remove('is-here');
      marks[active].el.classList.add('is-here');
      marks[active].el.setAttribute('aria-current', 'true');
      if (shownActive > -1) marks[shownActive].el.removeAttribute('aria-current');
      shownActive = active;
    }
  }

  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(paint);
  }

  function applyMode() {
    if (!WIDE.matches) {
      rail.hidden = true;
      return;
    }

    rail.hidden = false;
    place();

    if (REDUCE.matches) {
      /* Fully drawn and still. The line is a wayfinding device as much as an
         ornament, so it stays visible rather than being removed. */
      drawn.style.transform = 'scaleY(1)';
      shownProgress = 1;
      return;
    }

    shownProgress = -1;
    paint();
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', function () { place(); onScroll(); });
  window.addEventListener('load', function () { place(); onScroll(); });

  [REDUCE, WIDE].forEach(function (mql) {
    if (mql.addEventListener) mql.addEventListener('change', applyMode);
    else if (mql.addListener) mql.addListener(applyMode);
  });

  applyMode();
})();
