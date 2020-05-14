<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Informe de tareas";
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
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
    ?>

  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  ?>

</body>

</html>