$(document).ready(function () {
  $(document).on('click', '.dispatch', function (e) {
    const thisButton = $(this)
    let orderId = thisButton.data('orderId')
    // Update list of details every time a dispatch button is clicked
    const liDetails = thisButton.parents('li').children('.media-body').children('ol').children('li')
    let listOfDetailsToRemove = []
    $.each($(liDetails), function (indexInArray, valueOfElement) {
      listOfDetailsToRemove.push($(this).text())
    })
    $.each($('#listOfDetails li'), function (indexInArray, valueOfElement) {
      const detail = $(this)
      listOfDetailsToRemove.forEach((detailsToRemove) => {
        if (detailsToRemove === detail.find('span.detail').text()) {
          const amount = +detail.find('span.amount').text()
          const newAmount = amount - 1
          if (newAmount < 1) {
            detail.remove()
          } else {
            detail.find('span.amount').text(newAmount)
          }
        }
      })
    })
    thisButton.parents('li').hide(function () {
      thisButton.remove()
    })

    $.post('./includes/index.php', { orderId }, function (data, textStatus, jqXHR) {
      console.log(data)
      ordersAmount()
    })
  })

  $('#dispatchAll').click(function (e) {
    if (confirm('Esta seguro que desea despachar todos los estudiantes?')) {
      $.each($('.dispatch'), function (indexInArray, valueOfElement) {
        $(this).click()
        ordersAmount()
      })
    }
  })
  $(document).on('click', '.dispatchUp', function (e) {
    if (confirm('Esta seguro que desea despachar todos lo estudiantes para arriba?')) {
      const thisButton = $(this)
      $.each(thisButton.parents('li').prevAll('li'), function (indexInArray, valueOfElement) {
        $(this).find('.dispatch').click()
      })
      thisButton.next('.dispatch').click()
    }
  })
  const ordersAmount = function (amount = 1) {
    $('#ordersAmount').text(+$('#ordersAmount').text() - amount)
  }

  setInterval(function () {
    $.post(
      './includes/index.php',
      { updateOrders: true },
      function (data, textStatus, jqXHR) {
        const listOfDetails = data.listOfDetails
        $('#ordersList').html(data.list)
        $('#ordersAmount').text(data.amount)
        $('#listOfDetails').html('')
        for (const detail in listOfDetails) {
          $('#listOfDetails').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="detail">${detail}</span>
                        <span class="badge badge-primary badge-pill amount">${listOfDetails[detail]}</span>
                    </li>`)
        }
      },
      'json'
    )
  }, 10000)
})
