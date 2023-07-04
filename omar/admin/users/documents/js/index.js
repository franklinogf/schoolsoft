$(document).ready(function () {
    $("#addDocument").click(function (e) {
        e.preventDefault();
        $("#addDocumentModal").modal('show');
        $("#date").val(getDate());
        $("#title").val('')
        $("#description").val('')
    })

    $("body").on('click', '.edit', function (e) {
        e.preventDefault()
        const btn = $(this);
        const card = btn.parents('.card');
        const id = btn.data('id')
        console.log(id)
        let date = card.find('.date').text()
        const title = card.find('.title').text()
        date = date.split('-')
        date = date[2] + '-' + date[1] + '-' + date[0]
        $("#title").val(title)
        $("#date").val(date)
        $("#addDocumentOption").val('edit')
        $("#addDocumentId").val(id)
        $("#addDocumentModal").modal('show');
    })
    //submit
    $("#submitBtn").click(function (e) {
        e.preventDefault();
        const title = $("#title").val()
        const date = $("#date").val()
        const file = $("#file").val()
        if ($("#addDocumentOption").val() === 'save') {
            if (title !== '' && date !== '' && file !== '') {
                $("#alertMsg").addClass('invisible')
                $("#addDocumentForm").submit()
            } else {
                $("#alertMsg").removeClass('invisible')
            }
        }else{
            
            if (title !== '' && date !== '') {
                $("#alertMsg").addClass('invisible')
                $("#addDocumentForm").submit()
            } else {
                $("#alertMsg").removeClass('invisible')
            }
        }
    })
    $("body").on('click', '.del', function (e) {
        e.preventDefault()
        const btn = $(this);
        if (confirm(__LANG === 'es' ? 'Esta seguro que desea eliminarlo' : "Are you sure you want to delete it?")) {
            const id = btn.data('id')
            $.ajax({
                type: "POST",
                url: includeThisFile(),
                data: { option: 'delete', addDocumentId: id },
                success: function (response) {
                    btn.parents('.col').remove();
                }
            });
        }
    })
});