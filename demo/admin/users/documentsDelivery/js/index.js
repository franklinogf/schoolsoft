$(document).ready(function () {
    $("#documents").submit(function (e) {
        e.preventDefault();
        const thisForm = $(this)[0];
        const formData = new FormData(thisForm)

        let data = {}
        for (let pair of formData.entries()) {
            data[pair[0]] = pair[1];
        }
        console.log(data)
        $(".alert").alert('close')
        $.ajax({
            type: "POST",
            url: includeThisFile(),
            data: { saveDocument: true, ...data },
            // dataType: "json",
            complete: function (response) {
                console.log(response)
                $(thisForm).append(`<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                Se ha guardado correctamente
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>`)
            }
        });
    })
});
