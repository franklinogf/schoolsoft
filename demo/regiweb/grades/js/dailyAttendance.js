$(function () {
    $("#option").change(function (event) {
        if ($(this).val() === 'home') {
            $("#infoType").removeClass('hidden').prop('disabled',false)
            $("#infoStudents").addClass('hidden').prop('disabled',true)
            
        }else{
            $("#infoType").addClass('hidden').prop('disabled',true)
            $("#infoStudents").removeClass('hidden').prop('disabled',false)
        }
    })   

});