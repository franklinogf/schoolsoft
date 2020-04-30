$(document).ready(function () {
      
$(".delHomework").click((e) =>{
  if(confirm("¿Esta seguro de que desea borrar esta tarea?")){
   $(e.target).parents('.homework').hide('drop',{ direction: "down" },500,()=>{
      $(e.target).tooltip('hide')
      $(e.target).parents('.homework').remove();
   });
  }
})



});