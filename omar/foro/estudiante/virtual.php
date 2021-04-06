<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$student = new Student(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = "Salón Virtual";
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center">Salón Virtual</h1>

    <?php    
    $virtual = true;
    Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
    ?>

  </div>
  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
  ?>

</body>
</html>