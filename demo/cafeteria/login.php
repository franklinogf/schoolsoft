<?php
require_once '../app.php';

use Classes\Route;
use Classes\Session;

if (Session::is_logged(false)) {
   Route::redirect('/menu.php');
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title><?= __('Cafeteria') . ' - ' . __("Iniciar Sesión") ?></title>
   <link rel="icon" href="<?= school_logo() ?>" />
   <?php
   echo Route::bootstrapCSS();
   Route::css("/css/main.css", true);
   Route::css("/css/login.css", true);
   ?>
</head>

<body class="text-center">
   <img class="mb-4 img-fluid" src="<?= school_logo() ?>" alt="Logo" width="<?= school_config('app.logo.size.login') ?>">
   <form class="form-signin" method='POST' action="<?= Route::url('/cafeteria/includes/login.php') ?>">
      <h1 class="h3 mb-3 font-weight-normal "><?= __("Iniciar Sesión") ?></h1>
      <?php if (Session::get('errorLogin')) : ?>
         <div class="alert alert-danger animated zoomIn" role="alert">
            <strong><?= __("Error!") ?></strong> <?= Session::get('errorLogin', true) ?>
         </div>
      <?php endif ?>
      <label for="username" class="sr-only"><?= __("Usuario") ?></label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-user fa-sm"></i></div>
         </div>
         <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" autofocus>
      </div>

      <label for="password" class="sr-only"><?= __("Contraseña") ?></label>
      <div class="input-group">
         <div class="input-group-prepend">
            <div class="input-group-text"><i class="fas fa-key fa-sm"></i></div>
         </div>
         <input type="password" id="password" name="password" class="form-control mb-0" placeholder="Clave">
      </div>

      <button class="btn btn-lg btn-primary btn-block mt-2" type="submit"><?= __("Continuar") ?></button>
      <a class="btn btn-sm btn-secondary btn-block mt-2" href="<?= Route::url('/') ?>"><?= __("Pagina Principal") ?></a>
      <p class="mt-5 mb-3 text-muted"><?= __("Derechos reservados") ?> &copy; <?= date('Y') ?></p>
   </form>
   <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
   <?php

   Route::js('/js/app.js', true);
   Route::js('/js/login.js', true);
   ?>
</body>

</html>