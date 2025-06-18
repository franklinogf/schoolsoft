$(document).ready(function () {
    $(".studentCheckbox").change(function (e) {
        if ($(this).prop('checked')) {
            $(this).parents('.card').addClass('border-info')
        } else {
            $(this).parents('.card').removeClass('border-info')
        }
    })


    $("#reEnrollmentForm").submit(function (e) {
        e.preventDefault()
        let formData = new FormData(this)
        formData.append('reEnrollment', true)
        $.ajax({
            url: includeThisFile(),
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            // success: function (response) {
            //     console.log(response)
            // }
        });       
    })
});