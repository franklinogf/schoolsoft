<?php
require_once __DIR__ . '/../../app.php';

use App\Models\Teacher;
use Classes\Lang;
use Classes\Route;
use Classes\Session;


Session::is_logged();
$DataTable = true;
$teacher = Teacher::findOrFail(Session::id());
$lang = new Lang([
   ['Tareas recibidas', 'Homeworks received'],
   ['Mis cursos', 'My classes'],
   ['Cerrar', 'Close'],
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <?php
   $title = __('Tareas recibidas');
   Route::includeFile('/foro/profesor/includes/layouts/header.php');
   ?>
</head>

<body>

   <?php
   Route::includeFile('/foro/profesor/includes/layouts/menu.php');
   ?>
   <div class="container mt-5 pb-5">
      <h1 id="header" class="text-center"><?= !isset($_GET['class']) ? __("Mis cursos") : __('Lista de tareas') ?></h1>

      <?php if (!isset($_GET['class'])): ?>
         <?php
         Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
         ?>
      <?php else: ?>
         <?php
         Route::includeFile('/foro/profesor/includes/tables/tableHomeworks.php');
         ?>

         <!-- modal -->
         <div id="myModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
               <div class="modal-content">
                  <div class="modal-header bg-primary">
                     <h5 class="modal-title"></h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">

                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $lang->translation("Cerrar") ?></button>
                  </div>

               </div>
            </div>
         </div>
      <?php endif ?>

   </div>
   <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
   ?>

</body>

</html>