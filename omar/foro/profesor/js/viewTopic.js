$(document).ready(function () {
   const $modal = $("#myModal");
   const urlParams = new URLSearchParams(window.location.search);

   const _class = sessionStorage.getItem('class');
   sessionStorage.clear('class');

   $("#back").click(function(){
      sessionStorage.setItem("class", _class);
   })

   $("#editTopicBtn").click(e => {
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
            // `<button data-comment-id="${res.id}" class="btn btn-sm btn-danger mb-3 d-block ml-auto delComment">Borrar <i class="fas fa-trash-alt fa-sm"></i></button>`
            $("#commentsList").prepend(`
            <div class="media mt-3 pt-3 px-3 border-primary-gradient-top animated fadeInDown">
               <img src="${res.profilePicture}" class="align-self-center mr-3 rounded-circle" alt="profile picture" width="72" height="72">
               <div class="media-body">
                  <h5 class="mt-0"><i class="fas fa-user-tie fa-xs"></i> ${res.fullName}</h5>
                  <p class="m-0 p-2 text-break">${comment}</p>
                  <p class="text-muted text-right">${res.date} ${res.time}</p>                  
               </div>
            </div>`)
         }, 'json');
      } else {
         $commentInput.removeClass('is-invalid').addClass('is-invalid');
      }
   });

   $(document).on('click', '.delTopic', function (e) {
      const topicId = $(this).data('topicId')
      if (confirm('¿Seguro que quiere borrar este Tema?')) {
         $.post(includeThisFile(), { delTopic: topicId }, () => {
            window.location.href = getBaseUrl('topics.php')
            
         });
      }
   })

   $(document).on('click', '.delComment', function (e) {
      const commentId = $(this).data('commentId')
      if (confirm('¿Seguro que quiere borrar este comentario?')) {
         $.post(includeThisFile(), { delComment: commentId }, () => {
            animateCSS($(this).parents('.media'), 'zoomOut', () => {
               $(this).parents('.media').remove()
            });
         });
      }
   })



});