<?php
require_once '../app.php';

use Classes\Route;
use Classes\Controllers\School;
use Classes\Session;

if (Session::is_logged(false)) {
   Route::redirect('/'.Session::type());
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title>Foro - Iniciar Sesión</title>
   <link rel="icon" href="<?= School::logo() ?>"/>
   <?php
   Route::css("/css/main-bootstrap.css");
   Route::css("/css/main.css",true);
   Route::css("/foro/css/login.css");
   ?>
</head>

<body class="text-center">
   <form class="form-signin" method='POST' action="<?= Route::url('/foro/includes/login.php') ?>">
      <img class="mb-4" src="<?= School::logo() ?>" alt="Logo" width="<?= __LOGO_SIZE_W ?>" height="<?= __LOGO_SIZE_H ?>">
      <h1 class="h3 mb-3 font-weight-normal ">Iniciar Sesión</h1>
      <?php if (isset($_SESSION['errorLogin'])) : ?>
         <div class="alert alert-danger animated zoomIn" role="alert">
            <strong>Error!</strong> <?= $_SESSION['errorLogin'] ?>
         </div>
         <?php unset($_SESSION['errorLogin']) ?>
      <?php endif ?>
      <label for="username" class="sr-only">Usuario:</label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-user fa-sm"></i></div>
         </div>
         <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" autofocus>         
      </div>

      <label for="password" class="sr-only">Clave:</label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-key fa-sm"></i></div>
         </div>
         <input type="password" id="password" name="password" class="form-control mb-0" placeholder="Clave">
      </div>
      
      <button class="btn btn-lg btn-primary btn-block mt-2" type="submit">Continuar</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
   </form>
   <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<?php

 Route::js('/js/app.js');
 Route::js('/foro/js/login.js');
?>
</body>

</html>