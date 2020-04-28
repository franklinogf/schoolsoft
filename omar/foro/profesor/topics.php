<?php
require_once '../../app.php';

use Classes\Util;
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
   <title>Foro - Temas</title>
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
      ?>

      <!-- topics table -->
      <button id="newTopic" type="button" class="btn btn-secondary hidden">Nuevo tema</button>

      <table class="topicsTable table table-striped table-hover cell-border w-100 shadow">
         <thead>
            <tr class="bg-gradient-primary bg-primary">
               <th>Titulo</th>
               <th>Cierre</th>
               <th>Fecha</th>
               <th>Hora</th>
            </tr>
         </thead>
         <tbody>
         </tbody>
         <tfoot>
            <tr class="bg-gradient-secondary bg-secondary">
               <th>Titulo</th>
               <th>Cierre</th>
               <th>Fecha</th>
               <th>Hora</th>
            </tr>
            <tr class="bg-gradient-light bg-light">
               <td colspan="4"><button id="back" type="button" class="btn btn-block btn-primary">Atrás</button></td>
            </tr>
         </tfoot>
      </table>


      <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-lg" role="dialog">
            <div class="modal-content">
               <div class="modal-header bg-primary">
                  <h5 class="modal-title">Modificar tema</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="<?= Route::url('/foro/profesor/includes/topics.php') ?>" method="POST">
                  <input type="hidden" name="class" id="class">
                  <div class="modal-body">
                     <div class="form-row">
                        <div class="form-group col-12">
                           <label for="modalTitle">Titulo del tema:</label>
                           <input type="text" class="form-control" name='title' id="modalTitle">
                        </div>
                        <div class="form-group  col-12">
                           <label for="modalDescription">Descripcion del tema</label>
                           <textarea class="form-control" name="description" id="modalDescription" rows="3" required></textarea>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group col-6">
                           <label class="d-block">Tema disponible?</label>

                           <div class="custom-control custom-radio custom-control-inline">
                              <input class="custom-control-input" type="radio" name="state" id="radio1" value="a">
                              <label class="custom-control-label" for="radio1">Si</label>
                           </div>
                           <div class="custom-control custom-radio custom-control-inline">
                              <input class="custom-control-input" type="radio" name="state" id="radio2" value="c">
                              <label class="custom-control-label" for="radio2">No</label>
                           </div>

                        </div>
                        <div class="form-group col-6">
                           <label for="modalUntilDate">Disponible hasta:</label>
                           <input type="date" class="form-control" name='untilDate' id="modalUntilDate" min='<?= Util::date() ?>' value="<?= Util::date() ?>">
                        </div>

                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                     <button type="submit" name="insertTopic" class="btn btn-primary">Guardar</button>
                  </div>
               </form>
            </div>
         </div>
      </div>






   </div>
   <?php
   Route::includeFile('/foro/profesor/includes/layouts/scripts.php');
   Route::includeFile('/includes/datatable-js.php', true);
   Route::jqUI();
   ?>

</body>

</html>