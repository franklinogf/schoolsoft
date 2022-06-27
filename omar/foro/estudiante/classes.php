<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$student = new Student(Session::id());
$lang = new Lang([
['Mis cursos','My classes'],
['Nombre','Name'],
['Salón Hogar','Home classroom'],
['Correo electrónico','Email'],
['Genero','Gender'],
['Cerrar','Close'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = $lang->translation('Mis cursos');
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Mis Cursos") ?></h1>

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
                <img id="profilePicture" src="#" alt="Profile picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
                <hr>
              </div>
            </div>
            <div class="row">              
              <div class="col-6">
                <div class="badge badge-secondary text-wrap"><?= $lang->translation("Nombre") ?></div>
                <p id="name" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap"><?= $lang->translation("Salón Hogar") ?></div>
                <p id="grade" class="mt-3"></p>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="badge badge-secondary text-wrap"><?= $lang->translation("Correo electrónico") ?></div>
                <p id="email" class="mt-3"></p>
              </div>
              <div class="col-6">
                <div class="badge badge-secondary text-wrap"><?= $lang->translation("Genero") ?></div>
                <p id="gender" class="mt-3"></p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
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