<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Controllers\Teacher;
use Classes\Session;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - Salon Hogar</title>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 class="text-center">Mi Salon Hogar</h1>

    <?php
    $students = $teacher->homeStudents();
    Route::includeFile('/foro/profesor/includes/tables/tableStudents.php');
    ?>

    <a href="#" class="btn btn-primary mt-2">Enviar usuarios a los padres</a>

    <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= Route::url('/foro/profesor/includes/home.php') ?>" method="POST">
          <input type="hidden" name="id_student">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h5 class="modal-title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="username">Usuario</label>
                  <input type="text" class="form-control" name='username' id="username">
                  <div class="invalid-feedback">Ya existe este usuario</div>
                  <div class="valid-feedback">Usuario disponible</div>
                </div>
                <div class="form-group  col-md-6">
                  <label for="pass1">Nueva Clave</label>
                  <input type="password" class="form-control pass" name='password' id="pass1">
                  <label for="pass2">Confirmar Clave</label>
                  <input type="password" class="form-control pass" id="pass2">
                  <div class="invalid-feedback">Las claves no coinciden</div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" name="changeStudentUser" id="changeStudentUser" class="btn btn-primary">Guardar</button>
            </div>
        </form>
      </div>
    </div>
  </div>

  </div>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  ?>
</body>

</html>