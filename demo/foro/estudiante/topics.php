<?php
require_once __DIR__ . '/../../app.php';

use App\Models\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;


Session::is_logged();

$DataTable = true;
$student = Student::findOrFail(Session::id());
$lang = new Lang([
   ['Temas', 'Topics'],
   ["Mis cursos", 'My classes'],
   ['Leyenda', 'Legend'],
   ['Abierto', 'Open'],
   ['Cerrado', 'Closed'],
   ['Expiró', 'Expired'],
   ['Titulo', 'Title'],
   ['Fecha de cierre', 'Closing date'],
   ['Fecha creado', 'Created date'],
   ['Hora creado', 'Created time'],
   ['Atrás', 'Go back'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = __("Temas");
   Route::includeFile('/foro/estudiante/includes/layouts/header.php');
   ?>
</head>

<body>

   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>
   <div class="container mt-5 pb-5">
      <h1 id="header" class="text-center"><?= !isset($_GET['class'])  ? __("Mis Cursos") : __('Lista de temas') ?></h1>

      <?php if (!isset($_GET['class'])): ?>
         <?php
         Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
         ?>
      <?php else: ?>

         <!-- leyend -->
         <div class="card mx-auto bg-gradient-light bg-light leyend hidden" style="max-width: 30rem">
            <h6 class="card-header bg-gradient-info bg-info py-2"><?= __("Leyenda") ?></h6>
            <div class="card-body p-2">
               <div class="row text-center">
                  <div class="col-6 col-sm-4">
                     <i class="fas fa-square text-success"></i> <?= __("Abierto") ?>
                  </div>
                  <div class="col-6 col-sm-4">
                     <i class="fas fa-square text-danger"></i> <?= __("Cerrado") ?>
                  </div>
                  <div class="col-12 mt-2 col-sm-4 mt-sm-0">
                     <i class="fas fa-square text-warning"></i> <?= __("Expiró") ?>
                  </div>
               </div>
            </div>
         </div>

         <!-- topics table -->
         <table class="topicsTable table table-striped table-hover cell-border w-100 shadow table-pointer">
            <thead>
               <tr class="bg-gradient-primary bg-primary">
                  <th><?= $lang->translation("Titulo") ?></th>
                  <th><?= $lang->translation("Fecha de cierre") ?></th>
                  <th><?= $lang->translation("Fecha creado") ?></th>
                  <th><?= $lang->translation("Hora creado") ?></th>
               </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
               <tr class="bg-gradient-secondary bg-secondary">
                  <th><?= $lang->translation("Titulo") ?></th>
                  <th><?= $lang->translation("Fecha de cierre") ?></th>
                  <th><?= $lang->translation("Fecha creado") ?></th>
                  <th><?= $lang->translation("Hora creado") ?></th>
               </tr>
               <tr class="bg-gradient-light bg-light">
                  <td colspan="4"><button id="back" type="button" class="btn btn-block btn-primary"><?= $lang->translation("Atrás") ?></button></td>
               </tr>
            </tfoot>
         </table>
      <?php endif ?>
   </div>
   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>

</body>

</html>