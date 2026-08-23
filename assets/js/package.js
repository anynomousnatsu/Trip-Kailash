/**
 * Trip Kailash package page
 *
 * Two jobs: keep the estimated total honest as the party size changes, and
 * send the enquiry without throwing away the page the visitor is reading.
 *
 * The total is explicitly an estimate and says so. This business quotes by
 * invoice, and a number that looks like a checkout figure would be a promise
 * the operator has not made.
 *
 * No dependencies.
 */
(function () {
  'use strict';

  var form = document.querySelector('[data-tk-enquiry]');
  if (!form) return;

  var pax = form.querySelector('[data-tk-pax]');
  var totalEl = form.querySelector('[data-tk-total]');
  var resultEl = form.querySelector('[data-tk-result]');
  var submit = form.querySelector('[type="submit"]');

  /* ---- the running total --------------------------------------------- */

  function tiersFrom(el) {
    try {
      var parsed = JSON.parse(el.getAttribute('data-tiers') || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  /* The per-person price at this party size: the best tier they qualify for,
     or the base price if they qualify for none. */
  function priceEach(count, base, tiers) {
    var best = base;

    tiers.forEach(function (tier) {
      var min = parseFloat(tier.min_pax);
      var price = parseFloat(tier.price);
      if (!isNaN(min) && !isNaN(price) && count >= min && price < best) best = price;
    });

    return best;
  }

  function money(value) {
    return '$' + Math.round(value).toLocaleString();
  }

  function renderTotal() {
    if (!pax || !totalEl) return;

    var base = parseFloat(pax.getAttribute('data-base-price'));
    if (isNaN(base) || base <= 0) return;

    var count = parseInt(pax.value, 10);
    if (isNaN(count) || count < 1) count = 1;

    var each = priceEach(count, base, tiersFrom(pax));
    var total = each * count;
    var depositPercent = parseFloat(totalEl.getAttribute('data-deposit')) || 30;
    var deposit = total * (depositPercent / 100);

    totalEl.innerHTML = '';

    var line = document.createElement('span');
    line.className = 'tk-reserve__total-line';
    line.textContent = totalEl.getAttribute('data-label-total') + ' ' + money(total);
    totalEl.appendChild(line);

    var sub = document.createElement('span');
    sub.className = 'tk-reserve__total-sub';
    sub.textContent = money(deposit) + ' ' + totalEl.getAttribute('data-label-deposit');
    totalEl.appendChild(sub);

    /* Say out loud that the group rate applied, or the number looks like a
       mistake to someone who read the tier table two inches above. */
    if (each < base) {
      var saved = document.createElement('span');
      saved.className = 'tk-reserve__total-sub';
      saved.textContent = money(each) + ' each at this group size';
      totalEl.appendChild(saved);
    }
  }

  if (pax) {
    pax.addEventListener('input', renderTotal);
    pax.addEventListener('change', renderTotal);
    renderTotal();
  }

  /* ---- sending -------------------------------------------------------- */

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    if (!window.tripKailashData || !window.tripKailashData.ajaxUrl) {
      /* No endpoint means no silent failure: let the browser post normally
         rather than swallowing the enquiry. */
      form.submit();
      return;
    }

    if (submit) {
      submit.disabled = true;
      submit.dataset.label = submit.textContent;
      submit.textContent = 'Sending';
    }

    if (resultEl) {
      resultEl.textContent = '';
      resultEl.className = 'tk-reserve__result';
    }

    fetch(window.tripKailashData.ajaxUrl, {
      method: 'POST',
      body: new FormData(form),
      credentials: 'same-origin'
    })
      .then(function (response) { return response.json(); })
      .then(function (payload) {
        var ok = payload && payload.success;

        if (resultEl) {
          resultEl.className = 'tk-reserve__result ' + (ok ? 'is-good' : 'is-bad');
          resultEl.textContent = ok
            ? 'Your enquiry is with us. We reply within one working day, usually sooner.'
            : ((payload && payload.data && payload.data.message)
                || 'That did not send. Email us and we will pick it up from there.');
        }

        if (ok) form.reset();
      })
      .catch(function () {
        if (resultEl) {
          resultEl.className = 'tk-reserve__result is-bad';
          resultEl.textContent = 'That did not send. Email us and we will pick it up from there.';
        }
      })
      .finally(function () {
        if (submit) {
          submit.disabled = false;
          submit.textContent = submit.dataset.label || 'Send enquiry';
        }
        renderTotal();
      });
  });
})();
