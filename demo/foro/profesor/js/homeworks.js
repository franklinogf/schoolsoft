$(document).ready(function () {

  $(".delHomework").click((e) => {
    document_id = e.target.tagName === 'I' ? $(e.target).parent().prop('id') : $(e.target).prop('id');
    if (confirm("¿Esta seguro de que desea borrar esta tarea?")) {
    $.ajax({
      type: "POST",
      url: includeThisFile(),
      data: { 'delHomework': document_id },
      success: function (response) {
        animateCSS($(e.target).parents('.homework'), 'zoomOutDown', () => {
          if (e.target.tagName === 'I') {
            $(e.target).parent().tooltip('hide')
          } else {
            $(e.target).tooltip('hide')
          }
          $(e.target).parents('.homework').remove();
        })
      }
    });
    }
  })



});