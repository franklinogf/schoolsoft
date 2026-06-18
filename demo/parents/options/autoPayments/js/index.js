$(function () {
  const endpoint = './includes/autopayments_data.php'
  let currentAutoPay = null

  const numberFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })

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

  function formatAmount(value) {
    return numberFormatter.format(parseFloat(value || 0))
  }

  function setCurrentAutoPay(autoPay) {
    currentAutoPay = autoPay
    $('#autoPayId').val(autoPay ? autoPay.id : '')
    $('#deleteAutopay').toggleClass('d-none', !autoPay)
    $('#formTitle').text(autoPay ? `Autopago #${autoPay.id}` : 'Nuevo Autopago')
    renderItems(autoPay ? autoPay.items : [])
    $('#totalAmount').text(formatAmount(autoPay ? autoPay.total : 0))
  }

  function activePaymentType() {
    return $('#cardMethod').hasClass('active') ? 'tarjeta' : 'ach'
  }

  function resetForm() {
    $('#email').val('')
    $('#cc-name,#cc-number,#cc-expiration,#cc-cvv,#cc-zip').val('')
    $('#ach-name,#ach-type,#ach-number,#ach-route,#ach-zip').val('')
    $('#itemStudent').val('')
    $('#itemBudget').html('<option value="">Seleccione estudiante primero</option>')
    $('#itemAmount').val('')
    $('#dayOfPayment').val('')
    $('#cardMethod-tab').tab('show')
    setCurrentAutoPay(null)
  }

  function fillHeader(autoPay) {
    if (!autoPay) {
      resetForm()
      return
    }

    $('#email').val(autoPay.email || '')

    if (autoPay.tipoDePago === 'ach') {
      $('#achMethod-tab').tab('show')
    } else {
      $('#cardMethod-tab').tab('show')
    }

    $('#cc-name').val(autoPay.ccNombre || '')
    $('#cc-number').val(autoPay.ccNumero || '')
    $('#cc-expiration').val(autoPay.fechaExpiracion || '')
    $('#cc-cvv').val(autoPay.cvv || '')
    $('#cc-zip').val(autoPay.ccZip || '')

    $('#ach-name').val(autoPay.achNombre || '')
    $('#ach-type').val(autoPay.tipoCuenta || '')
    $('#ach-number').val(autoPay.achNumero || '')
    $('#ach-route').val(autoPay.numeroRuta || '')
    $('#ach-zip').val(autoPay.achZip || '')

    $('#dayOfPayment').val(autoPay.diaDePago || '')
  }

  function renderList(list) {
    const $list = $('#autopayList')
    $list.empty()

    if (!list.length) {
      $list.append('<div class="list-group-item text-muted text-center">No hay autopagos</div>')
      return
    }

    list.forEach((autoPay) => {
      const isActive = currentAutoPay && currentAutoPay.id === autoPay.id
      const label = `${autoPay.tipoDePago === 'ach' ? 'ACH' : 'Tarjeta'}`
      const itemCount = autoPay.items ? autoPay.items.length : 0
      const button = `
        <button type="button" class="list-group-item list-group-item-action autopay-item ${isActive ? 'active' : ''}" data-id="${autoPay.id}">
          <div class="d-flex justify-content-between align-items-center">
            <strong>#${autoPay.id}</strong>
            <span>$${formatAmount(autoPay.total)}</span>
          </div>
          <small>${label} | ${itemCount} renglones</small>
        </button>
      `
      $list.append(button)
    })
  }

  function renderItems(items) {
    const $body = $('#itemsBody')
    $body.empty()

    if (!items || !items.length) {
      $body.append(
        '<tr><td colspan="5" class="text-center text-muted">No hay renglones guardados</td></tr>'
      )
      return
    }

    items.forEach((item) => {
      $body.append(`
        <tr>
          <td>${item.studentName || ''}</td>
          <td>${item.budgetCode}</td>
          <td>${item.budgetDescription || ''}</td>
          <td class="text-right">$${formatAmount(item.amount)}</td>
          <td class="text-right">
            <button type="button" class="btn btn-sm btn-outline-danger delete-item" data-item-id="${item.id}">Eliminar</button>
          </td>
        </tr>
      `)
    })
  }

  function loadCodesByStudent() {
    const studentId = parseInt($('#itemStudent').val(), 10)

    $('#itemAmount').val('')

    if (!studentId) {
      $('#itemBudget').html('<option value="">Seleccione estudiante primero</option>')
      return
    }

    $.getJSON(endpoint, {
      action: 'studentCodes',
      studentId
    })
      .done((response) => {
        if (!response.success) {
          $('#itemBudget').html('<option value="">No hay codigos</option>')
          return
        }

        const codes = response.data || []
        if (!codes.length) {
          $('#itemBudget').html('<option value="">No hay codigos pendientes</option>')
          return
        }

        const options = ['<option value=""></option>']
        codes.forEach((item) => {
          options.push(`<option value="${item.code}">${item.description} (${item.code})</option>`)
        })

        $('#itemBudget').html(options.join(''))
      })
      .fail(() => {
        $('#itemBudget').html('<option value="">No hay codigos</option>')
      })
  }

  function loadAmountFromPayments() {
    const studentId = parseInt($('#itemStudent').val(), 10)
    const budgetCode = parseInt($('#itemBudget').val(), 10)

    if (!studentId || !budgetCode) {
      $('#itemAmount').val('')
      return
    }

    $.getJSON(endpoint, {
      action: 'paymentAmount',
      studentId,
      budgetCode
    })
      .done((response) => {
        if (!response.success) {
          $('#itemAmount').val('')
          return
        }

        const amount = parseFloat(response.data?.amount)
        if (Number.isNaN(amount) || amount <= 0) {
          $('#itemAmount').val('')
          return
        }

        $('#itemAmount').val(amount.toFixed(2))
      })
      .fail(() => {
        $('#itemAmount').val('')
      })
  }

  $('#itemStudent').on('change', loadCodesByStudent)
  $('#itemBudget').on('change', loadAmountFromPayments)

  function loadList(selectId = null) {
    return $.getJSON(endpoint, { action: 'list' }).done((response) => {
      if (!response.success) {
        Alert.fire('Error', response.message || 'No se pudo cargar la lista', 'error')
        return
      }

      const list = response.data || []
      if (selectId) {
        const found = list.find((item) => item.id === selectId)
        if (found) {
          setCurrentAutoPay(found)
          fillHeader(found)
        }
      } else if (currentAutoPay) {
        const found = list.find((item) => item.id === currentAutoPay.id)
        if (found) {
          setCurrentAutoPay(found)
          fillHeader(found)
        }
      }

      renderList(list)
    })
  }

  function loadDetail(id) {
    return $.getJSON(endpoint, { action: 'detail', id }).done((response) => {
      if (!response.success) {
        Alert.fire('Error', response.message || 'No se pudo cargar el autopago', 'error')
        return
      }

      setCurrentAutoPay(response.data)
      fillHeader(response.data)
      loadList(response.data.id)
    })
  }

  function validateHeader() {
    if (!$('#email').val()) {
      Alert.fire('Error', 'Debe ingresar email', 'error')
      return null
    }

    const payload = {
      action: 'saveHeader',
      id: $('#autoPayId').val(),
      email: $('#email').val(),
      tipoDePago: activePaymentType(),
      formaDePago: 'automatico',
      diaDePago: $('#dayOfPayment').val()
    }

    if (!payload.diaDePago) {
      Alert.fire('Error', 'Debe indicar un dia de pago', 'error')
      return null
    }

    if (payload.tipoDePago === 'tarjeta') {
      payload.ccNombre = $('#cc-name').val()
      payload.ccNumero = $('#cc-number').val()
      payload.fechaExpiracion = $('#cc-expiration').val()
      payload.ccv = $('#cc-cvv').val()
      payload.ccZip = $('#cc-zip').val()

      if (
        !payload.ccNombre ||
        !payload.ccNumero ||
        !payload.fechaExpiracion ||
        !payload.ccv ||
        !payload.ccZip
      ) {
        Alert.fire('Error', 'Complete todos los campos de tarjeta', 'error')
        return null
      }
    } else {
      payload.achNombre = $('#ach-name').val()
      payload.achNumero = $('#ach-number').val()
      payload.numeroRuta = $('#ach-route').val()
      payload.tipoCuenta = $('#ach-type').val()
      payload.achZip = $('#ach-zip').val()

      if (
        !payload.achNombre ||
        !payload.achNumero ||
        !payload.numeroRuta ||
        !payload.tipoCuenta ||
        !payload.achZip
      ) {
        Alert.fire('Error', 'Complete todos los campos de ACH', 'error')
        return null
      }
    }

    return payload
  }

  $('#newAutopay').on('click', function () {
    resetForm()
    loadList()
  })

  $('#saveHeader').on('click', function () {
    const payload = validateHeader()
    if (!payload) {
      return
    }

    $.post(endpoint, payload, null, 'json')
      .done((response) => {
        if (!response.success) {
          Alert.fire('Error', response.message || 'No se pudo guardar', 'error')
          return
        }

        setCurrentAutoPay(response.data)
        fillHeader(response.data)
        loadList(response.data.id)
        Alert.fire('Exito', 'Autopago guardado', 'success')
      })
      .fail(() => Alert.fire('Error', 'No se pudo guardar el autopago', 'error'))
  })

  $('#autopayList').on('click', '.autopay-item', function () {
    const id = parseInt($(this).data('id'), 10)
    if (!id) {
      return
    }

    loadDetail(id)
  })

  $('#deleteAutopay').on('click', function () {
    if (!currentAutoPay) {
      return
    }

    $.post(endpoint, { action: 'deleteAutopay', id: currentAutoPay.id }, null, 'json')
      .done((response) => {
        if (!response.success) {
          Alert.fire('Error', response.message || 'No se pudo eliminar', 'error')
          return
        }

        resetForm()
        loadList()
        Alert.fire('Exito', 'Autopago eliminado', 'success')
      })
      .fail(() => Alert.fire('Error', 'No se pudo eliminar el autopago', 'error'))
  })

  $('#addItem').on('click', function () {
    if (!currentAutoPay) {
      Alert.fire('Error', 'Guarde primero el autopago', 'error')
      return
    }

    const studentId = parseInt($('#itemStudent').val(), 10)
    const budgetCode = parseInt($('#itemBudget').val(), 10)
    const amount = parseFloat($('#itemAmount').val())

    if (!studentId || !budgetCode || !amount || amount <= 0) {
      Alert.fire('Error', 'Seleccione estudiante y codigo con balance en pagos', 'error')
      return
    }

    $.post(
      endpoint,
      {
        action: 'saveItem',
        autoPayId: currentAutoPay.id,
        studentId,
        budgetCode,
        amount
      },
      null,
      'json'
    )
      .done((response) => {
        if (!response.success) {
          Alert.fire('Error', response.message || 'No se pudo guardar el cargo', 'error')
          return
        }

        setCurrentAutoPay(response.data)
        fillHeader(response.data)
        loadList(response.data.id)
        $('#itemAmount').val('')
      })
      .fail(() => Alert.fire('Error', 'No se pudo guardar el cargo', 'error'))
  })

  $('#itemsBody').on('click', '.delete-item', function () {
    if (!currentAutoPay) {
      return
    }

    const itemId = parseInt($(this).data('item-id'), 10)
    if (!itemId) {
      return
    }

    $.post(endpoint, { action: 'deleteItem', autoPayId: currentAutoPay.id, itemId }, null, 'json')
      .done((response) => {
        if (!response.success) {
          Alert.fire('Error', response.message || 'No se pudo eliminar el renglon', 'error')
          return
        }

        loadDetail(currentAutoPay.id)
      })
      .fail(() => Alert.fire('Error', 'No se pudo eliminar el renglon', 'error'))
  })

  loadList()
})
