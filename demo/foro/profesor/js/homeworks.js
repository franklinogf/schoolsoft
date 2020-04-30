$(document).ready(function () {

  $(".delHomework").click((e) => {  

    if (confirm("¿Esta seguro de que desea borrar esta tarea?")) {
      animateCSS($(e.target).parents('.homework'), 'zoomOutDown', () => {
        if (e.target.tagName === 'I') {
          $(e.target).parent().tooltip('hide')
        } else {
          $(e.target).tooltip('hide')
        }
        $(e.target).parents('.homework').remove();
      })
    }
  })



});