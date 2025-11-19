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
["Informe de clases virtuales",'Virtual classes report'],
['Lista de clases virtuales','Virtual classes list'],
['Titulo', 'Title'],
['Fecha','Date'],
['Hora','Time'],
['Atrás','Go back']
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
  <?php
  $title = $lang->translation("Informe de clases virtuales");
  Route::includeFile('/foro/profesor/includes/layouts/header.php');
  ?>
</head>

<body>

  <?php
  Route::includeFile('/foro/profesor/includes/layouts/menu.php');
  ?>
  <div class="container mt-5 pb-5">
    <h1 id="header" class="text-center"><?= $lang->translation("Lista de clases virtuales") ?></h1>

    <?php
    Route::includeFile('/foro/profesor/includes/tables/tableClasses.php');
    ?>
    <!-- virtual classes table -->
    <div class="table_wrap">
      <table class="virtualClassesTable table table-striped table-hover cell-border w-100 shadow">
        <thead class="bg-gradient-primary bg-primary border-0">
          <tr>
            <th><?= $lang->translation("Titulo") ?></th>
            <th><?= $lang->translation("Fecha") ?></th>
            <th><?= $lang->translation("Hora") ?></th>
          </tr>
        </thead>
        <tbody>

        </tbody>
        <tfoot>
          <tr class="bg-gradient-secondary bg-secondary">
            <th><?= $lang->translation("Titulo") ?></th>
            <th><?= $lang->translation("Fecha") ?></th>
            <th><?= $lang->translation("Hora") ?></th>
          </tr>
          <tr class="bg-gradient-light bg-light">
            <td colspan="3"><button id="back" type="button" class="btn btn-block btn-primary"><?= $lang->translation("Atrás") ?></button></td>
          </tr>
        </tfoot>
      </table>
    </div>





  </div>
  <?php
  Route::includeFile('/includes/layouts/scripts.php', true);
  ?>

</body>

</html>