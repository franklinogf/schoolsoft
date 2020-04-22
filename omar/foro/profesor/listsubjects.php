<?php

use Classes\Route;
use Classes\Controllers\Teacher;

include '../../app.php';
if (!isset($_SESSION['logged'])) {
  Route::redirect('/foro');
}
$teacher = new Teacher($_SESSION['logged']['user']['id']);
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Foro - Mis Cursos</title>
  <?php  
  Route::includeFile('/foro/profesor/includes/layouts/links.php');
  Route::includeFile('/includes/datatable-css.php', true);
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 class="text-center">Mis Cursos</h1>
    <!-- classes table -->
    <table class="classesTable table table-striped table-hover cell-border w-100 shadow">
      <thead class="bg-gradient-primary bg-primary border-0">
        <tr>
          <th>Curso</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($teacher->classes() as $class) : ?>
          <tr>
            <td><?= $class->curso ?></td>
            <td><?= $class->desc1 ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
      <tfoot>
        <tr class="bg-gradient-secondary bg-secondary">
          <th>Curso</th>
          <th>Descripción</th>
        </tr>
      </tfoot>
    </table>  

  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  Route::includeFile('/includes/datatable-js.php', true);  
  ?>

</body>

</html>