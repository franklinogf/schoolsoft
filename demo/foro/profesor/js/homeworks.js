$(document).ready(function () {



  $(".editHomework").click((e) => {
    clearForm();

    document_id = e.target.tagName === 'I' ?
      $(e.target).parent().tooltip('hide').data('homeworkId') :
      $(e.target).tooltip('hide').data('homeworkId');

    $.post(includeThisFile(), { getHomework: document_id }, res => {

      $("#homeworkFormBtn").prop('name', 'editHomework');
      $("form").prepend(`<input type="hidden" name="document_id" id="homework_id" value="${document_id}"/>`)
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
          addExistingFile(fileRealName(file.nombre), file.id)
        });
      }
      // scroll the view to the form when the edit homework button is pressed
      $('html').animate({
        scrollTop: $("form").offset().top
      }, 500, () => {
        animateCSS('form', 'pulse')
      });
    },
      "json"
    );


  })

  $(".delHomework").click((e) => {
    //  check if the fontawasome icon was click instead of the button    
    document_id = e.target.tagName === 'I' ? $(e.target).parent().data('homeworkId') : $(e.target).data('homeworkId');
    if (confirm("¿Esta seguro de que desea borrar esta tarea?")) {

      $.post(includeThisFile(), { delHomework: document_id }, () => {
        const homeworkCard = $(e.target).parents('.homework')
        animateCSS(homeworkCard, 'zoomOutDown', () => {
          animateCSS(homeworkCard.nextAll(),'slideInUp')
          if (e.target.tagName === 'I') {
            $(e.target).parent().tooltip('hide')
          } else {
            $(e.target).tooltip('hide')
          }
          homeworkCard.remove();
        })
      })

    }
  })

  $(document).on('click', 'button.delExistingFile', e => {
    const fileId = e.target.tagName === 'I' ? $(e.target).parent().data('fileId') : $(e.target).data('fileId');

    if (confirm("¿Seguro que quiere eliminar este archivo de la base de datos?")) {
      $.post(includeThisFile(), { delExistingFile: fileId }, () => {
        animateCSS($(e.target).parents('.input-group'), 'zoomOut', () => {
          $(e.target).parents('.input-group').remove()
        })
        animateCSS($(".homework").find(`[data-file-id="${fileId}"]`), 'zoomOut', () => {
          // get the parent of the file before removing it from the DOM
          const parent = $(".homework").find(`[data-file-id="${fileId}"]`).parent();
          $(".homework").find(`[data-file-id="${fileId}"]`).remove()
          parent.change()
        })
      })

    }
  })

  $('div.btn-group-vertical').change(e => {
    if ($(e.target).children().length === 0) {
      $(e.target).remove();
    } else {
      // rename the files
      const files = $(e.target).children();
      files.map((i, file) => {
        file.text = `Archivo ${i + 1}`
      });
    }
  })

  function clearForm() {
    $("#document_id").remove();
    $("#homeworkFormBtn").prop('name', 'addHomework');
    $("button.addFile").nextAll().remove()
    $('input').val('');
    $('textarea').val('');
    $('input[type=radio],input[type=checkbox]').prop('checked', false);
  }
});