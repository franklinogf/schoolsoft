$(document).ready(function () {
  let ok = 0
  let focused = false
 



  $(document).on('click', 'button.takeExam', function (e) {
    const homeworkId = $(this).data('examId')
    // popup(`takeExam.php?examId=${homeworkId}`)
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

  $(".alert .close").click(function () {
    animateCSS($('.alert'), 'zoomOut', () => {
      $(".alert").alert('close')
    })
  })

  function popup(url) {
    params = 'width=' + screen.width;
    params += ', height=' + screen.height;
    params += ', top=0, left=0'
    params += ', location=no';
    params += ', resizable=no';
    // params = "menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes";
    newwin = window.open(url, 'exam', params);
    $(document).mousemove(function (event) {
      console.log("focused", newwin.isFocus)

      if (false === newwin.closed) {
        ok = 0;
        newwin.focus()

      } else {
        if (ok == 0) {
          ok = 1;
          alert('Ha terminado el examen.');
          location.reload();
        }
      }
      // if (ok == 2) {
      //   if (confirm('Usted a salido del examen, si continua se cerrara y se terminara el examen.')) {
      //     newwin.close()
      //   } else {
      //     focused = true;
      //     newwin.focus()
      //     setTimeout(function() {
      //       focused = false
      //     },1000)

      //   }
      //   ok = 1

      // }





    });


    // if (window.focus) { newwin.focus() }
    newwin.focus()
    return false;

  }

});