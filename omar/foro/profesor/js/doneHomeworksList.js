$(document).ready(function () {
  let _class = '';
  const classesTableWrapper = $(".classesTable").parents('.dataTables_wrapper');
  const homeworksTableWrapper = $(".homeworksTable").parents('.dataTables_wrapper');
  homeworksTableWrapper.hide(0);

  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      _class = data[0];
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { 'homeworksByClass': _class },
        dataType: "json",
        success: (res) => {
         
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
              $("#header").text('Lista de tareas')
                .show('drop', { direction: "right" }, 400);
            });
          }
          else {
            alert('No existen tareas en esta clase');
          }
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
      window.open(getBaseUrl('pdf/pdfDoneHomeworks.php?id='+homeworkId),'doneHomework')
      

    }
  });



});