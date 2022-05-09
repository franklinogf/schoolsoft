$(document).ready(function () {
    $(".download").click(function(event) {
        event.preventDefault();
        const documentId = $(this).data("id");
        const documentTitle = $(this).parent().parent().children('.card-header').text()
        const link = $(this).prop("href");
        $.post(includeThisFile(),{saveHistory:documentId,documentTitle},function(data){
            location.href = link;
        });
    });
});