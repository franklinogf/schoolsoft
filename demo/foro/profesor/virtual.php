<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
$lang = new Lang([
  ['Salón virtual', 'Virtual classroom'],
  ['Titulo', 'Title'],
  ['Contraseña para acceder', 'Password to access'],
  ['Fecha', 'Date'],
  ['Hora', 'Time'],
  ['Información para los estudiantes', 'Information for the students'],
  ['Cerrar', 'Close'],
  ['Eliminar', 'Delete'],
  ['Guardar', 'Save'],
  ['¿Esta seguro que desea eliminarla?', 'Are you sure you want to delete it?'],
  ['Cancelar', 'Cancel'],
  ['Aceptar', 'Accept'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Salón Virtual");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Salón Virtual") ?></h1>

    <?php
    $virtual = true;
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    ?>


    <!-- Modal -->
    <div id="virtualModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="virtualModal" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <form class="needs-validation" novalidate>
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="link">Link</label>
                <input type="text" class="form-control" placeholder="Link de la clase virtual" id="link" required>
              </div>
              <div class="form-group">
                <label for="title"><?= $lang->translation("Titulo") ?></label>
                <input type="text" class="form-control" id="title" required>
              </div>
              <div class="form-group">
                <label for="password"><?= $lang->translation("Contraseña para acceder") ?></label>
                <input type="text" class="form-control" id="password">
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group">
                    <label for="date"><?= $lang->translation("Fecha") ?></label>
                    <input type="date" class="form-control" id="date" required>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group">
                    <label for="time"><?= $lang->translation("Hora") ?></label>
                    <input type="time" class="form-control" id="time" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="information"><?= $lang->translation("Información para los estudiantes") ?></label>
                <textarea class="form-control" id="information"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
              <button type="button" class="btn btn-danger hidden"><?= $lang->translation("Eliminar") ?></button>
              <button type="submit" id="virtualBtn" class="btn btn-primary"><?= $lang->translation("Guardar") ?></button>
            </div>
          </div>
          <input type="hidden" id="virtualId">
        </form>
      </div>
    </div>
    <!-- alert delete modal -->
    <div id="deleteModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-danger">
          <div class="modal-body">
            <?= $lang->translation("¿Esta seguro que desea eliminarla?") ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"><?= $lang->translation("Cancelar") ?></button>
            <button type="button" id="virtualDelBtn" class="btn btn-sm btn-danger"><?= $lang->translation("Aceptar") ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>