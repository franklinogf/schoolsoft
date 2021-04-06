<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = "Salón Virtual";
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  $virtual = true;
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center">Salón Virtual</h1>

    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    ?>


    <!-- Modal -->
    <div id="virtualModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="virtualModal" aria-hidden="true">
      <div class="modal-dialog">
        <form class="needs-validation" novalidate>
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="virtualModal"></h5>
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
                <label for="title">Titulo</label>
                <input type="text" class="form-control" id="title" required>
              </div>
              <div class="row">
                <div class="col">
                  <div class="form-group">
                    <label for="date">Fecha</label>
                    <input type="date" class="form-control" id="date" required>
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <label for="time">Hora</label>
                    <input type="time" class="form-control" id="time" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" id="virtualBtn" class="btn btn-primary">Guardar</button>
            </div>
          </div>
          <input type="hidden" id="virtualId">
        </form>
      </div>
    </div>
  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  ?>

</body>

</html>