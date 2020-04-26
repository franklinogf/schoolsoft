let classesTable;
let studentsTable;



//* --------------------------- functions --------------------------- *//

function formatDate(value) {

  if (value !== '0000-00-00') {
    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    const date = new Date(value);

    const day = date.getDate()
    const month = months[date.getMonth()];
    const year = date.getFullYear()

    return day + ' ' + month + ' ' + year
  }

  return '';

}

function formatTime(value) {
  let [h, m, s] = value.split(':');
  let p = 'AM'

  if (h > 12) {
    p = 'PM'
    h -= 12
  }


  return h + ':' + m + ':' + s + ' ' + p

}

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
    if ($('.classesTable')) classesTable = $(".classesTable").DataTable();

    // Students table custom info
    if ($('.studentsTable')) studentsTable = $('.studentsTable').DataTable();

    // Homework table custom info
    if ($('.homeworksTable')) homeworksTable = $('.homeworksTable').DataTable();

    // Topics table custom info
    if ($('.topicsTable')) topicsTable = $('.topicsTable').DataTable();
  }


});