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
    $('label[for=money]').text(`Cantidad a depositar a ${studentName}`)
    $('#student_id').val(studentId)
    $('.pagar').prop('disabled', false)
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
      Alert.fire('Error', 'Debe seleccionar un estudiante para realizar el deposito', 'error')
      return false
    }
    if (!this.checkValidity()) {
      event.preventDefault()
      event.stopPropagation()
      $(this).addClass('was-validated')
      return false
    }
    $('#amount').val(total)
  })
})
