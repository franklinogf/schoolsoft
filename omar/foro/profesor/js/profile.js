$(document).ready(function () {
   let prevPicture = $('.profile-picture').prop('src');

  
   // Change profile picture
   $("#pictureBtn").click(() => {
      $("#picture").click();
   });


   $('#picture').change(e =>{      
      previewPicture(e.target,'.profile-picture');
      $('#pictureCancel').removeAttr('hidden');
   })
   $('#pictureCancel').click(e => {
      $('.profile-picture').prop('src',prevPicture);
      $('#picture').val('');
      if(e.target.tagName === 'I'){
         $(e.target).parent().attr('hidden',true);
      }else{
         $(e.target).attr('hidden',true);
      }
   })

   // check passwords to submit 

   $('#pass1').change(() => {
      checkPasswords(1);
   });

   $('#pass2').change(() => {
      checkPasswords(2);
   });

   $('form').submit(event => {
      if (!checkPasswords(1)) {
         event.preventDefault();
      }
   });

   // functions
   function previewPicture(input,where) {      
      if (input.files && input.files[0]) {
          var reader = new FileReader();          
          reader.onload = function (e) {
              $(where).prop('src', e.target.result);
          }          
          reader.readAsDataURL(input.files[0]);
      }
  }


   function checkPasswords(id) {

      if ($('#pass' + (id === 1 ? '1' : '2')).val().length > 0) {
         if ($('#pass' + (id !== 1 ? '1' : '2')).val().length > 0) {
            if ($("#pass" + (id === 1 ? '1' : '2')).val() !== $('#pass' + (id !== 1 ? '1' : '2')).val()) {
               alert("Las claves deben de coincidir");
               $("#pass" + (id === 1 ? '1' : '2')).focus();
               return false
            } else {
               return true;
            }
         }
      }
      return true;
   }


});

