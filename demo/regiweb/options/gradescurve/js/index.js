$(document).ready(function () {
  $("#savePoints").click(function () {
    const points = $("#points").val();
    const trimester = $("#trimester").val();
    const course = $("#course").val();
    const value = $("#value").val();

    const data = {
      points,
      trimester,
      course,
      value,
    };
    console.log({ data });
    $.ajax({
      url: "./includes/index.php",
      type: "PUT",
      data: data,
      dataType: "json",
      success: function (data) {
        console.log(data);
        if (data.status === "success") {
          Toast.fire("Puntos guardados", "", "success");
        } else {
          Toast.fire("Error", "No se pudieron guardar los puntos", "error");
        }
      },
      error: function () {
        Toast.fire("Error", "No se pudieron guardar los puntos", "error");
      },
    });
  });
});
