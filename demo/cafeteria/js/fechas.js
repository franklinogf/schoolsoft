$(document).ready(function () {
  // Form validation
  const form = document.querySelector('.needs-validation')
  const fecha1Input = document.getElementById('fecha1')
  const fecha2Input = document.getElementById('fecha2')
  const dateValidationMessage = document.getElementById('dateValidation')
  const generateButton = document.getElementById('generateReport')

  // Validate date range
  function validateDateRange() {
    const fecha1 = new Date(fecha1Input.value)
    const fecha2 = new Date(fecha2Input.value)

    if (fecha1Input.value && fecha2Input.value) {
      if (fecha1 > fecha2) {
        dateValidationMessage.style.display = 'block'
        fecha2Input.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio')
        return false
      } else {
        dateValidationMessage.style.display = 'none'
        fecha2Input.setCustomValidity('')
        return true
      }
    }
    return true
  }

  // Add event listeners for date validation
  fecha1Input.addEventListener('change', validateDateRange)
  fecha2Input.addEventListener('change', validateDateRange)

  // Form submission handling
  if (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault()
      event.stopPropagation()

      // Validate form
      const isValid = form.checkValidity() && validateDateRange()

      if (isValid) {
        // Add loading state to button
        generateButton.classList.add('loading')
        generateButton.disabled = true

        // Submit form after short delay for visual feedback
        setTimeout(() => {
          // Create a temporary form to submit since we prevented default
          const tempForm = document.createElement('form')
          tempForm.action = form.action
          tempForm.method = form.method
          tempForm.target = form.target

          // Copy all form data
          const formData = new FormData(form)
          for (let [key, value] of formData.entries()) {
            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = key
            input.value = value
            tempForm.appendChild(input)
          }

          document.body.appendChild(tempForm)
          tempForm.submit()
          document.body.removeChild(tempForm)

          // Remove loading state
          setTimeout(() => {
            generateButton.classList.remove('loading')
            generateButton.disabled = false
          }, 2000)
        }, 500)
      }

      form.classList.add('was-validated')
    })
  }

  // Quick action buttons
  window.setToday = function () {
    const today = new Date().toISOString().split('T')[0]
    fecha1Input.value = today
    fecha2Input.value = today

    // Add visual feedback
    animateInput(fecha1Input)
    animateInput(fecha2Input)
    validateDateRange()
  }

  window.setThisWeek = function () {
    const today = new Date()
    const firstDay = new Date(today.setDate(today.getDate() - today.getDay()))
    const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6))

    fecha1Input.value = firstDay.toISOString().split('T')[0]
    fecha2Input.value = lastDay.toISOString().split('T')[0]

    // Add visual feedback
    animateInput(fecha1Input)
    animateInput(fecha2Input)
    validateDateRange()
  }

  window.setThisMonth = function () {
    const today = new Date()
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1)
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0)

    fecha1Input.value = firstDay.toISOString().split('T')[0]
    fecha2Input.value = lastDay.toISOString().split('T')[0]

    // Add visual feedback
    animateInput(fecha1Input)
    animateInput(fecha2Input)
    validateDateRange()
  }

  // Animation helper
  function animateInput(input) {
    input.style.transform = 'scale(1.05)'
    input.style.transition = 'transform 0.2s ease'

    setTimeout(() => {
      input.style.transform = 'scale(1)'
    }, 200)
  }

  // Enhanced radio button interactions
  $('.custom-control-input[type="radio"]').change(function () {
    // Remove active class from all option cards
    $('.option-card').removeClass('active')

    // Add active class to selected option card
    $(this).closest('.option-card').addClass('active')

    // Add subtle animation
    const card = $(this).closest('.option-card')[0]
    card.style.transform = 'scale(1.02)'
    setTimeout(() => {
      card.style.transform = 'scale(1)'
    }, 150)
  })

  // Initialize form validation state
  validateDateRange()

  // Add fade-in animation to quick actions
  setTimeout(() => {
    $('.quick-actions').addClass('show')
  }, 300)

  // Tooltip initialization for better UX
  $('[data-toggle="tooltip"]').tooltip()

  // Add hover effects to form controls
  $('.form-control').hover(
    function () {
      $(this).addClass('shadow-sm')
    },
    function () {
      $(this).removeClass('shadow-sm')
    }
  )

  // Keyboard shortcuts
  $(document).keydown(function (e) {
    // Ctrl/Cmd + Enter to submit form
    if ((e.ctrlKey || e.metaKey) && e.keyCode === 13) {
      e.preventDefault()
      if (form && form.checkValidity() && validateDateRange()) {
        $(generateButton).click()
      }
    }

    // Escape to go back
    if (e.keyCode === 27) {
      const backButton = document.querySelector('a[href="./menu.php"]')
      if (backButton && confirm('¿Deseas regresar al menú principal?')) {
        window.location.href = backButton.href
      }
    }
  })

  // Add success feedback when dates are valid
  function showSuccessFeedback() {
    if (fecha1Input.value && fecha2Input.value && validateDateRange()) {
      // Show subtle success indicator
      const successIcon = '<i class="fas fa-check-circle text-success ml-1"></i>'
      if (!$('.date-range-section .text-success').length) {
        $('.section-title:first').append(successIcon)
        setTimeout(() => {
          $('.date-range-section .fa-check-circle').fadeOut()
        }, 2000)
      }
    }
  }

  // Update validation on input
  fecha1Input.addEventListener('input', showSuccessFeedback)
  fecha2Input.addEventListener('input', showSuccessFeedback)
})

// Set minimum date to prevent past dates (optional)
// Uncomment if you want to restrict date selection
/*
$(document).ready(function() {
  const today = new Date().toISOString().split('T')[0]
  $('#fecha1').attr('min', today)
  $('#fecha2').attr('min', today)
})
*/
