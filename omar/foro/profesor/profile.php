<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
$lang = new Lang([
  ['Mi perfil', 'My profile'],
  ["Cambiar foto de perfil", "Change profile picture"],
  ["Información importante", "Important information"],
  ["Usuario", "Username"],
  ["Salon hogar", "Home classroom"],
  ["Total de estudiantes", "Total students"],
  ["Nombre", "Name"],
  ["Apellidos", "Surnames"],
  ["Correo electrónico", "Email"],
  ["Celular", "Cellphone"],
  ["Compañia telefonica", "Cellphone company"],
  ["Nueva contraseña", "New Password"],
  ["Confirmar contraseña", "Confirm Password"],
  ["Las contraseñas no coinciden", "Passwords do not match"],
  ["Guardar", "Save"],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Mi perfil");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center"><?= $lang->translation("Mi Perfil") ?> <i class="far fa-id-card"></i></h1>
    <form action="<?= Route::url('/foro/profesor/includes/profiles.php') ?>" method="POST" enctype="multipart/form-data">
      <div class="row mt-5">
        <div class="col-lg-6 col-sm-12">
          <input type="hidden" name="id_teacher" value="<?= $teacher->id ?>">
          <img src="<?= $teacher->profilePicture() ?>" alt="Profile picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
          <div class="form-group text-center mt-2">
            <button id="pictureBtn" type='button' class="btn btn-secondary"><?= $lang->translation("Cambiar foto de perfil") ?></button>
            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
          </div>
        </div>
        <div class="offset-lg-2 col-lg-4 col-sm-12 mt-sm-3">
          <hr class="d-lg-none d-sm-block" />
          <div class="card border-info">
            <h6 class="card-header bg-gradient-info bg-info">
              <?= $lang->translation("Información importante") ?>
            </h6>
            <div class="card-body">
              <p class="text-monospace">ID: <span class="badge badge-info"><?= $teacher->id ?> </span></p>
              <p class="text-monospace"><?= $lang->translation("Usuario") ?>: <span class="badge badge-info"><?= $teacher->usuario ?></span></p>
              <?php if (!__COSEY) : ?>
                <p class="text-monospace"><?= $lang->translation("Salon hogar") ?>: <span class="badge badge-info"><?= $teacher->grado ?></span></p>
              <?php endif ?>
              <p class="text-monospace"><?= $lang->translation("Total de estudiantes") ?>: <span class="badge badge-info"><?= sizeof($teacher->homeStudents()) ?></span></p>
            </div>
          </div>
        </div>
      </div>
      <hr class="mb-3" />
      <div class="row">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name"><?= $lang->translation("Nombre") ?></label>
            <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name" required autocomplete="nope">
          </div>
          <div class="form-group col-md-6">
            <label for="lastname"><?= $lang->translation("Apellidos") ?></label>
            <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='lastName' id="lastname" required autocomplete="nope">
          </div>
          <div class="form-group col-md-12">
            <label for="email1"><?= $lang->translation("Correo electrónico principal") ?></label>
            <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" value='<?= $teacher->email1 ?>' class="form-control" name='email1' id="email1" autocomplete="nope">
            <label for="email2"><?= $lang->translation("Correo electrónico secundario") ?></label>
            <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" value='<?= $teacher->email2 ?>' class="form-control" name='email2' id="email2" autocomplete="nope">
          </div>
          <div class="form-group col-md-6">
            <label for="cellPhone"><?= $lang->translation("Celular") ?></label>
            <input type="tel" value="<?= $teacher->cel ?>" class="form-control" id="cellPhone" name="cellPhone" pattern="[0-9]{10}" autocomplete="nope">
          </div>
          <div class="form-group col-md-6">
            <label for="cellCompany"><?= $lang->translation("Compañia telefonica") ?></label>
            <select id="cellCompany" class="form-control" name="cellCompany">
              <option <?= $teacher->comp === '' ? 'selected=""' : '' ?> value=""><?= $lang->translation("Seleccionar") ?></option>
              <?php foreach (Util::phoneCompanies() as $company) : ?>
                <option <?= $teacher->comp === $company ? 'selected=""' : '' ?> value="<?= $company ?>"><?= $company ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group  col-md-6">
            <label for="pass1"><?= $lang->translation("Nueva contraseña") ?></label>
            <input type="password" class="form-control pass" name='password' id="pass1" autocomplete="nope">
            <label for="pass2"><?= $lang->translation("Confirmar contraseña") ?></label>
            <input type="password" class="form-control pass" id="pass2" autocomplete="nope">
            <div class="invalid-feedback"><?= $lang->translation("Las contraseñas no coinciden") ?></div>
          </div>

          <div class="form-group col-md-12">
            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= $lang->translation("Guardar") ?></button>
          </div>
        </div>
      </div>
    </form>
  </div>


  <?php
  Route::includeFile('/includes/layouts/progressBar.php', true);
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>
</body>

</html>