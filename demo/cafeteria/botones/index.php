<?php

use App\Models\CafeteriaButton;
use Classes\Route;

require_once '../../app.php';

$buttons = CafeteriaButton::orderBy('orden')->get();

$cant_buttons = count($buttons);

$directory = "../../../cafeteria_im";
$images = glob($directory . "/*");


//maximo de botones permitidos para crear
$max_buttons = 15;
?>
<!doctype html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = __("Botones de la cafeteria");
  Route::includeFile('/cafeteria/includes/layouts/header.php');
  ?>

  <!-- <link rel="stylesheet" type="text/css" href="../css/all.css"> -->

  <style type="text/css">
    /* Custom styles for Bootstrap 4.6 */


    .card-img-wrapper {
      overflow: hidden;
      height: 130px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
    }

    .card-img-top {
      max-width: 100%;
      max-height: 130px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .card:hover .card-img-top {
      transform: scale(1.05);
    }

    .card-body {
      padding: 8px !important;
      text-align: center;
    }

    .card-title {
      margin-bottom: 5px;
      font-size: 0.9rem;
      font-weight: 600;
      color: #495057;
    }

    .price b {
      color: #28a745;
      font-size: 1.1rem;
    }

    .price b:before {
      font-family: "Font Awesome 5 Free";
      content: "\f155";
      font-size: 15px;
      margin-right: 2px;
    }

    #sortable {
      min-height: 250px;
      border-radius: 0.375rem !important;
      height: 100%;

      .card {
        margin-left: 5px !important;
        margin-bottom: 15px !important;
        max-width: 132px;
        cursor: pointer;
        float: left;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, .125);
      }

      .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, .15) !important;
      }

      .card:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, .1) !important;
      }
    }

    .sort-placeholder {
      width: 125.6px !important;
      height: 208px !important;
      background: rgba(0, 123, 255, 0.1);
      border: 2px dashed #007bff;
      border-radius: 0.375rem;
      margin: 5px;
      float: left;
    }

    /* Image selection styles */
    #images img {
      width: 73px;
      height: 73px;
      cursor: pointer;
      margin: 5px;
      border-radius: 0.375rem;
      transition: all 0.3s ease;
    }

    #images img:hover {
      transform: scale(1.05);
      box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
    }

    .selected-img {
      box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5) !important;
      border: 2px solid #007bff !important;
    }

    .selected-img:hover {
      box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.7) !important;
    }

    /* Modal improvements */
    .modal-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }

    .modal-title {
      color: #495057;
      font-weight: 600;
    }

    /* Button improvements */
    .btn {
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
    }

    .btn:active {
      transform: translateY(0);
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
      .card {
        max-width: calc(50% - 10px);
        margin: 5px !important;
      }

      #sortable {
        padding: 20px !important;
      }
    }

    @media (max-width: 576px) {
      .card {
        max-width: calc(100% - 10px);
      }
    }

    /* Accessibility improvements */
    .card:focus,
    #images img:focus {
      outline: 2px solid #007bff;
      outline-offset: 2px;
    }

    /* Loading state */
    .card.loading {
      opacity: 0.6;
      pointer-events: none;
    }

    /* Empty state */
    .empty-state {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-radius: 0.5rem;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container-fluid">
    <div class="py-4 text-center">
      <h2 class="display-4">Botones <i class="fas fa-clipboard-list text-primary"></i></h2>
      <p class="lead">Administra los botones de la cafetería</p>
    </div>

    <div class="row">
      <!-- LIST START -->
      <div class="col-lg-8 col-md-12 order-md-1 mb-4">
        <div id="sortable" class="bg-white shadow-sm rounded p-4">
          <?php if ($cant_buttons === 0): ?>
            <div class="text-center py-5 text-muted">
              <i class="fas fa-clipboard-list fa-3x mb-3"></i>
              <h5>No hay botones creados</h5>
              <p>Haga clic en "Agregar Botón" para crear el primer botón.</p>
            </div>
          <?php else: ?>

            <?php foreach ($buttons as $btn): ?>
              <div id="<?= $btn['id'] ?>" class="card shadow-sm" data-action='edit' data-target="#Modal" data-toggle="modal" role="button" tabindex="0">
                <div class="card-img-wrapper">
                  <img class="card-img-top" src="<?= isset($btn['foto']) ? "../../../cafeteria_im/{$btn['foto']}" : '../../../cafeteria_im/no-image.png' ?>" alt="<?= $btn['foto'] ?>">
                </div>
                <div class="card-body">
                  <h6 class="card-title"><?= htmlspecialchars($btn['articulo']) ?></h6>
                  <span class="price"><b><?= htmlspecialchars($btn['precio']) ?></b></span>
                </div>
              </div>
            <?php endforeach ?>

          <?php endif ?>
        </div>
      </div>
      <!-- LIST END -->
      <!-- agregar boton -->
      <div class="col-lg-4 col-md-12 order-md-2 mb-4">
        <div class="sticky-top">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-cogs text-primary"></i> Acciones
              </h5>
              <a href="#" id="add" class="btn btn-primary btn-lg btn-block mb-3 <?= ($cant_buttons == $max_buttons) ? 'disabled' : '' ?>" data-action='add' data-toggle="modal" data-target="#Modal">
                <i class="fas fa-plus"></i> Agregar Botón
              </a>
              <a href="../menu.php" class="btn btn-outline-secondary btn-lg btn-block">
                <i class="fas fa-arrow-left"></i> Volver al Menú
              </a>
              <?php if ($cant_buttons >= $max_buttons): ?>
                <small class="form-text text-muted mt-2">
                  <i class="fas fa-info-circle"></i> Has alcanzado el límite máximo de <?= $max_buttons ?> botones.
                </small>
              <?php else: ?>
                <small class="form-text text-muted mt-2">
                  <i class="fas fa-info-circle"></i> Botones creados: <?= $cant_buttons ?>/<?= $max_buttons ?>
                </small>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="ModalTitle">Agregar Boton</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form id="form" method="post" action="guardar.php" novalidate>
              <div class="modal-body">
                <div class="form-group">
                  <label for="title">Titulo</label>
                  <input type="text" class="form-control" required id="title" name="titulo" placeholder="Titulo del articulo">
                  <div class="invalid-feedback">
                    Por favor ingrese un título válido.
                  </div>
                </div>
                <div class="form-group">
                  <label for="price">Precio grados bajos:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" required class="form-control" id="price" name="price" placeholder="Precio grados 1-6">
                    <div class="invalid-feedback">
                      Por favor ingrese un precio válido.
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="price2">Precio grados altos:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" class="form-control" id="price2" name="price2" placeholder="Precio grados 7-12">
                  </div>
                </div>
                <div class="form-group">
                  <label for="discount_price">Precio descuento:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" min="0" class="form-control" id="discount_price" name="discount_price" placeholder="Precio para hijos de empleados">
                  </div>
                </div>
                <div class="form-group">
                  <h3>Seleccionar imagen:</h3>
                  <div id="images" class="border p-2 rounded">
                    <?php foreach ($images as $image): ?>
                      <img src="<?= $image ?>" alt="<?= substr(strrchr($image, '/'), 1) ?>" class="img-thumbnail" role="button" tabindex="0">
                    <?php endforeach ?>
                  </div>
                  <small class="form-text text-muted">Haga clic en una imagen para seleccionarla.</small>
                </div>
                <input id="id" type="hidden" name="id">
                <input id="image" type="hidden" name="image">
                <input id="orden" type="hidden" name="orden">
              </div>
              <div class="modal-footer">
                <button type="button" id="cancelar" class="btn btn-secondary" data-dismiss="modal">
                  <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" id="eliminar" class="btn btn-danger">
                  <i class="fas fa-trash"></i> Eliminar
                </button>
                <button type="submit" id="guardar" class="btn btn-primary">
                  <i class="fas fa-save"></i> Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>



    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <?php
    $jqUI = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

    <script>
      $(document).ready(function() {
        // Modern async function for getting button info
        const getButtonInfo = async (id) => {
          try {
            return await $.get(`./buscar.php?id=${id}`);
          } catch (error) {
            console.error('Error fetching button info:', error);
            return null;
          }
        };

        // Initialize sortable with improved settings
        $("#sortable").sortable({
          grid: [3, 5],
          appendTo: "#sortable",
          cursor: "grabbing",
          cursorAt: {
            left: 5
          },
          delay: 500,
          revert: 200,
          tolerance: "pointer",
          placeholder: "sort-placeholder",
          update: function(event, ui) {
            ordenar();
          },
          start: function(event, ui) {
            ui.item.addClass('sorting');
          },
          stop: function(event, ui) {
            ui.item.removeClass('sorting');
          }
        });

        $(".card-deck").disableSelection();

        // Improved ordering function
        function ordenar() {
          const sortedArray = $("#sortable").sortable("toArray");

          $.post('orden.php', {
              "ids": sortedArray
            })
            .done(function(data) {
              console.log('Order updated successfully:', data);
            })
            .fail(function(xhr, status, error) {
              console.error('Error updating order:', error);
              // Show user-friendly error message
              showAlert('Error al actualizar el orden. Por favor, intente nuevamente.', 'danger');
            });
        }

        // Utility function to show alerts
        function showAlert(message, type = 'info') {
          const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
              ${message}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          `;
          $('.container-fluid').prepend(alertHtml);

          // Auto-dismiss after 5 seconds
          setTimeout(() => {
            $('.alert').alert('close');
          }, 5000);
        }

        // Enhanced delete confirmation
        $("#eliminar").click(function(event) {
          event.preventDefault();

          if (confirm('¿Está seguro que desea eliminar este botón? Esta acción no se puede deshacer.')) {
            const buttonId = $("#id").val();
            $("#form").prop('action', 'eliminar.php');

            // Add loading state
            $("#" + buttonId).addClass('loading');

            // Remove from DOM and update order
            $("#" + buttonId).fadeOut(300, function() {
              $(this).remove();
              ordenar();
            });

            $('#form').submit();
          }
        });

        // Enhanced modal handling
        $('#Modal').on('shown.bs.modal', async function(event) {
          const modal = $(this);
          const button = $(event.relatedTarget);
          const action = button.data('action');

          // Clear previous validation states
          modal.find('.is-invalid').removeClass('is-invalid');
          modal.find('.invalid-feedback').hide();
          $("#images img").removeClass('selected-img');

          if (action === 'add') {
            // Add mode
            modal.find('.modal-title').text('Agregar Botón');
            modal.find('#title').val('');
            modal.find('#price').val('');
            modal.find('#price2').val('');
            modal.find('#discount_price').val('');
            modal.find('#guardar').html('<i class="fas fa-save"></i> Guardar');
            modal.find('#eliminar').hide();
            modal.find('#form').prop('action', 'guardar.php');
            modal.find('#image').val('');

            // Focus on first input
            setTimeout(() => $('#title').focus(), 300);

          } else {
            // Edit mode
            const buttonId = $(button).prop('id');

            try {
              const buttonData = await getButtonInfo(buttonId);

              if (buttonData) {
                const {
                  articulo,
                  foto,
                  precio,
                  precio2,
                  precio_descuento
                } = buttonData;

                modal.find('.modal-title').text('Editar Botón');
                modal.find('#title').val(articulo);
                modal.find('#price').val(precio);
                modal.find('#price2').val(precio2);
                modal.find('#discount_price').val(precio_descuento);
                modal.find('#id').val(buttonId);
                modal.find('#guardar').html('<i class="fas fa-edit"></i> Actualizar');
                modal.find('#eliminar').show();
                modal.find('#form').prop('action', 'editar.php');
                modal.find('#image').val(foto);

                // Highlight selected image
                $("#images img").each(function() {
                  if ($(this).prop('alt') === foto) {
                    $(this).addClass('selected-img');
                    return false; // Break the loop
                  }
                });
              }
            } catch (error) {
              showAlert('Error al cargar los datos del botón.', 'danger');
              modal.modal('hide');
            }
          }
        });

        // Clear selection when modal is hidden
        $('#Modal').on('hidden.bs.modal', function() {
          $("#images img").removeClass('selected-img');
          $(this).find('.is-invalid').removeClass('is-invalid');
        });

        // Enhanced form validation and submission
        $("#form").on('submit', function(event) {
          event.preventDefault();

          const form = this;
          let isValid = true;
          let hasSelectedImage = false;

          // Clear previous validation states
          $(form).find('.is-invalid').removeClass('is-invalid');

          // Validate required fields
          $(form).find('[required]').each(function() {
            if (!$(this).val().trim()) {
              $(this).addClass('is-invalid');
              isValid = false;
            }
          });

          // Check if image is selected
          $("#images img").each(function() {
            if ($(this).hasClass('selected-img')) {
              hasSelectedImage = true;
              return false; // Break the loop
            }
          });

          // Set order value
          $("#orden").val($(".card").length + 1);

          // Validate image selection for non-delete actions
          if ($(form).prop('action') !== 'eliminar.php' && !hasSelectedImage) {
            showAlert('Debe seleccionar una imagen para el artículo. Si no desea utilizar una imagen, seleccione "No imagen".', 'warning');
            isValid = false;
          }

          // Submit if valid
          if (isValid) {
            // Add loading state to submit button
            const submitBtn = $(form).find('[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            // Submit the form
            form.submit();
          }
        });

        // Enhanced image selection with keyboard support
        $("#images img").on('click keypress', function(e) {
          if (e.type === 'click' || (e.type === 'keypress' && (e.which === 13 || e.which === 32))) {
            e.preventDefault();

            // Remove selection from all images
            $("#images img").removeClass('selected-img');

            // Add selection to clicked image
            $(this).addClass('selected-img');

            // Update hidden input
            $("#image").val($(this).attr('alt'));

            // Provide audio feedback for accessibility
            if (e.type === 'keypress') {
              $(this).trigger('focus');
            }
          }
        });

        // Add keyboard navigation for cards
        $(".card").on('keypress', function(e) {
          if (e.which === 13 || e.which === 32) { // Enter or Space
            e.preventDefault();
            $(this).trigger('click');
          }
        });

        // Initialize tooltips if Bootstrap supports them
        if (typeof $().tooltip === 'function') {
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    </script>
</body>

</html>