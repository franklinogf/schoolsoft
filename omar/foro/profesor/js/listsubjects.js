$(document).ready(function () {
   const _checkAll = ".checkAll";
   const _check = ".check";

   // check all
   $(_checkAll).click(function () {
      if ($(this).prop("checked")) {
         $(`${_check}, ${_checkAll}`).prop("checked", true);
      } else {
         $(`${_check}, ${_checkAll}`).prop("checked", false);
      }
   });

   $(".check").change(function () {
      if ($(`${_check}:checked`).length === 0) {
         $(`${_checkAll}`).prop("checked", false);
         $(`${_checkAll}`).prop("indeterminate", false);
      } else if ($(`${_check}:checked`).length === $(".check").length) {
         $(`${_checkAll}`).prop("indeterminate", false);
         $(`${_checkAll}`).prop("checked", true);
      } else {
         $(`${_checkAll}`).prop("indeterminate", true);
      }
   });

   // Datatable
   const classesTable = $(".classesTable").DataTable();
   

   $(".classesTable tbody").on("click", "tr", function () {
      const row = classesTable.row(this);
      if (row.index() !== undefined) {        
         const check = $(_check).eq(row.index());         
         check.prop('checked',!check.prop('checked'));
         check.change();
      }
   });
});
