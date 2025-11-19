<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase\DB;

Session::is_logged();
$lang = new Lang([
    ['ID del estudiante', 'Student ID'],
    ['Atrás', 'Go back'],
    ['Detalle de compras de articulos de los estudiantes', 'Details of student item purchases'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$students = new Student();

$students2 = DB::table('year')->where([
    ['cantidad', '<>', 0],
    ['year', $year]
])->orderBy('apellidos')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Detalle de compras de articulos de los estudiantes');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Detalle de compras de articulos de los estudiantes') ?></h1>
        <form id="form" action="pdf/articulos_pagados.php" target="_blank" method="POST">
            <?php
            $__tableData = $students2; // $students->all(); #Informacion que se va a utilizar
            $__tableDataCheckbox = true; #decirle que quiere usar los check box
            $__dataPk = 'ss'; #el identificador principal
            // Un array de las columnas y sus respectivos valores
            $__tableDataInfo = [
                [
                    'title' => ['es' => 'Apellidos', 'en' => 'Last name'],
                    'values' => ['apellidos']
                ],
                [
                    'title' => ['es' => 'Nombre', 'en' => 'Name'],
                    'values' => ['nombre']
                ],
                [
                    'title' => ['es' => 'Grado', 'en' => 'Grade'],
                    'values' => ['grado']
                ],
            ];
            //                    'title' => ["es" => "Nombre compleo", 'en' => "Full Name"],
            Route::includeFile('/includes/layouts/table.php', true);
            ?>
        </form>

        <a href="<?= Route::url('/admin/access/reports/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>

    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <!-- Helper para hacer que si se envien todo los que se han seleccionado,
    aqui se pone el nombre con el que va a recibir en la otra pagina -->
    <script>
        $(document).ready(function() {
            $("#form").submit(function(e) {
                tableDataToSubmit("#form", dataTable[0], 'students[]')
            });
        });
    </script>
</body>

</html>