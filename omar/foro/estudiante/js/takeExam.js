$(document).ready(function () {

  $("form").submit(function (e) {
    if ($(this)[0].checkValidity() === false) {
      // e.preventDefault()
      // e.stopPropagation()
      $('.alert').removeClass('invisible')
      animateCSS( $('.alert'),'zoomIn')
    }

  })



});