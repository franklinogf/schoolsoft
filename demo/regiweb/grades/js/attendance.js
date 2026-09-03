$(function () {
  let _oldDate = ''
  let _grade = ''
  let _class = ''
  const _attendanceOption = $('#attendanceOption').val() //from ADMIN page
  let date = $('#date').val()
  let __translation
  if (__LANG === 'es') {
    __translation = ['Ausencias', 'Tardanzas', 'No hay estudiantes', 'Seleccionar']
  } else {
    __translation = ['Absence', 'Tardy', 'No students', 'Select']
  }

  if (_attendanceOption === '1' && __SCHOOL_ACRONYM !== 'cbtm') {
    fillTable({
      getStudents: _attendanceOption,
      grade: $('#grade').val(),
      date
    })
  } else if (__SCHOOL_ACRONYM === 'cbtm' && $('#gradesButtons button').length === 1) {
    fillTable({
      getStudents: 1,
      date,
      grade: $('#gradesButtons button.active').data('grade')
    })
  }
  $('#date').change(function (e) {
    if ($('#classButtons').length > 0) {
      if ($('#classButtons button.active').length > 0) {
        $('#classButtons button.active').click()
      }
    } else if ($('#gradesButtons').length > 0) {
      if ($('#gradesButtons button.active').length > 0) {
        $('#gradesButtons button.active').click()
      }
    } else {
      fillTable({
        getStudents: _attendanceOption,
        date: $(this).val(),
        grade: $('#grade').val()
      })
    }
  })
  $('#classButtons button').click(function (e) {
    date = $('#date').val()
    if (!$(this).hasClass('active') || date !== _oldDate) {
      $('#classButtons button').removeClass('active')
      $(this).addClass('active')
      _class = $(this).data('class')
      _oldDate = date
      fillTable({
        class: _class,
        getStudents: _attendanceOption,
        date
      })
    }
  })
  $('#gradesButtons button').click(function (e) {
    date = $('#date').val()
    if (!$(this).hasClass('active') || date !== _oldDate) {
      $('#gradesButtons button').removeClass('active')
      $(this).addClass('active')
      _grade = $(this).data('grade')
      _oldDate = date
      fillTable({
        grade: _grade,
        getStudents: _attendanceOption,
        date
      })
    }
  })

  $('#attendanceForm').submit(function (e) {
    e.preventDefault()
    const $btn = $('#saveAttendance')
    loadingBtn($btn, '', __LANG === 'es' ? 'Guardando...' : 'Saving...')
    const serializedData =
      $(this).serialize() +
      '&saveAttendance=' +
      encodeURIComponent(_attendanceOption) +
      '&date=' +
      encodeURIComponent($('#date').val()) +
      '&grade=' +
      encodeURIComponent(_grade) +
      '&class=' +
      encodeURIComponent(_class)
    $.ajax({
      type: 'POST',
      url: includeThisFile(),
      data: serializedData,
      dataType: 'json',
      success: function (response) {
        if (response?.status === 'success') {
          alert(
            __LANG === 'es' ? 'Asistencia guardada correctamente' : 'Attendance saved successfully'
          )
        } else {
          alert(__LANG === 'es' ? 'Ocurrió un error al guardar' : 'An error occurred while saving')
        }
      },
      error: function (xhr, status, error) {
        console.error('error', xhr, status, error)
        alert(__LANG === 'es' ? 'Ocurrió un error al guardar' : 'An error occurred while saving')
      },
      complete: function () {
        loadingBtn($btn, __LANG === 'es' ? 'Guardar' : 'Save')
      }
    })
  })

  // functions
  function fillTable(dataJson) {
    console.log('dataJson', dataJson)
    $('#studentsList').removeClass('invisible')
    $('#studentsList .table tbody').html(
      '<tr><td class="text-center" colspan="9"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></td></tr>'
    )
    $.ajax({
      type: 'POST',
      url: includeThisFile(),
      data: dataJson,
      dataType: 'json',
      success: function (response) {
        // console.log({ response })

        $('#studentsList .table tbody').text('')
        if (!response?.error) {
          const defaultP = { p1: '', p2: '', p3: '', p4: '', p5: '', p6: '' }
          response.data.forEach((student, index) => {
            // get the value depending on the option marked on ADMIN
            const studentInfo = (response.attendance || {})[`${student.ss}`]
            const attendanceValue = studentInfo ? studentInfo.code : ''
            const attendanceP = (studentInfo && studentInfo.p) || defaultP

            $('#studentsList .table tbody').append(`
                <tr>
                    <th scope="row">${index + 1}</th>
                    <td>${student.apellidos} ${student.nombre}</td>
                    <td class="text-right">
                        <select style="width: 100%;" data-ss='${student.ss}' name="students[${student.ss}][codigo]" class="form-control ml-auto">
                            <option value= '' selected>${__translation[3]}</option>
                            ${
                              __SCHOOL_ACRONYM !== 'cbtm'
                                ? `
                            <optgroup label="${__translation[0]}">
                                <option ${attendanceValue == '1' ? 'selected' : ''} value="1">${
                                  attendanceCodes[1]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '2' ? 'selected' : ''} value="2">${
                                  attendanceCodes[2]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '3' ? 'selected' : ''} value="3">${
                                  attendanceCodes[3]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '4' ? 'selected' : ''} value="4">${
                                  attendanceCodes[4]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '5' ? 'selected' : ''} value="5">${
                                  attendanceCodes[5]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '6' ? 'selected' : ''} value="6">${
                                  attendanceCodes[6]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '7' ? 'selected' : ''} value="7">${
                                  attendanceCodes[7]['description'][__LANG]
                                }</option>
                            </optgroup>
                            <optgroup label="${__translation[1]}">
                                <option ${attendanceValue == '8' ? 'selected' : ''} value="8">${
                                  attendanceCodes[8]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '9' ? 'selected' : ''} value="9">${
                                  attendanceCodes[9]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '10' ? 'selected' : ''} value="10">${
                                  attendanceCodes[10]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '11' ? 'selected' : ''} value="11">${
                                  attendanceCodes[11]['description'][__LANG]
                                }</option>
                                <option ${attendanceValue == '12' ? 'selected' : ''} value="12">${
                                  attendanceCodes[12]['description'][__LANG]
                                }</option>
                            </optgroup>`
                                : `
                            <optgroup label="${__translation[0]}">
                                <option ${
                                  attendanceValue == '13' ? 'selected' : ''
                                } value="13">Only AM</option>
                                <option ${
                                  attendanceValue == '14' ? 'selected' : ''
                                } value="14">Only PM</option>
                                <option ${
                                  attendanceValue == '15' ? 'selected' : ''
                                } value="15">All day</option>
                            </optgroup>
                            <optgroup label="${__translation[1]}">
                                <option ${
                                  attendanceValue == '16' ? 'selected' : ''
                                } value="16">Tardy</option>
                            </optgroup>`
                            }
                        </select>
                    </td>
                    ${
                      hasAttendanceP
                        ? `${Object.keys(attendanceP)
                            .map(
                              (p) => `<td class="text-center">
                        <input data-ss='${student.ss}' data-p='${p}' name="students[${student.ss}][${p}]" class="form-control" value='${attendanceP[p] ?? ''}'/>
                      </td>`
                            )
                            .join('')}`
                        : ''
                    }
                </tr>
                `)
          })
        } else {
          $('#studentsList .table tbody').html(`
                <tr>
                    <td class="text-center text-danger" colspan='3'>${__translation[2]}</td>
                </tr>
                `)
        }
      },
      error: function (xhr, status, error) {
        console.error('error', xhr, status, error)
      }
    })
  }
})
