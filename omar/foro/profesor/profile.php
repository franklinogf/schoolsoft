<?php
use Classes\Route;
use Classes\Controllers\Teacher;

include '../../app.php';

$teacher = new Teacher(16);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro - Mi Perfil</title>
  <?php
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
        <form>
          <img src="<?= $teacher->profilePicture() ?>" alt="Profile Picture" class="profile-picture img-thumbnail rounded mx-auto d-block">
          <div class="form-group text-center mt-2">
            <button type='button' class="btn btn-secondary">Cambiar Foto</button>
            <input type="file" hidden name="foto" id="foto">
          </div>
          <hr class="mb-3"/>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name">Nombre</label>
              <input type="text" value='<?= $teacher->nombre ?>' class="form-control" name='name' id="name">
            </div>
            <div class="form-group col-md-6">
              <label for="lastname">Apellidos</label>
              <input type="text" value='<?= $teacher->apellidos ?>' class="form-control" name='lastname' id="lastname">
            </div>            
            <div class="form-group col-md-12">
              <label for="email1">E-mail Principal</label>
              <input type="email" value='<?= $teacher->email1 ?>' class="form-control" name='email1' id="email1">
              <label for="email2">E-mail Secundario</label>
              <input type="email" value='<?= $teacher->email2 ?>' class="form-control" name='email2' id="email2">
            </div>
            <div class="form-group  col-md-6">
              <label for="pass1">Nueva Clave</label>
              <input type="password" class="form-control" name='pass1' id="pass1">
              <label for="pass2">Confirmar Clave</label>
              <input type="password" class="form-control" name='pass2' id="pass2">
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
          <h6 class="card-header bg-info">
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