$(document).ready(function () {

    /* ------------------- Delete students from the next year ------------------- */
    $("#delete").click(function (e) {
        if ($("#new option:selected").length > 0) {
            const students = $("#new").val()
            let msg = '';
            if (students.length > 1) {
                msg = __LANG === 'es' ? 'Esta seguro que quiere borrar estos estudiantes?' : 'Are you sure you want to delete this student?';
            } else {
                msg = __LANG === 'es' ? 'Esta seguro que quiere borrar este estudiante?' : 'Are you sure you want to delete these students?';
            }
            if (confirm(msg)) {
                deleteStudents(students)
            }

        } else {
            $("#modalAlert p").text(__LANG === 'es' ? 'Debe de seleccionar al menos uno' : 'You must select at least one');
            $("#modalAlert").modal('show');
        }
    })
    $("#deleteAll").click(function (e) {
        if (confirm(__LANG === 'es' ? 'Esta seguro que quiere borrar todos los estudiantes?' : 'Are you sure you want to delete all the students?')) {
            $("#new option").prop('selected', true);
            const students = $("#new").val()
            deleteStudents(students)
        }
    })


    $("#selectAll").click(function (e) {
        $("#old option").prop('selected', true);
    })
    $("#pass").click(function (e) {
        e.preventDefault();

        if ($("#old option:selected").length > 0) {
            passStudents = $("#old").val()
            let jsonData = {}
            // No specific grade
            if ($("#pass").data('type') === 'regular') {
                jsonData = { passStudents, newYear: $("#year2").val() }
            } else if ($("#pass").data('type') === 'grade') {
                jsonData = { passStudents, newYear: $("#year2").val(), grade: $("#newGrade").val() }
            } else {
                jsonData = { passStudents, newYear: $("#year2").val(), grade: $("#passGrade").val() }

            }

            $.post(includeThisFile(), jsonData,
                function (data, textStatus, jqXHR) {
                    if ($("#newGrade").val() !== '') {
                        searchGradeStudents('new', $("#newGrade").val(), { new: $("#year2").val() });
                    } else {
                        searchAllStudents('new')
                    }
                    $("#oldStudentsAmount").text(+$("#oldStudentsAmount").text() - passStudents.length)
                    passStudents.forEach(oldStudentMT => {
                        $(`#old option[value=${oldStudentMT}]`).remove()
                    });

                }
            );
        } else {
            $("#modalAlert p").text(__LANG === 'es' ? 'Debe de seleccionar al menos uno' : 'You must select at least one');
            $("#modalAlert").modal('show');
        }
    })

    $("#studentSurnames").keyup(function (e) {

        if ($("#studentSurnames").val().length > 0) {
            $("#studentSurnamesBtn").prop('disabled', false)
            if (e.keyCode === 13) {
                $("#studentSurnamesBtn").click()
            }
        } else {
            $("#studentSurnamesBtn").prop('disabled', true)
            if ($("#oldGrade").val()) {
                searchGradeStudents('old', $("#oldGrade").val());
                $("#pass").html(`${__LANG === 'es' ? 'Pasar a grado' : 'Pass to grade'} <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>`)
                $("#pass").prop('disabled', true).data('type', 'grade');
            } else {
                searchAllStudents('old')
                $("#newGrade").val('').change().prop('disabled', true)
                $("#pass").html(`${__LANG === 'es' ? 'Pasar' : 'Pass'} <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>`)
                $("#pass").prop('disabled', false).data('type', 'regular');
            }
            $("#passGrade").addClass("invisible")

        }
    })
    $("#studentSurnames").focus(function () {
        $("#studentSurnames").select()
    })

    $("#studentSurnamesBtn").click(function (e) {

        let jsonData = {
            searchBySurname: $("#studentSurnames").val()
        }
        if ($("#oldGrade").val() !== '') {
            jsonData.grade = $("#oldGrade").val()
        }
        searchStudentsBySurnames(jsonData)
        $("#pass").html(`${__LANG === 'es' ? 'Pasar a grado' : 'Pass to grade'} <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>`)
        $("#pass").prop('disabled', false).data('type', 'passGrade');
        $("#newGrade").prop('disabled', true);
        $("#passGrade").removeClass("invisible")
    })

    $("#oldGrade").change(function (e) {
        $("#passGrade").addClass("invisible")
        if ($("#oldGrade").val()) {
            searchGradeStudents('old', $("#oldGrade").val());
            $("#newGrade").prop('disabled', false);
            $("#pass").html(`${__LANG === 'es' ? 'Pasar a grado' : 'Pass to grade'} <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>`)
            if ($("#newGrade").val()) {
                $("#pass").prop('disabled', false);
            } else {
                $("#pass").prop('disabled', true).data('type', 'grade');
            }
        } else {
            $("#newGrade").val('').prop('disabled', true).change();
            $("#pass").prop('disabled', false).data('type', 'regular');
            searchAllStudents('old')
            $("#pass").html(`${__LANG === 'es' ? 'Pasar' : 'Pass'} <i class="fas fa-angle-double-right d-none d-lg-block"></i> <i class="fas fa-angle-double-down d-lg-none"></i>`)
        }
        $("#studentSurnames").val('')
        $("#studentSurnamesBtn").prop('disabled', true)
    })

    $("#newGrade").change(function (e) {
        if ($("#newGrade").val()) {
            searchGradeStudents('new', $("#newGrade").val(), { new: $("#year2").val() });
            $("#pass").prop('disabled', $("#old option").length > 0 ? false : true);
        } else {
            $("#pass").prop('disabled', true);
            searchAllStudents('new')
        }

    })
    const deleteStudents = (deleteStudents) => {
        $.post(includeThisFile(), { deleteStudents },
            function (data, textStatus, jqXHR) {
                if ($("#newGrade").val() !== '') {
                    searchGradeStudents('new', $("#newGrade").val(), { new: $("#year2").val() });
                } else {
                    searchAllStudents('new')
                }
                if ($("#oldGrade").val() !== '') {
                    searchGradeStudents('old', $("#oldGrade").val());
                } else {
                    searchAllStudents('old')
                }
                deleteStudents.forEach(newStudentMT => {
                    $(`#new option[value=${newStudentMT}]`).remove()
                });

            }
        );
    }

    const searchStudents = (type, jsonData) => {
        $.post(includeThisFile(), jsonData,
            function (data, textStatus, jqXHR) {
                console.log(data)
                $(`#${type}`).html('')
                let students = ''
                data.forEach(student => {
                    students += `<option value="${student.mt}">${student.apellidos}, ${student.nombre} (${student.id}) ${student.grado}</option>`
                });
                $(`#${type}`).html(students)
                $(`#${type}StudentsAmount`).text(data.length)
            }, 'json'
        );
    }
    const searchAllStudents = (type) => {
        if (type === 'old') {
            year = $("#year1").val()
        } else {
            year = $("#year2").val()
        }
        searchStudents(type, { search: year })
        $(`#${type}StudentsTitle`).html(`${__LANG === 'es' ? 'Todos los estudiantes' : 'All the students'} <span id="${type}StudentsAmount" class="badge badge-primary"></span>`)
    }
    const searchStudentsBySurnames = (jsonData) => {
        searchStudents('old', jsonData)
        $(`#oldStudentsTitle`).html(`${__LANG === 'es' ? 'Todos los estudiantes que contiene en el apellido' : 'All the students that contains on the last names'} <b>\"${jsonData.searchBySurname}\"</b> <span id="oldStudentsAmount" class="badge badge-primary"></span>`)

    }
    const searchGradeStudents = (type, grade, optional = {}) => {
        searchStudents(type, { searchGrade: grade, ...optional })
        $(`#${type}StudentsTitle`).html(`${__LANG === 'es' ? 'Todos los estudiantes del grado' :'All the students in grade'} ${grade} <span id="${type}StudentsAmount" class="badge badge-primary"></span>`)
    }
});