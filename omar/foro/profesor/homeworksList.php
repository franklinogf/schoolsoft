<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - Informe de Tareas</title>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center">Mis Cursos</h1>

    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    Route::includeFile('/foro/profesor/includes/tables/tablesHomeworks.php');
    ?>

    

   
    
  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  ?>

</body>

</html>