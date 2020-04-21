$(document).ready(function () {
  const studentsTable = $('.studentsTable').DataTable({
    "language": {
      "decimal": ".",
      "emptyTable": "No hay datos disponibles",
      "info": "Mostrando _START_ de _END_ de un total de _TOTAL_",
      "infoEmpty": "Mostrando 0 de 0 de un total de 0 ",
      "infoFiltered": "(Filtrado de un total de _MAX_ )",
      "loadingRecords": "Cargando...",
      "processing": "Procesando...",
      "search": "Buscar:",
      "zeroRecords": "No se encontraron datos",
      "paginate": {
        "first": "Primera",
        "last": "Ultima",
        "next": "Siguente",
        "previous": "Anterior"
      },
      "aria": {
        "sortAscending": ": Activar para ordernar la columna de forma ascendente",
        "sortDescending": ": Activar para ordernar la columna de forma descendente"
      }
    },
    "pageLength": 10,
    "lengthChange": false,
    "ordering": false

  });

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
          url: getBaseUrl('includes/homes.php'),
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

  $('#pass1').change(() => {
    checkPasswords(1);
  });

  $('#pass2').change(() => {
    checkPasswords(2);
  });

  $('form').submit(event => {


    if (!checkPasswords() || $('#username').val().length === 0) {
      event.preventDefault();
    }

  });


  function checkPasswords(id = 1) {

    if ($('#pass' + (id === 1 ? '1' : '2')).val().length > 0) {
      if ($('#pass' + (id !== 1 ? '1' : '2')).val().length > 0) {
        if ($("#pass" + (id === 1 ? '1' : '2')).val() !== $('#pass' + (id !== 1 ? '1' : '2')).val()) {
          $('.pass').addClass('is-invalid')
            .removeClass('is-valid')
          $("#pass" + (id === 1 ? '1' : '2')).focus();
          return false
        } else {
          $('.pass').addClass('is-valid')
            .removeClass('is-invalid')
          return true;
        }
      }
    }
    return true;
  }


  // delete everything when the modal hides

  $('#myModal').on('hidden.bs.modal', function (e) {
    const modal = $(this);
    modal.find('input').val('')
      .removeClass('is-invalid is-valid');
  })

  // functions
  function getBaseUrl(fileName) {
    var re = new RegExp(/^.*\//);
    return re.exec(window.location.href) + fileName;
  }

});

