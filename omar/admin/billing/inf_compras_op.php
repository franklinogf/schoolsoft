<?php
require_once '../../app.php';

use Classes\Controllers\Parents;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$lang = new Lang([
    ['Lista de estudiantes', 'Student List'],
    ['Atrás', 'Go back'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Deudas', 'Debtors'],
    ['Apellidos', 'Last Name'],
    ['Procesar', 'Process'],
    ['Enviar por E-mail', 'Send E-mail'],
    ['Se enviaron por correo electrónico todas las deudas que están marcadas en la lista.', 'All debts that are marked on the list were sent by email.'],
]);
$school = new School(Session::id());
$year = $school->info('year2');
$students = new Student();
$students = $students->all();
$target = 'excel_compra';
$target = 'pdf/pdf_compra';

$debe = 0;
foreach ($students as $student) {
    $debe = 0;
    $result10 = DB::table('pagos')
        ->whereRaw("id='$student->id' and ss='$student->ss' and year='$year' and baja='' and fecha_d <= '" . date('Y-m-d') . "'")->orderBy('codigo')->get();
    foreach ($result10 as $row10) {
        $debe = $debe + ($row10->deuda - $row10->pago);
    }
    $thisCourse2 = DB::table("year")->where([
        ['id', $student->id],
        ['ss', $student->ss],
        ['year', $year]
    ])->update([
        'tr1' => $debe,
    ]);
}

$students = DB::table('year')
    ->whereRaw("year='$year' and activo='' and tr1 > 0 ")->orderBy('tr1 DESC')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Lista de estudiantes');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Lista de estudiantes') . ' ' . $year ?> </h1>
        <div class="container mt-1">
            <form id="form" action="<?php echo $target ?>.php" method="post" target="studentID" target="_blank">
                <?php
                $students = DB::table('compra_cafeteria')->select("DISTINCT ss, apellido, nombre, grado")->where([['year', $year]])->orderBy('apellido, nombre')->get();
                $__tableData = $students;
                //            $__tableData = DB::table('year')->where([['tr1','>', 0], ['year', $year], ['activo', '']])->orderBy('tr1 DESC, apellidos')->get();
                $__tableDataCheckbox = true; #decirle que quiere usar los check box
                $__dataPk = 'ss'; #el identificador principal
                // Un array de las columnas y sus respectivos valores
                $__tableDataInfo = [
                    [
                        'title' => ['es' => 'Apellidos', 'en' => 'Last name'],
                        'values' => ['apellido']
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
                Route::includeFile('/includes/layouts/table.php', true);
                ?>
                <div><b>
                    </b></div>
                <input name="buscar" style="width: 140px;" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Procesar") ?>" />
            </form>
        </div>
    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script>
        $(document).ready(function() {
            $("#form").submit(function(e) {
                tableDataToSubmit("#form", dataTable[0], 'students[]')
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            idioma = '<?php echo $idioma; ?>';
            $('#excel_compra').click(function(e) {
                e.preventDefault()
                $('form').prop('action', 'pdf_compra.php');
                $('form').submit();
            });
        });
    </script>

</body>

</html>