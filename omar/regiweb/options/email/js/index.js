$(function () {
    $('.options').click(function (e) {
        e.preventDefault()
        $('.alert').remove();
        const id = $(this).data('id')
        $(".option").addClass('hidden')
        $(".option select").prop('disabled', true)

        $("#value").removeClass('hidden')
        animateCSS($("#value"), 'slideInDown')
        $(`#${id}`).removeClass('hidden')
        $(`#${id} select`).prop('disabled', false)
        animateCSS($(`#${id}`), 'fadeIn')

    });

    
});