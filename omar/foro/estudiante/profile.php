<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$student = new Student(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = "Mi Perfil";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body class='pb-5'>
  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center">Mi Perfil <i class="far fa-id-card"></i></h1>
    <div class="row mt-5">
      <div class="col-lg-6 col-sm-12">
        <form action="<?= Route::url('/foro/estudiante/includes/profiles.php') ?>" method="POST" enctype="multipart/form-data">
          <img src="<?= $student->profilePicture() ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block" width="250" height="250">
          <div class="form-group text-center mt-2">
            <button id="pictureBtn" type='button' class="btn btn-secondary">Cambiar Foto</button>
            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
          </div>
        
      </div>
      <div class="offset-lg-2 col-lg-4 col-sm-12 mt-sm-3">
        <hr class="d-lg-none d-sm-block" />
        <div class="card border-info">
          <h6 class="card-header bg-gradient-info bg-info">
            Información importante
          </h6>
          <div class="card-body">
            <p class="text-monospace">ID: <span class="badge badge-info"><?= $student->mt ?> </span></p>
            <p class="text-monospace">Usuario: <span class="badge badge-info"><?= $student->usuario ?></span></p>
            <p class="text-monospace">Grado: <span class="badge badge-info"><?= $student->grado ?></span></p>
          </div>
        </div>

      </div>
    </div>
    <hr class="mb-3" />
    <div class="row">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="name">Nombre</label>
          <input type="text" value='<?= $student->nombre ?>' class="form-control" name='name' id="name" required autocomplete="nope">
        </div>
        <div class="form-group col-md-6">
          <label for="lastname">Apellidos</label>
          <input type="text" value='<?= $student->apellidos ?>' class="form-control" name='lastName' id="lastname" required autocomplete="nope">
        </div>
        <div class="form-group col-md-12">
          <label for="email">E-mail</label>
          <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" value='<?= $student->email ?>' class="form-control" name='email' id="email" autocomplete="nope">
        </div>
        <div class="form-group col-md-6">
          <label for="cellPhone">Celular</label>
          <input type="tel" value="<?= $student->cel ?>" class="form-control" id="cellPhone" name="cellPhone" pattern="[0-9]{10}" autocomplete="nope">
        </div>
        <div class="form-group col-md-6">
          <label for="cellCompany">Compañia telefonica</label>
          <select id="cellCompany" class="form-control" name="cellCompany">
            <option <?= $student->comp === '' ? 'selected=""' : '' ?> value="">Seleciona una</option>
            <option <?= $student->comp === 'T-Movil' ? 'selected=""' : '' ?> value="T-Movil">T-Mobile</option>
            <option <?= $student->comp === 'AT&T' ? 'selected=""' : '' ?> value="AT&T">AT&T</option>
            <option <?= $student->comp === 'Sprint' ? 'selected=""' : '' ?> value="Sprint">Sprint</option>
            <option <?= $student->comp === 'Open M.' ? 'selected=""' : '' ?> value="Open M.">Open M.</option>
            <option <?= $student->comp === 'Moviltar' ? 'selected=""' : '' ?> value="Movistar">Movistar</option>
            <option <?= $student->comp === 'Claro' ? 'selected=""' : '' ?> value="Claro">Claro</option>
            <option <?= $student->comp === 'Suncom' ? 'selected=""' : '' ?> value="Suncom">Suncom</option>
            <option <?= $student->comp === 'Verizon' ? 'selected=""' : '' ?> value="Verizon">Verizon</option>
            <option <?= $student->comp === 'Boost' ? 'selected=""' : '' ?> value="Boost">Boost Mobile</option>
          </select>
        </div>
        <div class="form-group  col-md-6">
          <label for="pass1">Nueva Clave</label>
          <input type="password" class="form-control pass" name='password' id="pass1" autocomplete="nope">
          <label for="pass2">Confirmar Clave</label>
          <input type="password" class="form-control pass" id="pass2" autocomplete="nope">
          <div class="invalid-feedback">Las claves no coinciden</div>
        </div>
        <div class="form-group col-md-12">
          <button type="submit" class="btn btn-primary btn-lg btn-block">Guardar</button>
        </div>
      </div>
    </div>
    </form>
  </div>


  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
  ?>
</body>

</html>