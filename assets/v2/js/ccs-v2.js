/* =====================================================================
   ccs-v2.js — UI enhancements for the redesigned CCS calculator.
   ---------------------------------------------------------------------
   This script NEVER touches the calculation logic. It talks to the
   existing calculator purely through the DOM: it drives the original
   navigation by triggering the (hidden) progress steps, mirrors state
   into the new sidebar, and mirrors the rendered summary figures into
   the sticky panel. If this file is removed, the calculator still works.
   ===================================================================== */
(function () {
  'use strict';
  if (typeof jQuery === 'undefined') { return; }

  jQuery(function ($) {
    var $root = $('.ccs-redesign').first();
    if (!$root.length) { return; }
    var root = $root[0];

    /* ---------- colour helpers (brand awareness) ---------- */
    function parseRGB(c) {
      if (!c) { return null; }
      var m = c.match(/rgba?\(([^)]+)\)/i);
      if (m) {
        var p = m[1].split(',').map(function (s) { return parseFloat(s); });
        return { r: p[0] || 0, g: p[1] || 0, b: p[2] || 0, a: (p[3] === undefined ? 1 : p[3]) };
      }
      m = c.replace('#', '');
      if (/^[0-9a-f]{3}$/i.test(m)) { m = m.replace(/./g, '$&$&'); }
      if (/^[0-9a-f]{6}$/i.test(m)) {
        return { r: parseInt(m.slice(0, 2), 16), g: parseInt(m.slice(2, 4), 16), b: parseInt(m.slice(4, 6), 16), a: 1 };
      }
      return null;
    }
    function toHex(o) {
      function h(x) { return ('0' + Math.max(0, Math.min(255, Math.round(x))).toString(16)).slice(-2); }
      return '#' + h(o.r) + h(o.g) + h(o.b);
    }
    function mix(c, t, a) { return { r: c.r + (t.r - c.r) * a, g: c.g + (t.g - c.g) * a, b: c.b + (t.b - c.b) * a }; }
    function lum(c) {
      function f(v) { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); }
      return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    }

    /* Derive the brand palette from the admin primary button colour. */
    function applyBrand() {
      var $btn = $root.find('.nav-next, .button-primary').filter(':visible').first();
      if (!$btn.length) { $btn = $root.find('.nav-next, .button-primary').first(); }
      var brand = null;
      if ($btn.length) { brand = parseRGB($btn.css('background-color')); }
      if (!brand || brand.a === 0) { brand = parseRGB('#0073aa'); }
      root.style.setProperty('--ccs-brand', toHex(brand));
      root.style.setProperty('--ccs-brand-dark', toHex(mix(brand, { r: 0, g: 0, b: 0 }, 0.18)));
      root.style.setProperty('--ccs-brand-soft', toHex(mix(brand, { r: 255, g: 255, b: 255 }, 0.90)));
      root.style.setProperty('--ccs-brand-contrast', lum(brand) > 0.6 ? '#1f2937' : '#ffffff');
    }

    /* ---------- Sidebar navigation ---------- */
    // Clicking a sidebar item drives the ORIGINAL navigation by triggering the
    // matching (hidden) progress step, which already enforces "backward only".
    $root.on('click', '.ccs-nav-item', function () {
      var step = $(this).data('step');
      $('#childcare-progress .progress-step[data-step="' + step + '"]').trigger('click');
    });

    // Mirror active/completed state from the hidden progress steps.
    function syncNav() {
      $('#childcare-progress .progress-step').each(function () {
        var step = $(this).data('step');
        var $item = $root.find('.ccs-nav-item[data-step="' + step + '"]');
        $item.toggleClass('is-active', $(this).hasClass('active'));
        $item.toggleClass('is-completed', $(this).hasClass('completed'));
      });
      // On the long Summary step, use content-sized sticky rails (not equal-height stretch)
      var activeStep = $('#childcare-progress .progress-step.active').data('step');
      $root.toggleClass('ccs-summary-active', String(activeStep) === '4');
    }
    var progressEl = document.getElementById('childcare-progress');
    if (progressEl && window.MutationObserver) {
      new MutationObserver(syncNav).observe(progressEl, { subtree: true, attributes: true, attributeFilter: ['class'] });
    }
    syncNav();

    /* ---------- Live summary panel ---------- */
    function txt(sel) { return ($.trim($(sel).first().text()) || ''); }
    function subsidyPct() {
      var known = $('#know_ccs_percentage').val();
      var v = (known === 'yes') ? $('#standard_ccs_percentage').val() : $('#standard_ccs_percentage_calc').val();
      v = parseFloat(v);
      return isNaN(v) ? null : (Math.round(v * 100) / 100);
    }

    function buildFigures() {
      return '' +
        '<div class="ccs-panel-figures">' +
          '<div class="ccs-fig ccs-fig--pay">' +
            '<div class="ccs-fig-label">What you pay</div>' +
            '<div class="ccs-fig-val" data-fig="pay">—</div>' +
            '<div class="ccs-fig-sub" data-fig="period">per fortnight</div>' +
          '</div>' +
          '<div class="ccs-fig ccs-fig--govt">' +
            '<div class="ccs-fig-label">Government pays</div>' +
            '<div class="ccs-fig-val" data-fig="govt">—</div>' +
            '<div class="ccs-fig-sub" data-fig="period2">per fortnight</div>' +
          '</div>' +
          '<div class="ccs-fig-row">' +
            '<div class="ccs-fig-mini"><div class="ccs-fig-label">Subsidy</div><div class="ccs-fig-val" data-fig="pct">—</div></div>' +
            '<div class="ccs-fig-mini"><div class="ccs-fig-label">Children</div><div class="ccs-fig-val" data-fig="kids">—</div></div>' +
          '</div>' +
          '<div class="ccs-fig-meta">' +
            '<div class="ccs-fig-meta-row"><span>Location</span><b data-fig="loc">—</b></div>' +
            '<div class="ccs-fig-meta-row"><span>Activity</span><b data-fig="act">—</b></div>' +
            '<div class="ccs-fig-meta-row" data-fig="incrow"><span>Family income</span><b data-fig="inc">—</b></div>' +
            '<div class="ccs-fig-meta-row"><span>CCS hours</span><b data-fig="ccsh">—</b></div>' +
            '<div class="ccs-fig-meta-row"><span>Withholding</span><b data-fig="wh">—</b></div>' +
            '<div class="ccs-fig-meta-row"><span>Standard CCS</span><b data-fig="std">—</b></div>' +
            '<div class="ccs-fig-meta-row"><span>Higher CCS</span><b data-fig="high">—</b></div>' +
          '</div>' +
          '<p class="ccs-panel-note" data-fig="note">Fill in the steps to see your live estimate.</p>' +
        '</div>';
    }

    function moneyOrDash(sel) { var t = txt(sel); return (t && t !== '$0.00') ? t : '—'; }

    var figuresReady = false;
    function ensureFigures() {
      if (figuresReady) { return; }
      $root.find('.ccs-panel-placeholder').remove();
      $root.find('.ccs-panel-body').append(buildFigures());
      figuresReady = true;
    }
    function hasAnyInput() {
      var loc = $.trim($('#suburb').val() || '');
      var inc = $.trim($('#family_ati').val() || '');
      var pct = subsidyPct();
      var kids = $('#children-details .child-details').length;
      var anyDay = $('#children-details .fortnight-day-btn.active').length;
      var anyFee = $('#children-details .child-fee').filter(function () { return $.trim(this.value) !== ''; }).length;
      return !!(loc || inc || (pct !== null) || kids > 1 || anyDay || anyFee || $('#summary-content').is(':visible'));
    }

    // Single progressive updater: fills what's known so far, and the full
    // pay/government figures once the summary has been calculated.
    function updatePanel() {
      if (!hasAnyInput()) { return; }
      ensureFigures();
      var $fig = $root.find('.ccs-panel-figures');
      var results = $('#summary-content').is(':visible');
      $root.toggleClass('ccs-has-results', results);
      $fig.toggleClass('is-pending', !results);
      $('#ccs-panel-email, #ccs-panel-print').prop('disabled', !results);

      var period = txt('.ccs-redesign .period-label-header') || 'Fortnightly';
      var periodLabel = 'per ' + period.toLowerCase()
        .replace('fortnightly', 'fortnight').replace('weekly', 'week')
        .replace('monthly', 'month').replace('yearly', 'year');
      $fig.find('[data-fig="pay"]').text(results ? moneyOrDash('.ccs-redesign .summary-you-total') : '—');
      $fig.find('[data-fig="govt"]').text(results ? moneyOrDash('.ccs-redesign .summary-govt-total') : '—');
      $fig.find('[data-fig="period"], [data-fig="period2"]').text(periodLabel);

      var pct = subsidyPct();
      $fig.find('[data-fig="pct"]').text(pct === null ? '—' : pct + '%');
      var kids = $('#children-details .child-details').length;
      $fig.find('[data-fig="kids"]').text(kids ? String(kids) : '—');

      var loc = $.trim($('#suburb').val() || '');
      $fig.find('[data-fig="loc"]').text(loc || '—');
      var act = $.trim($('#activity option:selected').text() || '');
      $fig.find('[data-fig="act"]').text((act && act.toLowerCase().indexOf('select') === -1) ? act : '—');
      var known = $('#know_ccs_percentage').val();
      var incRaw = parseFloat($('#family_ati').val());
      $fig.find('[data-fig="inc"]').text(isNaN(incRaw) ? '—' : '$' + incRaw.toLocaleString('en-AU'));
      // When the user knows their CCS %, family income isn't collected — hide that row.
      $fig.find('[data-fig="incrow"]').toggle(known !== 'yes');

      // Household details (mirror of the grid removed from the centre summary)
      var hoursTxt = $.trim($('#ccs_hours_display').val() || '');
      $fig.find('[data-fig="ccsh"]').text(hoursTxt || '—');
      var whV = $('#ccs_withholding_percentage').val();
      $fig.find('[data-fig="wh"]').text((whV === '' || whV == null) ? '—' : (whV + '%'));
      var stdV = parseFloat((known === 'yes') ? $('#standard_ccs_percentage').val() : $('#standard_ccs_percentage_calc').val());
      var highV = parseFloat((known === 'yes') ? $('#higher_ccs_percentage').val() : $('#higher_ccs_percentage_calc').val());
      $fig.find('[data-fig="std"]').text(isNaN(stdV) ? '—' : (stdV.toFixed(2) + '%'));
      $fig.find('[data-fig="high"]').text(isNaN(highV) ? '—' : (highV.toFixed(2) + '%'));

      $fig.find('[data-fig="note"]').text(results
        ? 'Estimate only. Final entitlement is determined by Services Australia.'
        : 'Live preview — complete all steps for your full estimate.');

      if (results) { renderComparison(); }
    }

    var summaryContent = document.getElementById('summary-content');
    if (summaryContent && window.MutationObserver) {
      new MutationObserver(updatePanel).observe(summaryContent, { attributes: true, attributeFilter: ['style', 'class'] });
    }
    var mainCard = document.getElementById('summary-main-card');
    if (mainCard && window.MutationObserver) {
      new MutationObserver(updatePanel).observe(mainCard, { subtree: true, childList: true, characterData: true });
    }
    // Progressive updates as the user enters details in any step
    $root.on('input change', '#suburb, #family_ati, #activity, #atsi, #know_ccs_percentage, #standard_ccs_percentage, #standard_ccs_percentage_calc, .child-fee', updatePanel);
    $root.on('click', '.ccs-nav-item, #next1, #next2, #next3, #back2, #back3, #ccs-add-child, .ccs-child-remove, .fortnight-day-btn', function () { setTimeout(updatePanel, 60); });

    /* ---------- Hours-per-day: slider <-> numeric input ---------- */
    // Adds a synchronized numeric input beside each child's hours slider.
    // Writes go through the ORIGINAL slider (val + trigger input/change) so the
    // existing calculation runs unchanged.
    function enhanceHours(scope) {
      $(scope).find('.child-hours-slider').each(function () {
        var $s = $(this);
        var $card = $s.closest('.child-details');
        if (!$card.length || $card.find('.ccs-hours-num').length) { return; }
        var min = parseFloat($s.attr('min')) || 9;
        var max = parseFloat($s.attr('max')) || 12;
        var step = parseFloat($s.attr('step')) || 0.5;
        var $row = $(
          '<div class="ccs-hours-manual">' +
            '<span>Or enter hours:</span>' +
            '<input type="number" class="ccs-hours-num" inputmode="decimal">' +
            '<span class="ccs-hours-unit">hrs/day</span>' +
          '</div>'
        );
        $row.find('.ccs-hours-num').attr({ min: min, max: max, step: step }).val($s.val());
        var $wrap = $s.closest('.slider-container');
        ($wrap.length ? $wrap : $s).after($row);
      });
    }
    function pairedSlider($n) { return $n.closest('.child-details').find('.child-hours-slider').first(); }
    // slider -> number
    $root.on('input', '.child-hours-slider', function () {
      $(this).closest('.child-details').find('.ccs-hours-num').val($(this).val());
    });
    // number -> slider (live while in range; clamp + snap to step on blur/change)
    $root.on('input', '.ccs-hours-num', function () {
      var v = parseFloat($(this).val());
      if (isNaN(v)) { return; }
      var $s = pairedSlider($(this));
      var min = parseFloat($s.attr('min')) || 9, max = parseFloat($s.attr('max')) || 12;
      if (v < min || v > max) { return; }
      $s.val(v).trigger('input').trigger('change');
    });
    $root.on('change', '.ccs-hours-num', function () {
      var $s = pairedSlider($(this));
      var min = parseFloat($s.attr('min')) || 9, max = parseFloat($s.attr('max')) || 12, step = parseFloat($s.attr('step')) || 0.5;
      var v = parseFloat($(this).val());
      if (isNaN(v)) { v = parseFloat($s.val()); }
      v = Math.min(max, Math.max(min, v));
      v = Math.round(v / step) * step;
      $(this).val(v);
      $s.val(v).trigger('input').trigger('change');
    });
    var kidsEl = document.getElementById('children-details');
    if (kidsEl && window.MutationObserver) {
      new MutationObserver(function () { enhanceHours(kidsEl); }).observe(kidsEl, { childList: true, subtree: true });
    }

    /* ---------- Premium Email modal ---------- */
    // Builds a modal and MOVES the existing email form into it (IDs preserved),
    // so the exact same HubSpot/custom submission runs — only the shell changes.
    function buildEmailModal() {
      if (document.getElementById('ccs-email-modal')) { return; }
      var $modal = $(
        '<div class="ccs-modal" id="ccs-email-modal" aria-hidden="true">' +
          '<div class="ccs-modal__overlay" data-ccs-close></div>' +
          '<div class="ccs-modal__dialog" role="dialog" aria-modal="true" aria-label="Email your estimate">' +
            '<div class="ccs-modal__head">' +
              '<span class="ccs-modal__title">' +
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>' +
                'Email your estimate</span>' +
              '<button type="button" class="ccs-modal__close" data-ccs-close aria-label="Close">' +
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
              '</button>' +
            '</div>' +
            '<div class="ccs-modal__body childcare-step"></div>' +
          '</div>' +
        '</div>'
      );
      $root.append($modal);
      var $content = $('#email-form-content');
      if ($content.length) { $content.show().appendTo($modal.find('.ccs-modal__body')); }
      $('#summary-email-form').addClass('ccs-hidden-inline');
    }
    function openEmailModal() {
      buildEmailModal();
      $('#ccs-email-modal').addClass('is-open').attr('aria-hidden', 'false');
      $('body').addClass('ccs-modal-open');
    }
    function closeEmailModal() {
      $('#ccs-email-modal').removeClass('is-open').attr('aria-hidden', 'true');
      $('body').removeClass('ccs-modal-open');
    }
    $root.on('click', '[data-ccs-close]', closeEmailModal);
    $(document).on('keydown.ccsv2', function (e) { if (e.key === 'Escape') { closeEmailModal(); } });

    /* ---------- Panel action buttons ---------- */
    // Print: reuse the existing client-side print/PDF flow verbatim.
    $('#ccs-panel-print').on('click', function () {
      if (this.disabled) { return; }
      $('#ccs-download-estimate').trigger('click');
    });
    // Email: open the premium modal (same underlying submission).
    $('#ccs-panel-email').on('click', function () {
      if (this.disabled) { return; }
      openEmailModal();
    });

    /* ---------- Advanced suburb search: keyboard navigation ---------- */
    // Arrow up/down to move through suggestions, Enter to select, Esc to close.
    // Selection itself still runs the original click handler, so nothing changes.
    $root.on('keydown', '#suburb', function (e) {
      var $items = $('#suburb-suggestions .suburb-suggestion');
      if (!$items.length) { return; }
      var cur = $items.index($items.filter('.is-active'));
      if (e.key === 'ArrowDown') { e.preventDefault(); cur = (cur < 0) ? 0 : Math.min($items.length - 1, cur + 1); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); cur = (cur < 0) ? 0 : Math.max(0, cur - 1); }
      else if (e.key === 'Enter') { if (cur >= 0) { e.preventDefault(); $items.eq(cur).trigger('click'); } return; }
      else if (e.key === 'Escape') { $('#suburb-suggestions').hide(); return; }
      else { return; }
      $items.removeClass('is-active');
      var el = $items.eq(cur).addClass('is-active')[0];
      if (el && el.scrollIntoView) { el.scrollIntoView({ block: 'nearest' }); }
    });
    // Mouse hover clears keyboard highlight so the two don't fight.
    $root.on('mouseenter', '.suburb-suggestion', function () {
      $('#suburb-suggestions .suburb-suggestion').removeClass('is-active');
    });

    /* ---------- Children: open any accordion card that has a validation error ---------- */
    $(document).on('click', '#next3', function () {
      setTimeout(function () {
        $('#children-details .child-details').each(function () {
          if ($(this).find('.error-message').length) {
            $(this).addClass('is-open').find('.ccs-child-head').attr('aria-expanded', 'true');
          }
        });
      }, 40);
    });

    /* ---------- Summary: move period toggle + child select into the panel ---------- */
    function relocateSummaryControls() {
      var $panel = $root.find('.ccs-summary-panel');
      if (!$panel.length || $panel.find('.ccs-panel-controls').length) { return; }
      var $controls = $('<div class="ccs-panel-controls"></div>');
      var $show = $('.show_total');
      var $sel = $('#child-select-wrapper');
      var $oldParent = $show.parent();
      if ($show.length) { $controls.append($show); }
      if ($sel.length) { $controls.append($sel); }
      if ($controls.children().length) { $panel.find('.ccs-panel-actions').after($controls); }
      // hide the now-empty container that used to hold these in the centre column
      if ($oldParent.length && !$oldParent.children().length && $.trim($oldParent.text()) === '') { $oldParent.hide(); }
    }
    // Collapsible child detail cards on the summary (default CLOSED; toggle like Full breakdown)
    $root.on('click', '.child-detail-card h5', function () {
      $(this).closest('.child-detail-card').toggleClass('is-open');
    });

    /* =====================================================================
       2025-26 vs 2026-27 comparison — a faithful, self-contained recompute
       (mirrors the calculator's own maths exactly) run for BOTH rate sets
       from the entered inputs, plus an official rule-change table.
       ===================================================================== */
    var CMP_2025 = { low: 85279, zero: 535279, higherCutoff: 367563,
      caps: { cbdc_below: 14.63, cbdc_school: 12.81, fdc: 13.56, oshc_below: 14.63, oshc_school: 12.81, ihc: 39.80 } };
    var CMP_2026 = { low: 88520, zero: 538520, higherCutoff: 370727,
      caps: { cbdc_below: 15.19, cbdc_school: 13.30, fdc: 14.08, oshc_below: 15.19, oshc_school: 13.30, ihc: 41.31 } };

    function cmpMoney(v) { v = Math.round((v || 0) * 100) / 100; return '$' + v.toLocaleString('en-AU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function cmpAge(dob) {
      if (!dob) { return 0; }
      var p = String(dob).split('-'); if (p.length !== 3) { return 0; }
      var y = +p[0], m = +p[1], d = +p[2]; if (!y || !m || !d) { return 0; }
      var b = new Date(y, m - 1, d), n = new Date(); if (b > n) { return 0; }
      var a = n.getFullYear() - b.getFullYear(), md = n.getMonth() - b.getMonth();
      if (md < 0 || (md === 0 && n.getDate() < b.getDate())) { a--; }
      return a;
    }
    function cmpCap(ct, age, r) {
      var below = age < 6;
      if (ct === 'fdc') { return r.caps.fdc; }
      if (ct === 'oshc') { return below ? r.caps.oshc_below : r.caps.oshc_school; }
      if (ct === 'ihc') { return r.caps.ihc; }
      return below ? r.caps.cbdc_below : r.caps.cbdc_school;
    }
    function cmpStd(income, r) {
      var s;
      if (income <= r.low) { s = 90; } else if (income >= r.zero) { s = 0; } else { s = 90 - (income - r.low) / 5000; }
      s = Math.max(0, Math.min(90, s));
      var i33 = r.low + (90 - 33) * 5000;
      if (income > i33) { s = Math.max(0, s); } else if (s < 33 && s > 0) { s = 33; }
      return s;
    }
    function cmpHigher(income, standard, r) {
      var ati = income, st = standard, h = st;
      if (isNaN(ati) || ati < 0) { return st; }
      if (ati >= r.higherCutoff) { h = st; }
      else if (ati <= 143273) { h = 95; }
      else if (ati < 188273) { h = 95 - ((ati - 143273) / 3000); }
      else if (ati < 267563) { h = 80; }
      else if (ati < 357563) { h = 80 - ((ati - 267563) / 3000); }
      else { h = 50; }
      h = Math.max(0, Math.min(95, h));
      if (h <= st) { h = st; }
      return h;
    }
    function cmpCompute(r) {
      var knows = $('#know_ccs_percentage').val();
      var income = parseFloat($('#family_ati').val()) || 0;
      var activity = parseFloat($('#activity').val()) || 0;
      var whv = parseFloat($('#ccs_withholding_percentage').val());
      var wh = isNaN(whv) ? 0.05 : whv / 100;
      var stdPct, highPct;
      if (knows === 'yes') {
        stdPct = (parseFloat($('#standard_ccs_percentage').val()) / 100) || 0;
        highPct = (parseFloat($('#higher_ccs_percentage').val()) / 100) || 0;
      } else {
        var s = cmpStd(income, r); stdPct = s / 100; highPct = cmpHigher(income, s, r) / 100;
      }
      var atsi = $('#atsi').val() === 'yes';
      var hpw = ((activity > 48 || atsi) ? 100 : 72) / 2;
      var totalSub = 0, totalOut = 0, idx = 0;
      $('#children-details .child-details').each(function () {
        var dob = $(this).find('.child-dob').val();
        var hrs = parseFloat($(this).find('.child-hours-slider').val()) || 0;
        var fee = parseFloat($(this).find('.child-fee').val()) || 0;
        var d1 = $(this).find('.fortnight-day-btn[data-week="1"].active').length;
        var d2 = $(this).find('.fortnight-day-btn[data-week="2"].active').length;
        var w1Fee = d1 * fee, w2Fee = d2 * fee;
        var age = cmpAge(dob);
        var pct = (idx >= 1 && age <= 5 && income < r.higherCutoff) ? highPct : stdPct;
        var hourlyFee = hrs > 0 ? fee / hrs : 0;
        var ct = $(this).find('.child-care-type').val() || 'cbdc';
        var eff = Math.min(hourlyFee, cmpCap(ct, age, r));
        var hCCS = eff * pct;
        var ent = hCCS * hpw;
        var w1bw = Math.min(d1 * hrs * hCCS, ent, w1Fee), w2bw = Math.min(d2 * hrs * hCCS, ent, w2Fee);
        var w1s = w1bw - w1bw * wh, w2s = w2bw - w2bw * wh;
        totalSub += w1s + w2s;
        totalOut += Math.max(0, w1Fee - w1s) + Math.max(0, w2Fee - w2s);
        idx++;
      });
      return { govt: totalSub, you: totalOut, std: stdPct * 100 };
    }
    function renderComparison() {
      var $wrap = $('#ccs-comparison'); if (!$wrap.length) { return; }
      if (!$('#children-details .child-details').length) { $wrap.empty(); return; }
      var a = cmpCompute(CMP_2025), b = cmpCompute(CMP_2026);
      var diff = a.you - b.you; // + => you pay less under 2026-27
      var impact;
      if (Math.abs(diff) < 0.005) { impact = '<div class="ccs-cmp-impact ccs-cmp-impact--same">Your estimate is unchanged under the 2026–27 rules.</div>'; }
      else if (diff > 0) { impact = '<div class="ccs-cmp-impact ccs-cmp-impact--good">You pay <b>' + cmpMoney(diff) + '</b> less per fortnight under the 2026–27 rules.</div>'; }
      else { impact = '<div class="ccs-cmp-impact ccs-cmp-impact--bad">You pay <b>' + cmpMoney(-diff) + '</b> more per fortnight under the 2026–27 rules.</div>'; }
      function card(res, cls, yr, badge) {
        return '<div class="ccs-cmp-card ' + cls + '">' +
          '<div class="ccs-cmp-year">' + yr + (badge ? ' <span class="ccs-cmp-badge">' + badge + '</span>' : '') + '</div>' +
          '<div class="ccs-cmp-metric ccs-cmp-metric--pay"><span>You pay / fortnight</span><b>' + cmpMoney(res.you) + '</b></div>' +
          '<div class="ccs-cmp-metric"><span>Government pays</span><b>' + cmpMoney(res.govt) + '</b></div>' +
          '<div class="ccs-cmp-metric"><span>Subsidy</span><b>' + (Math.round(res.std * 100) / 100) + '%</b></div>' +
          '</div>';
      }
      function rrow(label, o, n) {
        return '<div class="ccs-cmp-rrow"><span class="ccs-cmp-rlabel">' + label + '</span>' +
          '<span class="ccs-cmp-rold">' + o + '</span><span class="ccs-cmp-rnew' + (n !== o ? ' is-up' : '') + '">' + n + '</span></div>';
      }
      var rules = rrow('90% income threshold', '$85,279', '$88,520') +
        rrow('Zero-subsidy cutoff', '$535,279', '$538,520') +
        rrow('CBDC hourly cap (below school age)', '$14.63', '$15.19') +
        rrow('CBDC hourly cap (school age)', '$12.81', '$13.30') +
        rrow('Higher CCS income limit', '$367,563', '$370,727');
      $wrap.html('<div class="ccs-cmp">' +
        '<div class="ccs-cmp-head"><h4>2025–26 vs 2026–27</h4><p>How the new Child Care Subsidy rules affect your estimate.</p></div>' +
        '<div class="ccs-cmp-cards">' + card(a, 'ccs-cmp-card--old', '2025–26', '') +
        '<span class="ccs-cmp-arrow" aria-hidden="true">→</span>' + card(b, 'ccs-cmp-card--new', '2026–27', 'Now') + '</div>' +
        impact +
        '<div class="ccs-cmp-rules"><div class="ccs-cmp-rhead"><span>Rule change</span><span>2025–26</span><span>2026–27</span></div>' + rules + '</div>' +
        '<p class="ccs-cmp-note">Estimates based on published CCS rates (2026–27 effective 6 July 2026). Higher-CCS taper bands use current figures. Final entitlements are determined by Services Australia.</p>' +
        '</div>');
    }

    /* ---------- init ---------- */
    applyBrand();
    setTimeout(applyBrand, 400);
    enhanceHours(document);
    updatePanel();
    relocateSummaryControls();
    // Email modal is built lazily on first open (openEmailModal), by which time
    // the underlying email form has been initialised by the calculator.
  });
})();
