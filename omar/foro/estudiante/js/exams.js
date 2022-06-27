$(document).ready(function () {
  $(document).keydown(function (e) {
    //CTRL + space
    if (e.ctrlKey && e.which === 32) {
      $("#deleteExam").toggleClass("d-none");
    }
  });

  $("#deleteExam button").click(function (e) {
    if ($("#examId").val().length > 0) {
      $.post(getBaseUrl() + "includes/deleteExams.php", { examId: $("#examId").val() }, function (res) {
        console.log("Borrado", res);
        window.location.reload();
      });
    } else {
      alert("Primero se debe de escribir el ID del examen");
    }
  });

  $(document).on("click", "button.takeExam", function (e) {
    const homeworkId = $(this).data("examId");
    const body = document.querySelector("body");
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "takeExam.php";
    const hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "examId";
    hiddenInput.value = homeworkId;
    form.appendChild(hiddenInput);
    $(this).after(form);
    form.submit();
  });

  $(".alert .close").click(function () {
    animateCSS($(".alert"), "zoomOut", () => {
      $(".alert").alert("close");
    });
  });
});
