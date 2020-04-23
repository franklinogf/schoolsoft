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
  <title>Foro - Lista de clases</title>
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
    <h1 class="text-center">Lista de cursos </h1>
    <!-- classes table -->
    <form action="<?= Route::url('/foro/profesor/pdf/pdfUsers.php') ?>" method="POST" target="pdfUsers">
      <table class="classesTable table table-striped table-hover cell-border w-100 shadow">
        <thead class="bg-gradient-primary bg-primary border-0">
          <tr>
            <th class="checkbox">
              <div class="custom-control custom-checkbox">
                <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                <label class="custom-control-label" for="check1"></label>
              </div>
            </th>
            <th>Curso</th>
            <th>Descripción</th>
        </thead>
        <tbody>
          <?php foreach ($teacher->classes() as $class) : ?>
            <tr>
              <td>
                <div class="custom-control custom-checkbox">
                  <input class="custom-control-input check" type="checkbox" id="<?= $class->curso ?>" name="class[]" value="<?= $class->curso ?>">
                <label class=" custom-control-label" for="<?= $class->curso ?>"></label>
                </div>
              </td>
              <td><?= $class->curso ?></td>
              <td><?= $class->desc1 ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
        <tfoot>
          <tr class="bg-gradient-secondary bg-secondary">
            <th>
              <div class="custom-control custom-checkbox">
                <input class="custom-control-input bg-success checkAll" type="checkbox" id="check2">
                <label class="custom-control-label" for="check2"></label>
              </div>
            </th>
            <th>Curso</th>
            <th>Descripción</th>
          </tr>
          <tr class="bg-gradient-light bg-light">
            <td colspan="3"><button type="submit" class="btn btn-block btn-primary">Continuar</button></td>
          </tr>
        </tfoot>
      </table>
    </form>

    <div class="alert alert-danger mt-3 invisible" role="alert">
      Debe de seleccionar al menos uno
      <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

  </div>
  <?php
  Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
  Route::includeFile('/includes/datatable-js.php', true);
  ?>

</body>

</html>