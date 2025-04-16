<?php
require "./bootstrap.php";

use Classes\Route;

?>
<!DOCTYPE html>
<html lang="<?= config('app.locale') ?>">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   <title><?= __("Pagina no encontrada") ?></title>
   <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>
   <?php
   Route::css("css/main-bootstrap.css", true);
   ?>
</head>

<body>
   <div class="container mt-5">
      <div class="jumbotron text-center">
         <i class="fas fa-times fa-7x text-danger"></i>
         <h1><?= __("Pagina no encontrada") ?></h1>
         <h2 class="text-danger">404</h2>
      </div>
   </div>
</body>

</html>