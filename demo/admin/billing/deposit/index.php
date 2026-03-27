<?php
require_once __DIR__ . '/../../../app.php';

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

//$estudiantesSS = $_REQUEST['students'] ?? [];

$estudiantesSS = $_REQUEST['students'] ?? '';

if ($estudiantesSS != '')
   {
   foreach ($estudiantesSS as $id2) 
           {

           $depo = DB::table('depositos')->where([
              ['id2', $id2]
           ])->orderBy('id')->first();


    DB::table('depositosb')->insert([
        'id' => $depo->id ?? '',
        'ss' => $depo->ss ?? '',
        'fecha' => $depo->fecha ?? '',
        'hora' => $depo->hora ?? '',
        'cantidad' => $depo->cantidad ?? '',
        'year' => $depo->year ?? '',
        'grado' => $depo->grado ?? '',
        'studentId' => $depo->studentId ?? '',
        'email' => $depo->email ?? '',
        'descripcion' => $depo->descripcion ?? '',
        'autorizacion' => $depo->autorizacion ?? '',
        'referencia' => $depo->referencia ?? '',
        'tarjetaUltimosDigitos' => $depo->tarjetaUltimosDigitos ?? '',
        'zip' => $depo->zip ?? '',
        'tipoDePago' => $depo->tipoDePago ?? '',
        'nombreEnLaTarjeta' => $depo->nombreEnLaTarjeta ?? '',
        'otros' => $depo->otros ?? '',
        'date' => $depo->date,
        'fechab' => date('Y-m-d'),
    ]);


           DB::table('depositos')->where('id2', $id2)->delete();
           }
   }


//$students2 = DB::table('depositos')->select("DISTINCT ss, apellidos, nombre")->where([
$students2 = DB::table('depositos')->where([
        ['year', $year]
    ])->orderBy('fecha DESC, hora DESC')->get();

$students3 = DB::table('depositosb')->where([
        ['year', $year]
    ])->orderBy('fecha DESC, hora DESC')->get();



?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Lista de depositos para borrar');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Lista de depositos para borrar') ?></h1>
        <form id="form" action="" method="POST">
            <?php
            $__tableData = $students2; // $students->all(); #Informacion que se va a utilizar
            $__tableDataCheckbox = true; #decirle que quiere usar los check box
            $__dataPk = 'id2'; #el identificador principal
            // Un array de las columnas y sus respectivos valores
            $__tableDataInfo = [
                [
                    'title' => ['es' => 'ID', 'en' => 'ID'],
                    'values' => ['id']
                ],
                [
                    'title' => ['es' => 'S.S.', 'en' => 'S.S.'],
                    'values' => ['ss']
                ],
                [
                    'title' => ['es' => 'Fecha', 'en' => 'Date'],
                    'values' => ['fecha']
                ],
                [
                    'title' => ['es' => 'Hora', 'en' => 'S.S.'],
                    'values' => ['hora']
                ],
                [
                    'title' => ['es' => 'Cantidad', 'en' => 'Amount'],
                    'values' => ['cantidad']
                ],
                [
                    'title' => ['es' => 'referencia', 'en' => 'referen'],
                    'values' => ['referencia']
                ],
                [
                    'title' => ['es' => 'tipoDePago', 'en' => 'referen'],
                    'values' => ['tipoDePago']
                ],

            ];
            Route::includeFile('/includes/layouts/table.php', true);
            ?>


        </form>


    </div>


    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation('Lista de depositos borrados') ?></h1>
        <form id="form" action="" method="POST">
            <?php
            $__tableData = $students3; // $students->all(); #Informacion que se va a utilizar
            $__tableDataCheckbox = false; #decirle que quiere usar los check box
            $__dataPk = ''; #el identificador principal
            // Un array de las columnas y sus respectivos valores
            $__tableDataInfo = [
                [
                    'title' => ['es' => 'ID', 'en' => 'ID'],
                    'values' => ['id']
                ],
                [
                    'title' => ['es' => 'S.S.', 'en' => 'S.S.'],
                    'values' => ['ss']
                ],
                [
                    'title' => ['es' => 'Fecha', 'en' => 'Date'],
                    'values' => ['fecha']
                ],
                [
                    'title' => ['es' => 'Hora', 'en' => 'S.S.'],
                    'values' => ['hora']
                ],
                [
                    'title' => ['es' => 'Cantidad', 'en' => 'Amount'],
                    'values' => ['cantidad']
                ],
                [
                    'title' => ['es' => 'referencia', 'en' => 'referen'],
                    'values' => ['referencia']
                ],
                [
                    'title' => ['es' => 'tipoDePago', 'en' => 'referen'],
                    'values' => ['tipoDePago']
                ],
                [
                    'title' => ['es' => 'Borrado', 'en' => 'Deleted'],
                    'values' => ['fechab']
                ],
            ];
            Route::includeFile('/includes/layouts/table.php', true);
            ?>


        </form>


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