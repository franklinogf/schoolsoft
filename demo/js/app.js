//* --------------------------- functions --------------------------- *//

// function for animations

/** 
Animations class
bounce         	flash           	pulse            	rubberBand
shake          	headShake       	swing            	tada
wobble         	jello           	bounceIn         	bounceInDown
bounceInLeft   	bounceInRight   	bounceInUp       	bounceOut
bounceOutDown  	bounceOutLeft   	bounceOutRight   	bounceOutUp
fadeIn         	fadeInDown      	fadeInDownBig    	fadeInLeft
fadeInLeftBig  	fadeInRight     	fadeInRightBig   	fadeInUp
fadeInUpBig    	fadeOut         	fadeOutDown      	fadeOutDownBig
fadeOutLeft    	fadeOutLeftBig  	fadeOutRight     	fadeOutRightBig
fadeOutUp      	fadeOutUpBig    	flipInX          	flipInY
flipOutX       	flipOutY        	lightSpeedIn     	lightSpeedOut
rotateIn       	rotateInDownLeft	rotateInDownRight	rotateInUpLeft
rotateInUpRight	rotateOut       	rotateOutDownLeft	rotateOutDownRight
rotateOutUpLeft	rotateOutUpRight	hinge            	jackInTheBox
rollIn         	rollOut         	zoomIn           	zoomInDown
zoomInLeft     	zoomInRight     	zoomInUp         	zoomOut
zoomOutDown    	zoomOutLeft     	zoomOutRight     	zoomOutUp
slideInDown    	slideInLeft     	slideInRight     	slideInUp
slideOutDown   	slideOutLeft    	slideOutRight    	slideOutUp
heartBeat			
*/
function animateCSS(element, animationName, callback) {
  $("head").append('<link id="_animatedLink" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">')
  const node = $(element)
  node.addClass(`animated ${animationName}`)
  function handleAnimationEnd() {

    if (animationName.indexOf('In') > 0) {
      node.css({ 'opacity': 1 })
    } else if (animationName.indexOf('Out') > 0) {
      node.css({ 'opacity': 0 })
    }
    node.removeClass(`animated ${animationName}`)
    node.off('animationend', handleAnimationEnd)
    $("#_animatedLink").remove()

    if (typeof callback === 'function') callback()
  }

  node.on('animationend', handleAnimationEnd)
}

// fomart date
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

function getBaseUrl(fileName = '') {
  var re = new RegExp(/^.*\//);
  return re.exec(window.location.href) + fileName;
}

function includeThisFile() {
  const phpFile = getBaseUrl() + 'includes/' + baseName(window.location.href) + '.php'
  return phpFile
}

function baseName(str) {
  var base = new String(str).substring(str.lastIndexOf('/') + 1);
  if (base.lastIndexOf(".") != -1) {
    base = base.substring(0, base.lastIndexOf("."));
  }
  return base;
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
  // delete everything when the modal hides

  if ($('.modal').length > 0) {
    $('.modal').on('hidden.bs.modal', function (e) {
      const modal = $(this);
      modal.find('input').val('');
      modal.find('textarea').val('');
      modal.find('input[type=radio],input[type=checkbox]').prop('checked', false);
    })
  }

  // add file button
  if ($("button.addFile").length > 0) {
    $("button.addFile").click(e => {
      $(e.target).after(`<div class="input-group mt-3 col-12 col-lg-6 mx-auto">
    <div class="custom-file">
       <input type="file" class="custom-file-input file" name="file[]">
       <label class="custom-file-label text-nowrap overflow-hidden">Seleccionar Archivo</label>
    </div>
    <div class="input-group-append">
       <button class="btn btn-danger delFile" type="button"><i class="fas fa-trash-alt"></i></button>
    </div>
 </div>`);
    });

    $(document).on('change', 'input.file', e => {
      //get the file name   
      var fileName = baseName($(e.target).val());
      //replace the "Seleccionar archivo" label
      $(e.target).next('.custom-file-label').html(fileName)
    })

    $(document).on('click', 'button.delFile', e => {
      if ($(e.target).parents('.input-group-append').prev().children('input.file').val() !== '') {
        if (confirm("¿Seguro que quiere eliminar este archivo?")) {
          $(e.target).parents('.input-group').remove()
        }
      } else {
        $(e.target).parents('.input-group').remove()
      }
    })
  }
  // end add file

  // enable tooltips  
  if ($('[data-toggle="tooltip"]').length > 0) {
    $('[data-toggle="tooltip"]').tooltip()
  }




});