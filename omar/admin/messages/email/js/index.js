$(function () {
  $("#form").submit(function (e) {
    e.preventDefault();
    const fd = new FormData(this);
    if ($("#saveMessage").prop("checked")) {
      $.ajax({
        type: "POST",
        url: "./includes/savedMessage.php",
        data: {
          titulo: fd.get("title"),
          asunto: fd.get("subject"),
          mensaje: fd.get("title"),
        },
        dataType: "json",
        success: function (response) {
          $("#savedMessages tbody").prepend(`
                <tr class="cursor-pointer" data-id="${response.id}">
                    <td class="selectMessage">${response.titulo}</td>
                    <td class="selectMessage">${response.asunto}</td>
                    <td class="text-center"><button class="btn btn-sm btn-outline-danger delete">Borrar</button></td>
                 </tr>
                `);
        },
      });
    }
    $.ajax({
      type: "POST",
      url: "./includes/send.php",
      data: fd,
      dataType: "json",
      success: function (response) {
        Toast.fire(
          "Mensajes",
          `Enviados: ${response.sent}  <br/>No enviados: ${response.notSent}`,
          "success"
        );
        $("#form")[0].reset();
        $(".fileInput").remove();
      },
      error: function (error) {
        console.log(error);
      },
      cache: false,
      contentType: false,
      processData: false,
    });
  });

  $(document).on("click", ".selectMessage", function (e) {
    const tr = $(this).parents("tr");
    const id = tr.data("id");
    $.ajax({
      type: "GET",
      url: "./includes/savedMessage.php",
      data: { id },
      dataType: "json",
      success: function (response) {
        console.log({ response });
        $("#title").val(response.titulo);
        $("#subject").val(response.asunto);
        $("#message").val(response.mensaje);
        const form = $("form").offset().top - 20;
        window.scrollTo({ top: form, behavior: "smooth" });
      },
    });
  });

  $(document).on("click", ".delete", function (e) {
    e.preventDefault();
    const tr = $(this).parents("tr");
    const id = tr.data("id");

    ConfirmationAlert.fire({
      title:
        __LANG === "es"
          ? "Seguro que desea borrar este mensaje guardado?"
          : "Are you sure you want to delete this saved message?",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "DELETE",
          url: "./includes/savedMessage.php",
          data: { id },
          complete: function (response) {
            console.log({ response });
            Toast.fire(
              __LANG === "es" ? "Mensaje elimanado" : "Message deleted",
              "",
              "success"
            );
            tr.remove();
          },
        });
      }
    });
  });
});
