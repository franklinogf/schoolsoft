$(document).ready(function () {
   

   //continuar debe de haber seleccionado al menos uno
   $("form").submit(function (event) {
      if ($(`[type='checkbox'].check:checked`).length === 0) {   
         event.preventDefault();
         console.log('Debe de seleccionar uno');
         $('.alert').removeClass('invisible')

      }

   });

   $('.alert button').click(function () {

      $(this).parent().addClass('invisible');

   });

   
});
