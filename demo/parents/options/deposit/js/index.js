$(function () {
  let total = 0
  let studentName = ''
  const minAmount = +$('#minAmount').val()

  /* ----------------------------- Masked Inputs ----------------------------- */

  $('.justText').mask('Z', {
    translation: {
      Z: {
        pattern: /[A-Za-z ]/,
        recursive: true
      }
    }
  })
  $('#money').mask('###0.00', {
    reverse: true,
    selectOnFocus: true
  })

  /* ----------------- select the student to deposit the money ---------------- */

  $('#students button').click(function (event) {
    const studentId = $(this).data('student-id')
    studentName = $(this).children('.name').text()
    $('#students button').removeClass('active')
    $('#students button .name').removeClass('text-white')
    $(this).addClass('active')
    $(this).children('.name').addClass('text-white')
    $('label[for=money]').text(depositLang.amountTo.replace(':name', studentName))
    $('#student_id').val(studentId)
    $('#students').closest('.card').removeClass('border-danger')
  })

  $('#money').change(function (event) {
    if ($(this).val().length > 0) {
      total = parseFloat($(this).val()).toFixed(2)
      if (total < minAmount) {
        $(this).addClass('is-invalid')
        $(this).val('')
      } else {
        $(this).val(total)
        $(this).removeClass('is-invalid')
      }
    }
  })

  /* -------------------------- validate and submit -------------------------- */

  $('#depositForm').submit(function (event) {
    if (!$('#student_id').val()) {
      event.preventDefault()
      $('#students').closest('.card').addClass('border-danger')
      Alert.fire(depositLang.error, depositLang.selectStudent, 'error')
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
    $('#amount').val(total)
    // Avoid double requests while PlacetoPay takes time to answer
    $(this).data('submitted', true)
    $('.pagar')
      .prop('disabled', true)
      .html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' + depositLang.processing)
  })

  // Re-enable the button if the page is restored from the back/forward cache
  $(window).on('pageshow', function (event) {
    if (event.originalEvent.persisted) {
      window.location.reload()
    }
  })
})
