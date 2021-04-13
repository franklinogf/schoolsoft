$(document).ready(function () {
  let _class = '';
  const classesTableWrapper = $(".classesTable").parents('.dataTables_wrapper');
  const virtualClassesTableWrapper = $(".virtualClassesTable").parents('.dataTables_wrapper');
  virtualClassesTableWrapper.hide(0);

  $('.classesTable tbody').on('click', 'tr', function () {
    $('.classesTable tbody tr').addClass('disabled')
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      _class = data[0];
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { 'virtualByClass': _class },
        dataType: "json",
        complete: (res) => {
          res = res.responseJSON
          if (res.response === true) {
            res.data.map(virtual => {
              const thisRow = virtualClassesTable.row.add({
                0: virtual.titulo,
                1: formatDate(virtual.fecha),
                2: formatTime(virtual.hora),
              }).draw();

              $(thisRow.node()).prop('id', virtual.id)

            })

            classesTableWrapper.hide('drop', { direction: "left" }, 400, () => {
              virtualClassesTableWrapper.show('drop', { direction: "right" }, 400);
            });
            $("#header").hide('drop', { direction: "left" }, 400, () => {
              $("#header").text('Lista de clases virtuales')
                .show('drop', { direction: "right" }, 400);
            });
          }
          else {
            alert('No existen clases virtuales con este clase');
          }
          $('.classesTable tbody tr').removeClass('disabled')
        }
      });


    }
  });

  $("#back").click((e) => {
    virtualClassesTableWrapper.hide('drop', { direction: "right" }, 400, () => {
      virtualClassesTable.rows().remove();
      classesTableWrapper.show('drop', { direction: "left" }, 400);
    });
    $("#header").hide('drop', { direction: "right" }, 400, () => {
      $("#header").text('Mis Cursos')
        .show('drop', { direction: "left" }, 400);
    });
  })

  $('.virtualClassesTable tbody').on('click', 'tr', function () {
    const row = virtualClassesTable.row(this)
    if (row.index() !== undefined) {
      const virtualId = $(row.node()).prop('id');
      console.log(virtualId)
      openWindowWithPost(getBaseUrl('pdf/pdfVirtual.php'), { id: virtualId })
    }
  });



});