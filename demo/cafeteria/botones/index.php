<?php

use App\Models\CafeteriaButton;
use Classes\Route;

require_once __DIR__ . '/../../app.php';

$buttons = CafeteriaButton::all();

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
  <link rel="stylesheet" href="css/botones.css">
</head>

<body>
  <!-- Hero Section -->
  <div class="hero-section bg-primary text-white py-4 mb-4">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h1 class="mb-0">
            <i class="fas fa-th-large mr-2"></i>
            Gestión de Botones
            <span class="badge badge-light text-primary ml-2"><?= $cant_buttons ?>/<?= $max_buttons ?></span>
          </h1>
          <p class="mb-0 mt-2 text-light opacity-75">
            Administra los botones de la cafetería con facilidad
          </p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
          <a href="../menu.php" class="btn btn-outline-light btn-lg">
            <i class="fas fa-arrow-left mr-1"></i>
            Volver al Menú
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <!-- Buttons Grid Section -->
      <div class="col-lg-8 col-md-12 order-md-1 mb-4">
        <div class="buttons-container card shadow-sm border-0">
          <div class="card-header bg-gradient-light">
            <h5 class="card-title mb-0">
              <i class="fas fa-grip-horizontal mr-2 text-primary"></i>
              Botones de Cafetería
              <small class="text-muted float-right">Arrastra para reorganizar</small>
            </h5>
          </div>
          <div class="card-body p-0">
            <div id="sortable" class="sortable-grid">
              <?php if ($cant_buttons === 0): ?> <div class="empty-state text-center py-5">
                  <div class="empty-icon mb-3">
                    <i class="fas fa-plus-circle text-muted"></i>
                  </div>
                  <h5 class="text-muted">No hay botones creados</h5>
                  <p class="text-muted mb-4">Comienza agregando tu primer botón de cafetería</p>
                  <button class="btn btn-primary btn-lg" data-action='add' data-toggle="modal" data-target="#Modal">
                    <i class="fas fa-plus mr-1"></i>
                    Crear primer botón
                  </button>
                </div>
              <?php else: ?>
                <?php foreach ($buttons as $btn): ?>
                  <div id="<?= $btn['id'] ?>" class="food-card" data-action='edit' data-target="#Modal" data-toggle="modal" role="button" tabindex="0">
                    <div class="food-card-image">
                      <img src="<?= isset($btn['foto']) ? "../../../cafeteria_im/{$btn['foto']}" : '../../../cafeteria_im/no-image.png' ?>" alt="<?= $btn['foto'] ?>">
                    </div>
                    <div class="food-card-content">
                      <h6 class="food-title"><?= htmlspecialchars($btn['articulo']) ?></h6>
                      <div class="food-price">
                        <span class="price-amount"><?= htmlspecialchars($btn['precio']) ?></span>
                      </div>
                    </div>
                    <div class="food-card-overlay">
                      <i class="fas fa-edit"></i>
                    </div>
                  </div>
                <?php endforeach ?>
              <?php endif ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Control Panel Section -->
      <div class="col-lg-4 col-md-12 order-md-2 mb-4">
        <div class="sticky-top">
          <!-- Actions Card -->
          <div class="control-panel card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-primary text-white">
              <h5 class="card-title mb-0">
                <i class="fas fa-cogs mr-2"></i>
                Panel de Control
              </h5>
            </div>
            <div class="card-body">
              <button class="btn btn-success btn-lg btn-block mb-3 add-button <?= ($cant_buttons == $max_buttons) ? 'disabled' : '' ?>"
                data-action='add'
                data-toggle="modal"
                data-target="#Modal"
                <?= ($cant_buttons == $max_buttons) ? 'disabled' : '' ?>>
                <i class="fas fa-plus mr-1"></i>
                Agregar Botón
              </button>

              <?php if ($cant_buttons >= $max_buttons): ?>
                <div class="alert alert-warning alert-sm mb-3">
                  <i class="fas fa-exclamation-triangle mr-1"></i>
                  <strong>Límite alcanzado</strong><br>
                  <small>Has alcanzado el máximo de <?= $max_buttons ?> botones.</small>
                </div>
              <?php endif ?>

              <div class="stats-section">
                <h6 class="section-title">Estadísticas</h6>
                <div class="stats-grid">
                  <div class="stat-item">
                    <div class="stat-value"><?= $cant_buttons ?></div>
                    <div class="stat-label">Botones creados</div>
                  </div>
                  <div class="stat-item">
                    <div class="stat-value"><?= $max_buttons - $cant_buttons ?></div>
                    <div class="stat-label">Disponibles</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions Card -->
          <div class="quick-actions card shadow-sm border-0">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">
                <i class="fas fa-bolt mr-1 text-warning"></i>
                Acciones Rápidas
              </h6>
            </div>
            <div class="card-body">
              <button class="btn btn-outline-info btn-block mb-2" data-action="sort-alphabetically">
                <i class="fas fa-sort-alpha-down mr-1"></i>
                Ordenar A-Z
              </button>
              <button class="btn btn-outline-info btn-block mb-2" data-action="sort-by-price">
                <i class="fas fa-dollar-sign mr-1"></i>
                Ordenar por precio
              </button>
              <a href="../menu.php" class="btn btn-outline-secondary btn-block">
                <i class="fas fa-arrow-left mr-1"></i>
                Salir
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Enhanced Modal -->
  <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-gradient-primary text-white">
          <h5 class="modal-title" id="ModalTitle">
            <i class="fas fa-plus-circle mr-2"></i>
            Agregar Botón
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="form" method="post" action="guardar.php" class="needs-validation" novalidate>
          <div class="modal-body p-4">
            <!-- Basic Information Section -->
            <div class="form-section mb-4">
              <h6 class="section-title">
                <i class="fas fa-info-circle mr-1 text-primary"></i>
                Información Básica
              </h6>

              <div class="form-group">
                <label for="title" class="form-label font-weight-medium">
                  <i class="fas fa-tag mr-1 text-success"></i>
                  Título del artículo
                </label>
                <input type="text"
                  class="form-control form-control-lg"
                  required
                  id="title"
                  name="titulo"
                  placeholder="Ej: Hamburguesa con papas"
                  maxlength="50">
                <div class="invalid-feedback">
                  Por favor ingrese un título válido (máximo 50 caracteres).
                </div>
                <small class="form-text text-muted">
                  Nombre descriptivo que aparecerá en el botón
                </small>
              </div>
            </div>

            <!-- Pricing Section -->
            <div class="form-section mb-4">
              <h6 class="section-title">
                <i class="fas fa-dollar-sign mr-1 text-warning"></i>
                Configuración de Precios
              </h6>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="price" class="form-label font-weight-medium">
                    Precio grados bajos (1-6):
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-success text-white">
                        <i class="fas fa-dollar-sign"></i>
                      </span>
                    </div>
                    <input type="number"
                      step="0.01"
                      min="0"
                      required
                      class="form-control form-control-lg"
                      id="price"
                      name="price"
                      placeholder="0.00">
                    <div class="invalid-feedback">
                      Por favor ingrese un precio válido.
                    </div>
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="price2" class="form-label font-weight-medium">
                    Precio grados altos (7-12):
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-info text-white">
                        <i class="fas fa-dollar-sign"></i>
                      </span>
                    </div>
                    <input type="number"
                      step="0.01"
                      min="0"
                      class="form-control form-control-lg"
                      id="price2"
                      name="price2"
                      placeholder="0.00">
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="discount_price" class="form-label font-weight-medium">
                  <i class="fas fa-percentage mr-1 text-danger"></i>
                  Precio con descuento (empleados):
                </label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-danger text-white">
                      <i class="fas fa-dollar-sign"></i>
                    </span>
                  </div>
                  <input type="number"
                    step="0.01"
                    min="0"
                    class="form-control form-control-lg"
                    id="discount_price"
                    name="discount_price"
                    placeholder="0.00">
                </div>
                <small class="form-text text-muted">
                  Precio especial para hijos de empleados (opcional)
                </small>
              </div>
            </div>

            <!-- Image Selection Section -->
            <div class="form-section mb-4">
              <h6 class="section-title">
                <i class="fas fa-images mr-1 text-info"></i>
                Selección de Imagen
              </h6>

              <div class="image-gallery" id="images">
                <?php foreach ($images as $image): ?>
                  <div class="image-option" role="button" tabindex="0">
                    <img src="<?= $image ?>"
                      alt="<?= substr(strrchr($image, '/'), 1) ?>"
                      class="gallery-image">
                    <div class="image-overlay">
                      <i class="fas fa-check"></i>
                    </div>
                  </div>
                <?php endforeach ?>
              </div>

              <small class="form-text text-muted">
                <i class="fas fa-mouse-pointer mr-1"></i>
                Haga clic en una imagen para seleccionarla
              </small>
            </div>

            <!-- Hidden Fields -->
            <input id="id" type="hidden" name="id">
            <input id="image" type="hidden" name="image">
            <input id="orden" type="hidden" name="orden">
          </div>

          <div class="modal-footer bg-light border-0 p-4">
            <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
              <i class="fas fa-times mr-1"></i>
              Cancelar
            </button>
            <button type="button" id="eliminar" class="btn btn-danger btn-lg btn-hidden">
              <i class="fas fa-trash mr-1"></i>
              Eliminar
            </button>
            <button type="submit" id="guardar" class="btn btn-success btn-lg">
              <i class="fas fa-save mr-1"></i>
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>



  </div>

  <?php
  $jqUI = true;
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>
  <script src="js/botones.js"></script>
</body>

</html>