$(document).ready(function () {
   const $modal = $("#myModal");
   const urlParams = new URLSearchParams(window.location.search);

   $("#editTopicBtn").click(e => {

      console.log('a: ', getBaseUrl("includes/viewTopic.php"));

      $.post(includeThisFile(), { topicById: urlParams.get('id') }, res => {

         if (res.response) {
            $modal.find("#id_topic").val(res.data.id)
            $modal.find("#modalTitle").val(res.data.titulo)
            $modal.find("#modalDescription").val(res.data.descripcion)
            $modal.find("#radio1").prop('checked', res.data.estado === 'a')
            $modal.find("#radio2").prop('checked', res.data.estado === 'c')
            $modal.find("#modalUntilDate").val(res.data.desde)
            $modal.modal('show')
         }
      },
         "json"
      );
   });

   $("#insertComment").click(e => {
      const $commentInput = $("#comment");
      const comment = $commentInput.val()

      if (comment.length > 0) {
         $commentInput.removeClass('is-invalid')
         $.post(includeThisFile(), { newComment: urlParams.get('id'), comment }, res => {
            $commentInput.val('')
            $("#commentsList").prepend(`<div class="media mt-3 pt-3 px-3 border-primary-gradient-top animated fadeInDown">
         <img src="${res.profilePicture}" class="align-self-center mr-3 rounded-circle" alt="profile picture" width="72" height="72">
         <div class="media-body">
           <h5 class="mt-0"><i class="fas fa-user-tie fa-xs"></i> ${res.fullName}</h5>
           <p class="m-0 p-2">${comment}</p>
           <p class="text-muted text-right">${res.date} ${res.time}</p>
         </div>
       </div>`)
         }, 'json');
      } else {
         $commentInput.removeClass('is-invalid').addClass('is-invalid');
      }
   });



});