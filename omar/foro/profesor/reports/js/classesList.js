$(document).ready(function () {

   //continuar debe de haber seleccionado al menos uno
   $("form").submit(function (event) {
      if ($(`[type='checkbox'].check:checked`).length === 0) {
         event.preventDefault();
         $('.alert').removeClass('invisible')
      }
      tableDataToSubmit("form", dataTable[0], 'class[]')

   });

   $('.alert button').click(function () {
      $(this).parent().addClass('invisible');
   });


});
