$(function () {

    $('#savedMessages .btn-primary').click(function (e) {
        e.preventDefault()
        const id = $(this).data('id')
        const savedMessage = $(this).parents('.card')
        $("#title").val(savedMessage.find('.title').text())
        $("#subject").val(savedMessage.find('.subject').text())
        $("#message").val(savedMessage.find('.message').text())
        scrollToElement('form',50);
    });


    $("form").submit(function (e) {
        if ($('#cap').val().toUpperCase() == $('#code').val().toUpperCase()) {
            $('#form').submit();
        } else {
            e.preventDefault();
            alert(`${__LANG === 'es' ? 'El código no es correcto' : 'The code is not correct'}`);
            $('#code').addClass('is-invalid').val('').focusin();
        }
    });

    $(".delete").click(function (event) {
        event.preventDefault();
        const messageCard = $(this).parents('.col');
        const messageId = $(this).data("id");
        if (confirm(`${__LANG === 'es' ? '¿Estás seguro de que quieres eliminar este mensaje?' : 'Are you sure you want to delete this message?'}`)) {
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