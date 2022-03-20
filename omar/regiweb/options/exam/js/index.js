$(function () {
    let _examInfo = null
    let _optionNumber = null
    let _deleteId = null
    let _type = null
    let _gradeOptionsSearched = null

    // enable new and search buttons when page charge
    $('[data-target="#newExamModal"], [data-target="#searchExamModal"]').prop('disabled', false)

    // console.log(correctExamsTable)
    /* ----------------------------- Correct Examns ----------------------------- */
    // Fill done exams students
    $('#correctExamsModal').on('show.bs.modal', function (event) {

        correctExams()
    })
    $('#correctExamsModal').on('hide.bs.modal', function (event) {
        $("#correctExamsModal .modal-footer button").prop('disabled', true)
        correctExamsTable.rows().remove()

    })
    // Correct Exams
    $("#correctExamsButton").click(function () {
        loadingBtn($("#correctExamsButton"), '', 'Corrigiendo...')
        $.post(includeThisFile(), { correctExams: _examInfo.id },
            function (data, textStatus, jqXHR) {
                console.log('correctExam: ', data)
                loadingBtn($("#correctExamsButton"), 'Corregir examenes')
                correctExams();
            }
        );
    })
    // 
    $("#correctExamsButton2").click(function () {
        loadingBtn($("#correctExamsButton2"), '', 'Pasando puntos...')
        $.post(includeThisFile(), { passPoints: _examInfo.id },
            function (data, textStatus, jqXHR) {
                console.log('passPoints: ', data)
                loadingBtn($("#correctExamsButton2"), 'Pasar puntos')
            }
        );
    })
    $("#correctExamsButton3").click(function () {
        loadingBtn($("#correctExamsButton3"), '', 'Pasando porcentajes...')
        $.post(includeThisFile(), { passPoints: _examInfo.id, passPorcent: true },
            function (data, textStatus, jqXHR) {
                console.log('passPorcent: ', data)
                loadingBtn($("#correctExamsButton3"), 'Pasar porcentajes')
            }
        );
    })

    // View Exam
    $("#correctExamsTable tbody").on('click', 'tr', function (e) {

        const studentMt = $(this).data('mt')
        if (studentMt !== undefined) {
            console.log('studentMt:', studentMt)
            $("#viewExamModal").modal('show')
            loadingBtn($("#viewExamModal .modal-body"))

            $.post("./includes/correctedExam.php",{examId:_examInfo.id,studentMt},
                function (data, textStatus, jqXHR) {
                    $("#viewExamModal .modal-body").html(data)
                }
            );
    //         $("#viewExamModal .modal-body").html(`
    //     <div class="container bg-white px-3 py-5 p-md-5 shadow">
    //     ${_examInfo.fvs.topics ? `        
    //           <h4 class="mt-3">${topicNumber++} - ${_examInfo.desc1 === 'si' ? _examInfo.desc1_1 : _examInfo.fvs.title} <span class="badge badge-info">${_examInfo.fvs.value}</span></h4>
    //           ${_examInfo.fvs.topics.map((topic, index) => {
    //             count = index + 1
    //             return `              
    //           <div class="form-group">
    //             <label class="font-weight-bold" for="fv${count}"> ${count}) ${topic.pregunta}</label>
    //             <select id="fv${count}" class="form-control readonly">
    //                 <option value="" selected>Selecciona la respuesta</option>
    //                 <option value="v">Verdadero</option>
    //                 <option value="f">Falso</option>
    //             </select>
    //         </div>`}).join('')}                          
    //              `  : ''}
           

    //  </div>`)

        }
    })

    /* ----------------------------- Grades options ----------------------------- */
    $("#gradeOptionsSearchButton").click(function (e) {
        e.preventDefault()
        loadingBtn($("#gradeOptionsSearchButton"), '', 'Buscando...')
        $.post(includeThisFile(), {
            gradeOptionsSearch: true,
            grade: $("#gradeOptionsModalGrade").val(),
            trimester: $("#gradeOptionsTrimester").val(),
            type: $("#gradeOptionsType").val()
        },
            function (data) {
                loadingBtn($("#gradeOptionsSearchButton"), 'Buscar')
                if (data.response) {
                    const gradeOptions = data.data
                    let selectedBefore = ''
                    for (let index = 1; index <= 10; index++) {
                        $(`#gradeOptionsSelected${index}`).val(index).prop('checked', _examInfo.id == gradeOptions[`nota${index}`] ? true : false)
                        $(`#gradeOptionsDescription${index}`).val(gradeOptions[`tema${index}`])
                        $(`#gradeOptionsValue${index}`).val(gradeOptions[`val${index}`])
                        $(`#gradeOptionsDate${index}`).val(gradeOptions[`fec${index}`] !== '0000-00-00' ? gradeOptions[`fec${index}`] : '')
                        if (selectedBefore === '') {
                            selectedBefore = _examInfo.id == gradeOptions[`nota${index}`] ? index : ''
                        }

                    }
                    _gradeOptionsSearched = {
                        selectedBefore,
                        gradeOptions: 'edit',
                        optionId: gradeOptions.id
                    }


                } else {
                    for (let index = 1; index <= 10; index++) {
                        $(`#gradeOptionsSelected${index}`).prop('checked', false)
                        $(`#gradeOptionsDescription${index}`).val('')
                        $(`#gradeOptionsValue${index}`).val('')
                        $(`#gradeOptionsDate${index}`).val('')
                    }
                    _gradeOptionsSearched = {
                        gradeOptions: 'save'
                    }
                }
                _gradeOptionsSearched = {
                    ..._gradeOptionsSearched,
                    grade: data.grade,
                    trimester: data.trimester,
                    type: data.type
                }
                $("#gradeOptionsButton,#gradeOptionsList input").prop('disabled', false)
            },
            "json"
        );
    });

    $("#gradeOptionsButton").click(function (e) {
        e.preventDefault()
        if (_gradeOptionsSearched) {
            loadingBtn($("#gradeOptionsButton"), '', 'Guardando...')
            dataToSend = {};
            dataToSend['selected'] = $("[name=gradeOptionsSelected]:checked").length > 0 ? $("[name=gradeOptionsSelected]:checked").val() : ''
            for (let index = 1; index <= 10; index++) {
                dataToSend[`description${index}`] = $(`#gradeOptionsDescription${index}`).val()
                dataToSend[`value${index}`] = $(`#gradeOptionsValue${index}`).val()
                dataToSend[`date${index}`] = $(`#gradeOptionsDate${index}`).val()

            }
            $.post(includeThisFile(), {
                ...dataToSend,
                ..._gradeOptionsSearched,
                examId: _examInfo.id
            },
                function (data) {
                    _gradeOptionsSearched.gradeOptions = 'edit'
                    loadingBtn($("#gradeOptionsButton"), 'Guardar')
                });
        }
    })

    $('#gradeOptionsModal').on('hide.bs.modal', function (event) {
        _gradeOptionsSearched = null
        $("#gradeOptionsButton,#gradeOptionsList input").prop('disabled', true)
    })

    /* -------------------------- Exam information ------------------------- */

    // Fill information
    $('#infoExamModal').on('show.bs.modal', function (event) {
        $("#infoExamGrade").val(_examInfo.curso)
        $("#infoExamStartTime").val(_examInfo.hora)
        $("#infoExamEndTime").val(_examInfo.hora_final)
        $("#infoExamDate").val(_examInfo.fecha)
        $("#infoExamTime").val(_examInfo.tiempo)
        $("#infoExamPreviewGrade1").prop('checked', _examInfo.ver_nota === 'si' ? true : false)
        $("#infoExamPreviewGrade2").prop('checked', _examInfo.ver_nota === 'no' ? true : false)
        $("#infoExamAvailability1").prop('checked', _examInfo.activo === 'si' ? true : false)
        $("#infoExamAvailability2").prop('checked', _examInfo.activo === 'no' ? true : false)
    })

    // update information
    $("#infoExamForm").submit(function (e) {
        e.preventDefault();
        e.stopPropagation();
        if ($(this)[0].checkValidity() === false) {
            $(this).addClass('was-validated')
        } else {
            loadingBtn($("#infoExamButton"), '', 'Guardando...')
            $.post(includeThisFile(), {
                examInfo: _examInfo.id,
                grade: $("#infoExamGrade").val(),
                startTime: $("#infoExamStartTime").val(),
                endTime: $("#infoExamEndTime").val(),
                date: $("#infoExamDate").val(),
                time: $("#infoExamTime").val(),
                previewGrade: $("#infoExamPreviewGrade1").prop('checked') ? 'si' : 'no',
                availability: $("#infoExamAvailability1").prop('checked') ? 'si' : 'no',
            },
                function (data) {
                    _examInfo.curso = $("#infoExamGrade").val()
                    _examInfo.hora = $("#infoExamStartTime").val()
                    _examInfo.hora_final = $("#infoExamEndTime").val()
                    _examInfo.fecha = $("#infoExamDate").val()
                    _examInfo.tiempo = $("#infoExamTime").val()
                    _examInfo.ver_nota = $("#infoExamPreviewGrade1").prop('checked') ? 'si' : 'no'
                    _examInfo.activo = $("#infoExamAvailability1").prop('checked') ? 'si' : 'no'
                    loadingBtn($("#infoExamButton"), 'Guardar')
                });
        }
    })

    /* -------------------------------------------------------------------------- */
    /*                                   Options                                  */
    /* -------------------------------------------------------------------------- */

    // All Options
    $(".optionAddButton").click(function (e) {
        $(this).removeClass('border-danger')
        $(this).siblings(".alert").remove()
        if ($(`#option${_optionNumber}Question`).val() !== "" && $(`#option${_optionNumber}Value`).val() > 0) {
            dataToSend = {
                optionNumber: _optionNumber,
                question: $(`#option${_optionNumber}Question`).val(),
                value: $(`#option${_optionNumber}Value`).val()
            }
            if (_optionNumber <= 3) {
                dataToSend = {
                    ...dataToSend,
                    answer: $(`#option${_optionNumber}Answer`).val(),
                }
            }
            if (_optionNumber === 2) {
                for (let index = 1; index <= 8; index++) {
                    dataToSend[`answer${index}`] = $(`#option2Answer${index}`).val()
                }
            } else if (_optionNumber === 4) {
                const answerAmount = $("#option4AnswersAmount").val();
                for (let index = 1; index <= answerAmount; index++) {
                    dataToSend[`answer${index}`] = $(`#option4Answer${index}`).val()
                    dataToSend.answerAmount = answerAmount;
                }
            } else if (_optionNumber === 5) {
                dataToSend.amountOfLines = $("#option5AmountOfLines").val()
            }
            if ($(this).data('action') === 'save') {
                dataToSend = {
                    ...dataToSend,
                    addQuestion: _examInfo.id,
                }
                loadingBtn($(this), '', 'Guardando...')

            } else {
                loadingBtn($(this), '', 'Actualizando...')
                dataToSend = {
                    ...dataToSend,
                    editQuestion: $(this).data('questionId'),
                }
            }

            $.post(includeThisFile(), dataToSend, function (data) {
                loadingBtn($(this), 'Agregar')
                clearInputs()
                fillOption()
                loadMenu()

            });
        } else {
            $(this).addClass('border-danger').after('<div class="alert alert-danger mt-2" role="alert">Debe de llenar todos los campos!</div>')
        }
    })

    // Option 4 change amount of answers
    $("#option4AnswersAmount").change(function (e) {
        let answersInput = '';
        for (let index = 1; index <= $(this).val(); index++) {
            let isEditing = false
            if ($("#option4Add").data('action') === 'edit') {
                isEditing = true
                itemId = $("#option4Add").data('questionId')
                itemValue = $(".optionModal.show").find(`.item[data-id=${itemId}]`).find(`.itemAnswer${index}`).val()
            }
            answersInput += `<input id="option4Answer${index}" name="option4Answer${index}" placeholder="respuesta ${index}" ${isEditing ? `value="${itemValue ?? ''}"` : ''} type="text" class="form-control" required>`
        }
        $("#option4Answers").html(answersInput)
    })

    // Fill the options
    $('.optionModal').on('show.bs.modal', function (event) {
        _optionNumber = $(this).data('optionNumber')
        if (_examInfo[`desc${_optionNumber}`] === 'si') {
            $(`#option${_optionNumber}Check`).prop('checked', true).change()
            $(`#option${_optionNumber}Description`).val(_examInfo[`desc${_optionNumber}_1`])
        }
        fillOption()

    })

    // reset the forms
    $('.optionModal').on('hidden.bs.modal', function (event) {
        clearInputs();
        if (_optionNumber === 4) {
            $("#option4Answers").html('')
        }
        _optionNumber = null


    })

    /* ----------------------------- Delete questions ----------------------------- */
    $(".optionModal").on('click', '.deleteQuestion', function (e) {
        e.preventDefault();
        const item = $(this).parents('.item')
        _deleteId = $(item).data('id')
        _type = 'question'
        $("#deleteModal").modal('show')

    })

    /* ------------------------------ Delete Modal ------------------------------ */
    $('#deleteModal').on('hidden.bs.modal', function (event) {
        _deleteId = null
        _type = null
    })

    $("#deleteButton").click(function (e) {
        loadingBtn($("#deleteButton"), '', 'Eliminando...')
        $.post(includeThisFile(), {
            optionNumber: _optionNumber,
            deleteQuestion: _deleteId,
            type: _type
        },
            function (data, textStatus, jqXHR) {
                fillOption()
                loadMenu()
                $("#deleteModal").modal('hide')
                loadingBtn($("#deleteButton"), 'Eliminar')
            });
    })

    /* ----------------------------- Edit questions ----------------------------- */
    $(".optionModal").on('click', '.editQuestion', function (e) {
        e.preventDefault();
        const item = $(this).parents('.item')
        const itemTitle = $(item).find('.itemTitle').text()
        if (_optionNumber <= 3) {
            const itemAnswer = $(item).find('.itemAnswer').text()
            $(`#option${_optionNumber}Answer`).val(itemAnswer)
        }
        const itemValue = $(item).find('.itemValue').text()
        $(`#option${_optionNumber}Question`).val(itemTitle)
        $(`#option${_optionNumber}Value`).val(itemValue)
        $(`#option${_optionNumber}Add`).text("Editar").data('action', 'edit').data('questionId', $(this).data('id'))
        if (_optionNumber === 2) {
            for (let index = 1; index <= 8; index++) {
                $(`#option2Answer${index}`).val($(item).find(`.itemAnswer${index}`).val())
            }
        } else if (_optionNumber === 5) {
            $(`#option5AmountOfLines`).val($(item).find(".itemLines").val())
        } else if (_optionNumber === 4) {
            const option4amount = $(item).find(".itemAnswerAmount").val()
            $("#option4AnswersAmount").val(option4amount).change()
            for (let index = 1; index <= option4amount; index++) {
                $(`#option4Answer${index}`).val($(item).find(`.itemAnswer${index}`).val())
            }
        }
    })

    // AddAnswer option 3
    $("#option3AddCode").click(function (e) {
        $(this).removeClass('border-danger')
        $(this).siblings(".alert").remove()
        if ($(`#option3Code`).val() !== "") {
            dataToSend = {
                answer: $(`#option3Code`).val(),
            }
            if ($(this).data('action') === 'save') {
                dataToSend = {
                    ...dataToSend,
                    addAnswer: _examInfo.id,
                }
                loadingBtn($(this), '', 'Guardando...')

            } else {
                loadingBtn($(this), '', 'Actualizando...')
                dataToSend = {
                    ...dataToSend,
                    editAnswer: $(this).data('answerId'),
                }
            }

            console.log('dataToSend:', dataToSend)
            $.post(includeThisFile(), dataToSend, function (data) {
                loadingBtn($(this), 'Agregar')
                clearInputs()
                fillOption()
                loadMenu()

            });
        } else {
            $(this).addClass('border-danger').after('<div class="alert alert-danger mt-2" role="alert">Debe de llenar todos los campos!</div>')
        }
    })

    /* ----------------------------- Delete answer ----------------------------- */
    $(".optionModal").on('click', '.deleteAnswer', function (e) {
        e.preventDefault();
        const item = $(this).parents('.item')
        _deleteId = $(item).data('id')
        _type = 'answer'
        $("#deleteModal").modal('show')

    })

    /* ----------------------------- Edit answer ----------------------------- */
    $(".optionModal").on('click', '.editAnswer', function (e) {
        e.preventDefault();
        const item = $(this).parents('.item')
        const itemTitle = $(item).find('.itemTitle').text()
        const itemId = $(this).data('id')
        $(`#option${_optionNumber}Code`).val(itemTitle)
        $(`#option${_optionNumber}AddCode`).text("Editar").data('action', 'edit').data('answerId', itemId)
    })

    /* --------------------------- created a new exam --------------------------- */
    $("#newExamButton").click(function (e) {
        e.preventDefault()
        if ($("#newExamTitle").val() !== '') {
            $("#newExamTitle").removeClass('is-invalid')
            loadingBtn($(this), '', 'Creando...')
            $.post(includeThisFile(), {
                newExam: true,
                title: $("#newExamTitle").val(),
                grade: $("#newExamGrade").val()
            },
                function (data) {
                    _examInfo.id = data.examId
                    _examInfo.titulo = data.title
                    _examInfo.curso = data.grade
                    _examInfo.fecha = data.date
                    _examInfo.activo = 'no'
                    _examInfo.ver_nota = 'no'
                    $("#searchExamId").prepend(`<option value='${_examInfo.id}' selected>${_examInfo.titulo}</option>`)
                    loadingBtn($(this), 'Crear')
                    $("#newExamModal,#infoExamModal").modal('hide')
                    loadMenu()
                },
                'json'
            );
        } else {
            $("#newExamTitle").addClass('is-invalid')
        }
    })

    /* ----------------------------- Duplicate exam ----------------------------- */
    $("#duplicateExamButton").click(function (e) {
        e.preventDefault();
        loadingBtn($(this), '', 'Duplicando...')
        $.post(includeThisFile(), {
            duplicateExam: _examInfo.id,
            title: $("#duplicateExamTitle").val(),
            grade: $("#duplicateExamGrade").val()
        },
            function (data) {
                console.log('duplicate:', data)
                loadingBtn($(this), 'Duplicar Examen')
                _examInfo = data
                $("#searchExamId").prepend(`<option value='${_examInfo.id}' selected>${_examInfo.titulo}</option>`)
                $("#infoExamModal,#duplicateExamModal").modal('hide')
                loadMenu()
            }, 'json');

    });

    /* ---------------------------- Search exam by Id --------------------------- */
    $("#searchExamBtn").click(function (e) {
        loadingBtn($("#searchExamBtn"), '', 'Buscando...')
        const examId = $("#searchExamId").val()
        $.post(includeThisFile(), {
            searchExam: examId
        }, function (data) {
            loadingBtn($("#searchExamBtn"), 'Buscar')
            const exam = $.parseJSON(data)
            _examInfo = exam
            console.log('_examInfo:', _examInfo)

            loadMenu();
            $("#searchExamModal").modal('hide')
        });
    })

    /* ---------------------------- Update exam title --------------------------- */
    $("#title").change(function (e) {
        const title = $(this).val();
        if (title !== '') {
            $(this).removeClass('is-invalid')
            $.post(includeThisFile(), {
                'changeTitle': title,
                examId: _examInfo.id
            }, function () {
                $(`#searchExamId option[value=${_examInfo.id}]`).text(title)
            });
        } else {
            $(this).addClass('is-invalid')
        }
    })

    /* ----------------------- Description checkbox check ----------------------- */
    $(".modal input[type=checkbox]").change(function (e) {
        const descriptionInput = $("#" + $(this).data('target'))
        if ($(this).prop('checked')) {
            descriptionInput.prop('disabled', false).focus()
        } else {
            descriptionInput.prop('disabled', true)
        }
    })

    /* -------------------------------------------------------------------------- */
    /*                                  Functions                                 */
    /* -------------------------------------------------------------------------- */


    function correctExams() {
        $.post(includeThisFile(), { fillCorrectExams: _examInfo.id, grade: _examInfo.curso },
            function (data, textStatus, jqXHR) {
                if (data.response) {
                    $("#correctExamsModal .modal-footer button").prop('disabled', false)
                    correctExamsTable.rows().remove()
                    data.data.map((student) => {
                        const totalPoints = student.puntos + student.bonos;
                        const thisRow = correctExamsTable.row.add({
                            0: `${student.nombre} ${student.apellidos}`,
                            1: student.terminado_el,
                            2: totalPoints,
                            3: ((totalPoints / _examInfo.valor) * 100).toFixed(0) + '%'
                        }).draw()
                        $(thisRow.node()).data('mt', student.mt)
                    })
                }
            },
            "json"
        );
    }
    function resetButton() {
        $(`#option${_optionNumber}Add`).text("Agregar").data('action', 'save').removeData('questionId').prop('disabled', false).removeClass('disabled')
        if (_optionNumber === 3) {
            $(`#option3AddCode`).text("Agregar").data('action', 'save').removeData('answerId').prop('disabled', false).removeClass('disabled')
        }
    }
    function clearInputs() {
        const modal = $(`.optionModal[data-option-number=${_optionNumber}]`)

        modal.find('.optionAddButton').removeClass('border-danger')
        modal.find(".alert").remove()
        resetButton()
        modal.find(`#option${_optionNumber}Question`).val('');
        modal.find(`#option${_optionNumber}Answer`).val('');
        modal.find(`#option${_optionNumber}Value`).val('');
        modal.find('.answers').find("input").val('');
        if (_optionNumber === 3) {
            $(`#option3Code`).val('')
        } else if (_optionNumber === 4) {
            $("#option4Answers").html('')
            $("#option4AnswersAmount").val('')
        } else if (_optionNumber === 5) {
            $("#option5AmountOfLines").val('1')
        }
    }
    function fillOption() {
        const optionElement = $(`#option${_optionNumber}CreatedQuestions`)
        loadingBtn(optionElement)
        if (_optionNumber === 3) loadingBtn($("#option3CreatedAnswers"))

        $.post(includeThisFile(), { searchOption: _optionNumber, examId: _examInfo.id },
            function (data, textStatus, jqXHR) {
                resetButton()
                if (data.response) {
                    const dataInfo = data.data
                    if (dataInfo[0]) {
                        optionElement.html("")
                        dataInfo[0].forEach(el => {
                            let correctAnswer
                            if (_optionNumber === 1) {
                                hasCorrectAnswer = true
                                correctAnswer = el.respuesta
                            } else if (_optionNumber === 2) {
                                hasCorrectAnswer = true
                                correctAnswer = el.correcta
                            } else if (_optionNumber === 3) {
                                hasCorrectAnswer = true
                                correctAnswer = el.respuesta_c
                            } else {
                                hasCorrectAnswer = false
                            }
                            const html = `<li class="list-group-item item" data-id="${el.id}">
                   <span class="text-monospace itemTitle">${el.pregunta}</span>
                            ${_optionNumber === 2 ? `${function () {
                                    let answers = '';
                                    for (let index = 1; index <= 8; index++) {
                                        answers += `<input type='hidden' class='itemAnswer${index}' value='${el[`respuesta${index}`]}'>`
                                    }
                                    return answers
                                }()}` : ''}
                            ${_optionNumber === 4 ? `${function () {
                                    let answers = '';
                                    let amount = 0;
                                    for (let index = 1; index <= 5; index++) {
                                        if (el[`respuesta${index}`] !== '') {
                                            answers += `<input type='hidden' class='itemAnswer${index}' value='${el[`respuesta${index}`]}'>`
                                            amount++
                                        }
                                    }

                                    return `<input type='hidden' class='itemAnswerAmount' value='${amount}'> ${answers}`
                                }()}` : ''}
                            ${_optionNumber === 5 ? `<input type='hidden' class='itemLines' value='${el.lineas}'>` : ''}
                   <span class="float-right">
                       ${hasCorrectAnswer ? `<span class="badge badge-secondary">respuesta: <span class="itemAnswer">${correctAnswer}</span></span>` : ''}
                       <span class="badge badge-secondary">valor: <span class="itemValue">${el.valor}</span></span>
                       <a href="#" class="badge badge-info text-light editQuestion" data-id="${el.id}">Editar</a>
                       <a href="#" class="badge badge-danger text-light deleteQuestion" data-id="${el.id}">Borrar</a>
                   </span>
               </li>`
                            optionElement.append(html)
                        });


                    }

                    if (dataInfo[1]) {
                        $("#option3CreatedAnswers,#option3Answer").html("")
                        dataInfo[1].forEach(el => {
                            const html = `<li class="list-group-item item" data-id="${el.id}">
                   <span class="text-monospace itemTitle">${el.respuesta}</span>
                   <span class="float-right">
                       <span class="badge badge-secondary">id: ${el.id}</span>
                       <a href="#" class="badge badge-info text-light editAnswer" data-id="${el.id}">Editar</a>
                       <a href="#" class="badge badge-danger text-light deleteAnswer" data-id="${el.id}">Borrar</a>
                   </span>
               </li>`
                            const option = `<option value='${el.id}'>${el.respuesta}</option>`
                            $("#option3CreatedAnswers").append(html)
                            $("#option3Answer").append(option)
                        });


                    } else {

                        $("#option3CreatedAnswers").html("<h3 class='text-center text-muted'>No hay preguntas creadas</h3>")
                    }

                } else {
                    optionElement.html("<h3 class='text-center text-muted'>No hay preguntas creadas</h3>")
                }
            }, 'json');

    }

    function loadMenu() {
        loadingBtn($('.amount,.value'))
        $("#menuButtons button,#settingsButtons button,#title").prop("disabled", true)
        let examTotalAmount = 0;
        let examTotalValue = 0;
        $.post(includeThisFile(), {
            'menu': _examInfo.id
        }, function (data) {
            const menuValues = $.parseJSON(data)
            menuValues.forEach((menu, index) => {
                examTotalAmount += menu.amount;
                examTotalValue += menu.value;
                const option = index + 1
                $(`.option${option}`).children().children('.amount').text(`cantidad: ${menu.amount}`)
                $(`.option${option}`).children().children('.value').text(`valor: ${menu.value}`)
            });

            $("#examTotalAmount").text(`cantidad: ${examTotalAmount}`)
            $("#examTotalValue").text(`valor: ${examTotalValue}`)
            // Enabled buttons and title
            $("#title").prop("disabled", false).val(_examInfo.titulo)
            $("#menuButtons button,#settingsButtons button").prop("disabled", false)
            updateExamTotal(examTotalValue)
        });
    }

    function updateExamTotal(totalValue) {
        $.post(includeThisFile(), { examTotal: _examInfo.id, totalValue });
    }
});