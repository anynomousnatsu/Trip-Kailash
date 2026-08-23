/**
 * Repeater rows on the package edit screen.
 *
 * Add and remove rows for the itinerary, fixed departures, group pricing and
 * FAQ. Delegated, so rows added after load behave like the ones rendered by
 * PHP.
 *
 * The row index only has to be unique within its repeater, not sequential:
 * the save handler reindexes and drops blank rows anyway. Using a counter
 * that never goes backwards avoids the collision you get from using the
 * current row count after something in the middle has been removed.
 */
(function () {
  'use strict';

  var counters = {};

  function nextIndex(repeater) {
    var field = repeater.getAttribute('data-field');
    if (typeof counters[field] === 'undefined') {
      counters[field] = repeater.querySelectorAll('.tk-repeater__row').length;
    }
    counters[field] += 1;
    return counters[field] + Date.now() % 1000;
  }

  document.addEventListener('click', function (event) {
    var add = event.target.closest('.tk-repeater__add');

    if (add) {
      event.preventDefault();
      var repeater = add.closest('.tk-repeater');
      var template = repeater.querySelector('.tk-repeater__template');
      var rows = repeater.querySelector('.tk-repeater__rows');
      if (!template || !rows) return;

      var html = template.innerHTML.split('__i__').join(String(nextIndex(repeater)));
      var holder = document.createElement('div');
      holder.innerHTML = html;

      var row = holder.firstElementChild;
      if (!row) return;

      // The template ships disabled so the browser never submits it. A real
      // row has to be enabled or it saves as nothing.
      Array.prototype.forEach.call(row.querySelectorAll('[disabled]'), function (input) {
        input.removeAttribute('disabled');
      });

      rows.appendChild(row);

      var first = row.querySelector('input, textarea');
      if (first) first.focus();
      return;
    }

    var remove = event.target.closest('.tk-repeater__remove');

    if (remove) {
      event.preventDefault();
      var target = remove.closest('.tk-repeater__row');
      if (target) target.remove();
    }
  });
})();
