$(document).ready(function () { 
   
  $(document).on('click', 'button.takeExam', function (e) {
    const homeworkId = $(this).data('examId')   
    const body = document.querySelector('body')
    const form = document.createElement('form');
    form.method = 'POST'
    form.action = "takeExam.php"
    const hiddenInput = document.createElement('input')
    hiddenInput.type = 'hidden'
    hiddenInput.name = 'examId'
    hiddenInput.value = homeworkId
    form.appendChild(hiddenInput)
    $(this).after(form)
    form.submit();


  })
  
$(".alert .close").click(function(){
  animateCSS( $('.alert'),'zoomOut',()=>{
    $(".alert").alert('close')
  })
})
 


});