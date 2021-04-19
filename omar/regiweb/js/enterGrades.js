$(function () {

    /* ---------------------------------- vars ---------------------------------- */
    $(".loading").hide()
    const _cppd = !!+$("#cppd").val()
    $("input:disabled").prop('disabled', false);
    if (_cppd) {
        const _A = +$("#valueA").val()
        const _B = +$("#valueB").val()
        const _C = +$("#valueC").val()
        const _D = +$("#valueD").val()
        const _F = +$("#valueF").val()

        function NumberToDecimal($value) {
            if ($value >= _A) {
                return 4
            } else if ($value >= _B) {
                return 3
            } else if ($value >= _C) {
                return 2
            } else if ($value >= _D) {
                return 1
            } else if ($value >= _F) {
                return 0
            }
        }

        $(".grade").change(function (event) {
            let tpaTotal = 0
            let tdpTotal = 0
            const parentTr = $(this).parents('tr')
            const allGrades = parentTr.find('.grade')

            const tpa = parentTr.find('.tpa')
            const tdp = parentTr.find('.tdp')


            const totalGrade = parentTr.find('.totalGrade')

            $.each(allGrades, function (index, grade) {
                if ($(grade).val()) {
                    const tdpInput = $("#values").find(`#val${index + 1}`)
                    if (tdpInput.val()) {
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
        })

    } else {
        const _report = $("#report").val()
        const _noteType = $("#noteType1").prop('checked') ? 1 : 2
        $(".grade").change(function (event) {
            let tpaTotal = 0
            let tdpTotal = 0
            const parentTr = $(this).parents('tr')
            const allGrades = parentTr.find('.grade')

            const tdia = parentTr.find('.tdia')
            const tlib = parentTr.find('.tlib')
            const pcor = parentTr.find('.pcor')

            const tpa = parentTr.find('.tpa')
            const tdp = parentTr.find('.tdp')

            const _tdia = parentTr.find('._tdia')
            const _tlib = parentTr.find('._tlib')
            const _pcor = parentTr.find('._pcor')

            const totalGrade = parentTr.find('.totalGrade')

            $.each(allGrades, function (index, grade) {
                if ($(grade).val()) {
                    if (index !== allGrades.length - 1) {
                        const tdpInput = $("#values").find(`#val${index + 1}`)
                        if (tdpInput.val()) {
                            tpaTotal += +$(grade).val()
                            tdpTotal += tdpInput.val() ? +tdpInput.val() : 0
                        }
                    } else {
                        tpaTotal += +$(grade).val()
                    }
                }

            });
            // tpa
            tpaTotal += tdia.val() ? +tdia.val() : 0
            tpaTotal += tlib.val() ? +tlib.val() : 0
            tpaTotal += pcor.val() ? +pcor.val() : 0

            // tdp
            tdpTotal += _tdia.val() && tdia.val() ? +_tdia.val() : 0
            tdpTotal += _tlib.val() && tlib.val() ? +_tlib.val() : 0
            tdpTotal += _pcor.val() && pcor.val() ? +_pcor.val() : 0

            tpa.val(tpaTotal || '')
            tdp.val(tdpTotal || '')

            // Grade total 
            let gradeTotal
            if (_report === 'Notas') {
                tpaTotal += tdia.val() ? +tdia.val() : 0
                gradeTotal = (tpaTotal / tdpTotal) * 100
            } else {
                gradeTotal = _noteType === 2 ? tpaTotal : (tpaTotal / tdpTotal) * 100
            }
            totalGrade.val(Math.round(gradeTotal) || '')
        })

    }



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
                console.log(`Se ha actualizado el ${type}`)
            }
        })

    })





});