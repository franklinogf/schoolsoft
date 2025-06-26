/**
 * Buttons Management System - Enhanced JavaScript
 * Modern Bootstrap 4.6 compatible script with improved UX
 */

$(document).ready(function () {
  // Initialize the page
  initializePage()

  // Event listeners
  setupEventListeners()

  // Initialize sortable
  initializeSortable()

  // Initialize modal
  initializeModal()

  // Initialize image gallery
  initializeImageGallery()

  // Initialize form validation
  initializeFormValidation()
})

/**
 * Initialize page components
 */
function initializePage() {
  // Add loading state management
  setupLoadingStates()

  // Initialize tooltips if Bootstrap tooltip is available
  if (typeof $().tooltip === 'function') {
    $('[data-toggle="tooltip"]').tooltip()
  }

  // Initialize keyboard navigation
  setupKeyboardNavigation()

  console.log('Buttons management system initialized')
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
  // Modal event listeners
  $('#Modal').on('show.bs.modal', handleModalShow)
  $('#Modal').on('hidden.bs.modal', handleModalHidden)

  // Form submission
  $('#form').on('submit', handleFormSubmit)

  // Delete button
  $('#eliminar').on('click', handleDelete)

  // Food card clicks (edit buttons)
  $(document).on('click', '.food-card', handleFoodCardClick)

  // Quick action buttons
  $('.quick-actions').on('click', 'button[data-action]', function (e) {
    e.preventDefault()
    const action = $(this).data('action')
    if (action === 'sort-alphabetically') {
      sortAlphabetically()
    } else if (action === 'sort-by-price') {
      sortByPrice()
    }
  })

  // Prevent form submission on invalid forms
  $('.needs-validation').on('submit', function (e) {
    if (!this.checkValidity()) {
      e.preventDefault()
      e.stopPropagation()
    }
    $(this).addClass('was-validated')
  })
}

/**
 * Initialize sortable functionality
 */
function initializeSortable() {
  if (typeof $.ui !== 'undefined' && $.ui.sortable) {
    $('#sortable')
      .sortable({
        placeholder: 'ui-sortable-placeholder',
        helper: 'clone',
        cursor: 'grabbing',
        tolerance: 'pointer',
        distance: 10,
        start: function (event, ui) {
          ui.item.addClass('ui-sortable-helper')
          // Remove any loading classes that might interfere
          ui.item.removeClass('loading')
          // Force full opacity
          ui.item.css('opacity', '1')
          ui.helper.css('opacity', '1')
          showSortingFeedback()
        },
        stop: function (event, ui) {
          ui.item.removeClass('ui-sortable-helper')
          // Reset any inline styles
          ui.item.css('opacity', '')
          hideSortingFeedback()
          updateOrder()
        }
      })
      .disableSelection()

    console.log('Sortable initialized')
  } else {
    console.warn('jQuery UI Sortable not available')
  }
}

/**
 * Initialize modal functionality
 */
function initializeModal() {
  // Reset modal state when opening
  $('#Modal').on('show.bs.modal', function () {
    resetModalState()
  })

  // Focus first input when modal opens
  $('#Modal').on('shown.bs.modal', function () {
    $('#title').focus()
  })
}

/**
 * Initialize image gallery
 */
function initializeImageGallery() {
  // Image selection functionality
  $('.image-option').on('click', function () {
    selectImage($(this))
  })

  // Keyboard navigation for images
  $('.image-option').on('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault()
      selectImage($(this))
    }
  })
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
  // Real-time validation
  $('#title').on('input', function () {
    validateTitle($(this))
  })

  $('#price, #price2, #discount_price').on('input', function () {
    validatePrice($(this))
  })

  // Price formatting
  $('#price, #price2, #discount_price').on('blur', function () {
    formatPrice($(this))
  })
}

/**
 * Handle modal show event
 */
function handleModalShow(e) {
  const button = $(e.relatedTarget)
  const action = button.data('action')
  const modal = $(this)

  if (action === 'add') {
    setupAddMode(modal)
  } else if (action === 'edit') {
    setupEditMode(modal, button)
  }
}

/**
 * Handle modal hidden event
 */
function handleModalHidden() {
  resetForm()
  clearValidation()
}

/**
 * Setup add mode
 */
function setupAddMode(modal) {
  modal.find('.modal-title').html(`
        <i class="fas fa-plus-circle mr-2"></i>
        Agregar Botón
    `)

  $('#eliminar').addClass('btn-hidden')
  $('#guardar').html(`
        <i class="fas fa-save mr-1"></i>
        Guardar
    `)

  // Set next order
  const nextOrder = $('.food-card').length + 1
  $('#orden').val(nextOrder)
}

/**
 * Setup edit mode
 */
function setupEditMode(modal, button) {
  const foodCard = button.closest('.food-card')
  const id = foodCard.attr('id')

  modal.find('.modal-title').html(`
        <i class="fas fa-edit mr-2"></i>
        Editar Botón
    `)

  $('#eliminar').removeClass('btn-hidden')
  $('#guardar').html(`
        <i class="fas fa-save mr-1"></i>
        Actualizar
    `)

  // Load data
  loadButtonData(id)
}

/**
 * Load button data for editing
 */
function loadButtonData(id) {
  showLoading('#form')

  $.ajax({
    url: `buscar.php?id=${id}`,
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      populateForm(data)
      hideLoading('#form')
    },
    error: function (xhr, status, error) {
      hideLoading('#form')
      showAlert('Error al cargar los datos del botón', 'danger')
      console.error('Error loading button data:', error)
    }
  })
}

/**
 * Populate form with data
 */
function populateForm(data) {
  $('#id').val(data.id)
  $('#title').val(data.articulo)
  $('#price').val(parseFloat(data.precio || 0).toFixed(2))
  $('#price2').val(parseFloat(data.precio2 || 0).toFixed(2))
  $('#discount_price').val(parseFloat(data.precio_descuento || 0).toFixed(2))
  $('#orden').val(data.orden)

  // Select image
  if (data.foto) {
    selectImageByName(data.foto)
  }
}

/**
 * Handle form submission
 */
function handleFormSubmit(e) {
  e.preventDefault()

  if (!validateForm()) {
    return false
  }

  const form = $(this)
  const formData = new FormData(form[0])

  showLoading('#guardar')

  $.ajax({
    url: form.attr('action'),
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      hideLoading('#guardar')
      handleFormSuccess(response)
    },
    error: function (xhr, status, error) {
      hideLoading('#guardar')
      showAlert('Error al guardar el botón', 'danger')
      console.error('Form submission error:', error)
    }
  })
}

/**
 * Handle successful form submission
 */
function handleFormSuccess(response) {
  showAlert('Botón guardado correctamente', 'success')
  $('#Modal').modal('hide')

  // Reload page after short delay
  setTimeout(() => {
    window.location.reload()
  }, 1000)
}

/**
 * Handle delete button click
 */
function handleDelete() {
  const id = $('#id').val()

  if (!id) {
    showAlert('No se pudo identificar el botón a eliminar', 'warning')
    return
  }

  // Confirm deletion
  if (!confirm('¿Está seguro de que desea eliminar este botón?')) {
    return
  }

  showLoading('#eliminar')

  $.ajax({
    url: 'eliminar.php',
    method: 'POST',
    data: { id: id },
    success: function (response) {
      hideLoading('#eliminar')
      showAlert('Botón eliminado correctamente', 'success')
      $('#Modal').modal('hide')

      // Reload page after short delay
      setTimeout(() => {
        window.location.reload()
      }, 1000)
    },
    error: function (xhr, status, error) {
      hideLoading('#eliminar')
      showAlert('Error al eliminar el botón', 'danger')
      console.error('Delete error:', error)
    }
  })
}

/**
 * Handle food card clicks
 */
function handleFoodCardClick(e) {
  // Don't trigger if clicking on overlay or during sorting
  if ($(e.target).closest('.food-card-overlay').length || $(this).hasClass('ui-sortable-helper')) {
    return
  }

  const card = $(this)
  card.attr('data-action', 'edit')
  $('#Modal').modal('show')
}

/**
 * Select image in gallery
 */
function selectImage(imageOption) {
  // Remove previous selection
  $('.image-option').removeClass('selected')

  // Add selection to clicked image
  imageOption.addClass('selected')

  // Get image filename
  const imageSrc = imageOption.find('img').attr('src')
  const imageName = imageSrc.substring(imageSrc.lastIndexOf('/') + 1)

  // Update hidden field
  $('#image').val(imageName)

  // Visual feedback
  showImageSelectionFeedback(imageOption)
}

/**
 * Select image by filename
 */
function selectImageByName(filename) {
  $('.image-option').each(function () {
    const img = $(this).find('img')
    const src = img.attr('src')
    if (src.includes(filename)) {
      selectImage($(this))
      return false // Break loop
    }
  })
}

/**
 * Show image selection feedback
 */
function showImageSelectionFeedback(imageOption) {
  // Animate selection
  imageOption.addClass('animate__animated animate__pulse')

  setTimeout(() => {
    imageOption.removeClass('animate__animated animate__pulse')
  }, 600)
}

/**
 * Sort buttons alphabetically
 */
function sortAlphabetically() {
  const container = $('#sortable')
  const cards = container.find('.food-card').toArray()

  cards.sort((a, b) => {
    const titleA = $(a).find('.food-title').text().toLowerCase()
    const titleB = $(b).find('.food-title').text().toLowerCase()
    return titleA.localeCompare(titleB)
  })

  // Animate and reorder
  animateSorting(cards, 'Ordenando alfabéticamente...')
}

/**
 * Sort buttons by price
 */
function sortByPrice() {
  const container = $('#sortable')
  const cards = container.find('.food-card').toArray()

  cards.sort((a, b) => {
    const priceA = parseFloat($(a).find('.price-amount').text()) || 0
    const priceB = parseFloat($(b).find('.price-amount').text()) || 0
    return priceA - priceB
  })

  // Animate and reorder
  animateSorting(cards, 'Ordenando por precio...')
}

/**
 * Animate sorting process
 */
function animateSorting(sortedCards, message) {
  const container = $('#sortable')

  // Show feedback
  showSortingFeedback(message)

  // Fade out cards
  container.find('.food-card').fadeOut(300, function () {
    // Reorder and fade in
    container.empty()

    sortedCards.forEach((card, index) => {
      $(card)
        .hide()
        .appendTo(container)
        .fadeIn(200, function () {
          //   $(this).css('opacity', 1)
        })
    })

    // Update order after animation
    setTimeout(() => {
      updateOrder()
      hideSortingFeedback()
    }, sortedCards.length * 10 + 200)
  })
}

/**
 * Update button order
 */
function updateOrder() {
  const sortedArray = $('#sortable').sortable('toArray')

  $.ajax({
    url: 'orden.php',
    method: 'POST',
    data: { ids: sortedArray },
    success: function (response) {
      console.log('Order updated successfully:', response)
    },
    error: function (xhr, status, error) {
      console.error('Error updating order:', error)
      showAlert('Error al actualizar el orden', 'warning')
    }
  })
}

/**
 * Show sorting feedback
 */
function showSortingFeedback(message = 'Reordenando...') {
  if ($('#sorting-feedback').length === 0) {
    $('body').append(`
            <div id="sorting-feedback" class="alert alert-info position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span class="message">${message}</span>
            </div>
        `)
  } else {
    $('#sorting-feedback .message').text(message)
  }
}

/**
 * Hide sorting feedback
 */
function hideSortingFeedback() {
  $('#sorting-feedback').fadeOut(300, function () {
    $(this).remove()
  })
}

/**
 * Form validation functions
 */
function validateForm() {
  let isValid = true

  // Validate title
  if (!validateTitle($('#title'))) {
    isValid = false
  }

  // Validate at least one price
  const price1 = parseFloat($('#price').val()) || 0
  const price2 = parseFloat($('#price2').val()) || 0
  const discountPrice = parseFloat($('#discount_price').val()) || 0

  if (price1 <= 0 && price2 <= 0 && discountPrice <= 0) {
    showAlert('Debe ingresar al menos un precio válido', 'warning')
    isValid = false
  }

  // Validate image selection
  if (!$('#image').val()) {
    showAlert('Debe seleccionar una imagen', 'warning')
    isValid = false
  }

  return isValid
}

/**
 * Validate title field
 */
function validateTitle(field) {
  const value = field.val().trim()
  const isValid = value.length >= 2 && value.length <= 50

  field.removeClass('is-valid is-invalid')
  field.addClass(isValid ? 'is-valid' : 'is-invalid')

  return isValid
}

/**
 * Validate price field
 */
function validatePrice(field) {
  const value = parseFloat(field.val()) || 0
  const isValid = value >= 0

  field.removeClass('is-valid is-invalid')
  if (field.val().trim() !== '') {
    field.addClass(isValid ? 'is-valid' : 'is-invalid')
  }

  return isValid
}

/**
 * Format price field
 */
function formatPrice(field) {
  const value = parseFloat(field.val()) || 0
  if (value > 0) {
    field.val(value.toFixed(2))
  }
}

/**
 * Utility functions
 */
function resetForm() {
  $('#form')[0].reset()
  $('#id, #image, #orden').val('')
  $('.image-option').removeClass('selected')
  clearValidation()
}

function clearValidation() {
  $('#form').removeClass('was-validated')
  $('.form-control').removeClass('is-valid is-invalid')
}

function resetModalState() {
  clearValidation()
  $('#eliminar').addClass('btn-hidden')
}

function setupLoadingStates() {
  // Add loading animation styles if not present
  if (!$('#loading-styles').length) {
    $('head').append(`
            <style id="loading-styles">
                .btn.loading {
                    position: relative;
                    color: transparent !important;
                }
                .btn.loading::after {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 1.2rem;
                    height: 1.2rem;
                    border: 2px solid transparent;
                    border-top: 2px solid currentColor;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
            </style>
        `)
  }
}

function setupKeyboardNavigation() {
  // Enable keyboard navigation for food cards
  $('.food-card')
    .attr('tabindex', '0')
    .on('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault()
        $(this).click()
      }
    })
}

function showLoading(selector) {
  $(selector).addClass('loading').prop('disabled', true)
}

function hideLoading(selector) {
  $(selector).removeClass('loading').prop('disabled', false)
}

function showAlert(message, type = 'info', duration = 3000) {
  // Remove existing alerts
  $('.custom-alert').remove()

  const alert = $(`
        <div class="alert alert-${type} alert-dismissible fade show custom-alert position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${getAlertIcon(type)} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `)

  $('body').append(alert)

  // Auto dismiss
  if (duration > 0) {
    setTimeout(() => {
      alert.alert('close')
    }, duration)
  }
}

function getAlertIcon(type) {
  const icons = {
    success: 'check-circle',
    danger: 'exclamation-triangle',
    warning: 'exclamation-triangle',
    info: 'info-circle'
  }
  return icons[type] || 'info-circle'
}

// Global functions for backward compatibility
window.sortAlphabetically = sortAlphabetically
window.sortByPrice = sortByPrice
