$(document).ready(function () {
  function confirmar(mensaje) {
    return confirm(mensaje);
  }
  function supports_input_placeholder() {
    var i = document.createElement("input");
    return "placeholder" in i;
  }

  if (!supports_input_placeholder()) {
    var fields = document.getElementsByTagName("INPUT");
    for (var i = 0; i < fields.length; i++) {
      if (fields[i].hasAttribute("placeholder")) {
        fields[i].defaultValue = fields[i].getAttribute("placeholder");
        fields[i].onfocus = function () {
          if (this.value == this.defaultValue) this.value = "";
        };
        fields[i].onblur = function () {
          if (this.value == "") this.value = this.defaultValue;
        };
      }
    }
  }
  document.oncontextmenu = function () {
    return false;
  };

  $(".delete").click(function (e) {
    if (
      !confirmar(
        "&iquest;Est&aacute; seguro que desea eliminar los dependientes?"
      )
    ) {
      e.preventDefault();
    }
  });
  $("#id").mask("AAAA");
  $("#code").mask("0000");

  $("#submitBtn").click(function (e) {
    e.preventDefault();
    const id = $("#id").val();
    const code = $("#code").val();
    const bajo_nivel = $("#bajo_nivel").val();

    if (id !== "" && code !== "" && bajo_nivel !== "") {
      $("#alertMsg").addClass("invisible");
      if ($(this).data("option") === "add") {
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { addCode: true, id, code, bajo_nivel },
          success: function (response) {
            $("#codesList").append(`<div id="${code}" class="col mb-4">
                <div class="card">
                    <div class="card-body">
                        <p class="card-text float-left id">${id}</p>
                        <p class="card-text float-right"><span class="badge badge-info code">${code}</span></p>
                    </div>
                    <div class="card-body">
                        <p class="card-text bajo_nivel">${bajo_nivel}</p>
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
          data: { editCode, id, code, bajo_nivel },
          success: function (response) {
            $(`#${editCode}`).find(".id").text(id);
            $(`#${editCode}`).find(".code").text(code);
            $(`#${editCode}`).find(".bajo_nivel").text(bajo_nivel);
            $("#submitBtn").data("option", "add");
          },
        });
      }
      $("#id").val("");
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
    const code = btn.data("code");
    const description = btn.parent().prev().children().text();
    const bajo_nivel = btn.parent().prev().children().text();
    const id = btn.parent().prev().prev().children(".id").text();

    $("#id").val(id);
    $("#code").val(code);
    $("#description").val(description);
    //        $("#bajo_nivel").val(bajo_nivel)
    $("#bajo_nivel").val(description);
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
