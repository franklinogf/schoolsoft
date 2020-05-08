$(document).ready(function () {
  let _class = '';

  const classesTableWrapper = $(".classesTable").parents('.dataTables_wrapper');
   

  $('.classesTable tbody').on('click', 'tr', function () {

    const row = classesTable.row(this)
    if (row.index() !== undefined) {
      const teacherId = $(row.node()).data('id');
      const modal = $('#myModal')

      $.ajax({
        type: "POST",
        url: includeThisFile(),
        data: { 'teacherById': teacherId },
        dataType: "json",
        success: (res) => {
          if (res.response === true) {
            const modalHeader = res.data.genero === 'Femenino' ? 'Perfil de la profesora' : 'Perfil del profesor'
            modal.find('.modal-title').html(modalHeader)
            modal.find('#profilePicture').prop('src',res.data.foto)
            modal.find('#name').text(`${res.data.nombre}`)
            modal.find('#grade').text(res.data.grado)
            modal.find('#email').text(res.data.email)
            const gender = {
              'Femenino': '<i class="fas fa-female fa-2x"></i>',
              'Masculino': '<i class="fas fa-male fa-2x"></i>'
            };
            modal.find('#gender').html(gender[res.data.genero])
            modal.modal('show')

          }else{
            alert('Este curso no tiene un profesor asignado')
          }
        }
      });

    }
  });



});