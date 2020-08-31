$(document).ready(function () {
  let exit = false;
  let showFinished = true;
  let time = parseInt($("#timer").text());

  $("input,select,textarea").attr("disabled", true);

  $("#askForExam").click(function () {
    if (document.fullscreenElement) {
      // exitFullscreen is only available on the Document object.
      document.exitFullscreen();
    } else {
      $("#modalExam").modal("show")
    }
  });

  $("#startExam").click(function (e) {
    $("input,select,textarea").attr("disabled", false);
    $("#timeRectangule").removeClass("hidden")
    $(".blur").removeClass("blur");
    $("#modalExam").modal("hide")
    $("#menuButtons").remove()
    const timer = setInterval(() => {
      time--
      $("#timer").text(time)
      if (time === 1) {
        clearInterval(timer)
        let oneMinuteTime = 60;
        const timerOnMinute = setInterval(() => {
          oneMinuteTime--
          $("#timer").text(oneMinuteTime)
          if (oneMinuteTime === 0) {
            alert("Se ha acabo el tiempo, se ha guardado su progreso, Examen terminado!")
            clearInterval(timerOnMinute)
            exit = true;
            showFinished = false;
            $("form").submit();
          }
        }, 1000);
      }
    }, 60000); //60mil para 60 segundos 
    document.documentElement.requestFullscreen();
  })

  $("#finishExam").click(function (e) {
    exit = true
    $("form").submit()
  })

  document.addEventListener("fullscreenchange", function (e) {
    if (document.fullscreenElement) {
      console.log(`Entered fullscreen mode.`);
    } else {
      if (showFinished) {
        alert("Se ha terminado el examen");
      }
      exit = true;
      $("form").submit();
      console.log("Leaving full-screen mode.");
    }
  });

  $("form").submit(function (e) {
    if (!exit) {
      if ($(this)[0].checkValidity() === false) {
        $("#modalAlert").modal("show")
        e.preventDefault();
        e.stopPropagation();
      }
    }
  });
});