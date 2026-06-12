$(document).ready(function () {
  const params = new URLSearchParams(window.location.search)
  const _class = params.get('class')

  const classesTableWrapper = $('.classesTable').parents('.dataTables_wrapper')
  const homeworksTableWrapper = $('.homeworksTable').parents('.dataTables_wrapper')
  const $modal = $('#myModal')

  if (_class !== null) {
    getDoneHomeworks(_class)
  }

  function getDoneHomeworks(subjectCode) {
    $.ajax({
      type: 'POST',
      url: includeThisFile(),
      data: { homeworksByClass: subjectCode },
      dataType: 'json',
      success: (res) => {
        if (res.response === true) {
          res.data.map((homework) => {
            const thisRow = homeworksTable.row
              .add({
                0: homework.titulo,
                1: formatDate(homework.fec_out) + ' ' + formatTime(homework.hora)
              })
              .draw()

            $(thisRow.node()).prop('id', homework.id_documento)
          })
        } else {
          alert(
            __LANG === 'es'
              ? 'No existen tareas en esta clase'
              : 'There are no homeworks in this class'
          )
        }
      }
    })
  }
  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data()
      window.location.href = getBaseUrl('doneHomeworks.php?class=' + data[0])
    }
  })

  $('#back').click((e) => {
    window.location.href = getBaseUrl('doneHomeworks.php')
  })

  $('.homeworksTable tbody').on('click', 'tr', function () {
    const row = homeworksTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data()
      const homeworkId = $(row.node()).prop('id')
      $.post(
        includeThisFile(),
        { doneHomeworksByHomeworkId: homeworkId },
        (res) => {
          if (res.response) {
            $modal.find('.modal-title').text(data[0])
            $modal.find('.modal-body').html(`
        <div class="accordion" id="doneHomeworks">
            ${res.data
              .map(
                (doneHw) => `
            <div class="card">
              <div class="card-header" id="doneHw${doneHw.id}">
                  <h2 class="mb-0">
                    <button class="btn btn-link text-dark btn-block d-flex justify-content-between" type="button" data-toggle="collapse" data-target="#card${
                      doneHw.id
                    }" aria-expanded="false" aria-controls="card${doneHw.id}">
                        <span>${doneHw.nombre}</span>
                        <span>${doneHw.fecha} ${doneHw.hora}</span>
                    </button>
                  </h2>
              </div>

              <div id="card${doneHw.id}" class="collapse" aria-labelledby="doneHw${
                doneHw.id
              }" data-parent="#doneHomeworks">
                  <div class="card-body">
                    ${nl2br(doneHw.nota)}  
                    ${
                      doneHw.archivos
                        ? `
                    <hr class="my-2"/>                     
                    <div class="row row-cols-4 row-cols-lg-6"> 
                      ${doneHw.archivos
                        .map((file) => {
                          return `<div class="col my-1">
                                    <a href="${file.url}" data-toggle="tooltip" title='${file.nombre}' class="btn btn-outline-dark btn-block btn-sm p-2" download="${file.nombre}">
                                        ${file.icon}
                                    </a>
                                  </div>`
                        })
                        .join('')}
                    </div> 
                    `
                        : ''
                    }           
                  </div>
              </div>
            </div>
            `
              )
              .join('')}
        </div>
        `)
            $modal.modal('show')
          } else {
            alert(
              __LANG === 'es'
                ? 'Ningun estudiante a entregado esta tarea'
                : 'No students have done this homework'
            )
          }
        },
        'json'
      )
    }
  })
})
