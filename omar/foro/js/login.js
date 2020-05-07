$(document).ready(function () {
   

setInterval(() => {
   animateCSS('img','jello');
}, 5000);

$("form").submit(function(e){
   if($("#username").val().length === 0){
      $("#username").addClass('is-invalid')
      e.preventDefault()
   }
   if($("#password").val().length === 0){
      $("#password").addClass('is-invalid')
      e.preventDefault()
   }
})

});