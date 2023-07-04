$(document).ready(function () {
    $("#addMemo").click(function (e) {
        $("#date").val(getDate());
        $("#deleteBtn").hide();
        $("#addMemoModal").modal('show');
    })
    $("#searchMemo").click(function (e) {
        e.preventDefault();
        const memoId = $("#memo").val()
        if (memoId !== '') {
            $("#addMemoOption").val('edit');
            $("#addMemoId").val(memoId);
            $("#addMemoStudentSs").val($("#student").val());
            $("#deleteBtn").show();
            $.ajax({
                type: "POST",
                url: includeThisFile(),
                data: { search: memoId },
                dataType: "json",
                success: function (memo) {
                    console.log(memo)
                    $("#title").val(memo.dpd)
                    $("#date").val(memo.fecha)
                    $("#teacher").val(memo.profesor)
                    $("#demerits").val(memo.demeritos)
                    $("#time").val(memo.hora)
                    $("#noRegistritation").prop('checked', memo.no_matricula === 'Si' ? true : false)
                    $("#absence").val(memo.falta)
                    $("#comment").val(memo.comentario)
                    $("#addMemoModal").modal('show');
                }
            });
        }
    });
    $("#deleteBtn").click(function (e) {
        e.preventDefault();
        if (confirm(__LANG === 'es' ? 'Esta seguro que desea eliminarlo' : "Are you sure you want to delete it?")) {
            $("#addMemoOption").val('delete');
            $("#addMemoForm").submit()
        }
    });
});