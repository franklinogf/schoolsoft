$(document).ready(function () {
  
  // document.documentElement.requestFullscreen()


  $("#startExam").click(function () {
    if (document.fullscreenElement) {
      // exitFullscreen is only available on the Document object.
      document.exitFullscreen();
    } else {
      if(confirm("Va a empezar el examen, si sale de la pantalla completa se tomara como realizado")){
        document.documentElement.requestFullscreen();
        $(this).remove()
      }
    }

  })
  document.addEventListener("fullscreenchange", function(e) {
    if (document.fullscreenElement) {
      console.log(`Entered fullscreen mode.`);
      $(".blur").removeClass('blur')

    } else {
      alert("Se ha terminado el examen")
      console.log('Leaving full-screen mode.');
    }
  })



  
  $("form").submit(function (e) {
    if ($(this)[0].checkValidity() === false) {
      e.preventDefault()
      e.stopPropagation()
      $('.alert').removeClass('invisible')
      animateCSS( $('.alert'),'zoomIn')
    }

  })



});
