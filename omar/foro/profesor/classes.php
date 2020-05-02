<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - Mis Cursos</title>
  <?php
  Route::fontawasome();
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  Route::includeFile('/includes/datatable-css.php', true);
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center">Mis Cursos</h1>

    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    ?>

    <!-- Students table -->

    <table class="studentsTable table table-striped table-hover cell-border w-100 shadow">
      <thead class="bg-gradient-primary bg-primary border-0">
        <tr>
          <th>Estudiante</th>
          <th>Usuario</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
        <tr class="bg-gradient-secondary bg-secondary">
          <th>Estudiante</th>
          <th>Usuario</th>
        </tr>
        <tr class="bg-gradient-light bg-light">
          <td colspan="2"><button id="back" type="button" class="btn btn-block btn-primary">Atrás</button></td>
        </tr>
      </tfoot>
    </table>

    <!-- modal -->
    <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">

        <input type="hidden" name="id_student">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title">Perfil del estudiante</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="row">
              <div class="col-12">
                <img id="profilePicture" src="<?= __NO_PROFILE_PICTURE ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block">
                <hr>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">ID</div>
                <p id="id" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Nombre</div>
                <p id="name" class="mt-3"></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Grado</div>
                <p id="grade" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Fecha de nacimiento</div>
                <p id="date" class="mt-3"></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Email</div>
                <p id="email" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Genero</div>
                <p id="gender" class="mt-3"></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>

        </div>
      </div>
    </div>

  </div>
  <?php
  $jqUI = true;
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  Route::includeFile('/includes/datatable-js.php', true);
  
  ?>

</body>

</html>