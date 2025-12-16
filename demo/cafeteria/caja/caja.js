$(document).ready(function () {
  $('.selectpicker').selectpicker()

  $('#sendReceipts').click(function (event) {
    event.preventDefault()
    console.log($('#date').val())
    $.ajax({
      type: 'POST',
      url: 'recibo.php',
      data: { date: $('#date').val() },
      dataType: 'json',
      // contentType: false,
      // cache: false,
      // processData: false,
      xhr: function () {
        var xhr = $.ajaxSettings.xhr()
        xhr.upload.onprogress = function (e) {
          // For uploads
          if (e.lengthComputable) {
            let progress = Math.round((e.loaded / e.total) * 100)
            console.log('progress:', progress)
            $('#progressModal .progress-bar')
              .prop('aria-valuenow', progress)
              .css('width', progress + '%')
              .text(progress + '%')
          }
        }
        return xhr
      },
      beforeSend: function () {
        $('#progressModal').modal('show')
      },
      success: function (res) {
        console.log('completed:', res)
      },
      fail: function (res) {
        console.log('error:', res)
      },
      complete: function () {
        $('#progressModal').modal('hide')
      }
    })
  })

  //  tabs show
  //Edit items
  $('#editSearchModal').on('focusin', '#editItems .form-control', function (event) {
    if ($(this).val().length > 0) {
      $(this).data('price', parseFloat($(this).val()).toFixed(2))
    }
  })
  $('#editSearchModal').on('change', '#editItems .form-control', function (event) {
    if ($(this).val().length > 0) {
      $(this).val(parseFloat($(this).val()).toFixed(2))
      let totalPrice = 0
      $('#editItems .form-control').each((index) => {
        totalPrice += parseFloat($('#editItems .form-control')[index].value)
      })
      $('#editTotal').val(parseFloat(totalPrice).toFixed(2))
    } else {
      $(this).val($(this).data('price'))
    }
  })
  // Barcode
  $('#searchModal').keydown(function (event) {
    // event.preventDefault();
    if ($(this).hasClass('show')) {
      $('#searchBarcode').focus()
    }
  })

  // search modal
  $('#searchModal').on('show.bs.modal', function (event) {
    setTimeout(function () {
      $('#searchBarcode').focus()
    }, 500)
  })

  $('#searchModal a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
    clearSearchModal()
  })

  $('#searchModal').on('hide.bs.modal', function (event) {
    clearSearchModal()
  })
  function clearSearchModal() {
    $('#alert').text('')
    $('#searchBarcode').val('')
    $('#searchId').val('')
    $('#payments tbody').text('')
    $('#payments').addClass('d-none')
    $('#searchEstu').val('').trigger('change')
    $('#searchSs').val('')
  }

  // search Barcode
  $('#searchBarcode').change(function (event) {
    const code = $(this).val()
    const date = $('#searchDate').val()
    if (code !== '') {
      searchPayment({
        code,
        date
      })
    }
  })

  // edit modal
  $('#editSearchModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget)
    const total = button.data('total')
    const id = button.data('id')
    const modal = $(this)
    $.ajax({
      type: 'POST',
      url: 'searchItems.php',
      data: { id },
      dataType: 'json',
      complete: function (response) {
        const purchaseItems = response.responseJSON
        $('#editItems').text('')
        purchaseItems.data.forEach((item) => {
          $('#editItems').append(`<li class="list-group list-group-flush">
              <div class="form-row">
                <label class="col-6 align-middle" for="${item.id}">${item.descripcion}</label>
                <div class="input-group input-group-sm col-3">
                <input type="text" class="form-control" data-removed="false" id="${
                  item.id
                }" value="${item.precio_final ? item.precio_final : item.precio}" />
                <div class="input-group-append">
                      <button class="btn btn-outline-danger removeItem" type="button"><i class="fa fa-trash"></i></button>
                </div>
              </div>
              </div>
          </li>`)
        })
        modal.find('#editTotal').val(total)
        modal.find('#editBefore').val(total)
        modal.find('#editId').val(id)
      }
    })
  })

  $('#editSearchModal').on('click', '.removeItem', function (event) {
    event.preventDefault()
    const btn = $(this)
    const input = btn.parent().prev()
    let removed = input.data('removed') === undefined ? false : input.data('removed')
    removed = !removed
    input.prop('disabled', removed)
    input.data('removed', removed)
    updateTotalPrice(input)
  })

  function updateTotalPrice(input) {
    let totalPrice = 0
    input.val(parseFloat(input.val()).toFixed(2))
    $('#editItems .form-control').each((index) => {
      if ($($('#editItems .form-control')[index]).data('removed') !== true) {
        totalPrice += parseFloat($('#editItems .form-control')[index].value)
      }
    })
    $('#editTotal').val(parseFloat(totalPrice).toFixed(2))
  }

  $('#editSearchModal').on('change', '#editItems .form-control', function (event) {
    if ($(this).val().length > 0) {
      updateTotalPrice($(this))
    } else {
      $(this).val($(this).data('price'))
    }
  })

  $('#editSearchModal .btn-secondary').click(function (event) {
    $('#editSearchModal').modal('hide')
  })
  // delete modal
  $('#delSearchModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget)
    const id = button.data('id')
    const total = button.data('total')
    const modal = $(this)
    modal.find('#delId').val(id)
    modal.find('#delTotal').val(total)
    modal.find('.modal-body p').html(`Desea eliminar la compra con el id <b>#${id}</b>?`)
  })

  $('#delSearchModal .btn-secondary').click(function (event) {
    $('#delSearchModal').modal('hide')
  })

  $('#delBtn').click(function (event) {
    $('#editTotal').removeClass('is-invalid')
    $.ajax({
      type: 'POST',
      url: 'editPayment.php',
      data: {
        del: true,
        total: parseFloat($('#delTotal').val()),
        id: $('#delId').val(),
        ss: $('#searchEstu').val()
      },
      complete: function (response) {
        console.log('response:', response)
        $(`#payments tbody tr#${$('#delId').val()}`).remove()
        $('#delSearchModal').modal('hide')
        if ($('#payments tbody tr').length === 0) {
          $('#payments').addClass('d-none')
        }
      }
    })
  })
  $('#editBtn').click(function (event) {
    if ($('#editTotal').val().length > 0) {
      $('#editTotal').removeClass('is-invalid')
      let items = []

      $('#editItems .form-control').each((index) => {
        const item = $($('#editItems .form-control')[index])
        items.push({
          id: item.prop('id'),
          price: item.val(),
          removed: item.data('removed')
        })
      })

      $.ajax({
        type: 'POST',
        url: 'editPayment.php',
        data: {
          total: $('#editTotal').val(),
          beforeTotal: $('#editBefore').val(),
          id: $('#editId').val(),
          ss: $('#searchEstu').val() || $('#searchSs').val(),
          items
        },
        dataType: 'json',
        success: function (response) {
          console.log('response:', response)

          $(`#payments tbody tr#${$('#editId').val()}`)
            .find('td:nth-child(2)')
            .text(parseFloat(response.total).toFixed(2))
          $(`i[data-id=${$('#editId').val()}]`).data('total', parseFloat(response.total).toFixed(2))
          $('#editSearchModal').modal('hide')
        },
        error: function (err) {
          console.error('error:', err)
          $('#editTotal').addClass('is-invalid')
        }
      })
    } else {
      $('#editTotal').addClass('is-invalid')
    }
  })

  // Search Payment
  $('#searchBtn').click(function (evet) {
    const ss = $('#searchEstu').val()
    const date = $('#searchDate').val()
    if (ss !== '') {
      $('#searchEstu').removeClass('is-invalid')
      searchPayment({
        ss,
        date
      })
    }
  })
  $('#searchIdBtn').click(function (evet) {
    const id = $('#searchId').val()
    if (id !== '') {
      $('#searchEstu').removeClass('is-invalid')
      searchPayment({
        id
      })
    }
  })

  $('#searchId').keypress(function (event) {
    var keycode = event.keyCode ? event.keyCode : event.which
    if (keycode == '13') {
      event.preventDefault()
      $('#searchIdBtn').click()
    }
  })
  // search function
  function searchPayment(data) {
    $.ajax({
      type: 'POST',
      url: 'searchPuchase.php',
      data: data,
      dataType: 'json',
      complete: function (response) {
        const payments = response.responseJSON
        console.log('payments:', payments)
        if (!payments?.exist) {
          if (Object.keys(data)[0] === 'code') {
            $('#alert').html(`
              <div class="alert alert-warning" role="alert">
                No existe un estudiante con este codigo.
              </div>                  
              `)
          } else if (Object.keys(data)[0] === 'id') {
            $('#alert').html(`
            <div class="alert alert-warning" role="alert">
            No existe un pago con este ID.
            </div>                  
            `)
          }
          return
        }
        if (Object.keys(data)[0] === 'id') {
          $('#searchSs').val(payments.data[0].ss)
        }
        if (payments?.data.length > 0) {
          $('#payments tbody').text('')
          payments.data.forEach((payment) => {
            $('#payments').removeClass('d-none')
            $('#payments tbody').append(`<tr id="${payment.id}">
                <td>${payment.id}</td>
                <td>${payment.total}</td>
                <td>
                <i data-toggle="modal" data-target="#editSearchModal" data-total="${payment.total}" data-id="${payment.id}" class="far fa-edit text-info" role="button"></i>
                <i data-toggle="modal" data-target="#delSearchModal" data-total="${payment.total}" data-id="${payment.id}" class="far fa-trash-alt text-danger"  role="button"></i>
                </td>
              </tr>`)
            $('#alert').text('')
          })
        } else {
          $('#payments').addClass('d-none')
          $('#payments tbody').text('')
          if (Object.keys(data)[0] !== 'code' && Object.keys(data)[0] !== 'id') {
            $('#alert').html(`
            <div class="alert alert-warning" role="alert">
              Este estudiante no ha realizado ninguna compra.
            </div>                  
            `)
          }
        }
      }
    })
  }

  // disable btn when clicked
  $('#pagarForm').submit(function (event) {
    // event.preventDefault()
    $('#btnPagar')
      .html(
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Pagando...`
      )
      .prop('disabled', true)
    // $.ajax({
    //   type: "POST",
    //   url: "pagar.php",
    //   data: $("form").serialize()
    // });
    // $("#btnPagar").text("Pagar")
    // $("#pagarModal").modal('hide')
    // $('li.pric').remove()
    // Total();
  })

  $('#checkCredit').change(function (event) {
    if ($(this).prop('checked')) {
      $("input[name='tdp2']").prop({
        required: false,
        disabled: true,
        checked: false
      })
      const total = parseFloat($('#cantidadEfectivo').val())
      const additional = total >= 100 ? 2 : 1
      $('#cantidadEfectivo').val((total + additional).toFixed(2))
      $('#creditoAdicional').removeClass('d-none')
      $('#creditoAdicionalText').text(`$${additional.toFixed(2)} adicional por pagar con crédito`)
      $('#creditoAdicionalValue').val(additional.toFixed(2))
    }
  })
  $('#checkCredit2').change(function (event) {
    if ($(this).prop('checked')) {
      $("input[name='tdp2']").prop({
        required: true,
        disabled: false,
        checked: false
      })
      $('#cantidadEfectivo').val((parseFloat($('#cantidadEfectivo').val()) - 1).toFixed(2))
      $('#creditoAdicional').addClass('d-none')
      $('#creditoAdicionalText').text('')
      $('#creditoAdicionalValue').val('0')
    }
  })
  //buscar el deposito en el metodo 3
  $('#estudiante').keypress(function (e) {
    if (e.keyCode == '13') {
      e.preventDefault()
      $('#estudiante').change()
    }
  })
  $('#estudiante').change(function (event) {
    event.stopPropagation()
    if ($(this).val() !== '') {
      $.ajax({
        type: 'POST',
        url: 'buscar.php',
        data: {
          estudiante: $(this).val()
        },
        dataType: 'json',
        success: function ($data) {
          if ($data !== null) {
            if ($data.rec1 !== '') {
              alert($data.rec1)
            }
            $('#btnPagar').prop('disabled', false)
            if (parseFloat($data.cantidad) > 0) {
              $('#credito').addClass('d-none')
              $('#checkCredit').prop('checked', false)
            } else {
              $('#credito').removeClass('d-none')
              $('#checkCredit').prop('checked', false)
            }
            $('#cantidadDeposito').val($data.cantidad)
            $('#nombre_estudiante').text($data.nombre + ' ' + $data.apellidos)

            if ($data.tipo !== null) {
              $.get(`../../picture/${$data.tipo}.jpg`)
                .done(function () {
                  $('#profilePicture')
                    .prop('src', `../../picture/${$data.tipo}.jpg`)
                    .removeClass('d-none')
                  $('#studentProfile').removeClass('d-none')
                })
                .fail(function () {
                  $('#profilePicture').prop('src', '#').addClass('d-none')
                  $('#studentProfile').addClass('d-none')
                })
            } else {
              $('#profilePicture').prop('src', '#').addClass('d-none')
              $('#studentProfile').addClass('d-none')
            }

            $('#cbarra').val($('#estudiante').val())
            $('#estudiante').val('')

            if (parseFloat($('#cantidadPagar').val()) > parseFloat($data.cantidad)) {
              if (parseFloat($data.cantidad) < 0) {
                $('#cantidadEfectivo').val(parseFloat($('#cantidadPagar').val()).toFixed(2))
              } else {
                $('#cantidadEfectivo').val(
                  parseFloat($('#cantidadPagar').val() - parseFloat($data.cantidad)).toFixed(2)
                )
              }
              $('#credito').removeClass('d-none')
              $('#checkCredit').prop('checked', false)
            } else {
              $('#cantidadEfectivo').val('0.00')
              $('#credito').addClass('d-none')
              $('#checkCredit').prop('checked', false)
            }

            $('#btnPagar').focus()
          } else {
            $('#btnPagar').prop('disabled', true)
            $('#profilePicture').prop('src', '#').addClass('d-none')
            $('#studentProfile').addClass('d-none')
            $('#cantidadDeposito').val('')
            $('#nombre_estudiante').text('No se ha encontrado un estudiante')
            $('#estudiante').focus()
          }
        }
      })
    }
  })

  //buscar el deposito en el metodo 4
  $('#buscarEstu').click(function (event) {
    event.preventDefault()
    if ($('#estu').val() != '') {
      $('#btnPagar').prop('disabled', false)
      $.post(
        'buscar.php',
        {
          estu: $('#estu').val()
        },
        function (data, textStatus, xhr) {
          console.log(data)
          if (data.rec1 !== '') {
            alert(data.rec1)
          }
          $('#cantidadDeposito').val(data.cantidad)
          $('#profilePicture').prop('src', data.tipo).removeClass('d-none')
          $('#studentProfile').removeClass('d-none')

          if (parseFloat($('#cantidadPagar').val()) > parseFloat(data.cantidad)) {
            if (parseFloat(data.cantidad) < 0) {
              $('#cantidadEfectivo').val(parseFloat($('#cantidadPagar').val()).toFixed(2))
            } else {
              $('#cantidadEfectivo').val(
                parseFloat($('#cantidadPagar').val() - parseFloat(data.cantidad)).toFixed(2)
              )
            }
            $("input[name='tdp2']").prop({
              required: true,
              disabled: false,
              checked: false
            })
            $('#credito').removeClass('d-none')
            $('#checkCredit').prop('checked', false)
          } else {
            $("input[name='tdp2']").prop({
              required: false,
              disabled: true,
              checked: false
            })
            $('#cantidadEfectivo').val('0.00')
            $('#credito').addClass('d-none')
            $('#checkCredit').prop('checked', false)
          }
        },
        'json'
      ).fail(function (err) {
        console.error('error', err)
      })
      $('#btnPagar').focus()
    }
  })

  //Cambiar metodo de pago

  $('.metodo').change(function () {
    $('#profilePicture').addClass('d-none').prop('src', '#')
    if ($('.metodo:checked').prop('id') === 'metodo3') {
      $('#btnPagar').prop('disabled', true)
      $('#IDestu').val('').hide()
      $('#IDestudiante')
        .val('')
        .show('fast', function (event) {
          $('#estudiante').focus()
        })

      $('#cantidadDeposito').val('')
      $('#cantidadEfectivo').val('')
      $('#nombre_estudiante').text('')
      $('#cbarra').val('')
      $('.deposito').show('fast')
    } else if ($('.metodo:checked').prop('id') === 'metodo4') {
      $('#btnPagar').prop('disabled', true)

      $('#cantidadDeposito').val('')
      $('#cantidadEfectivo').val('')
      $('#nombre_estudiante').text('')
      $('#cbarra').val('')
      $('.deposito').show('fast')

      $('#IDestu').show('fast')
      $('#IDestudiante').val('').hide('fast')
    } else {
      $('#btnPagar').prop('disabled', false)
      $('#credito').addClass('d-none')
      $('#checkCredit').prop('checked', false)
      $('#cantidadDeposito').val('')
      $('#cantidadEfectivo').val('')
      $('#nombre_estudiante').text('')
      $('#cbarra').val('')

      $('.deposito').hide('fast')

      $('#IDestudiante,#IDestu').hide()
      $('#IDestudiante,#IDestu').val('')
    }
  })

  $('#pagarModal').on('hide.bs.modal', function (event) {
    $('input[name=tdp2]').prop('checked', false)
    $('#estu').val('').trigger('change')
    $('#estudiante').val('')
    $('#profilePicture').addClass('d-none').prop('src', '#')
    $('#studentProfile').addClass('d-none')
  })
  $('#pagarModal').on('shown.bs.modal', function (event) {
    var modal = $(this)

    // var button = $(event.relatedTarget);
    var $price = $('#total').text()
    var $price = $price.substring(1, $price.lenght)
    modal.find('#cantidadPagar').val($price)
    $('#IDestudiante').hide(0)
    $('#estudiante').val('')
    $('#IDestu').val('').hide(0)
    $('#credito').addClass('d-none')
    $('#checkCredit').prop('checked', false)
    $('.deposito').hide(0)
    $('#cantidadDeposito').val('')
    $('#profilePicture').prop('src', '#').addClass('d-none')
    $('#studentProfile').addClass('d-none')
    $('#cantidadEfectivo').val('')
    $('#cbarra').val('')
    $('#nombre_estudiante').text('')
    $('#metodo3').click()
    $('.metodo').change()
  })

  // Barcode
  $(document).keydown(function (event) {
    // event.preventDefault();
    if (!$('.modal').hasClass('show')) {
      $('#barcode').focus()
    }
  })

  $('#barcode').change(function (event) {
    if ($(this).val() != '') {
      $.post(
        'buscar.php',
        {
          code: $(this).val()
        },
        function (data, textStatus, xhr) {
          var $data = jQuery.parseJSON(data)
          $('#cart').prepend(
            '<li class="list-group-item d-flex justify-content-between lh-condensed pric">' +
              '<div>' +
              '<input type="hidden" name="barcode[]" value="' +
              $data.cbarra +
              '">' +
              '<h6 class="title">' +
              $data.articulo +
              '</h6>' +
              '</div>' +
              '<span class="price text-muted">$' +
              $data.precio +
              '</span>' +
              '</li>'
          )

          $('#barcode').val('')
          Total()
        }
      )
    }
  })

  //total del carrito
  function Total() {
    var $price = 0
    var $cant = 0
    $('#cart li.pric').each(function () {
      $price = $price + parseFloat($(this).find('.price').text().substring(1))
      $cant++
    })
    $('#total').text('$' + parseFloat($price).toFixed(2))
    $('#cant').text($cant)

    if ($('#cant').text() == '0') {
      $('#pagar').addClass('disabled')
    } else {
      $('#pagar').removeClass('disabled')
    }
  }

  //pasar a lista del carrito

  $('.food-card').click(function () {
    var $title = $(this).find('.card-title').text()
    var $priceElement = $(this).find('.price span')
    var $price = $priceElement.length
      ? $priceElement.text().replace('$', '')
      : $(this).find('.price').text()
    var $id = $(this).find('.id').val()
    $('#cart').prepend(
      '<li class="list-group-item d-flex justify-content-between lh-condensed pric">' +
        '<div>' +
        '<input type="hidden" name="id[]" value="' +
        $id +
        '">' +
        '<h6 class="title">' +
        $title +
        '</h6>' +
        '</div>' +
        '<span class="price text-muted">$' +
        $price +
        '</span>' +
        '</li>'
    )

    Total()
  })

  $('#cart').on('click', 'li.pric', function () {
    $(this).hide('fast', function () {
      $(this).remove()
      Total()
    })
  })
})
