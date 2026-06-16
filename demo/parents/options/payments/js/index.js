$(function () {
  function updateTotal() {
    let total = 0
    $('.chargeCheckbox:checked').each(function () {
      total += parseFloat($(this).data('amount'))
    })
    $('#totalSelected').text(total.toFixed(2))
    $('#payButton').prop('disabled', total === 0)
  }

  $('.chargeCheckbox').on('change', updateTotal)
})
