<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$DataTable = true;
$teacher = new Teacher(Session::id());
$lang = new Lang([
['Informe de tareas','Homeworks report'],
['Lista de tareas','Homework list'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head> 
  <?php
  $title = $lang->translation("Informe de tareas");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Lista de tareas") ?></h1>
    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    ?>

  </div>
  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>