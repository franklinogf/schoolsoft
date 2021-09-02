$(document).ready(function () {

  let prevUsername = '';

  $('.studentsTable tbody').on('click', 'tr', function () {
    const row = studentsTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      const modal = $('#myModal')
      prevUsername = data[1];

      modal.find('input[name=id_student]').val(row.id())
      modal.find('.modal-title').text(data[0])
      modal.find('#username').val(data[1])
      modal.modal('show')
    }
  });

  // Check if user already exists
  $('#username').change(e => {
    if ($('#username').val().length > 0) {
      if ($('#username').val() !== prevUsername) {
        $.ajax({
          type: "POST",
          url: includeThisFile(),
          data: { 'checkUser': $('#username').val() },
          dataType: "json",
          success: (res) => {
            if (res.response === true) {
              $('#username').removeClass('is-valid')
                .addClass('is-invalid')
                .val('')
                .focus();
            }
            else {
              $('#username').removeClass('is-invalid')
                .addClass('is-valid');
            }
          }
        });
      }
      else {
        $('#username').removeClass('is-invalid is-valid');

      }
    }

  })

  // check passwords to submit 

  $('#pass1,#pass2').change(() => {
    checkPasswords();
  });

  $('form').submit(event => {

    if (!checkPasswords() || $('#username').val().length === 0) {
      event.preventDefault();
    }

  });

  // delete everything when the modal hides

  $('#myModal').on('hidden.bs.modal', function (e) {
    const modal = $(this);
    modal.find('input').val('')
      .removeClass('is-invalid is-valid');
  })

  let timer
  $("#sendEmails").click(function (e) {
    e.preventDefault();
    $.ajax({
      type: "get",
      url: $(this).prop('href'),
      beforeSend: function () {

        $("#progressModal").modal('show')
        count = 1;
        timer = setInterval(() => {
          if (count <= 100) {
            $("#progressModal .progress-bar").prop('aria-valuenow', count).css('width', count + '%').text(count + '%')
            count++;
          }
        }, 50);
      },
      complete: function (data) {
        console.log(data)
        $("#progressModal .progress-bar").prop('aria-valuenow', 100).css('width', '100%').text('100%')
        clearInterval(timer);
        $("#progressModal").modal('hide')
      }

    })

  })


});

