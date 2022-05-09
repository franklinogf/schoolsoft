$(function () {

    /* ---------------------------------- vars ---------------------------------- */
    $(".loading").hide()
    const _cppd = !!+$("#optionCppd").val()
    const _sumTrimester = !!+$("#sumTrimester").val()
    const _trimester = $("#trimester").val()
    let _letter = $("#letra").prop('checked')
    const _report = $("#report").val()
    const _subjectCode = $("#subject").val()
    const _noteType = $("#noteType1").prop('checked') ? 1 : 2
    const _allGrades = $(".table tbody tr")
    if ($("#save")[0]) $("input:disabled").prop('disabled', false)

    init()

    $(".grade").change(function (event) {
        const parentTr = $(this).parents('tr')
        calculate(parentTr, _cppd)
    })

    // save values with ajax
    $("#values input").change(function (event) {
        const thisValue = $(this).val()
        const type = $(this).prop('id')
        const valueId = $('#valueId').val()
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: {
                changeValue: type,
                value: thisValue,
                id: valueId
            },
            dataType: "json",
            complete: function (response) {
                init()
                console.log(`Se ha actualizado el ${type}`)
            }
        })

    })
    $(document).ajaxStart(function () {
        $(this).html("<img src='demo_wait.gif'>");
    });
    $("#form").submit(function (event) {
        event.preventDefault();
        loadingBtn($("#form .btn"), '', 'Guardando...')
        $(".grade").prop('readonly', true);
        const thisForm = $(this)[0];
        const formData = new FormData(thisForm)
        let data = {}
        for (let pair of formData.entries()) {
            if (!(pair[0] in data)) {
                data[pair[0]] = []
            }
            data[pair[0]].push(pair[1])
        }
        console.log(data)

        $.ajax({
            type: "POST",
            url: $(this).prop('action'),
            data: {
                submitForm: 'Notas',
                data: data
            },
            // dataType: "json",
            complete: function (response) {
                $(".grade").prop('readonly', false);
                loadingBtn($("#form .btn"), 'Guardar')
                console.log(response)
                init()
            }
        });
    })

    // options
    $("#options input").change(function (event) {
        const thisOption = $(this).prop('checked')
        const type = $(this).prop('id')
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: {
                changeOption: type,
                value: thisOption,
                subjectCode: _subjectCode,
            },
            dataType: 'json',
        })


    })



    $("#letra").change(function () {
        _letter = $("#letra").prop('checked')
        if (_letter) {
            $("#optionLetter").val($("#letra").val())
        } else {
            $("#optionLetter").val('0');
        }
        init()
    })
    $("#pal").change(function () {

        init()
    })


    /* -------------------------------- functions ------------------------------- */
    function init() {
        $.each(_allGrades, function (index, tr) {
            calculate($(tr), _cppd)
        });

    }
    // option convert number to letters
    function NumberToLetter(value) {
        if (!isNaN(value) && value !== '') {
            if (value >= 90) {
                return 'A'
            } else if (value >= 80) {
                return 'B'
            } else if (value >= 70) {
                return 'C'
            } else if (value >= 60) {
                return 'D'
            } else {
                return 'F'
            }
        }
        return value
    }
    function isString(value) {
        return (/[a-zA-Z]/).test(value)
    }
    function calculate(parentTr, cppd = false) {
        const parentAllGrades = parentTr.find('.grade')
        const tpa = parentTr.find('.tpa')
        const tdp = parentTr.find('.tdp')
        const totalGrade = parentTr.find('.totalGrade')
        let tpaTotal = _sumTrimester && (_trimester === 'Trimestre-2' || _trimester === 'Trimestre-4') ? +parentTr.find('._tpaTotal').val() : null
        let tdpTotal = _sumTrimester && (_trimester === 'Trimestre-2' || _trimester === 'Trimestre-4') ? +parentTr.find('._tdpTotal').val() : 0

        if (cppd) {
            const _A = +$("#valueA").val()
            const _B = +$("#valueB").val()
            const _C = +$("#valueC").val()
            const _D = +$("#valueD").val()
            const _F = +$("#valueF").val()

            function NumberToDecimal(value) {
                if (value >= _A) {
                    return 4
                } else if (value >= _B) {
                    return 3
                } else if (value >= _C) {
                    return 2
                } else if (value >= _D) {
                    return 1
                } else if (value >= _F) {
                    return 0
                }
            }

            $.each(parentAllGrades, function (index, grade) {
                if ($(grade).val()) {
                    const tdpInput = $("#values").find(`#val${index + 1}`)
                    if (tdpInput.val() && !isString($(grade).val())) {
                        tpaTotal += NumberToDecimal(+$(grade).val())
                        tdpTotal += 1
                    }
                }

            });

            tpa.val(tpaTotal || '')
            tdp.val(tdpTotal || '')

            // Grade total             
            const gradeTotal = +(tpaTotal / tdpTotal)
            totalGrade.val(gradeTotal ? gradeTotal.toFixed(2) : '')
        } else {

            const tdia = parentTr.find('.tdia')
            const tlib = parentTr.find('.tlib')
            const pcor = parentTr.find('.pcor')

            const _tdia = parentTr.find('._tdia')
            const _tlib = parentTr.find('._tlib')
            const _pcor = parentTr.find('._pcor')

            $.each(parentAllGrades, function (index, grade) {
                if ($(grade).val() !== '') {
                    if (index !== parentAllGrades.length - 1) {
                        const tdpInput = $("#values").find(`#val${index + 1}`)
                        if (tdpInput.val() && !isString($(grade).val())) {
                            tpaTotal += +$(grade).val()
                            tdpTotal += tdpInput.val() ? +tdpInput.val() : 0
                            console.log('tdpTotal:', tdpTotal)
                        }
                    } else {
                        tpaTotal += +$(grade).val()
                    }
                }

            });
            // tpa
            if (tdia.val()) {
                tpaTotal += +tdia.val()
            }
            if (tlib.val()) {
                tpaTotal += +tlib.val()
            }
            if (pcor.val()) {
                tpaTotal += +pcor.val()
            }
            // tpaTotal += tdia.val() ? +tdia.val() : 0
            // tpaTotal += tlib.val() ? +tlib.val() : 0
            // tpaTotal += pcor.val() ? +pcor.val() : 0

            // tdp
            tdpTotal += _tdia.val() && tdia.val() ? +_tdia.val() : 0
            tdpTotal += _tlib.val() && tlib.val() ? +_tlib.val() : 0
            tdpTotal += _pcor.val() && pcor.val() ? +_pcor.val() : 0
            tpa.val(tpaTotal !== '' ? tpaTotal : '')
            tdp.val(tdpTotal || '')

            // Grade total 
            let gradeTotal
            if (_report === 'Notas') {
                gradeTotal = (tpaTotal / tdpTotal) * 100
            } else {
                gradeTotal = _noteType === 2 ? tpaTotal : (tpaTotal / tdpTotal) * 100
            }
            totalGrade.val(isFinite(gradeTotal) ? Math.round(gradeTotal) : '')
            if (_letter) {
                const numberLetter = +$("#letra").val() - 1;
                const grade = parentTr.find('.grade').eq(numberLetter)
                const notLetterGrades = parentTr.find(`.grade`).not(grade)
                // check if is letters
                if (isString(grade.val())) {
                    if (grade.val()) {
                        totalGrade.val(grade.val())
                        notLetterGrades.prop('readonly', true)
                    }
                } else {
                    notLetterGrades.prop('readonly', false)
                }
            } else {
                _allGrades.find('.grade').prop('readonly', false);
            }
        }
        if ($('#pal').prop('checked')) {
            $.each($(".totalGrade"), function (index, totalInput) {
                $(totalInput).val(NumberToLetter($(totalInput).val()))
            })
        }
    }
});