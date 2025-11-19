<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$teacher = new Teacher(Session::id());
$lang = new Lang([
  ['Informe de tareas realizadas','Done homeworks report'],
  ['Lista de tareas realizadas','Done homework list'],
  ]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = $lang->translation("Informe de tareas realizadas");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Lista de tareas realizadas") ?></h1>

    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    Route::includeFile('/foro/profesor/includes/tables/tableHomeworks.php');
    ?>

    

   
    
  </div>
  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>