$(document).ready(function () {
  const params = new URLSearchParams(window.location.search)
  const _class = params.get('class')

  const classesTableWrapper = $('.classesTable').parents('.table_wrap')
  const studentsTableWrapper = $('.studentsTable').parents('.table_wrap')

  if (_class !== null) {
    getStudents(_class)
  }

  function getStudents(subjectCode) {
    $.ajax({
      type: 'POST',
      url: includeThisFile(),
      data: { studentsByClass: _class },
      dataType: 'json',
      success: (res) => {
        if (res.response === true) {
          res.data.map((student) => {
            const thisRow = studentsTable.row
              .add({
                0: `${student.apellidos} ${student.nombre}`,
                1: student.usuario
              })
              .draw()

            $(thisRow.node()).prop('id', student.mt)
          })
        } else {
          alert(
            __LANG === 'es'
              ? 'No existen estudiantes en esta clase'
              : 'There are no students in this class'
          )
        }
      }
    })
  }

  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data()
      window.location.href = getBaseUrl('classes.php?class=' + data[0])
    }
  })

  $('#back').click((e) => {
    window.location.href = getBaseUrl('classes.php')
  })

  $('.studentsTable tbody').on('click', 'tr', function () {
    const row = studentsTable.row(this)
    if (row.index() !== undefined) {
      const studentId = $(row.node()).prop('id')
      const modal = $('#myModal')

      $.ajax({
        type: 'POST',
        url: includeThisFile(),
        data: { studentByPK: studentId },
        dataType: 'json',
        success: (res) => {
          if (res.response === true) {
            modal.find('#id').text(res.data.id)
            const username =
              res.data.usuario && `<span class="badge badge-secondary">${res.data.usuario}</span>`
            modal
              .find('.modal-title')
              .html(__LANG === 'es' ? 'Perfil del estudiante ' : 'Student profile ' + username)
            modal.find('#profilePicture').prop('src', res.data.foto)
            modal.find('#name').text(`${res.data.nombre}`)
            modal.find('#grade').text(res.data.grado)
            modal.find('#date').text(res.data.fecha === '0000-00-00' ? '' : res.data.fecha)
            modal.find('#email').text(res.data.email)
            const gender = {
              F: '<i class="fas fa-female fa-2x"></i>',
              1: '<i class="fas fa-female fa-2x"></i>',
              M: '<i class="fas fa-male fa-2x"></i>',
              2: '<i class="fas fa-male fa-2x"></i>'
            }
            modal.find('#gender').html(gender[res.data.genero.toUpperCase()])
            modal.modal('show')
          }
        }
      })
    }
  })
})
