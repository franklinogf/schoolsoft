

$(function () {
    let _oldDate = ''
    let _grade = ''
    let _class = ''
    const _attendanceOption = $("#attendanceOption").val()
    let date = $("#date").val()
    if (_attendanceOption === "1") {
        fillTable({
            getStudents: _attendanceOption,
            date
        })
    }
    $("#date").change(function (e) {
        if ($("#classButtons").length > 0) {
            if ($("#classButtons button.active").length > 0) {
                $("#classButtons button.active").click()
            }
        } else if ($("#gradesButtons").length > 0) {
            if ($("#gradesButtons button.active").length > 0) {
                $("#gradesButtons button.active").click()
            }
        }
        else {
            fillTable({
                getStudents: _attendanceOption,
                date: $(this).val()
            })
        }
    })
    $("#classButtons button").click(function (e) {
        date = $("#date").val()
        if (!$(this).hasClass('active') || date !== _oldDate) {
            $("#classButtons button").removeClass('active')
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
    $("#gradesButtons button").click(function (e) {
        date = $("#date").val()
        if (!$(this).hasClass('active') || date !== _oldDate) {
            $("#gradesButtons button").removeClass('active')
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

    $("#studentsList").on('change','select',function (e) { 
        const value = $(this).val()
        const ss = $(this).data('ss')
        let data = {
            changeAttendance: _attendanceOption,
            value,
            ss,
            date,
            grade:_grade
        }
        if(_attendanceOption === '3') data.class = _class
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: data,
            // dataType: "json",
            complete: function (response) {
                console.log('change:',response)
            }
        });
     })

    // functions
    function fillTable(dataJson) {
        $("#studentsList").removeClass('invisible')
        $("#studentsList .table tbody").html(`
    <tr>
        <td class="text-center" colspan='3'><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></td>
    </tr>
    `)
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: dataJson,
            dataType: "json",
            complete: function (res) {
                console.log('res:', res)
                const response = res.responseJSON
                const newDate = new Date(date);
                newDate.setDate(newDate.getDate() + 1)
                const day = newDate.getDate()
                let attendanceValue = '';
                $("#studentsList .table tbody").text('')
               if(response.response){
                response.data.forEach((student, index) => {
                    if (dataJson.attendanceOption === '3') {
                        attendanceValue = response.attendance[student.ss]
                    } else {
                        attendanceValue = student[`d${day}`]
                    }
                    $("#studentsList .table tbody").append(`
                    <tr>
                        <th scope="row">${index + 1}</th>
                        <td>${student.nombre} ${student.apellidos}</td>
                        <td class="text-right">
                            <select data-ss='${student.ss}' class="form-control w-50 ml-auto">
                                <option value= '' selected>Selecciona...</option>
                                <optgroup label="Ausencias">
                                    <option ${attendanceValue == "1" ? 'selected' : ''} value="1">Situación en el hogar</option>
                                    <option ${attendanceValue == "2" ? 'selected' : ''} value="2">Determinación del hogar (viaje)</option>
                                    <option ${attendanceValue == "3" ? 'selected' : ''} value="3">Actividad con padres (open house)</option>
                                    <option ${attendanceValue == "4" ? 'selected' : ''} value="4">Enfermedad</option>
                                    <option ${attendanceValue == "5" ? 'selected' : ''} value="5">Cita</option>
                                    <option ${attendanceValue == "6" ? 'selected' : ''} value="6">Actividad educativa del colegio</option>
                                    <option ${attendanceValue == "7" ? 'selected' : ''} value="7">Sin excusa del hogar</option>
                                </optgroup>
                                <optgroup label="Tardanzas">
                                    <option ${attendanceValue == "8" ? 'selected' : ''} value="8">Sin excusa del hogar</option>
                                    <option ${attendanceValue == "9" ? 'selected' : ''} value="9">Situación en el hogar</option>
                                    <option ${attendanceValue == "10" ? 'selected' : ''} value="10">Problema en la transportación</option>
                                    <option ${attendanceValue == "11" ? 'selected' : ''} value="11">Enfermedad</option>
                                    <option ${attendanceValue == "12" ? 'selected' : ''} value="12">Cita</option>
                                </optgroup>
                            </select>
                        </td>
                    </tr>
                    `)

                });
               }else{
                $("#studentsList .table tbody").html(`
                <tr>
                    <td class="text-center text-danger" colspan='3'>No hay ningún estudiante</td>
                </tr>
                `)
               }

            }
        });
    }
});