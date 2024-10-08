<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;

Session::is_logged();
$DataTable = true;
$student = new Student(Session::id());
$lang = new Lang([
['Salón Virtual','Virtual classroom'],
['Cerrar','Close']
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation('Salón Virtual');
  Route::includeFile('/foro/estudiante/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Salón Virtual") ?></h1>

    <?php
    $virtual = true;
    Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
    ?>


    <!-- Modal -->
    <div id="virtualModal" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="virtualModal" aria-hidden="true">
      <div class="modal-dialog">
        <form class="needs-validation" novalidate>
          <div class="modal-content">
            <div class="modal-header">
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
          <input type="hidden" id="virtualId">
        </form>
      </div>
    </div>

  </div>
  <?php
   Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>