$(function () {
  $("#class").change(function (event) {
    if ($(this).find(":selected").data("verano")) {
      $("#tri").prop("readonly", true).val("Verano");
      $("#tra").prop("readonly", true).val("V-Nota");
    } else {
      $("#tri").prop("readonly", false).val("");
      $("#tra").prop("readonly", false).val("");
    }
  });
});
