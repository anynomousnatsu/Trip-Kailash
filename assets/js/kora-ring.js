/**
 * Trip Kailash kora ring
 *
 * Hold, and the circle is walked. Progress builds while the pointer is down,
 * eases back down on an early release rather than snapping to zero, and the
 * payoff lands only when the circle closes.
 *
 * The altitude readout is interpolated across the real profile of the kora,
 * so the number climbing past 5,630 at the Dolma La is the number a pilgrim
 * actually crosses. That is the whole reason the interaction is worth doing:
 * it teaches something true rather than animating for its own sake.
 *
 * Keyboard and assistive technology do not hold; they press. So the control is
 * a real button and a plain activation completes the circle in one go, with
 * the same payoff. Reduced motion starts completed and never animates.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var root = document.getElementById('tk-kora');
  if (!root) return;

  var control = root.querySelector('.tk-kora__control');
  var pathEl = root.querySelector('.tk-kora__path');
  var marksEl = root.querySelector('.tk-kora__marks');
  var altEl = root.querySelector('[data-role="altitude"]');
  var placeEl = root.querySelector('[data-role="place"]');
  var readEl = root.querySelector('[data-role="readout"]');

  if (!control || !pathEl || !altEl || !readEl) return;

  var markers;
  try {
    markers = JSON.parse(root.getAttribute('data-markers'));
  } catch (e) {
    return;
  }

  if (!markers || markers.length < 2) return;

  var REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)');
  var CIRCUMFERENCE = 2 * Math.PI * 92;

  var progress = 0;      // 0 to 1 around the circle
  var holding = false;
  var rafId = null;
  var lastTick = 0;
  var completed = false;

  /* Last written values, so nothing touches the DOM without a reason. */
  var shownAlt = null;
  var shownPlace = null;
  var shownState = null;
  var shownMark = -1;

  function clamp(v, lo, hi) { return v < lo ? lo : (v > hi ? hi : v); }

  /* Draw the day markers around the ring, evenly spaced in walking order. */
  function drawMarkers() {
    if (!marksEl) return;
    var svgNS = 'http://www.w3.org/2000/svg';

    markers.forEach(function (marker, i) {
      var t = i / (markers.length - 1);
      var angle = -Math.PI / 2 + t * Math.PI * 2;
      var dot = document.createElementNS(svgNS, 'circle');

      dot.setAttribute('cx', String(120 + Math.cos(angle) * 92));
      dot.setAttribute('cy', String(120 + Math.sin(angle) * 92));
      dot.setAttribute('r', '5');
      dot.setAttribute('class', 'tk-kora__mark');
      marksEl.appendChild(dot);
    });
  }

  /* Altitude between the two markers this progress falls between. */
  function altitudeAt(p) {
    var span = 1 / (markers.length - 1);
    var index = Math.min(markers.length - 2, Math.floor(p / span));
    var local = (p - index * span) / span;
    var from = markers[index].alt;
    var to = markers[index + 1].alt;

    return {
      value: Math.round(from + (to - from) * local),
      index: local > 0.5 ? index + 1 : index
    };
  }

  function render() {
    pathEl.style.strokeDashoffset = String(CIRCUMFERENCE * (1 - progress));

    var reading = altitudeAt(progress);

    if (reading.value !== shownAlt) {
      shownAlt = reading.value;
      altEl.textContent = reading.value.toLocaleString();
    }

    var place = markers[reading.index].label;

    if (place !== shownPlace) {
      shownPlace = place;
      if (placeEl) placeEl.textContent = place;
    }

    if (marksEl && reading.index !== shownMark) {
      shownMark = reading.index;
      var dots = marksEl.children;
      for (var i = 0; i < dots.length; i++) {
        dots[i].classList.toggle('is-lit', i <= reading.index);
      }
    }

    /* Three states, and the readout is only rewritten when it changes, so a
       screen reader is not told the same sentence sixty times a second. */
    var state = 'idle';
    if (completed) state = 'done';
    else if (progress > 0.42 && progress < 0.62) state = 'pass';
    else if (progress > 0.02) state = 'walking';

    if (state !== shownState) {
      shownState = state;

      if (state === 'done') {
        readEl.textContent = readEl.getAttribute('data-done');
        root.classList.add('is-complete');
      } else if (state === 'pass') {
        readEl.textContent = readEl.getAttribute('data-pass');
      } else if (state === 'idle') {
        readEl.textContent = readEl.getAttribute('data-idle');
      } else {
        readEl.textContent = '';
      }
    }
  }

  /* ---- the drive ------------------------------------------------------ */

  function tick(now) {
    var dt = Math.min(100, now - (lastTick || now));
    lastTick = now;

    if (holding) {
      /* About four seconds to walk the whole circle. Slow enough that the
         altitude climb is legible, short enough that nobody gives up. */
      progress = clamp(progress + dt / 4000, 0, 1);
    } else if (!completed) {
      /* Eases back rather than snapping. A snap says the attempt did not
         count; an ease says the walk is simply not finished. */
      progress = clamp(progress - dt / 2600, 0, 1);
    }

    if (progress >= 1 && !completed) {
      completed = true;
      holding = false;
    }

    render();

    var settled = completed || (!holding && progress <= 0);

    if (settled) {
      rafId = null;
      lastTick = 0;
      return;
    }

    rafId = window.requestAnimationFrame(tick);
  }

  function start() {
    if (rafId === null) {
      lastTick = 0;
      rafId = window.requestAnimationFrame(tick);
    }
  }

  function hold(event) {
    if (completed) return;
    /* Stop a press from selecting text or starting a scroll gesture, which
       is what makes a hold feel like a hold rather than a fight. */
    if (event.cancelable) event.preventDefault();
    holding = true;
    start();
  }

  function release() {
    holding = false;
    start();
  }

  function complete() {
    if (completed) return;
    progress = 1;
    completed = true;
    holding = false;
    render();
  }

  /* ---- input ---------------------------------------------------------- */

  control.addEventListener('pointerdown', hold);
  window.addEventListener('pointerup', release);
  window.addEventListener('pointercancel', release);

  /* Leaving the control mid-hold counts as letting go. Without this, dragging
     off the ring leaves it climbing with nothing pressed. */
  control.addEventListener('pointerleave', release);

  /* Space and Enter fire click on a button, and a keyboard user cannot hold.
     They press once and the circle closes, with the same payoff. */
  control.addEventListener('click', function (event) {
    event.preventDefault();
    complete();
  });

  /* ---- reduced motion, both directions -------------------------------- */

  function applyMode() {
    if (REDUCE.matches) {
      if (rafId !== null) {
        window.cancelAnimationFrame(rafId);
        rafId = null;
      }
      complete();
      return;
    }

    /* Coming back out of reduced motion mid-session leaves the circle closed
       rather than resetting it. The visitor has already been shown the
       payoff; taking it away to make them earn it again would be rude. */
    render();
  }

  if (REDUCE.addEventListener) REDUCE.addEventListener('change', applyMode);
  else if (REDUCE.addListener) REDUCE.addListener(applyMode);

  pathEl.style.strokeDasharray = String(CIRCUMFERENCE);
  pathEl.style.strokeDashoffset = String(CIRCUMFERENCE);
  drawMarkers();
  applyMode();
  render();
})();
