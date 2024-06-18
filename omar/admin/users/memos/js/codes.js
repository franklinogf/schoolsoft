$(document).ready(function () {
  $("#value").mask("000");
  // $("#code").mask('0000')

  $("#submitBtn").click(function (e) {
    e.preventDefault();
    const value = $("#value").val();
    const code = $("#code").val();
    const description = $("#description").val();

    if (code !== "" && description !== "") {
      $("#alertMsg").addClass("invisible");
      if ($(this).data("option") === "add") {
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { addCode: true, value, code, description },
          success: function (response) {
            console.log(response);
            const id = response.id;
            $("#codesList").append(`<div class="col mb-4">
                <div id="${id}" class="card h-100">
                    <div class="card-body">
                        <p class="card-text float-right"><span class="badge text-bg-info value">${value}</span></p>
                        <p class="card-text code">${code}</p>
                    </div>
                    <div class="card-body">
                        <p class="card-text description">${description}</p>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary edit" data-id="${id}">${
              __LANG === "es" ? "Editar" : "Edit"
            }</button>
                        <button class="btn btn-danger del" data-id="${id}">${
              __LANG === "es" ? "Eliminar" : "Delete"
            }</button>
                    </div>
                </div>
            </div>`);
          },
        });
      } else {
        const editCode = $(this).data("id");
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { editCode, value, code, description },
          success: function (response) {
            console.log(response);
            $(`#${editCode}`).find(".value").text(value);
            $(`#${editCode}`).find(".code").text(code);
            $(`#${editCode}`).find(".description").text(description);
            $("#submitBtn").data("option", "add");
          },
        });
      }
      $("#value").val("");
      $("#code").val("");
      $("#description").val("");
    } else {
      $("#alertMsg").removeClass("invisible");
    }
  });

  // Edit code from list
  $("body").on("click", ".edit", function (e) {
    e.preventDefault();
    const btn = $(this);
    const id = btn.data("id");
    const card = btn.parents(".card");
    const description = card.find(".description").text();
    const value = card.find(".value").text();
    const code = card.find(".code").text();

    $("#value").val(value);
    $("#code").val(code);
    $("#description").val(description);
    $("#submitBtn").data("option", "edit");
    $("#submitBtn").data("id", id);
    $("html").animate(
      {
        scrollTop: $("body").offset().top,
      },
      500,
      () => {
        animateCSS(".container", "pulse");
      }
    );
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
      const id = btn.data("id");
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { deleteCode: id },
        success: function (response) {
          btn.parents(".col").remove();
        },
      });
    }
  });
});
