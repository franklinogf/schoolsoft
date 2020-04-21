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

  const classesTable = $('.classesTable').DataTable({
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


  $('.classesTable tbody').on('click', 'tr', function () {
    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const data = row.data();
      console.log('data', data)
      
    }
  });


});