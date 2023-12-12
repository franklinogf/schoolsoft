$(document).ready(function () {
    $("#option").change(function (){
        console.log($(this).val())
        if($(this).val() === 'student'){
            $("#student").show()
            $("#grade").hide()
        }else{
            $("#student").hide()
            $("#grade").show()
        }
    })
});