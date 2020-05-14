<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$student = new Student(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Mis Cursos";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center">Mis Cursos</h1>

    <?php    
    Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
    ?>

    

    <!-- modal -->
    <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">

        <input type="hidden" name="id_student">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center">
            <div class="row">
              <div class="col-12">
                <img id="profilePicture" src="#" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                <hr>
              </div>
            </div>
            <div class="row">              
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Nombre</div>
                <p id="name" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap">Salon Hogar</div>
                <p id="grade" class="mt-3"></p>
              </div>
            </div>
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
  Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
  ?>

</body>

</html>