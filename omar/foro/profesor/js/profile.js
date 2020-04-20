$(document).ready(function () {

   $('#pass1').change(() => {
      checkPasswords(1);
   });

   $('#pass2').change(() => {
      checkPasswords(2);
   });

   $('form').submit(event => {
      if(!checkPasswords(1)){
         event.preventDefault();
      }
   });

   
   function checkPasswords(id) {
      
      if ($('#pass' + (id === 1 ? '1' : '2')).val().length > 0) {
         if ($('#pass' + (id !== 1 ? '1' : '2')).val().length > 0) {
            if ($("#pass" + (id === 1 ? '1' : '2')).val() !== $('#pass' + (id !== 1 ? '1' : '2')).val()) {
               alert("Las claves deben de coincidir");
               $("#pass" + (id === 1 ? '1' : '2')).focus();
               return false
            }else{
               return true;
            }
         }
      }
      return true;
   }
   
   
});

