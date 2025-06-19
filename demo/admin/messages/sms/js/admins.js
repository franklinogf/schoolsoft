$(document).ready(function () {
  $("#form").submit(function (e) {
    tableDataToSubmit("#form", dataTable[0], "admins[]");
  });
});
