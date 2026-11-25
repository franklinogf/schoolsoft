$(function () {
  /* ----------------------------- Masked Inputs ----------------------------- */

  $('.justText').mask('Z', {
    translation: {
      Z: {
        pattern: /[\p{L} ]/u,
        recursive: true
      }
    }
  })
  $('.credit-amount').mask('###0.00', {
    reverse: true,
    selectOnFocus: true
  })

  /* ------------------------------- Total ------------------------------- */

  function calculateTotal() {
    let total = 0
    $('.credit-amount').each(function () {
      const value = parseFloat($(this).val())
      if (!isNaN(value) && value > 0) total += value
    })
    return total
  }

  $('.credit-amount').on('input change', function () {
    $('#total').text('$' + calculateTotal().toFixed(2))
  })

  /* -------------------------- validate and submit -------------------------- */

  $('#creditForm').submit(function (event) {
    if (calculateTotal() <= 0) {
      event.preventDefault()
      Alert.fire(creditLang.error, creditLang.amountError, 'error')
      return false
    }
    if (!this.checkValidity()) {
      event.preventDefault()
      event.stopPropagation()
      $(this).addClass('was-validated')
      return false
    }
    if ($(this).data('submitted')) {
      event.preventDefault()
      return false
    }
    // Avoid double requests while PlacetoPay takes time to answer
    $(this).data('submitted', true)
    $('.pagar')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + creditLang.processing)
  })

  // Reload if the page is restored from the back/forward cache
  $(window).on('pageshow', function (event) {
    if (event.originalEvent.persisted) {
      window.location.reload()
    }
  })
})
