<?php

use Classes\Lang;
use Classes\Route;

$lang = new Lang([
   ['Pagina no encontrada', 'Page not found']
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?> ">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title><?= $lang->translation("Pagina no encontrada") ?></title>
   <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>
   <?php
   Route::css("/bootstrap/dist/css/bootstrap.min.css", true);
   ?>
</head>

<body>
   <div class="container mt-5">
      <div class="jumbotron text-center">
         <i class="fas fa-times fa-7x text-danger"></i>
         <h1><?= $lang->translation("Pagina no encontrada") ?></h1>
         <h2 class="text-danger">404</h2>
      </div>
   </div>
</body>

</html>