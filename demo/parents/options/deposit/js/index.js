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
      trxDescription: `Deposito de ${total} a ${studentName}`
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
        }
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
    let data = {
      username: userName,
      password: password,
      trxOper: 'sale',
      accountID: $('#cuenta').val(),
      customerName: $('#ach-name').val(),
      customerEmail: $('#email').val(),
      address1: '',
      address2: '',
      city: '',
      state: '',
      zipcode: $('#ach-zip').val(),
      trxID: '',
      refNumber: '',
      trxDescription: '',
      trxAmount: total,
      bankAccount: $('#ach-number').val(),
      routing: $('#ach-route').val(),
      accType: $('#ach-type').val(),
      filler1: '',
      filler2: '',
      filler3: ''
    }
    makePayment(
      `https://${
        demo ? 'uat.' : ''
      }mmpay.evertecinc.com/WebPaymentAPI/WebPaymentAPI.svc/ProcessACH/`,
      data,
      'ACH'
    )
  })

  function makePayment(_url, _data, _paymentMethod) {
    $('.pagar').prop('disabled', true)
    alertModal.show()
    $('#alertModal .modal-body').html(`
            <div class="d-flex justify-content-center w-100">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>                            
        `)

    $.ajax({
      url: 'getNextID.php',
      type: 'GET',
      dataType: 'json',
      complete: function (data) {
        const trxID = data.responseJSON.trxID
        console.log('NextID:', trxID)
        _data.trxID = trxID
        studentId = studentId || $('#students > button').data('student-id').toString()
        _data.filler1 = studentId
        studentName = studentName || $('#students > button').children('.name').text()
        const desc = `Deposito de ${total} a ${studentName}`
        _data.trxDescription = desc.substring(0, 50)
        console.log('informacion que se envia:', _data)
        let _emailData = {
          ..._data
        }
        const dataJson = JSON.stringify(_data)

        $.ajax({
          type: 'POST',
          url: _url,
          data: dataJson,
          crossDomain: true,
          contentType: 'application/json',
          dataType: 'json',
          complete: function (data) {
            const response = data.responseJSON
            console.log({ response })

            if (response.rCode === '00' || response.rCode === '0000') {
              _emailData.refNumber = response.refNumber
              _emailData.authNumber = response.authNumber
              _emailData.paymentMethod = _paymentMethod
              _emailData.trxDatetime = response.trxDatetime
              $('#alertModal .modal-body').html(`
                                <span><p class="badge bg-success fw-bold m-0">${response.rMsg}</p></span>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>                            
                            `)
              $('#alertModal .modal-content')
                .removeClass('border-danger')
                .addClass('border-success')
              // actualizar la cantidad en la tabla despues de que el pago sea completado
              if (!demo) {
                $.post(
                  'makeDeposit.php',
                  _emailData,
                  function (data) {
                    console.log({ data })
                    const totalDeposited = data.deposited

                    $(`#students button[data-student-id=${studentId}]`)
                      .children('.badge')
                      .text(`$${totalDeposited}`)
                    // $.post("sendEmail.php", _emailData);
                  },
                  'json'
                )
              }
            } else {
              $('#alertModal .modal-body').html(`                            
                <span><p class="badge bg-danger fw-bold m-0">${response.rMsg}</p></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>   
              `)
              $('#alertModal .modal-content')
                .removeClass('border-success')
                .addClass('border-danger')
              $('.pagar').prop('disabled', false)
            }
          }
        })
      }
    })
  }
})
