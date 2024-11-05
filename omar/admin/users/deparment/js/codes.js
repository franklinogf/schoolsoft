$(document).ready(function () {
  $("#id").mask("AAAA");
  $("#code").mask("0000");

  $("#submitBtn").click(function (e) {
    e.preventDefault();
    let canSubmit = true;
    const code = $("#code").val();
    const description = $("#description").val();

    $(".code").each(function () {
      if ($(this).text() === code) {
        alert("El código ya existe");
        canSubmit = false;
      }
    });

    if (canSubmit && code !== "" && description !== "") {
      $("#alertMsg").addClass("invisible");
      if ($(this).data("option") === "add") {
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { addCode: true, code, description },
          success: function (response) {
            $("#codesList").append(`<div id="${code}" class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text float-right"><span class="badge badge-info code">${code}</span></p>
                    </div>
                    <div class="card-body">
                        <p class="card-text description">${description}</p>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary edit" data-code="${code}">${
              __LANG === "es" ? "Editar" : "Edit"
            }</button>
                        <button class="btn btn-danger del" data-code="${code}">${
              __LANG === "es" ? "Eliminar" : "Delete"
            }</button>
                    </div>
                </div>
            </div>`);
          },
        });
      } else {
        const editCode = $(this).data("code");
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { editCode, code, description },
          success: function (response) {
            $(`#${editCode}`).find(".code").text(code);
            $(`#${editCode}`).find(".description").text(description);
            $("#submitBtn").data("option", "add");
          },
        });
      }
      $("#code").val("");
      $("#description").val("");
    } else {
      canSubmit && $("#alertMsg").removeClass("invisible");
    }
  });

  // Edit code from list
  $("body").on("click", ".edit", function (e) {
    e.preventDefault();
    const btn = $(this);
    const code = btn.data("code");
    const description = btn.parent().prev().children().text();

    $("#code").val(code);
    $("#description").val(description);
    $("#submitBtn").data("option", "edit");
    $("#submitBtn").data("code", code);
  });
  // Delete code from list
  $("body").on("click", ".del", function (e) {
    e.preventDefault();
    const btn = $(this);
    if (
      confirm(
        __LANG === "es"
          ? "Esta seguro que desea eliminarlo"
          : "Are you sure you want to delete it?"
      )
    ) {
      const code = btn.data("code");
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { deleteCode: true, code },
        success: function (response) {
          btn.parents(".col").remove();
        },
      });
    }
  });
});
