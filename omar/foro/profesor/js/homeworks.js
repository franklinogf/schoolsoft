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

      // $.ajax({
      //   type: "POST",
      //   url: getBaseUrl('includes/classes.php'),
      //   data: { 'studentsByClass': _class },
      //   dataType: "json",
      //   success: (res) => {
      //     if (res.response === true) {    
                    
      //       res.data.map(student => {
      //         const thisRow = homeworksTable.row.add({
      //           0: `${student.apellidos} ${student.nombre}`,
      //           1: student.usuario
      //         }).draw();

      //         $(thisRow.node()).prop('id', student.mt)

      //       })

      //       classesTableWrapper.hide('drop', { direction: "left" }, 400, () => {
      //         homeworksTableWrapper.show('drop', { direction: "right" }, 400);
      //         $("#header").animate({ opacity: 0}, 250,  () => {
      //           $("#header").text('Lista de estudiantes').animate({opacity: 1}, 250);
      //         });
      //       });
      //     }
      //     else {
      //       alert('No existen tareas en esta clase');
      //     }
      //   }
      // });


    }
  });

  $("#back").click((e) => {
    homeworksTableWrapper.hide('drop', { direction: "right" }, 400, () => {
      homeworksTable.rows().remove();
      classesTableWrapper.show('drop', { direction: "left" }, 400);
      $("#header").animate({ opacity: 0}, 250,  () => {
        $("#header").text('Mis Cursos').animate({opacity: 1}, 250);
      });
    });
  })

  $('.homeworksTable tbody').on('click', 'tr', function () {
    const row = homeworksTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();

    }
  });



});