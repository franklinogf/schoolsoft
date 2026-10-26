$(function () {
  const endpoint = './includes/autopayments_data.php'
  let currentAutoPay = null

  const numberFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })

  const urlParams = new URLSearchParams(window.location.search)
  if (urlParams.get('status') === 'error') {
    Alert.fire('Error', urlParams.get('message') || 'Error', 'error')
  }

  const stateLabels = {
    pendiente: ['Borrador', 'badge-secondary'],
    activo: ['Activo', 'badge-success'],
    pausado: ['Pausado', 'badge-warning'],
    fallido: ['Fallido', 'badge-danger']
  }

  // Draws everything that depends on the auto pay's state: badge, actions,
  // which of the 3 steps are available and the confirmation summary.
  function renderCardStatus(autoPay) {
    const saved = Boolean(autoPay)
    const state = saved ? autoPay.estado : null
    const hasCard = saved && autoPay.hasCard
    const itemCount = saved && autoPay.items ? autoPay.items.length : 0
    const cardLabel = hasCard
      ? `${autoPay.cardBrand || 'Tarjeta'} ${autoPay.cardLast4 ? '****' + autoPay.cardLast4 : ''}`.trim()
      : '-'

    const [label, css] = stateLabels[state] || ['', '']
    $('#stateBadge')
      .text(label)
      .removeClass('badge-secondary badge-success badge-warning badge-danger')
      .addClass(css)
      .toggleClass('d-none', !saved)

    $('#pauseAutopay').toggleClass('d-none', state !== 'activo')
    $('#resumeAutopay').toggleClass('d-none', state !== 'pausado')

    $('#stateNotice')
      .text(
        state === 'fallido'
          ? 'El cobro fue rechazado y el pago automatico se detuvo. Registre una tarjeta nueva para reactivarlo.'
          : ''
      )
      .toggleClass('d-none', state !== 'fallido')

    // Steps unlock in order: 1 saved -> 2 card -> 3 confirm
    $('#itemsSection').toggleClass('d-none', !saved)
    $('#step2').toggleClass('locked', !saved)
    $('#step3').toggleClass('locked', !(saved && hasCard))
    $('#step2Locked').toggleClass('d-none', saved)

    $('#step1 .step-done').toggleClass('d-none', !(saved && itemCount > 0))
    $('#step2 .step-done').toggleClass('d-none', !hasCard)
    $('#step3 .step-done').toggleClass('d-none', state !== 'activo' && state !== 'pausado')

    const $status = $('#cardStatus')
    $status
      .text(hasCard ? `${cardLabel}` : 'Sin tarjeta registrada')
      .removeClass('alert-secondary alert-success alert-danger')
      .addClass(hasCard ? 'alert-success' : state === 'fallido' ? 'alert-danger' : 'alert-secondary')
      .toggleClass('d-none', !saved)

    $('#registerCard')
      .text(hasCard ? 'Cambiar tarjeta' : 'Registrar tarjeta')
      .toggleClass('btn-danger', state === 'fallido')
      .toggleClass('btn-outline-primary', state !== 'fallido')
      .toggleClass('d-none', !saved)

    $('#sumCard').text(cardLabel)
    $('#sumDay').text(saved && autoPay.diaDePago ? autoPay.diaDePago : '-')
    $('#sumItems').text(itemCount)
    $('#sumTotal').text(formatAmount(saved ? autoPay.total : 0))

    const missing = []
    if (!itemCount) missing.push('agregue al menos un cargo')
    if (!hasCard) missing.push('registre una tarjeta')
    if (saved && !autoPay.diaDePago) missing.push('indique el dia de pago')

    const canActivate = state === 'pendiente' && missing.length === 0
    $('#activateAutopay')
      .prop('disabled', !canActivate)
      .toggleClass('d-none', Boolean(state) && state !== 'pendiente')
    $('#step3Hint').text(
      state === 'pendiente' && missing.length
        ? `Para activar: ${missing.join(', ')}.`
        : state === 'activo'
          ? `Pago automatico activo. Se cobra el dia ${autoPay.diaDePago} de cada mes.`
          : state === 'pausado'
            ? 'Pago automatico en pausa. No se cobrara hasta reanudarlo.'
            : ''
    )
  }

  function changeState(action) {
    if (!currentAutoPay) {
      return
    }

    $.post(endpoint, { action, id: currentAutoPay.id }, null, 'json')
      .done((response) => {
        if (!response.success) {
          Alert.fire('Error', response.message || 'No se pudo completar la accion', 'error')
          return
        }

        setCurrentAutoPay(response.data)
        fillHeader(response.data)
        loadList(response.data.id)
      })
      .fail((xhr) =>
        Alert.fire('Error', xhr.responseJSON?.message || 'No se pudo completar la accion', 'error')
      )
  }

  $('#activateAutopay').on('click', () => changeState('activate'))
  $('#pauseAutopay').on('click', () => changeState('pause'))
  $('#resumeAutopay').on('click', () => changeState('resume'))

  $('#registerCard').on('click', function () {
    $('#registerAutoPayId').val(currentAutoPay ? currentAutoPay.id : '')
    $('#registerCardModal').modal('show')
  })

  function formatAmount(value) {
    return numberFormatter.format(parseFloat(value || 0))
  }

  const monthNames = [
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre'
  ]

  function escapeHtml(value) {
    return $('<div>').text(value ?? '').html()
  }

  function renderHistory(rows) {
    const $body = $('#historyBody')
    $body.empty()

    if (!rows || !rows.length) {
      $body.append(
        '<tr><td colspan="5" class="text-center text-muted">No hay cobros registrados</td></tr>'
      )
      return
    }

    rows.forEach((row) => {
      const month = monthNames[parseInt(row.month, 10) - 1] || row.month || ''
      const approved = /^(aprobado|success)/i.test(row.message || '')

      $body.append(`
        <tr>
          <td>${escapeHtml(row.date || '')}</td>
          <td>${escapeHtml(month)}</td>
          <td class="${approved ? 'text-success' : 'text-danger'}">${escapeHtml(row.message)}</td>
          <td>${escapeHtml(row.authNumber || '')}</td>
          <td>${escapeHtml(row.reference || '')}</td>
        </tr>
      `)
    })
  }

  function loadHistory(autoPayId) {
    if (!autoPayId) {
      renderHistory([])
      return
    }

    $.getJSON(endpoint, { action: 'history', id: autoPayId })
      .done((response) => renderHistory(response.success ? response.data : []))
      .fail(() => renderHistory([]))
  }

  function setCurrentAutoPay(autoPay) {
    const changed = (autoPay ? autoPay.id : null) !== (currentAutoPay ? currentAutoPay.id : null)
    currentAutoPay = autoPay
    if (changed) {
      loadHistory(autoPay ? autoPay.id : null)
    }
    $('#autoPayId').val(autoPay ? autoPay.id : '')
    $('#deleteAutopay').toggleClass('d-none', !autoPay)
    $('#formTitle').text(autoPay ? `Autopago #${autoPay.id}` : 'Nuevo Autopago')
    renderItems(autoPay ? autoPay.items : [])
    $('#totalAmount').text(formatAmount(autoPay ? autoPay.total : 0))
    renderCardStatus(autoPay)
  }

  function resetForm() {
    $('#email').val('')
    $('#itemStudent').val('')
    $('#itemBudget').html('<option value="">Seleccione estudiante primero</option>')
    $('#itemAmount').val('')
    $('#dayOfPayment').val('')
    renderCardStatus(null)
    setCurrentAutoPay(null)
  }

  function fillHeader(autoPay) {
    if (!autoPay) {
      resetForm()
      return
    }

    $('#email').val(autoPay.email || '')
    renderCardStatus(autoPay)
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
      const label = `${(stateLabels[autoPay.estado] || [''])[0]} | ${
        autoPay.hasCard ? `Tarjeta ${autoPay.cardLast4 ? '****' + autoPay.cardLast4 : ''}` : 'Sin tarjeta'
      }`
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
      diaDePago: $('#dayOfPayment').val()
    }

    if (!payload.diaDePago) {
      Alert.fire('Error', 'Debe indicar un dia de pago', 'error')
      return null
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

    Alert.fire({
      title: 'Eliminar autopago',
      text: 'Se eliminara la tarjeta registrada y los renglones, y dejara de cobrarse. Los pagos ya realizados no se afectan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (!result.isConfirmed) {
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
        .fail((xhr) =>
          Alert.fire(
            'Error',
            xhr.responseJSON?.message || 'No se pudo eliminar el autopago',
            'error'
          )
        )
    })
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

  renderCardStatus(null)
  loadList()
})
