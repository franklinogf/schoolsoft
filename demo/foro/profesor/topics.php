<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$teacher = new Teacher(Session::id());
$lang = new Lang([
   ['Temas', 'Topics'],
   ['Mis cursos', 'My classes'],
   ['Leyenda', 'Legend'],
   ['Abierto', 'Open'],
   ['Cerrado', 'Closed'],
   ['Expiró', 'Expired'],
   ['Nuevo tema', 'New topic'],
   ['Titulo', 'Title'],
   ['Fecha de cierre', 'Closing date'],
   ['Fecha creado', 'Created date'],
   ['Hora creado', 'Created time'],
   ['Atrás', 'Go back'],
   ['Crear tema', 'Create topic'],
   ['Titulo', 'Title'],
   ['Descripción', 'Description'],
   ['¿Tema disponible?', 'Topic available?'],
   ['Disponible hasta', 'Available to'],
   ['Si', 'Yes'],
   ['Cerrar', 'Close'],
   ['Guardar', 'Save'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = $lang->translation('Topics');
   Route::includeFile('/foro/profesor/includes/layouts/header.php');
   ?>
</head>

<body>

   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>
   <div class="container mt-5 pb-5">
      <h1 id="header" class="text-center"><?= $lang->translation("Mis Cursos") ?></h1>
      <?php
      Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
      ?>
      <!-- leyend -->
      <div class="card mx-auto bg-gradient-light bg-light leyend hidden mb-3" style="max-width: 30rem">
         <h6 class="card-header bg-gradient-info bg-info py-2"><?= $lang->translation("Leyenda") ?></h6>
         <div class="card-body p-2">
            <div class="row text-center">
               <div class="col-6 col-sm-4">
                  <i class="fas fa-square text-success"></i> <?= $lang->translation("Abierto") ?>
               </div>
               <div class="col-6 col-sm-4">
                  <i class="fas fa-square text-danger"></i> <?= $lang->translation("Cerrado") ?>
               </div>
               <div class="col-12 mt-2 col-sm-4 mt-sm-0">
                  <i class="fas fa-square text-warning"></i> <?= $lang->translation("Expiró") ?>
               </div>
            </div>
         </div>
      </div>


      <!-- topics table -->
      <button id="newTopic" type="button" class="btn btn-secondary hidden"><i class="far fa-comment"></i> <?= $lang->translation("Nuevo tema") ?> </button>

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


      <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-lg" role="dialog">
            <div class="modal-content">
               <div class="modal-header bg-primary">
                  <h5 class="modal-title"><?= $lang->translation("Crear tema") ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="<?= Route::url('/foro/profesor/includes/topics.php') ?>" method="POST">
                  <input type="hidden" name="class" id="class">
                  <div class="modal-body">
                     <div class="form-row">
                        <div class="form-group col-12">
                           <label for="modalTitle"><?= $lang->translation("Titulo") ?></label>
                           <input type="text" class="form-control" name='title' id="modalTitle">
                        </div>
                        <div class="form-group  col-12">
                           <label for="modalDescription"><?= $lang->translation("Descripción") ?></label>
                           <textarea class="form-control" name="description" id="modalDescription" rows="3" required></textarea>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group col-6">
                           <label class="d-block"><?= $lang->translation("¿Tema disponible?") ?></label>

                           <div class="custom-control custom-radio custom-control-inline">
                              <input class="custom-control-input" type="radio" name="state" id="radio1" value="a">
                              <label class="custom-control-label" for="radio1"><?= $lang->translation("Si") ?></label>
                           </div>
                           <div class="custom-control custom-radio custom-control-inline">
                              <input class="custom-control-input" type="radio" name="state" id="radio2" value="c">
                              <label class="custom-control-label" for="radio2">No</label>
                           </div>

                        </div>
                        <div class="form-group col-6">
                           <label for="modalUntilDate"><?= $lang->translation("Disponible hasta") ?></label>
                           <input type="date" class="form-control" name='untilDate' id="modalUntilDate" min='<?= Util::date() ?>' value="<?= Util::date() ?>">
                        </div>

                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                     <button type="submit" name="insertTopic" class="btn btn-primary"><?= $lang->translation("Guardar") ?></button>
                  </div>
               </form>
            </div>
         </div>
      </div>






   </div>
   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>

</body>

</html>