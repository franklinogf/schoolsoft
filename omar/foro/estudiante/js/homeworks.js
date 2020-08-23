$(document).ready(function () {
  const $modal = $("#myModal")
  let homeworkId = null
  let doneHomeworkId = null

  $("#myModal form").submit(function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    const files = $('[name="file[]"]')
    // append files
    files.map(input => {
      fd.append('file[]', input.files)

    })
    // send messages
    if (!doneHomeworkId) {     
      console.log("Nueva tarea");
      fd.append('doneHomework', homeworkId)
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: fd,
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
          console.log("response:",res)
          $status = $(`.homework.${homeworkId}`).find('.fa-circle')
          $status.removeClass('text-white')
            .addClass('text-success')
          animateCSS($status, 'fadeIn slow')
          $modal.modal('hide')
          // $(`.sendHomework[data-homework-id=${homeworkId}]`).prop({disabled:true,ariaDisabled:true})
        }
      });
    } else {
      console.log('editar tarea');
      fd.append('editDoneHomework', doneHomeworkId)
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: fd,
        contentType: false,
        cache: false,
        processData: false,
        success: function (res) {
          $modal.modal('hide')
        }
      });
    }


  })

  $(document).on('click', 'button.sendHomework', function (e) {
    homeworkId = $(this).data('homeworkId')
    $modal.modal('show')

    $.post(includeThisFile(), { getDoneHomeworkById: homeworkId }, res => {
      if (res.response) {
        doneHomeworkId = res.data.id
        res.files.map(file => {
          addExistingFile(fileRealName(file.nombre), file.id)
        })
        $("#note").val(res.data.nota)
      }

    },
      "json"
    );

  })

  
  $(document).on('click', 'button.delExistingFile', function (e) {
    const fileId = $(this).data('fileId');

    if (confirm("¿Seguro que quiere eliminar este archivo de la base de datos?")) {
      $.post(includeThisFile(), { delExistingFile: fileId }, () => {
        animateCSS($(this).parents('.input-group'), 'zoomOut', () => {
          $(this).parents('.input-group').remove()
        })
      })

    }
  })


  $modal.on('hidden.bs.modal', function (e) {
    doneHomeworkId = null
  })




});