$(document).ready(function () {
   
console.log('yes')
   //continuar debe de haber seleccionado al menos uno
   $("form").submit(function (event) {
      event.preventDefault();
      if ($(`[type='checkbox'].check:checked`).length === 0) {   
         event.preventDefault();
         console.log('Debe de seleccionar uno');
         $('.alert').removeClass('invisible')
         tableDataToSubmit("#form", dataTable[0], 'class[]')

      }

   });

   $('.alert button').click(function () {

      $(this).parent().addClass('invisible');

   });

   
});
