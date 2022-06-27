$(document).ready(function () {
  let _class = '';
  const classesTableWrapper = $(".classesTable").parents('.table_wrap');
  const homeworksTableWrapper = $(".homeworksTable").parents('.table_wrap');
  homeworksTableWrapper.hide(0);

  $('.classesTable tbody').on('click', 'tr', function () {
    $('.classesTable tbody tr').addClass('disabled')
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      _class = data[0];
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { 'homeworksByClass': _class },
        dataType: "json",
        complete: (res) => {
          res = res.responseJSON
          if (res.response === true) {
            res.data.map(homework => {
              const thisRow = homeworksTable.row.add({
                0: homework.titulo,
                1: formatDate(homework.fec_out)
              }).draw();

              $(thisRow.node()).prop('id', homework.id_documento)

            })

            classesTableWrapper.hide('drop', { direction: "left" }, 400, () => {
              homeworksTableWrapper.show('drop', { direction: "right" }, 400);
            });
            $("#header").hide('drop', { direction: "left" }, 400, () => {
              $("#header").text(__LANG === 'es' ? 'Lista de tareas': 'Homeworks list')
                .show('drop', { direction: "right" }, 400);
            });
          }
          else {
            alert(__LANG === 'es' ? 'No existen tareas en esta clase': 'There are no homeworks in this class');
          }
          $('.classesTable tbody tr').removeClass('disabled')
        }
      });


    }
  });

  $("#back").click((e) => {
    homeworksTableWrapper.hide('drop', { direction: "right" }, 400, () => {
      homeworksTable.rows().remove();
      classesTableWrapper.show('drop', { direction: "left" }, 400);
    });
    $("#header").hide('drop', { direction: "right" }, 400, () => {
      $("#header").text('Mis Cursos')
        .show('drop', { direction: "left" }, 400);
    });
  })

  $('.homeworksTable tbody').on('click', 'tr', function () {
    const row = homeworksTable.row(this)
    if (row.index() !== undefined) {
      // const data = row.data();
      const homeworkId = $(row.node()).prop('id');
      openWindowWithPost(getBaseUrl('pdf/pdfDoneHomeworks.php'), { id: homeworkId })
    }
  });



});