$(document).ready(function () {
  $(document).on('click', '.dispatch', function (e) {
    const thisButton = $(this)
    let orderId = thisButton.data('orderId')

    // Add removing animation
    const orderCard = thisButton.closest('.order-card')
    orderCard.addClass('removing')

    // Update list of details every time a dispatch button is clicked
    const liDetails = thisButton.closest('.order-card').find('.order-list .order-item')
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
            detail.fadeOut(300, function () {
              detail.remove()
              checkEmptyStates()
            })
          } else {
            detail.find('span.amount').text(newAmount)
          }
        }
      })
    })

    // Hide the order card with animation
    orderCard.fadeOut(500, function () {
      orderCard.remove()
      ordersAmount()
      checkEmptyStates()
    })

    $.post('./includes/index.php', { orderId }, function (data, textStatus, jqXHR) {
      console.log(data)
    })
  })

  $('#dispatchAll').click(function (e) {
    if (confirm('¿Está seguro que desea despachar todos los estudiantes?')) {
      const dispatchButtons = $('.dispatch')
      let dispatched = 0

      dispatchButtons.each(function (index) {
        const button = $(this)
        setTimeout(() => {
          button.click()
          dispatched++
          if (dispatched === dispatchButtons.length) {
            setTimeout(() => {
              ordersAmount(dispatchButtons.length)
              checkEmptyStates()
            }, 500)
          }
        }, index * 200) // Stagger the animations
      })
    }
  })

  $(document).on('click', '.dispatchUp', function (e) {
    if (confirm('¿Está seguro que desea despachar todos los estudiantes para arriba?')) {
      const thisButton = $(this)
      const currentCard = thisButton.closest('.order-card')
      const cardsAbove = currentCard.prevAll('.order-card')

      let dispatched = 0
      const totalToDispatch = cardsAbove.length + 1

      // Dispatch all cards above
      cardsAbove.each(function (index) {
        const card = $(this)
        setTimeout(() => {
          card.find('.dispatch').click()
          dispatched++
          if (dispatched === totalToDispatch) {
            ordersAmount(totalToDispatch)
          }
        }, index * 200)
      })

      // Dispatch current card last
      setTimeout(() => {
        thisButton.siblings('.dispatch').click()
        dispatched++
      }, cardsAbove.length * 200)
    }
  })

  // Manual refresh button handler
  $('#refreshOrders').click(function (e) {
    e.preventDefault()
    const button = $(this)
    const icon = button.find('i')

    // Add spinning animation to icon
    icon.addClass('fa-spin')
    button.prop('disabled', true)

    // Force update
    updateOrders()

    // Remove animation after a short delay
    setTimeout(() => {
      icon.removeClass('fa-spin')
      button.prop('disabled', false)
    }, 1000)
  })

  const ordersAmount = function (amount = 1) {
    const currentAmount = +$('#ordersAmount').text()
    const newAmount = Math.max(0, currentAmount - amount)
    $('#ordersAmount').text(newAmount)

    // Add pulse animation to the badge
    $('#ordersAmount').addClass('pulse-animation')
    setTimeout(() => {
      $('#ordersAmount').removeClass('pulse-animation')
    }, 1000)
  }

  // Check for empty states and show appropriate messages
  const checkEmptyStates = function () {
    // Check orders list
    if ($('.order-card').length === 0) {
      if ($('.empty-state').length === 0) {
        $('#ordersList').html(`
          <div class="empty-state text-center py-5">
            <div class="empty-icon mb-3">
              <i class="fas fa-clipboard-check text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="text-muted">No hay órdenes pendientes</h4>
            <p class="text-muted">Todas las órdenes han sido despachadas</p>
          </div>
        `)
      }
    }

    // Check summary list
    if ($('#listOfDetails li').length === 0) {
      if ($('.empty-summary').length === 0) {
        $('#listOfDetails').parent().html(`
          <div class="empty-summary text-center py-4">
            <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
            <p class="text-muted mb-0">No hay artículos en espera</p>
          </div>
        `)
      }
    }
  }

  // Periodically fetch and update orders and summary without reloading the page
  let isUpdating = false
  const DEBUG_UPDATES = false // Set to true to see comparison details in console

  const updateOrders = function () {
    if (isUpdating) return // Prevent overlapping requests

    isUpdating = true

    // Add subtle loading indicator
    $('#ordersAmount').addClass('pulse-animation')

    $.post(
      './includes/index.php',
      { updateOrders: true },
      function (data, textStatus, jqXHR) {
        const listOfDetails = data.listOfDetails
        const ordersList = data.list
        const amount = data.amount

        // Store current scroll position
        const scrollPosition = $(window).scrollTop()

        // Update orders list with new structure
        const currentOrdersHtml = $('#ordersList').html()

        // Normalize HTML strings for comparison (remove extra whitespace)
        const normalizeHtml = (html) => {
          return html ? html.replace(/\s+/g, ' ').trim() : ''
        }

        const currentNormalized = normalizeHtml(currentOrdersHtml)
        const newNormalized = normalizeHtml(ordersList)

        if (DEBUG_UPDATES) {
          console.log('Orders comparison:', {
            currentLength: currentNormalized.length,
            newLength: newNormalized.length,
            areEqual: currentNormalized === newNormalized,
            current: currentNormalized.substring(0, 100) + '...',
            new: newNormalized.substring(0, 100) + '...'
          })
        }

        // Only update if content has actually changed
        if (currentNormalized !== newNormalized) {
          // Check if we're going from content to empty or vice versa
          const isCurrentlyEmpty = $('.order-card').length === 0 || $('.empty-state').length > 0
          const isNewEmpty = !ordersList || ordersList.trim() === ''

          if (DEBUG_UPDATES) {
            console.log('Updating orders list:', { isCurrentlyEmpty, isNewEmpty })
          }

          if (isCurrentlyEmpty || isNewEmpty) {
            // Direct update for empty state transitions
            $('#ordersList').html(ordersList)
            $(window).scrollTop(scrollPosition)
          } else {
            // Smooth transition for content updates
            $('#ordersList').fadeOut(200, function () {
              $(this).html(ordersList).fadeIn(200)
              // Restore scroll position
              $(window).scrollTop(scrollPosition)
            })
          }
        } else if (DEBUG_UPDATES) {
          console.log('Orders content unchanged, skipping update')
        }

        // Update order count
        $('#ordersAmount').text(amount)

        // Update summary list
        const summaryContainer = $('#listOfDetails').parent()

        // Check if we have details to show
        if (listOfDetails && Object.keys(listOfDetails).length > 0) {
          // Rebuild the summary list
          let summaryHtml = '<ul id="listOfDetails" class="list-group list-group-flush">'
          for (const detail in listOfDetails) {
            summaryHtml += `
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="detail font-weight-medium">${detail}</span>
                <span class="badge badge-info badge-pill amount">${listOfDetails[detail]}</span>
              </li>
            `
          }
          summaryHtml += '</ul>'

          // Compare normalized HTML to avoid unnecessary updates
          const currentSummaryHtml = summaryContainer.html()
          const currentSummaryNormalized = normalizeHtml(currentSummaryHtml)
          const newSummaryNormalized = normalizeHtml(summaryHtml)

          if (currentSummaryNormalized !== newSummaryNormalized) {
            summaryContainer.fadeOut(200, function () {
              $(this).html(summaryHtml).fadeIn(200)
            })
          }
        } else {
          // Show empty state for summary
          const emptyStateHtml = `
            <div class="empty-summary text-center py-4">
              <i class="fas fa-inbox text-muted mb-2" style="font-size: 2rem;"></i>
              <p class="text-muted mb-0">No hay artículos en espera</p>
            </div>
          `

          if (!summaryContainer.find('.empty-summary').length) {
            summaryContainer.fadeOut(200, function () {
              $(this).html(emptyStateHtml).fadeIn(200)
            })
          }
        }

        // Check if orders list is empty
        if (!ordersList || ordersList.trim() === '') {
          const emptyOrdersHtml = `
            <div class="empty-state text-center py-5">
              <div class="empty-icon mb-3">
                <i class="fas fa-clipboard-check text-muted" style="font-size: 4rem;"></i>
              </div>
              <h4 class="text-muted">No hay órdenes pendientes</h4>
              <p class="text-muted">Todas las órdenes han sido despachadas</p>
            </div>
          `

          if (!$('#ordersList').find('.empty-state').length) {
            $('#ordersList').fadeOut(200, function () {
              $(this).html(emptyOrdersHtml).fadeIn(200)
            })
          }
        }

        // Remove loading indicator and update timestamp
        setTimeout(() => {
          $('#ordersAmount').removeClass('pulse-animation')

          // Update last refresh time
          const now = new Date()
          const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
          })
          $('#lastUpdate').text(`Actualizado: ${timeString}`).fadeIn()

          isUpdating = false
        }, 500)
      },
      'json'
    ).fail(function () {
      // Handle error gracefully
      console.log('Error updating orders')
      isUpdating = false
      $('#ordersAmount').removeClass('pulse-animation')
    })
  }

  // Set up periodic updates
  setInterval(updateOrders, 10000)

  // Also update when page becomes visible again (user returns to tab)
  document.addEventListener('visibilitychange', function () {
    if (!document.hidden) {
      setTimeout(updateOrders, 1000) // Small delay when returning to tab
    }
  })

  // Initialize empty states check on page load
  checkEmptyStates()

  // Set initial timestamp
  const now = new Date()
  const timeString = now.toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
  $('#lastUpdate').text(`Cargado: ${timeString}`)
})
