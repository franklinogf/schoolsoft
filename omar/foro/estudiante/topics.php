<?php
require_once '../../app.php';

use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\Student;

Session::is_logged();
$jqUI = true;
$DataTable = true;
$student = new Student(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
   <title>Foro - Temas</title>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/links.php');
   ?>
</head>

<body>

   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/menu.php');
   ?>
   <div class="container mt-5 pb-5">
      <h1 id="header" class="text-center">Mis Cursos</h1>
      <?php
      Route::includeFile('/foro/estudiante/includes/tables/tableClasses.php');
      ?>

      <!-- topics table -->
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

   </div>
   <?php
   Route::includeFile('/foro/estudiante/includes/layouts/scripts.php');
   ?>

</body>

</html>