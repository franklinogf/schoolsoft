$(document).ready(function () {
  const table = $('#studentsTable').DataTable({
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


  $('#studentsTable tbody').on('click', 'tr', function () {
    const row = table.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      const modal = $('#myModal')
      modal.find('.modal-title').text(data[0])
      modal.find('#username').val(data[1])
      modal.modal('show')
    }
  });

// check passwords to submit 

$('#pass1').change(() => {
  checkPasswords(1);
});

$('#pass2').change(() => {
  checkPasswords(2);
});

$('form').submit(event => {
  if (!checkPasswords()) {
     event.preventDefault();
  }
});


function checkPasswords(id = 1) {

  if ($('#pass' + (id === 1 ? '1' : '2')).val().length > 0) {
     if ($('#pass' + (id !== 1 ? '1' : '2')).val().length > 0) {
        if ($("#pass" + (id === 1 ? '1' : '2')).val() !== $('#pass' + (id !== 1 ? '1' : '2')).val()) {
           alert("Las claves deben de coincidir");
           $("#pass" + (id === 1 ? '1' : '2')).focus();
           return false
        } else {
           return true;
        }
     }
  }
  return true;
}


// delete everything when the modal hides

$('#myModal').on('hidden.bs.modal', function (e) {
  const modal = $(this);
  modal.find('input').val('');
})

});