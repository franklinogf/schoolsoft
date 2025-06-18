$(function () {
  let studentId = null
  let total = 0
  let studentName = ''
  let minAmount = +$('#minAmount').val()

  /* ----------------------------- Masked Inputs ----------------------------- */

  $('#cc-expiration').mask('00/00', { placeholder: 'MM/YY' })
  $('#cc-cvv').mask('0009', { placeholder: '123' })
  $('.justText').mask('Z', {
    translation: {
      Z: {
        pattern: /[A-Za-z ]/,
        recursive: true
      }
    }
  })
  $('.justNumber').mask('0#')
  $('#cc-number').mask('0000 0000 0000 0000')
  $('.zip').mask('00000', { placeholder: '12345' })
  $('#money').mask('###0.00', {
    reverse: true,
    selectOnFocus: true
  })

  /* ----------------- reset forms when payment method changes ---------------- */
  const tabs = document.querySelectorAll('button[data-bs-toggle="pill"]')
  tabs.forEach((tab) => {
    tab.addEventListener('shown.bs.tab', function (event) {
      $('form').each(function (index) {
        $('form')[index].reset()
      })
    })
  })

  /* ----------------- select the student to deposit the money ---------------- */

  $('#students button').click(function (event) {
    studentId = $(this).data('student-id')
    studentName = $(this).children('.name').text()
    $('#students button').removeClass('active')
    $('#students button .name').removeClass('text-white')
    $(this).addClass('active')
    $(this).children('.name').addClass('text-white')
    $('label[for=money]').text(`Cantidad a depositar a ${studentName}`)
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

  /* -------------- Reset form after closing a the complete alert ------------- */
  $('#alertModal').on('hide.bs.modal', function (event) {
    if ($('#alertModal .modal-content').hasClass('border-success')) {
      $('form')[0].reset()
      $('form').removeClass('was-validated')
      $('#students button').removeClass('active')
      $('#students button .name').removeClass('text-white')
      $('label[for=money]').text(`Cantidad a depositar`)
    }
    $('#alertModal .modal-content').removeClass('border-success border-danger')
  })

  // pay button
  $('#pagar').click(function (event) {
    if ($('#email').val().length > 0 && $('#money').val().length > 0) {
      $('#email,#money').removeClass('is-invalid')
      if ($('#cardMethod').hasClass('active')) {
        $('#cardForm').submit()
      } else {
        $('#achForm').submit()
      }
    } else {
      if ($('#email').val().length === 0) {
        $('#email').addClass('is-invalid')
      }
      if ($('#money').val().length === 0) {
        $('#money').addClass('is-invalid')
      }
    }
  })

  /* ------------------------ validate form and submit ------------------------ */

  $('#cardForm').submit(function (event) {
    event.preventDefault()
    if (studentId === null) {
      Alert.fire('Error', 'Debe seleccionar un estudiante para realizar el deposito', 'error')
    }
    if (!$(this)[0].checkValidity()) {
      event.stopPropagation()
      $(this).addClass('was-validated')
      return false
    }
    const data = {
      accountID: $('#cuenta').val(),
      customerName: $('#cc-name').val(),
      customerEmail: $('#email').val(),
      zipcode: $('#cc-zip').val(),
      trxAmount: total,
      cardNumber: $('#cc-number').cleanVal(),
      expDate: $('#cc-expiration').cleanVal(),
      cvv: $('#cc-cvv').cleanVal(),
      trxDescription: `Deposito de ${total} a ${studentName}`,
      filler1: studentId
    }

    $.ajax({
      url: './includes/cardDeposit.php',
      type: 'POST',
      data: JSON.stringify(data),
      contentType: 'application/json',
      dataType: 'json',
      success: function (response) {
        console.log({ response })
        if (!response.success) {
          Alert.fire('Error', response.rMsg, 'error')
          return
        }
        $(`#students button[data-student-id=${studentId}]`)
          .children('.badge')
          .text(`$${response.newDepositAmount}`)
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error('There has been a problem with your ajax operation:', textStatus, errorThrown)
      }
    })
  })

  $('#achForm').submit(function (event) {
    event.preventDefault()
    if (!$(this)[0].checkValidity()) {
      event.stopPropagation()
      $(this).addClass('was-validated')
      return false
    }
    const data = {
      accountID: $('#cuenta').val(),
      customerName: $('#ach-name').val(),
      customerEmail: $('#email').val(),
      zipcode: $('#ach-zip').val(),
      trxDescription: `Deposito de ${total} a ${studentName}`,
      trxAmount: total,
      bankAccount: $('#ach-number').val(),
      routing: $('#ach-route').val(),
      accType: $('#ach-type').val(),
      filler1: studentId
    }

    $.ajax({
      url: './includes/achDeposit.php',
      type: 'POST',
      data: JSON.stringify(data),
      contentType: 'application/json',
      dataType: 'json',
      success: function (response) {
        console.log({ response })
        if (!response.success) {
          Alert.fire('Error', response.rMsg, 'error')
          return
        }
        $(`#students button[data-student-id=${studentId}]`)
          .children('.badge')
          .text(`$${response.newDepositAmount}`)
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error('There has been a problem with your ajax operation:', textStatus, errorThrown)
      }
    })
  })
})
