$(document).ready(function () {
   const _checkAll = ".checkAll";
   const _check = ".check";

   // check all
   $(_checkAll).change(function () {
      if ($(this).prop("checked")) {
         $(`${_check}, ${_checkAll}`).prop("checked", true);
      } else {
         $(`${_check}, ${_checkAll}`).prop("checked", false);
      }
   });

   $(_check).change(function () {
      if ($(`${_check}:checked`).length === 0) {
         $(`${_checkAll}`).prop("checked", false);
         $(`${_checkAll}`).prop("indeterminate", false);
      } else if ($(`${_check}:checked`).length === $(_check).length) {
         $(`${_checkAll}`).prop("indeterminate", false);
         $(`${_checkAll}`).prop("checked", true);
         $('.alert').addClass('invisible');
      } else {
         $('.alert').addClass('invisible');
         $(`${_checkAll}`).prop("indeterminate", true);
      }
   });

   //continuar debe de haber seleccionado al menos uno
   $("form").submit(function (event) {
      if ($(`${_check}:checked`).length === 0) {   
         event.preventDefault();
         console.log('Debe de seleccionar uno');
         $('.alert').removeClass('invisible')

      }

   });

   $('.alert button').click(function () {

      $(this).parent().addClass('invisible');

   });

   // Datatable
   $(".classesTable tbody").on("click", "tr", function () {
      const row = classesTable.row(this);
      if (row.index() !== undefined) {
         const check = $(_check).eq(row.index());
         check.prop('checked', !check.prop('checked'));
         check.change();
      }
   });
});
