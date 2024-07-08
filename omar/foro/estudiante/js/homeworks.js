$(document).ready(function () {
  const $modal = $("#myModal");
  const $progressModal = $("#progressModal");
  let homeworkId = null;
  let doneHomeworkId = null;

  $("#myModal form").submit(function (e) {
    e.preventDefault();

    const fd = new FormData(this);
    const files = $('[name="file[]"]');
    // append files
    files.map((input) => {
      fd.append("file[]", input.files);
    });
    // send messages
    if (!doneHomeworkId) {
      console.log("Nueva tarea");
      fd.append("doneHomework", homeworkId);
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: fd,
        contentType: false,
        cache: false,
        processData: false,
        xhr: function () {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function (e) {
            // For uploads
            if (e.lengthComputable) {
              $("#progressModal").modal("show");
              let progress = Math.round((e.loaded / e.total) * 100);
              $("#progressModal .progress-bar")
                .prop("aria-valuenow", progress)
                .css("width", progress + "%")
                .text(progress + "%");
            }
          };
          return xhr;
        },
        complete: function (res) {
          console.log("response:", res);
          $status = $(`.homework.${homeworkId}`).find(".fa-circle");
          $status.removeClass("text-white").addClass("text-success");
          animateCSS($status, "fadeIn slow");
          var MyInterval = setInterval(function () {
            console.log(
              "progress now:",
              $("#progressModal .progress-bar").prop("aria-valuenow")
            );
            if (
              $("#progressModal .progress-bar").prop("aria-valuenow") == 100
            ) {
              clearInterval(MyInterval);
              console.log("hola", $("#progressModal").modal("hide"));
              $("#progressModal").modal("hide");
              $modal.modal("hide");
            }
          }, 500);

          // $(`.sendHomework[data-homework-id=${homeworkId}]`).prop({disabled:true,ariaDisabled:true})
        },
      });
    } else {
      console.log("editar tarea");
      fd.append("editDoneHomework", doneHomeworkId);
      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: fd,
        contentType: false,
        cache: false,
        processData: false,
        xhr: function () {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function (e) {
            // For uploads
            if (e.lengthComputable) {
              $("#progressModal").modal("show");
              let progress = Math.round((e.loaded / e.total) * 100);
              $("#progressModal .progress-bar")
                .prop("aria-valuenow", progress)
                .css("width", progress + "%")
                .text(progress + "%");
            }
          };

          return xhr;
        },
        complete: function (res) {
          var MyInterval = setInterval(function () {
            console.log(
              "progress now:",
              $("#progressModal .progress-bar").prop("aria-valuenow")
            );
            if (
              $("#progressModal .progress-bar").prop("aria-valuenow") == 100
            ) {
              clearInterval(MyInterval);
              console.log("hola", $("#progressModal").modal("hide"));
              $("#progressModal").modal("hide");
              $modal.modal("hide");
            }
          }, 500);
        },
      });
    }
  });

  $(document).on("click", "button.sendHomework", function (e) {
    homeworkId = $(this).data("homeworkId");
    $modal.modal("show");

    $.post(
      includeThisFile(),
      { getDoneHomeworkById: homeworkId },
      (res) => {
        if (res.response) {
          doneHomeworkId = res.data.id;
          res.files.map((file) => {
            addExistingFile(fileRealName(file.nombre), file.id);
          });
          $("#note").val(res.data.nota);
        }
      },
      "json"
    );
  });

  $(document).on("click", "button.delExistingFile", function (e) {
    const fileId = $(this).data("fileId");

    if (
      confirm(
        __LANG === "es"
          ? "¿Seguro que quiere eliminar este archivo?"
          : "Are you sure you want to delete this file?"
      )
    ) {
      $.post(includeThisFile(), { delExistingFile: fileId }, () => {
        animateCSS($(this).parents(".input-group"), "zoomOut", () => {
          $(this).parents(".input-group").remove();
        });
      });
    }
  });

  $modal.on("hidden.bs.modal", function (e) {
    doneHomeworkId = null;
  });
});
