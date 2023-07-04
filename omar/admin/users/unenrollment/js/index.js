$(document).ready(function () {
    $("#save").click(function (e) {
        e.preventDefault();
        const date = $("#unerollmentDate").val()
        const code = $("#unerollmentCode").val()
        const studentSS = $("#studentSS").val()
        console.log(date)
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: {unenroll: studentSS,date,code},
            success: function (response) {
                console.log(response)
            }
        });

    })

    $("#unerollmentCode").change(function (e) {
        if ($(this).val() === '') {
            $("#unerollmentDate").val('')
            $("#unerollmentDate").prop('disabled', true)
        } else {
            $("#unerollmentDate").prop('disabled', false)
        }
    })
});