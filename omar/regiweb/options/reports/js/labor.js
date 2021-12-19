$(function () {

    $("#class").change(function (e) {
        if ($(this).val() !== '') {
            CleanDates()
            $.ajax({
                type: "POST",
                url: includeThisFile(),
                data: {
                    getDates: $(this).val(),
                },
                dataType: "json",
                complete: function (res) {
                    const response = res.responseJSON
                    if (response.response) {
                        $("#date").prop('disabled', false)
                        response.data.forEach((date, index) => {
                            $("#date").append(`<option value="${date.fecha}">${date.fecha}</option>`)
                        })
                    } else {
                        alert("Este curso no tiene fechas disponibles")
                        
                    }
                }
            });
        }
    })
    $("#date").change(function (e) {
        if ($(this).val() !== '') {
            $("#edit,#delete").removeClass('invisible')
        } else {
            $("#edit,#delete").addClass('invisible')

        }

    })
    $("#edit").click(function (e) {
        const grade = $("#class").val()
        const date = $("#date").val()
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: {
                getReport: true,
                class: grade,
                date
            },
            dataType: "json",
            complete: function (res) {
                const response = res.responseJSON
                console.log('response:', response)
                if (response.response) {
                    $("#newModal").modal('show')
                    $("#newModalSubmitBtn").data('action', 'edit')
                    const report = response.data
                    $("#newClass").val(report.curso)
                    $("#newDate").val(report.fecha)
                    $("#newMatters").val(report.asuntos)
                    $("#newDiscipline").val(report.disciplina)
                    $("#newAssists").val(report.asistencias)
                    $("#newFatherInterviews").val(report.entrevistap)
                    $("#newStudentInterviews").val(report.entrevistae)
                    $("#newMeetings").val(report.reuniones)
                    $("#newOthers").val(report.otros)
                    $("#newAmountMatters").val(report.cantasuntos)
                    $("#newAmountDiscipline").val(report.cantdisciplina)
                    $("#newAmountAssists").val(report.cantasistencias)
                    $("#newAmountFatherInterviews").val(report.cantentrevistap)
                    $("#newAmountStudentInterviews").val(report.cantentrevistae)
                    $("#newAmountMeetings").val(report.cantreuniones)
                    $("#newAmountOthers").val(report.cantotros)
                }

            }
        });
    })

    $("#delete").click(function (e) {
        if (confirm("esta seguro que desea eliminar este informe?")) {
            const grade = $("#class").val()
            const date = $("#date").val()
            $.ajax({
                type: "POST",
                url: includeThisFile(),
                data: {
                    daleteReport: true,
                    class: grade,
                    date
                },
                complete: function (res) {
                    $("#class").change()

                }
            });
        }
    })

    $('#newModal').on('show.bs.modal', function (event) {
        $("#newDate").val(getDate())
    })
    
    $('#newModal').on('hide.bs.modal', function (event) {
        $("#newModalSubmitBtn").data('action', 'save')
        $("#newClass").val('')
    })
    // form
    $("#newModalForm").submit(function (e) {
        e.preventDefault();
        const grade = $("#class").val()
            const date = $("#date").val()
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: {
                checkDate: true,
                class: grade,
                date
            },
            dataType:'json',            
            complete: function (res) {
               console.log('res:', res)
               if(!res.responseJSON.response || $("#newModalSubmitBtn").data('action') === 'edit'){
                loadingBtn($("#newModalSubmitBtn"), '', 'Guardando...');
                const fd = new FormData($("#newModalForm")[0]);
                fd.append($("#newModalSubmitBtn").data('action') === 'save' ? "submitNewReport" : "editReport", true);
                $.ajax({
                    type: "POST",
                    url: includeThisFile(),
                    data: fd,
                    contentType: false,
                    processData: false,
                    complete: function (res) {
                        $("#newModal").modal('hide')
                        loadingBtn($("#newModalSubmitBtn"), 'Guardar');
                        $("#class").change()
        
                    }
                });
               }else{
                   alert("Ya existe un informe con esta fecha y curso!")
               }
            }
        });
        
    })

    function CleanDates() {
        $("#date").prop('disabled', true)
        $(".edit,.delete").addClass('invisible')
        $("#date option").each(function (index) {
            if ($(this).val() !== '') {
                $(this).remove()
            }
        })
        $("#edit,#delete").addClass('invisible')
    }

})