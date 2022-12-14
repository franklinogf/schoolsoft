$(document).ready(function () {
    $("#grade").mask('AA-AA');
    let searched = false;

    $("#ss").keyup(function (e) {
        console.log(searched);
        if ($("#ss").val().length === 11) {
            if (!searched) {
                console.log("buscando ss");
                $.post(includeThisFile(), { searchSs: $("#ss").val() },
                    function (data, textStatus, jqXHR) {
                        searched = true;
                        if (data.exist) {
                            $("#ss").removeClass('is-valid').addClass('is-invalid')
                            $("#submit").prop('disabled', true)
                        } else {
                            $("#ss").removeClass('is-invalid').addClass('is-valid')
                            $("#submit").prop('disabled', false)

                        }
                    },
                    "json"
                );
            }
        } else {
            searched = false
        }

    })
    

    


   // Change profile picture
   let prevPicture = $('.profile-picture').prop('src');
   $("#pictureBtn").click(() => {
      $("#picture").click();
   });


   $('#picture').change(e => {
      if ($('#picture').val() !== '') {
         const valid = previewPicture(e.target, '.profile-picture');
         if (valid) {
            $('#pictureCancel').removeAttr('hidden');
         } else if (valid === false) {
            alert(__LANG === 'es' ? 'El archivo seleccionado no es una imagen' : 'The selected file is not an image');
         }
      } else {
         $('.profile-picture').prop('src', prevPicture);
      }

   })



   $('#pictureCancel').click(e => {
      $('.profile-picture').prop('src', prevPicture);
      $('#picture').val('');
      if (e.target.tagName === 'I') {
         $(e.target).parent().attr('hidden', true);
      } else {
         $(e.target).attr('hidden', true);
      }
   })

   function previewPicture(input, where) {
      if (input.files && input.files[0]) {
         const fileType = input.files[0]["type"];
         const validImageTypes = ["image/gif", "image/jpeg", "image/png", "image/jpg"];
         if (validImageTypes.includes(fileType)) {
            const reader = new FileReader();
            reader.onload = function (e) {
               $(where).prop('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            return true;
         }
         return false;
      }


      return undefined;

   }
});