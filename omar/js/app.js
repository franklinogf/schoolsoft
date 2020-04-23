let classesTable;
let studentsTable;



//* --------------------------- functions --------------------------- *//


function getBaseUrl(fileName) {
  var re = new RegExp(/^.*\//);
  return re.exec(window.location.href) + fileName;
}

// check input passwords
function checkPasswords(pass1 = '#pass1', pass2 = '#pass2', bothClass = '.pass') {

  if ($(pass1).val().length > 0) {
    if ($(pass2).val().length > 0) {
      if ($(pass1).val() !== $(pass2).val()) {
        $(bothClass).addClass('is-invalid')
          .removeClass('is-valid')
        $(pass1).focus();
        return false
      } else {
        $(bothClass).addClass('is-valid')
          .removeClass('is-invalid')
        return true;
      }
    }
  }
  return true;
}

$(function () {
  // Data table global configuration
if ($.fn.dataTable) {
  $.extend($.fn.dataTable.defaults, {
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
  // Classes table custom info
  classesTable = $(".classesTable").DataTable();
  
  // Students table custom info
  studentsTable = $('.studentsTable').DataTable();
}


});