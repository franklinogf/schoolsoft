$(function () {
  const $form = $('#paymentsForm')
  const $charges = $('.chargeCheckbox')

  /* ----------------------------- Masked Inputs ----------------------------- */

  $('.justText').mask('Z', {
    translation: {
      Z: {
        pattern: /[\p{L} ]/u,
        recursive: true
      }
    }
  })

  /* --------------------------- Selection and total -------------------------- */

  function selectedCountText(count) {
    const [zero, one, many] = paymentsLang.selectedCount
    if (count === 0) return zero
    if (count === 1) return one
    return many.replace(':count', count)
  }

  // Read the raw attribute: jQuery's .data() may coerce the key to a number
  function studentOf(el) {
    return $(el).attr('data-student')
  }

  function chargesOf(student) {
    return $charges.filter((_, el) => studentOf(el) === student)
  }

  function updateSelectAll(student) {
    const $group = chargesOf(student)
    const checked = $group.filter(':checked').length
    $('.selectAll')
      .filter((_, el) => studentOf(el) === student)
      .prop('checked', $group.length > 0 && checked === $group.length)
      .prop('indeterminate', checked > 0 && checked < $group.length)
  }

  function updateTotal() {
    // Work in cents to avoid floating point rounding
    let cents = 0
    const $checked = $charges.filter(':checked')
    $checked.each(function () {
      cents += Math.round(parseFloat($(this).data('amount')) * 100)
    })
    $('.js-total').text('$' + (cents / 100).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }))
    $('.js-count').text(selectedCountText($checked.length))
    $charges.each(function () {
      $(this).closest('.charge-row').toggleClass('is-selected', this.checked)
    })
  }

  $form.on('change', '.chargeCheckbox', function () {
    updateSelectAll(studentOf(this))
    updateTotal()
  })

  $form.on('change', '.selectAll', function () {
    chargesOf(studentOf(this)).prop('checked', this.checked)
    this.indeterminate = false
    updateTotal()
  })

  // Clicking anywhere on the row toggles its checkbox
  $form.on('click', '.charge-row, .select-all-row', function (event) {
    if ($(event.target).closest('input, label, a').length) return
    $(this).find('input[type=checkbox]').trigger('click')
  })

  /* -------------------------- validate and submit -------------------------- */

  $form.on('submit', function (event) {
    if ($(this).data('submitted')) {
      event.preventDefault()
      return false
    }
    if ($charges.filter(':checked').length === 0) {
      event.preventDefault()
      Alert.fire(paymentsLang.error, paymentsLang.selectCharge, 'error')
      return false
    }
    if (!this.checkValidity()) {
      event.preventDefault()
      event.stopPropagation()
      $(this).addClass('was-validated')
      const $invalid = $(this).find(':invalid').first()
      $('html, body').animate({ scrollTop: $('#checkoutCard').offset().top - 16 }, 300, () => $invalid.trigger('focus'))
      return false
    }
    // Avoid double requests while PlacetoPay takes time to answer
    $(this).data('submitted', true)
    $('.pagar')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + paymentsLang.processing)
  })

  // Re-enable the button if the page is restored from the back/forward cache
  $(window).on('pageshow', function (event) {
    if (event.originalEvent.persisted) {
      window.location.reload()
    }
  })

  updateTotal()
})
