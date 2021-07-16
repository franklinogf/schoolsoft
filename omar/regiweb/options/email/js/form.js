$(function () {
    $('#savedMessages .btn-primary').click(function (e) {
        e.preventDefault()
        const id = $(this).data('id')
        const savedMessage = $(this).parents('.card')
        $("#title").val(savedMessage.find('.title').text())
        $("#subject").val(savedMessage.find('.subject').text())
        $("#message").val(savedMessage.find('.message').text())
    });


    $("form").submit(function (e) {
        if ($('#cap').val().toUpperCase() == $('#code').val().toUpperCase()) {
            $('#form').submit();
        } else {
            e.preventDefault();
            alert("CODIGO CAPTCHA INCORRECTO");
            $('#code').addClass('is-invalid').val('').focusin();
        }
    });

    $(".delete").click(function (event) {
        event.preventDefault();
        const messageCard = $(this).parents('.col');
        const messageId = $(this).data("id");
        if (confirm("Esta seguro que desea borrar este mensaje?")) {
            $.ajax({
                type: "POST",
                url: includeThisFile(),
                data: { deleteMessage: messageId },
                complete: function (response) {
                    
                    console.log('messageCard:', messageCard)
                    animateCSS(messageCard, "zoomOutDown", () => {
                        animateCSS(messageCard.nextAll(), "slideInUp");                        
                        messageCard.remove();
                    })
                }
            });
        }
    });
});