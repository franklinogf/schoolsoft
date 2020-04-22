<?php
use Classes\Route;
use Classes\Controllers\Teacher;


include '../../app.php';
if(!isset($_SESSION['logged'])){
  Route::redirect('/foro');
}

$teacher = new Teacher($_SESSION['logged']['user']['id']);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro - Mi Perfil</title>
  <?php
    Route::fontawasome();
    Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>
<body class='pb-5'>
  <?php    
      Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5">
    <h1 class="text-center">Mi Perfil</h1>
    <div class="row mt-5">

      <div class="col-lg-6 col-sm-12">
        <form action="<?=  Route::url('/foro/profesor/includes/profiles.php') ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_teacher" value="<?= $teacher->id ?>">
          <img src="<?= $teacher->profilePicture() ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block">
          <div class="form-group text-center mt-2">
            <button id="pictureBtn" type='button' class="btn btn-secondary">Cambiar Foto</button>
            <button id="pictureCancel" type='button' hidden class="btn btn-danger"><i class="fas fa-times"></i></button>
            <input type="file" hidden name="picture" id="picture" accept="image/jpg,image/png,image/gif,image/jpeg">
          </div>
          <hr class="mb-3"/>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name">Nombre</label>
              <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name">
            </div>
            <div class="form-group col-md-6">
              <label for="lastname">Apellidos</label>
              <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='lastName' id="lastname">
            </div>            
            <div class="form-group col-md-12">
              <label for="email1">E-mail Principal</label>
              <input type="email" value='<?= $teacher->email1 ?>' class="form-control" name='email1' id="email1">
              <label for="email2">E-mail Secundario</label>
              <input type="email" value='<?= $teacher->email2 ?>' class="form-control" name='email2' id="email2">
            </div>
            <div class="form-group  col-md-6">
              <label for="pass1">Nueva Clave</label>
              <input type="password" class="form-control pass" name='password' id="pass1">
              <label for="pass2">Confirmar Clave</label>
              <input type="password" class="form-control pass" id="pass2">
              <div class="invalid-feedback">Las claves no coinciden</div>             
            </div>
            <div class="form-group col-md-12">
              <button type="submit" class="btn btn-primary btn-lg btn-block">Guardar</button>
            </div>
          </div>
        </form>
      </div>

      <div class="offset-lg-2 col-lg-4 col-sm-12 mt-sm-3">
        <hr class="d-lg-none d-sm-block"/>
        <div class="card border-info">
          <h6 class="card-header bg-gradient-info bg-info">
            Información importante
          </h6>
          <div class="card-body">
            <p class="text-monospace">ID: <span class="badge badge-info"><?= $teacher->id ?> </span></p>
            <p class="text-monospace">Usuario: <span class="badge badge-info"><?= $teacher->usuario ?></span></p>
            <p class="text-monospace">Salon Hogar: <span class="badge badge-info"><?= $teacher->grado ?></span></p>
            <p class="text-monospace">Total de estudiantes: <span class="badge badge-info"><?= sizeof($teacher->homeStudents()) ?></span></p>
          </div>
        </div>

      </div>

    </div>
  </div>


  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');  
  ?>
</body>

</html>