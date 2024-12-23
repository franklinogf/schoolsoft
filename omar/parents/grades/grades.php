<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Parents;
use Classes\Controllers\Student;

Session::is_logged();
$parents = new Parents(Session::id());
$year = $parents->info('year');
$studentSS = $_POST['studentSS'];
$trimester = $_POST['trimester'];
$trimesterNumber = substr($trimester, -1);
$report = $_POST['area'];
$student = new Student($studentSS);
$sumTrimester = $student->info('sutri') === 'NO'; //NO === SI
DB::table('acuse')->insert([
    'id' => $parents->id,
    'ss' => $studentSS,
    'grado' => $student->grado,
    'year' => $year,
    'ip' => Util::getIp(),
    'fecha' => Util::date(),
    'hora' => Util::time(),
    'tri' => $trimesterNumber,
    'cn' => '',
    'tra' => $report,
    'tri2' => $trimester,
    'fra' => '',
    'hoja' => '4'
]);
$_info = [
    "Notas" => [
        'table' => 'padres',
        'totalGrade' => 'nota',
        'values' => [
            [
                "table" => "padres4",
                "title" => "Pruebas cortas",
                "report" => "Pruebas-Cortas",
                "column" => "pc"
            ],
            [
                "table" => "padres2",
                "title" => "Trabajos diarios",
                "report" => "Trab-Diarios",
                "column" => "td"
            ],
            [
                "table" => "padres3",
                "title" => "Trabajos de libreta",
                "report" => "Trab-Libreta",
                "column" => "tl"
            ],
        ],
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],
        ]
    ],
    "Trimestral" => [
        'table' => 'padres',
        'totalGrade' => 'nota',
        'conduct' => 'con',
        'message' => 'com'
    ],
    "Cond-Asis" => [
        'table' => 'padres',
        'conduct' => 'con',
        'absence' => 'aus',
        'tardy' => 'tar'
    ],
    "Totales" => [
        'table' => 'padres',
        'semester1' => ['nota1', 'nota2', 'sem1'],
        'semester2' => ['nota3', 'nota4', 'sem2'],
    ]
];

$thisReport = $_info[$report][$trimester] ?? null;
$otherReports = $_info[$report]["values"] ?? null;
$trimesterNumber = substr($trimester, -1);
$totalGradeColumn = $_info[$report]['totalGrade'] ?? null;
if ($totalGradeColumn !== null) {
    $totalGradeColumn .= $trimesterNumber;
}
$lang = new Lang([
    ["Selección de notas", "Selection of grades"],
    ["Tarjeta de notas por curso", "Grades card per class"],
    ["Estás Notas No Necesariamente Son Finales, Pueden Cambiar", "These notes are not necessarily final, they can change"],
    ['Nota', "Grade"],
    ["Valor", "Value"],
    ["Fecha", "Date"],
    ["Tema", "Subject"],
    ["Notas", "Grades"],
    ["Pruebas cortas", "Short tests"],
    ["Trabajos diarios", "Daily homework"],
    ["Trabajos de libreta", "Book work"],
    ["Aun no tiene notas en este trimestre", "Has no grades in this trimester yet"],
    ["Notas trimestrales", "Quarterly notes"],
    ["Curso", "Class"],
    ["Conducta", "Behavior"],
    ["Mensaje", "Message"],
    ["Conducta y Asistencia", "Behavior and attendance"],
    ["Con", "Beh"],
    ['Aus', 'Abs'],
    ['Tar', "Tar"],
    ["Conducta", "Behavior"],
    ["Ausencia", "Absence"],
    ["Tardanza", "Tardy"],
    ["Totales por curso", "Total per class"],
    ["Primer Semestre", "First semester"],
    ["Segundo Semestre", "Second semester"],
    ["Atrás", "Go back"]
]);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Selección de notas");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container mt-3 mb-5">
        <div class="text-right">
            <a class="btn btn-primary" href="<?= Route::url("/parents/grades/gradesOptions.php?studentSS=$studentSS") ?>"><?= $lang->translation("Atrás") ?></a>
        </div>
        <?php if ($report === 'Notas'): ?>
            <h1 class="text-center my-2"><?= $lang->translation("Tarjeta de notas por curso") ?></h1>
            <h2 class="text-center mt-4"><?= $student->fullName() ?></h2>
            <p class="text-center"><?= str_replace('Trimestre', $lang->translation("Trimestre"), str_replace('-', ' ', $trimester)) ?></p>
            <p class="text-center text-info"><?= $lang->translation("Estás Notas No Necesariamente Son Finales, Pueden Cambiar") ?></p>

            <div class="accordion my-4" id="classesAccordion">
                <?php foreach ($student->classes() as $class): ?>
                    <div class="card">
                        <div class="card-header" id="heading<?= $class->curso ?>">
                            <h2 class="mb-0">
                                <button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?= $class->curso ?>" aria-expanded="true" aria-controls="collapse<?= $class->curso ?>">
                                    <?= "$class->descripcion - $class->curso" ?>
                                </button>
                            </h2>
                        </div>

                        <div id="collapse<?= $class->curso ?>" class="collapse" aria-labelledby="heading<?= $class->curso ?>" data-parent="#classesAccordion">
                            <div class="card-body">
                                <?php
                                $fatherTable = DB::table($_info[$report]['table'])->Where([
                                    ['ss', $studentSS],
                                    ['year', $year],
                                    ['curso', $class->curso]
                                ])->first();
                                $value = DB::table('valores')->where([
                                    ['curso', $class->curso],
                                    ['trimestre', $trimester],
                                    ['nivel', $report],
                                    ['year', $year]
                                ])->first();
                                ?>
                                <h5><?= $lang->translation($report) ?></h5>
                                <?php if (is_numeric($fatherTable->{$totalGradeColumn})): ?>
                                    <table class="table table-bordered table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th><?= $lang->translation("Nota") ?></th>
                                                <th><?= $lang->translation("Valor") ?></th>
                                                <th><?= $lang->translation("Fecha") ?></th>
                                                <th><?= $lang->translation("Tema") ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $valIndex = 1;
                                            $grades = $thisReport['grades'];
                                            for ($i = $grades[0]; $i <= $grades[1]; $i++):
                                                // var_dump($fatherTable);
                                                if (is_numeric($fatherTable->{"not$i"})):
                                                    ?>
                                                    <tr>
                                                        <td><?= $valIndex ?></td>
                                                        <td><?= $fatherTable->{"not$i"} ?></td>
                                                        <td><?= $value->{"val$valIndex"} ?></td>
                                                        <td><?= $value->{"fec$valIndex"} ?></td>
                                                        <td><?= $value->{"tema$valIndex"} ?></td>
                                                        <?php $valIndex++ ?>
                                                    </tr>
                                                <?php endif ?>
                                            <?php endfor ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p><?= $lang->translation("Aun no tiene notas en este trimestre") ?></p>
                                <?php endif ?>
                                <!-- Others grades -->
                                <?php foreach ($otherReports as $other):
                                    $fatherTable = DB::table($other['table'])->Where([
                                        ['ss', $studentSS],
                                        ['year', $year],
                                        ['curso', $class->curso]
                                    ])->first();
                                    $value = DB::table('valores')->where([
                                        ['curso', $class->curso],
                                        ['trimestre', $trimester],
                                        ['nivel', $other['report']],
                                        ['year', $year]
                                    ])->first();
                                    ?>
                                    <h5><?= $lang->translation($other['title']) ?></h5>
                                    <?php if (is_numeric($fatherTable->{$totalGradeColumn})): ?>
                                        <table class="table table-bordered table-sm">
                                            <thead class="thead-light">
                                                <th>#</th>
                                                <th><?= $lang->translation("Nota") ?></th>
                                                <th><?= $lang->translation("Valor") ?></th>
                                                <th><?= $lang->translation("Fecha") ?></th>
                                                <th><?= $lang->translation("Tema") ?></th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $valIndex = 1;
                                                $grades = $thisReport['grades'];
                                                for ($i = $grades[0]; $i <= $grades[1]; $i++):
                                                    // var_dump($fatherTable);
                                                    if (is_numeric($fatherTable->{"not$i"})):
                                                        ?>
                                                        <tr>
                                                            <td><?= $valIndex ?></td>
                                                            <td><?= $fatherTable->{"not$i"} ?></td>
                                                            <td><?= $value->{"val$valIndex"} ?></td>
                                                            <td><?= $value->{"fec$valIndex"} ?></td>
                                                            <td><?= $value->{"tema$valIndex"} ?></td>
                                                            <?php $valIndex++ ?>
                                                        </tr>
                                                    <?php endif ?>
                                                <?php endfor ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p><?= $lang->translation("Aun no tiene notas en este trimestre") ?></p>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php elseif ($report === 'Trimestral'): ?>
            <h1 class="text-center my-2"><?= $lang->translation("Notas trimestrales") ?></h1>
            <h2 class="text-center mt-4"><?= $student->fullName() ?></h2>
            <p class="text-center"><?= str_replace('Trimestre', $lang->translation("Trimestre"), str_replace('-', ' ', $trimester)) ?></p>
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th><?= $lang->translation("Curso") ?></th>
                        <th><?= $lang->translation("Nota") ?></th>
                        <th><?= $lang->translation("Conducta") ?></th>
                        <th><?= $lang->translation("Mensaje") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student->classes() as $class):
                        $fatherTable = DB::table($_info[$report]['table'])->Where([
                            ['ss', $studentSS],
                            ['year', $year],
                            ['curso', $class->curso]
                        ])->first();
                        ?>
                        <tr>
                            <td><?= $class->descripcion ?></td>
                            <td class="text-center"><?= $fatherTable->{$_info[$report]['totalGrade'] . $trimesterNumber} ?></td>
                            <td class="text-center"><?= $fatherTable->{$_info[$report]['conduct'] . $trimesterNumber} ?></td>
                            <td><?= $fatherTable->{$_info[$report]['message'] . $trimesterNumber} ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php elseif ($report === 'Cond-Asis'): ?>
            <h1 class="text-center my-2"><?= $lang->translation("Conducta y Asistencia") ?></h1>
            <h2 class="text-center mt-4"><?= $student->fullName() ?></h2>
            <p class="my-0"><small class="text-muted"><?= $lang->translation("Con") ?> = <?= $lang->translation("Conducta") ?></small></p>
            <p class="my-0"><small class="text-muted"><?= $lang->translation("Aus") ?> = <?= $lang->translation("Ausencia") ?></small></p>
            <p class="my-0"><small class="text-muted"><?= $lang->translation("Tar") ?> = <?= $lang->translation("Tardanza") ?></small></p>
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th class="align-middle" rowspan="2"><?= $lang->translation("Curso") ?></th>
                        <th colspan="3"><?= $lang->translation("Trimestre") ?> 1</th>
                        <th colspan="3"><?= $lang->translation("Trimestre") ?> 2</th>
                        <th colspan="3"><?= $lang->translation("Trimestre") ?> 3</th>
                        <th colspan="3"><?= $lang->translation("Trimestre") ?> 4</th>
                    </tr>
                    <tr>
                        <th><?= $lang->translation("Con") ?></th>
                        <th><?= $lang->translation("Aus") ?></th>
                        <th><?= $lang->translation("Tar") ?></th>
                        <th><?= $lang->translation("Con") ?></th>
                        <th><?= $lang->translation("Aus") ?></th>
                        <th><?= $lang->translation("Tar") ?></th>
                        <th><?= $lang->translation("Con") ?></th>
                        <th><?= $lang->translation("Aus") ?></th>
                        <th><?= $lang->translation("Tar") ?></th>
                        <th><?= $lang->translation("Con") ?></th>
                        <th><?= $lang->translation("Aus") ?></th>
                        <th><?= $lang->translation("Tar") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student->classes() as $class):
                        $fatherTable = DB::table($_info[$report]['table'])->Where([
                            ['ss', $studentSS],
                            ['year', $year],
                            ['curso', $class->curso]
                        ])->first();
                        ?>
                        <tr>
                            <td><?= $class->descripcion ?></td>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <td class="text-center"><?= $fatherTable->{$_info[$report]['conduct'] . $i} ?></td>
                                <td class="text-center"><?= $fatherTable->{$_info[$report]['absence'] . $i} ?></td>
                                <td class="text-center"><?= $fatherTable->{$_info[$report]['tardy'] . $i} ?></td>
                            <?php endfor ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <h1 class="text-center my-2"><?= $lang->translation("Totales por curso") ?></h1>
            <h2 class="text-center mt-4"><?= $student->fullName() ?></h2>
            <p class="text-center text-info"><?= $lang->translation("Estás Notas No Necesariamente Son Finales, Pueden Cambiar") ?></p>
            <table class="table table-bordered table-sm">
                <thead class="thead-light text-center">
                    <tr>
                        <th class="align-middle" rowspan="2"><?= $lang->translation("Curso") ?></th>
                        <th colspan="<?= $sumTrimester ? '2' : '3' ?>"><?= $lang->translation("Primer Semestre") ?></th>
                        <th colspan="<?= $sumTrimester ? '2' : '3' ?>"><?= $lang->translation("Segundo Semestre") ?></th>
                    </tr>
                    <tr>
                        <?php if ($sumTrimester): ?>
                            <th>Tri-1</th>
                            <th>Sem-1</th>
                            <th>Tri-3</th>
                            <th>Sem-2</th>
                        <?php else: ?>
                            <th>Tri-1</th>
                            <th>Tri-2</th>
                            <th>Sem-1</th>
                            <th>Tri-2</th>
                            <th>Tri-3</th>
                            <th>Sem-2</th>
                        <?php endif ?>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($student->classes() as $class):
                        $fatherTable = DB::table($_info[$report]['table'])->Where([
                            ['ss', $studentSS],
                            ['year', $year],
                            ['curso', $class->curso]
                        ])->first();
                        ?>
                        <tr>
                            <td><?= $class->descripcion ?></td>
                            <?php for ($i = 1; $i <= 2; $i++): ?>
                                <td class="text-center"><?= $fatherTable->{$_info[$report]["semester$i"][0]} ?></td>
                                <td class="text-center"><?= $fatherTable->{$_info[$report]["semester$i"][1]} ?></td>
                                <?php if (!$sumTrimester): ?>
                                    <td class="text-center"><?= $fatherTable->{$_info[$report]["semester$i"][2]} ?></td>
                                <?php endif ?>
                            <?php endfor ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>