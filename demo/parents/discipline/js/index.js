$(document).ready(function () {
    $("#accept").change(function (e) {
        if ($(this).prop('checked')) {
            $("#gradesBtn").prop('disabled', false)
        } else {
            $("#gradesBtn").prop('disabled', true)
        }
    })
});