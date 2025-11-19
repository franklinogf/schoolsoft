<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$lang = new Lang([
  ['Debe de seleccionar al menos uno', 'You must select at least one'],
  ['Lista de estudiantes por curso', 'Student list per course'],
  ['Informe de estudiantes por curso', 'Student report per course']
]);
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Informe de estudiantes por curso");;
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 class="text-center"><?= $lang->translation("Lista de estudiantes por curso") ?></h1>
    <!-- classes table -->
    <form action="<?= Route::url('/foro/profesor/pdf/pdfClasses.php') ?>" method="POST" target="pdfClasses">
      <?php
      $__tableData = $teacher->classes();
      $__tableDataCheckbox = true;
      $__tableDataCheckBoxName = 'class';
      $__tableDataInfo = [
        [
          'title' => ["es" => "Curso", 'en' => "Class"],
          'values' => ['curso']
        ],
        [
          'title' => ["es" => "Descripción", 'en' => "Description"],
          'values' => ['desc1']
        ]
      ];
      $__dataPk = 'curso';
      Route::includeFile('/includes/layouts/table.php', true) ?>
    </form>

    <div class="alert alert-danger mt-3 invisible" role="alert">
      <?= $lang->translation("Debe de seleccionar al menos uno") ?>
      <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>

  </div>
  <?php
  $DataTable = true;
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>