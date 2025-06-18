$(function () {

    $("#class").change(function (event) {
        if ($(this).find(':selected').data('verano')) {
            $("#tri").prop('disabled', true).val('Verano')
            $("#hiddenTri").val('Verano')
            $("#tra").prop('disabled', true).val('V-Nota')
            $("#hiddenTra").val('V-Nota')
        }else{
            $("#tri").prop('disabled', false).val('')
            $("#hiddenTri").val('')
            $("#tra").prop('disabled', false).val('')
            $("#hiddenTra").val('')
        }
    })

    $("#tri").change(function (event) { 
        console.log('changed')
        $("#hiddenTri").val($("#tri").val())
     })
    $("#tra").change(function (event) { 
        $("#hiddenTra").val($("#tra").val())
     })

});