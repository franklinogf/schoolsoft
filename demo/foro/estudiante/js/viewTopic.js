$(document).ready(function () {  
   const urlParams = new URLSearchParams(window.location.search);
   const _class = sessionStorage.getItem('class');
   sessionStorage.clear('class');

   $("#back").click(function(){
      sessionStorage.setItem("class", _class);
   })
   
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
           <h5 class="mt-0"><i class="fas fa-user-graduate fa-xs"></i> ${res.fullName}</h5>
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