<?php

use Classes\Route;

?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
   <title>Page not found</title>
   <script src="https://kit.fontawesome.com/f4bf4b6549.js" crossorigin="anonymous"></script>
   <?php
   Route::css("/bootstrap/dist/css/bootstrap.min.css", true);
   ?>
</head>

<body>
   <div class="container mt-5">
      <div class="jumbotron text-center">
         <i class="fas fa-times fa-7x text-danger"></i>
         <h1>Pagina no encontrada</h1>
         <h2 class="text-danger">404</h2>
         <?php if ($_SERVER["REQUEST_URI"] !== $_SERVER["SCRIPT_NAME"]) : ?>
            <a href="<?php $_SERVER["REQUEST_URI"] ?>" class="btn btn-danger mt-3">Volver a la pagina anterior</a>
         <?php endif ?>
      </div>
   </div>
</body>

</html>