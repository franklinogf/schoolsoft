$(document).ready(function () {
    $("#option").change(function (){
        console.log($(this).val())
        if($(this).val() === 'student'){
            $("#student").show()
            $("select[name='student']").prop('required', true);
            $("#grade").hide()
        }else{
            $("#student").hide()
            $("select[name='student']").prop('required', false);
            $("#grade").show()
        }
    })
});