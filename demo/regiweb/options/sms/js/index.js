$(function () {
    let option
    $('.options').on('click',function (e) {
        e.preventDefault()
        $('.alert').remove();
        option = $(this).data('id')
        $(".option").addClass('hidden')
        $(".option select").prop('disabled', true)

        $("#value").removeClass('hidden')
        animateCSS($("#value"), 'slideInDown')
        $(`#${option}`).removeClass('hidden')
        $(`#${option} select`).prop('disabled', false)
        animateCSS($(`#${option}`), 'fadeIn')
        $(".check,.checkAll").prop('checked', false)
        $(".custom-select").val('');


    });

    $("#form").submit(function(e){
        if (option === 'students') {
            tableDataToSubmit("#form", dataTable[0], 'students[]')
        } else if (option === 'classes') {
            tableDataToSubmit("#form", dataTable[1], 'classes[]')
        }else{
            $('#phoneNumber').val($("#phoneNumber").cleanVal())
        }

    })
    

    
});