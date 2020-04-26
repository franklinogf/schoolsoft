<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();

$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Foro - Tareas</title>
  <?php
  Route::fontawasome();
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  Route::includeFile('/includes/datatable-css.php', true);
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
  Route::includeFile('/includes/datatable-js.php', true);
  Route::jqUI();
  ?>

</body>

</html>