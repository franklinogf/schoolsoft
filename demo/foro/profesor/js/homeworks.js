$(document).ready(function () {

  $(".delHomework").click((e) => {
    document_id = e.target.tagName === 'I' ? $(e.target).parent().data('homeworkId') : $(e.target).data('homeworkId');
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

  $(".editHomework").click((e) => {
    clearForm();

    document_id = e.target.tagName === 'I' ?
      $(e.target).parent().tooltip('hide').data('homeworkId') :
      $(e.target).tooltip('hide').data('homeworkId');

    $.ajax({
      type: "POST",
      url: includeThisFile(),
      data: { 'getHomework': document_id },
      dataType: 'json',
      success: function (res) {
        $("#title").val(res.titulo)
        $("#description").val(res.descripcion)
        $("#class").val(res.curso)
        $("#sinceDate").val(res.fec_in === '0000-00-00' ? '' : res.fec_in)
        $("#untilDate").val(res.fec_out === '0000-00-00' ? '' : res.fec_out)
        $("#radio1").prop('checked', res.enviartarea === 'si' ? true : false)
        $("#radio2").prop('checked', res.enviartarea === 'si' ? false : true)
        $("#link1").val(res.lin1)
        $("#link2").val(res.lin2)
        $("#link3").val(res.lin3)
        if (res.archivos) {
          res.archivos.forEach(file => {
            addExistingFile(file.nombre, file.id)
          });
        }

        $('html').animate({
          scrollTop: $("form").offset().top
        }, 500, () => {
          animateCSS('form', 'pulse')

        });
      }
    });

  })

  $(document).on('click', 'button.delExistingFile', e => {
    const fileId = e.target.tagName === 'I' ? $(e.target).parent().data('fileId') : $(e.target).data('fileId');
    console.log('fileId: ', fileId);
    if (confirm("¿Seguro que quiere eliminar este archivo de la base de datos?")) {
      animateCSS($(e.target).parents('.input-group'), 'zoomOut', () => {
        $(e.target).parents('.input-group').remove()
      })
      animateCSS($(".homework").find(`[data-file-id="${fileId}"]`), 'zoomOut', () => {
        const parent = $(".homework").find(`[data-file-id="${fileId}"]`).parent();
        $(".homework").find(`[data-file-id="${fileId}"]`).remove()
        parent.change()
      })

    }
  })

  $('div.btn-group-vertical').change(e => {
    if($(e.target).children().length === 0){
      $(e.target).remove();
    }
  })

  function clearForm() {
    $("input").val('')
    $("button.addFile").nextAll().remove()
  }
});