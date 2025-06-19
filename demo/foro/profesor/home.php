<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
$lang = new Lang([
  ['Salón Hogar', 'Home classroom'],
  ['Crear usuarios automaticamente', 'Create users automatically'],
  ['Enviar usuarios a los padres', 'Send users to parents'],
  ['Usuario', 'User'],
  ['Ya existe este usuario', 'This user already exists'],
  ['Usuario disponible', 'User available'],
  ['Nueva contraseña', 'New password'],
  ['Confirmar contraseña', 'Confirm password'],
  ['Las contraseñas no coinciden', 'The passwords do not match'],
  ['Cerrar', 'Close'],
  ['Guardar', 'Save'],
  ['Debes de tener un correo para poder enviar los usuarios','You must have an email to be able to send users'],
  ['ir a mi perfil','go to my profile']
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Salón Hogar");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 class="text-center"><?= $lang->translation("Salón Hogar") ?></h1>

    <?php
    $students = $teacher->homeStudents();
    Route::includeFile('/foro/profesor/includes/tables/tableStudents.php');
    ?>

    <a id="createUsers" href="<?= Route::url('/foro/profesor/includes/createUsers.php') ?>" class="btn btn-primary mt-2"><?= $lang->translation("Crear usuarios automaticamente") ?></a>
    <?php if ($teacher->email1) : ?>
      <a id="sendEmails" href="<?= Route::url('/foro/profesor/includes/email/mailUsers.php') ?>" class="btn btn-secondary mt-2"><?= $lang->translation("Enviar usuarios a los padres") ?></a>
    <?php else : ?>
      <p class="text-danger mt-2"><?= $lang->translation("Debes de tener un correo para poder enviar los usuarios") ?>, <a href="<?= Route::url('/foro/profesor/profile.php') ?>"><?= $lang->translation("ir a mi perfil") ?></a></p>
    <?php endif ?>
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
                  <label for="username"><?= $lang->translation("Usuario") ?></label>
                  <input type="text" class="form-control" name='username' id="username">
                  <div class="invalid-feedback"><?= $lang->translation("Ya existe este usuario") ?></div>
                  <div class="valid-feedback"><?= $lang->translation("Usuario disponible") ?></div>
                </div>
                <div class="form-group  col-md-6">
                  <label for="pass1"><?= $lang->translation("Nueva contraseña") ?></label>
                  <input type="password" class="form-control pass" name='password' id="pass1">
                  <label for="pass2"><?= $lang->translation("Confirmar contraseña") ?></label>
                  <input type="password" class="form-control pass" id="pass2">
                  <div class="invalid-feedback"><?= $lang->translation("Las contraseñas no coinciden") ?></div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
              <button type="submit" name="changeStudentUser" id="changeStudentUser" class="btn btn-primary"><?= $lang->translation("Guardar") ?></button>
            </div>
        </form>
      </div>
    </div>
  </div>

  </div>

  <?php
  Route::includeFile('/includes/layouts/progressBar.php', true);
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>
</body>

</html>