$(document).ready(function () {
  let _class = '';
  const classesTableWrapper = $(".classesTable").parents('.dataTables_wrapper');
  const homeworksTableWrapper = $(".homeworksTable").parents('.dataTables_wrapper');
  const $modal = $('#myModal')
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
                1: formatDate(homework.fec_out) + ' ' + formatTime(homework.hora)
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
      const data = row.data();
      const homeworkId = $(row.node()).prop('id');
      $.post(includeThisFile(), { doneHomeworksByHomeworkId: homeworkId }, res => {
        if (res.response) {
          $modal.find('.modal-title').text(data[0])
          $modal.find('.modal-body').html(`
        <div class="accordion" id="doneHomeworks">
            ${res.data.map(doneHw => `
            <div class="card">
              <div class="card-header" id="doneHw${doneHw.id}">
                  <h2 class="mb-0">
                    <button class="btn btn-link text-dark btn-block d-flex justify-content-between" type="button" data-toggle="collapse" data-target="#card${doneHw.id}" aria-expanded="false" aria-controls="card${doneHw.id}">
                        <span>${doneHw.nombre}</span>
                        <span>${doneHw.fecha} ${doneHw.hora}</span>
                    </button>
                  </h2>
              </div>

              <div id="card${doneHw.id}" class="collapse" aria-labelledby="doneHw${doneHw.id}" data-parent="#doneHomeworks">
                  <div class="card-body">
                    ${nl2br(doneHw.nota)}  
                    ${doneHw.archivos && `
                    <hr class="my-2"/>                     
                    <div class="row row-cols-4 row-cols-lg-6"> 
                      ${doneHw.archivos.map(file => {
                          return `<div class="col my-1">
                                    <a href="${file.url}" data-toggle="tooltip" title='${file.nombre}' class="btn btn-outline-dark btn-block btn-sm p-2" download="${file.nombre}">
                                        ${file.icon}
                                    </a>
                                  </div>`
                            }).join('')}
                    </div> 
                    `}           
                  </div>
              </div>
            </div>
            `).join('')}
        </div>
        `)
          $modal.modal('show')
        } else {
          alert('Ningun estudiante a entregado esta tarea')
        }


      }, 'json');



    }
  });



});