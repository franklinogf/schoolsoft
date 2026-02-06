<?php
require_once __DIR__ . '/../../app.php';

use App\Enums\GradePageEnum;
use App\Enums\TrimesterEnum;
use App\Models\Admin;
use App\Models\Classes;
use App\Models\Teacher;
use App\Models\Value;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Builder;

Session::is_logged();
Server::is_post();
$_class = $_POST['class'];
$_trimester = TrimesterEnum::from($_POST['tri']);
$_report = GradePageEnum::from($_POST['tra']);

$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year();
$optionCppd = $school->cppd === 'Si';
$sumTrimester = $school->sutri === 'NO'; //NO === SI

$_value = Value::findOrCreateFor($_class, $_trimester, $_report, $year);

$gradeInfo = Classes::query()
    ->ofClass($_class)
    ->first();


$optionLetter = !$gradeInfo ? false : $gradeInfo->letra === "ON";

// only this school
if (__ONLY_CBTM__) {
    $gradeInfo->nota_por = '1';
}

// function generateColumn(string $header, string $col, bool $readonly = false, ?string $class = null, ?string $text = null)
// {
//     return [
//         'header' => $header,
//         'column' => $col,
//         'readonly' => $readonly,
//         'class' => $class,
//         'text' => $text,
//     ];
// }
// function generateGradeColumns(string $colNumber, ?string $bonusCol = null)
// {
//     $bonus = $bonusCol ?? 'not' . $colNumber . '0';
//     $cols = [
//         generateColumn(__('Bono'), $bonus, class: 'bonus'),
//     ];

//     if (__ONLY_CBTM__) {
//         $cols[] = generateColumn(__('Promedio'), 'average' . $colNumber, true, 'average', '60%');
//     }

//     return [
//         ...$cols,
//         generateColumn(__('T-Diario'), 'td' . $colNumber, true, 'tdia', __ONLY_CBTM__ ? '10%' : null),
//         generateColumn(__('T-Libreta'), 'tl' . $colNumber, true, 'tlib', __ONLY_CBTM__ ? '10%' : null),
//         generateColumn(__('P-Cor'), 'pc' . $colNumber, true, 'pcor', __ONLY_CBTM__ ? '20%' : null),
//         generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
//         generateColumn(__('TDP'), 'por' . $colNumber, true, 'tdp'),
//         generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
//     ];
// }

// function generateDHColumns(string $colNumber)
// {
//     $bonus = 'not' . $colNumber . '0';
//     return [
//         generateColumn(__('Nota') . ' 10', $bonus, false, 'grade'),
//         generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
//         generateColumn(__('TDP'), 'por' . $colNumber, true, 'tdp'),
//         generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
//     ];
// }
if ($optionCppd) {
    $_info = [
        "Notas" => [
            'table' => 'padres',
            'title' => 'Notas',
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 12],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [13, 24],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [25, 36],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]
            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'grades' => [37, 48],
                'values' => [
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]
            ]
        ],
        "V-Nota" => [
            'table' => 'padres',
            'title' => 'Notas de verano',
            'Verano' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 7],
                'others' => ['con1', 'aus1', 'tar1'],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ]
        ]
    ];
} else {
    // if (__ONLY_CBTM__) {
    //     $columns = [
    //         'es' => ['Bono', 'Promedio', 'T-Diario', 'T-Libreta', 'P-Cor'],
    //         'en' => ['Bonus', 'Average', 'DW', 'HW', 'Quiz'],
    //         'text' => [false, '60%', '10%', '10%', '20%']
    //     ];
    // } else {
    //     $columns = [
    //         'es' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
    //         'en' => ['Bonus', 'DW', 'HW', 'Quiz'],
    //         'text' => [false, false, false, false]
    //     ];
    // }

    // $_info = [
    //     GradePageEnum::GRADES->value => [
    //         'title' => __('Notas'),
    //         TrimesterEnum::FIRST->value => [
    //             'table' => 'padres',
    //             'grades' => [1, 9],
    //             "columns" => generateGradeColumns('1'),
    //         ],
    //         TrimesterEnum::SECOND->value => [
    //             'table' => 'padres',
    //             'grades' => [11, 19],
    //             "columns" => generateGradeColumns('2'),
    //         ],
    //         TrimesterEnum::THIRD->value => [
    //             'table' => 'padres',
    //             'grades' => [21, 29],
    //             "columns" => generateGradeColumns('3'),
    //         ],
    //         TrimesterEnum::FOURTH->value => [
    //             'table' => 'padres',
    //             'grades' => [31, 39],
    //             "columns" => generateGradeColumns('4'),
    //         ]
    //     ],
    //     GradePageEnum::GRADES_2->value => [
    //         'table' => 'padres7',
    //         'title' => 'Notas 2',
    //         'Trimestre-1' => [
    //             'totalGrade' => 'nota1',
    //             'totalAverage' => 'average1',
    //             'grades' => [1, 10],
    //         ],
    //         'Trimestre-2' => [
    //             'totalGrade' => 'nota2',
    //             'totalAverage' => 'average2',
    //             'grades' => [11, 20],
    //         ],
    //         'Trimestre-3' => [
    //             'totalGrade' => 'nota3',
    //             'totalAverage' => 'average3',
    //             'grades' => [21, 30],
    //         ],
    //         'Trimestre-4' => [
    //             'totalGrade' => 'nota4',
    //             'totalAverage' => 'average4',
    //             'grades' => [31, 40],
    //         ]
    //     ],
    //     GradePageEnum::SHORT_TESTS->value => [
    //         'title' => 'Pruebas Cortas',
    //         TrimesterEnum::FIRST->value => [
    //             'table' => 'padres4',
    //             'number' => '1',
    //             'grades' => [1, 9],
    //             "columns" => generateDHColumns('1'),
    //         ],
    //         TrimesterEnum::SECOND->value => [
    //             'table' => 'padres4',
    //             'number' => '2',
    //             'grades' => [11, 19],
    //             "columns" => generateDHColumns('2'),
    //         ],
    //         TrimesterEnum::THIRD->value => [
    //             'table' => 'padres4',
    //             'number' => '3',
    //             'grades' => [21, 29],
    //             "columns" => generateDHColumns('3'),
    //         ],
    //         TrimesterEnum::FOURTH->value => [
    //             'table' => 'padres4',
    //             'number' => '4',
    //             'grades' => [31, 39],
    //             "columns" => generateDHColumns('4'),
    //         ]
    //     ],
    //     GradePageEnum::DAILY_WORKS->value => [
    //         'title' => 'Trabajos Diarios',
    //         TrimesterEnum::FIRST->value => [
    //             'table' => 'padres2',
    //             'number' => '1',
    //             'grades' => [1, 9],
    //             "columns" => generateDHColumns('1'),
    //         ],
    //         TrimesterEnum::SECOND->value => [
    //             'table' => 'padres2',
    //             'number' => '2',
    //             'grades' => [11, 19],
    //             "columns" => generateDHColumns('2'),
    //         ],
    //         TrimesterEnum::THIRD->value => [
    //             'table' => 'padres2',
    //             'number' => '3',
    //             'grades' => [21, 29],
    //             "columns" => generateDHColumns('3'),
    //         ],
    //         TrimesterEnum::FOURTH->value => [
    //             'table' => 'padres2',
    //             'number' => '4',
    //             'grades' => [31, 39],
    //             "columns" => generateDHColumns('4'),
    //         ]
    //     ],
    //     GradePageEnum::DAILY_WORKS_2->value => [
    //         'table' => 'padres5',
    //         'title' => 'Trabajos Diarios 2',
    //         'columns' => [
    //             'es' => ['Nota 10'],
    //             'en' => ['Grade 10']
    //         ],
    //         'Trimestre-1' => [
    //             'totalGrade' => 'nota1',
    //             'grades' => [1, 10],
    //             'values' => [
    //                 'tpa' => 'tpa1',
    //                 'tdp' => 'por1'
    //             ]
    //         ],
    //         'Trimestre-2' => [
    //             'totalGrade' => 'nota2',
    //             'grades' => [11, 20],
    //             'values' => [
    //                 'tpa' => 'tpa2',
    //                 'tdp' => 'por2'
    //             ]
    //         ],
    //         'Trimestre-3' => [
    //             'totalGrade' => 'nota3',
    //             'grades' => [21, 30],
    //             'values' => [
    //                 'tpa' => 'tpa3',
    //                 'tdp' => 'por3'
    //             ]

    //         ],
    //         'Trimestre-4' => [
    //             'totalGrade' => 'nota4',
    //             'grades' => [31, 40],
    //             'values' => [
    //                 'tpa' => 'tpa4',
    //                 'tdp' => 'por4'
    //             ]

    //         ]
    //     ],
    //     GradePageEnum::NOTEBOOK_WORKS->value => [
    //         'title' => 'Trabajos de libreta',
    //         TrimesterEnum::FIRST->value => [
    //             'table' => 'padres3',
    //             'number' => '1',
    //             'grades' => [1, 9],
    //             "columns" => generateDHColumns('1'),
    //         ],
    //         TrimesterEnum::SECOND->value => [
    //             'table' => 'padres3',
    //             'number' => '2',
    //             'grades' => [11, 19],
    //             "columns" => generateDHColumns('2'),
    //         ],
    //         TrimesterEnum::THIRD->value => [
    //             'table' => 'padres3',
    //             'number' => '3',
    //             'grades' => [21, 29],
    //             "columns" => generateDHColumns('3'),
    //         ],
    //         TrimesterEnum::FOURTH->value => [
    //             'table' => 'padres3',
    //             'number' => '4',
    //             'grades' => [31, 39],
    //             "columns" => generateDHColumns('4'),
    //         ]
    //     ],
    //     GradePageEnum::NOTEBOOK_WORKS_2->value => [
    //         'table' => 'padres6',
    //         'title' => 'Trabajos de libreta 2',
    //         'columns' => [
    //             'es' => ['Nota 10'],
    //             'en' => ['Grade 10']
    //         ],
    //         'Trimestre-1' => [
    //             'totalGrade' => 'nota1',
    //             'grades' => [1, 10],
    //             'values' => [
    //                 'tpa' => 'tpa1',
    //                 'tdp' => 'por1'
    //             ]
    //         ],
    //         'Trimestre-2' => [
    //             'totalGrade' => 'nota2',
    //             'grades' => [11, 20],
    //             'values' => [
    //                 'tpa' => 'tpa2',
    //                 'tdp' => 'por2'
    //             ]
    //         ],
    //         'Trimestre-3' => [
    //             'totalGrade' => 'nota3',
    //             'grades' => [21, 30],
    //             'values' => [
    //                 'tpa' => 'tpa3',
    //                 'tdp' => 'por3'
    //             ]

    //         ],
    //         'Trimestre-4' => [
    //             'totalGrade' => 'nota4',
    //             'grades' => [31, 40],
    //             'values' => [
    //                 'tpa' => 'tpa4',
    //                 'tdp' => 'por4'
    //             ]

    //         ]
    //     ],
    //     GradePageEnum::CONDUCT_ATTENDANCE->value => [
    //         'table' => 'padres',
    //         'title' => 'Conducta y Asistencia',
    //         'Trimestre-1' => ['con1', 'aus1', 'tar1', 'de1'],
    //         'Trimestre-2' => ['con2', 'aus2', 'tar2', 'de2'],
    //         'Trimestre-3' => ['con3', 'aus3', 'tar3', 'de3'],
    //         'Trimestre-4' => ['con4', 'aus4', 'tar4', 'de4']
    //     ],
    //     GradePageEnum::FINAL_EXAM->value => [
    //         'table' => 'padres',
    //         'title' => 'Examen Final',
    //         'Trimestre-2' => 'ex1',
    //         'Trimestre-4' => 'ex2'
    //     ],
    //     GradePageEnum::SUMMER_GRADES->value => [
    //         'table' => 'padres',
    //         'title' => 'Notas de verano',
    //         'columns' => [
    //             'es' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
    //             'en' => ['Bonus', 'DW', 'HW', 'Quiz']
    //         ],
    //         'Verano' => [
    //             'totalGrade' => 'nota1',
    //             'grades' => [1, 7],
    //             'others' => ['con1', 'aus1', 'tar1'],
    //             'values' => [
    //                 'tdia' => 'td1',
    //                 'tlib' => 'tl1',
    //                 'pcor' => 'pc1',
    //                 'tpa' => 'tpa1',
    //                 'tdp' => 'por1'
    //             ]
    //         ]
    //     ]
    // ];
}

$_dates = $_trimester->getDateColumns();
$_end = $_trimester->getEndColumn();
// $_options =  $_info[$_report->value][$_trimester->value] ?: null;
$_columns = $_trimester->getColumns($_report);
$_trimesterNumber = $_trimester->getNumber();

$grades = Classes::query()
    ->withoutGlobalScopes()
    ->table($_report->getTableName())
    ->ofClass($_class)
    ->where('year', $year)
    ->when($_report === GradePageEnum::SUMMER_GRADES, function (Builder $query): void {
        $query->where('verano', '2');
    })->orderBy('apellidos')->orderBy('nombre')->get();

// functions
function findValue(string $table, Classes $grade)
{
    global $_class;
    global $year;
    return DB::table($table)->where([
        ['curso', $_class],
        ['ss', $grade->ss],
        ['year', $year]
    ])->first();
}
function findValueFor($table, $student)
{
    global $_class;
    global $year;
    return DB::table($table)->where([
        ['curso', $_class],
        ['ss', $student->ss],
        ['year', $year]
    ])->first();
}
function findTotal(string $type, Classes $grade)
{
    global $_info;
    global $_report;
    $tpaTotal = 0;
    global $_trimesterNumber;
    if ($_trimesterNumber === 2 || $_trimesterNumber === 4) {
        $lastTrimester = $_trimesterNumber - 1;
        $t = $_info[$_report]["Trimestre-$lastTrimester"]['values'][$type];
        $tpaTotal += (int) $grade->{$t};
    }
    return $tpaTotal;
}

$lang = new Lang([
    ["Entrada de notas", 'Enter grades'],
    ["Curso:", "Class:"],
    ["Trimestre:", "Trimester:"],
    ["Entrando notas a:", "Entering grades to:"],
    ["Tipo de nota:", "Type of grade:"],
    ["Fecha de inicio:", "Start date:"],
    ["Fecha de cierre:", "Closing date:"],
    ["Total de estudiantes:", "Total students:"],
    ["Porciento", "Percent"],
    ["Suma", "Sum"],
    ["Pasar a letras", "Convert to letters"],
    ["Está opción se aplica en la columna", "This option is applied in the column"],
    ["Nota", "Grade"],
    ["exclusivamente.", "exclusively."],
    ["Conversión", "Conversion"],
    ['Está opción es para convertir de numero a letra.', 'This option is to convert from numbers to letters.'],
    ["Aviso terminar", "Notice finish"],
    ["Cuando termine el trimestre marque está Opción.", "When the trimester ends mark this option."],
    ["¿Quieres que estas notas sean?", "Do you want these grades to be?"],
    ["Calculando notas", "Calculating grades"],
    ["Nombre del estudiante", 'Student name'],
    ["TPA", "TAP"],
    ["TDP", "TP"],
    ["Conducta", "Behavior"],
    ["Ausencias", "Absence"],
    ["Tardanzas", "Tardy"],
    ["Deméritos", "Demerits"],
    ["Nota del Examen Final", "Final exam grade"],
    ["Los examenes finales solo estan en el trimestre 2 y trimestre 4", "The final exams are only in trimester 2 and trimester 4"],
    ["Volver", "Back"],
    ["Guardar", "Save"],
    ["Lo Sentimos, La fecha Ha Vencido o la selección del trimestre es equivocada. Intentelo de Nuevo o Comuniquese con la Administración.", "Sorry, the date has expired or the selection of the trimester is invalid. Try it again or contact the administration."],
    ["Recuerda ir a la pagina de notas y darle a grabar para tener los promédios correctos.", "Remember to go to the grades page and save it to have the correct averages."],
    ["Valores", "Values"],
    ["Tema", "Topic"],
    ["Valor", "Value"],
    ["Fecha", "Date"],
    ["Promedio", "Average"],
    ["Notas 2", "Grades 2"],

]);

$canEdit = true; //Carbon::now()->betweenIncluded($school->{$_dates[0]}, $school->{$_dates[1]});

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Entrada de notas');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>


    <div class="container-lg mt-lg-3 px-0">
        <div class="card border-info">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col">
                        <p class="text-monospace"><?= __("Curso") ?>: <span class="badge badge-info"><?= $_class ?> </span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Trimestre") ?>: <span class="badge badge-info"><?= $_trimester->getLabel() ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Total de estudiantes") ?>: <span class="badge badge-info"><?= $grades->count() ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Entrando notas a") ?>: <span class="badge badge-info"><?= $_report->getLabel() ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Fecha de inicio") ?>: <span class="badge badge-info"><?= Util::formatDate($school->{$_dates[0]}, true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Fecha de cierre") ?>: <span class="badge badge-info"><?= Util::formatDate($school->{$_dates[1]}, true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= __("Tipo de nota") ?>: <span class="badge badge-info"><?= $gradeInfo && $gradeInfo->nota_por === "1" ? $lang->translation("Porciento") : $lang->translation("Suma") ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($_report === GradePageEnum::GRADES || $_report === GradePageEnum::SUMMER_GRADES):
            $letterNumber = $_report === GradePageEnum::GRADES ? '9' : '7';
        ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div id="options" class="row row-cols-1">
                        <div class="col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="letra" value="<?= $letterNumber ?>" <?= ($gradeInfo && $gradeInfo->letra === "ON") ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="letra"><?= $lang->translation("Pasar a letras") ?></label>
                            </div>
                            <small><?= $lang->translation("Está opción se aplica en la columna") ?>
                                <b><?= $_report === GradePageEnum::GRADES ? $lang->translation("Nota") . '-9' : $lang->translation("Nota") . '-7' ?></b>
                                <?= $lang->translation("exclusivamente.") ?></small>
                        </div>
                        <div class="col mt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="pal" value="ON" <?= ($gradeInfo && $gradeInfo->pal === "ON") ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="pal"><?= $lang->translation("Conversión") ?></label>
                            </div>
                            <small><?= $lang->translation("Está opción es para convertir de numero a letra.") ?></small>
                        </div>
                        <?php if ($_end): ?>
                            <?php if ($school->sie === 'Si' && $school->sieab === '4'): ?>
                                <div class="col mt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="<?= $_end ?>" value='X' <?= ($gradeInfo && $gradeInfo->{$_end} === "X") ? 'checked=""' : '' ?> disabled>
                                        <label class="custom-control-label" for="<?= $_end ?>"><?= $lang->translation("Aviso terminar") ?></label>
                                    </div>
                                    <small><?= $lang->translation("Cuando termine el trimestre marque está Opción.") ?></small>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- only school cbtm -->
            <?php if (!__ONLY_CBTM__): ?>
                <div class="card border-secondary mt-2">
                    <div class="card-body">
                        <div class="row row-cols-1">
                            <div class="col">
                                <h4><?= $lang->translation("¿Quieres que estas notas sean?") ?></h4>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="noteType1" class="custom-control-input noteTypeOption" name="nota_por" value="1" <?= $gradeInfo->nota_por === "1" ? 'checked=""' : '' ?> disabled>
                                    <label class="custom-control-label" for="noteType1"><?= $lang->translation("Porciento") ?></label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="noteType2" class="custom-control-input noteTypeOption" name="nota_por" value="2" <?= $gradeInfo->nota_por === "2" ? 'checked=""' : '' ?> disabled>
                                    <label class="custom-control-label" for="noteType2"><?= $lang->translation("Suma") ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>
    <!-- loading spinner -->
    <div class="loading text-center my-3">
        <h3 class="font-weight-bolder"><?= $lang->translation("Calculando notas") ?></h3>
        <span class="spinner-border spinner-border-lg" role="status" aria-hidden="true"></span>
    </div>
    <div class="container-fluid">
        <!-- Students list -->
        <form id="form" action="<?= Route::url('/regiweb/grades/includes/enterGrades.php') ?>" method="POST">
            <!-- Required hidden inputs -->
            <input type="hidden" name="report" id="report" value="<?= $_report->value ?>">
            <input type="hidden" name="trimester" id="trimester" value="<?= $_trimester->value ?>">
            <input type="hidden" name="subject" id="subject" value="<?= $_class ?>">
            <input type="hidden" name="optionCppd" id="optionCppd" value="<?= $optionCppd ?>">
            <input type="hidden" name="sumTrimester" id="sumTrimester" value="<?= $sumTrimester ?>">
            <div class="table-responsive my-3 shadow">
                <table class="table table-sm table-hover bg-white">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th style="width: 5px;" scope="col">#</th>
                            <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                            </th>
                            <?php foreach ($_trimester->getGradesNumbersColumn($_report, $optionCppd) as $index => $number):
                                $n = $index + 1; ?>
                                <th scope="col"><?= __("Nota") . " {$n}" ?></th>
                            <?php endforeach ?>
                            <?php foreach ($_columns as $col): ?>
                                <th scope="col">
                                    <?= $col['header'] ?>
                                    <?php if ($col['text'] !== null): ?>
                                        <span style="font-size: 15px;" class="text-muted"><?= $col['text'] ?></span>
                                    <?php endif ?>
                                </th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $index => $grade): ?>
                            <tr>
                                <th scope="row">
                                    <?= $index + 1 ?>
                                    <?php
                                    // important information for the values
                                    // Only on Notas and when "Cambiar Porciento a Punto Decimal" is not activated
                                    if ($_report === GradePageEnum::GRADES && !$optionCppd):
                                        if ($gradeInfo->nota_por === "2") {
                                            $_student = findValue(GradePageEnum::DAILY_WORKS->getTableName(), $grade);
                                            $tdia =  $_student?->{'por' . $_trimester->getNumber()};
                                            $_student = findValue(GradePageEnum::NOTEBOOK_WORKS->getTableName(), $grade);
                                            $tlib = $_student?->{'por' . $_trimester->getNumber()};
                                            $_student = findValue(GradePageEnum::SHORT_TESTS->getTableName(), $grade);
                                            $pcor = $_student?->{'por' . $_trimester->getNumber()};
                                        } else {
                                            $tdia = $grade?->{'tl1'} ? '100' : '';
                                            $tlib = $grade?->{'td1'} ? '100' : '';
                                            $pcor = $grade?->{'pc1'} ? '100' : '';
                                        }
                                    ?>
                                        <?php if ($sumTrimester && ($_trimesterNumber === 2 || $_trimesterNumber === 4)): ?>
                                            <input type="hidden" class="_tpaTotal" name="tpaTotal" id="tpaTotal" value="<?= findTotal('tpa', $grade) ?>">
                                            <input type="hidden" class="_tdpTotal" name="tdpTotal" id="tdpTotal" value="<?= findTotal('tdp', $grade) ?>">
                                        <?php endif ?>
                                        <input type="hidden" class="_tdia" value="<?= $tdia ?>">
                                        <input type="hidden" class="_tlib" value="<?= $tlib ?>">
                                        <input type="hidden" class="_pcor" value="<?= $pcor ?>">
                                    <?php endif; ?>
                                </th>
                                <td>
                                    <?= "$grade->apellidos $grade->nombre"; ?>
                                </td>
                                <?php foreach ($_trimester->getGradesNumbersColumn($_report) as $number): ?>
                                    <td>
                                        <input class="form-control form-control-sm text-center grade" type="text" name="<?= "students[$grade->ss][not{$number}]" ?>" value="<?= $grade->{"not{$number}"} ?>" disabled>
                                    </td>
                                <?php endforeach ?>
                                <?php foreach ($_columns as $col): ?>
                                    <td>
                                        <input class="text-center <?= $col['readonly'] ? 'form-control-plaintext' : 'form-control form-control-sm' ?> <?= $col['class'] ?: $col['column'] ?>" type="text" name="<?= "students[$grade->ss][{$col['column']}]" ?>" value="<?= $grade->{$col['column']} ?>" <?= $col['readonly'] ? 'readonly' : '' ?> <?= !$canEdit ? 'disabled' : '' ?>>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <!-- <button type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button> -->
                <?php if ($canEdit): ?>
                    <button id="save" type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3"><?= $lang->translation("Guardar") ?></button>
                <?php else: ?>
                    <h4 class="text-center text-danger">
                        <?= $lang->translation("Lo Sentimos, La fecha Ha Vencido o la selección del trimestre es equivocada. Intentelo de Nuevo o Comuniquese con la Administración.") ?>
                    </h4>
                <?php endif ?>
            </div>
        </form>
        <!-- end Students list -->
        <?php if ($_report !== GradePageEnum::GRADES && $_report !== GradePageEnum::SUMMER_GRADES && $_report !== GradePageEnum::FINAL_EXAM && $_report !== GradePageEnum::CONDUCT_ATTENDANCE): ?>
            <h2 class="text-center text-info mb-0">
                *<?= $lang->translation("Recuerda ir a la pagina de notas y darle a grabar para tener los promédios correctos.") ?>*
            </h2>
        <?php endif ?>

        <!-- Values -->
        <?php if ($_report !== GradePageEnum::CONDUCT_ATTENDANCE): ?>
            <div class="container my-5">
                <div class="accordion" id="valuesAccordion">
                    <div class="card">
                        <div class="card-header bg-secondary" id="valuesHead">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left text-light font-weight-bold" type="button" data-toggle="collapse" data-target="#values" aria-expanded="true" aria-controls="values">
                                    <?= $lang->translation("Valores") ?>
                                </button>
                            </h2>
                        </div>
                        <div id="values" class="collapse" aria-labelledby="valuesHead" data-parent="#valuesAccordion">
                            <input type="hidden" id="valueId" value="<?= $_value->id ?>">
                            <div class="card-body">
                                <div class="form-row">
                                    <?php $cant = ($_report === GradePageEnum::FINAL_EXAM) ? 1 : count($_trimester->getGradesNumbersColumn($_report)) ?>
                                    <?php for ($i = 1; $i <= $cant; $i++): ?>
                                        <div class="form-row col-12 col-md-8 mb-2">
                                            <div class="form-group col-12">
                                                <label for="<?= "tema$i" ?>"><?= $lang->translation("Tema") ?> <?= $i ?></label>
                                                <input <?= !$canEdit ? 'disabled' : '' ?> class="form-control" type="text" id="<?= "tema$i" ?>" value="<?= $_value->{"tema{$i}"} ?>" />
                                            </div>
                                            <div class="form-group col-4 col-md-2 text-center">
                                                <label for="<?= "val$i" ?>"><?= $lang->translation("Valor") ?></label>
                                                <input <?= !$canEdit ? 'disabled' : '' ?> class="form-control text-center" type="text" id="<?= "val$i" ?>" data-value="<?= $_value->{"val{$i}"} ?>" value="<?= $_value->{"val{$i}"} ?>" />
                                            </div>
                                            <div class="form-group col-8 col-md-3">
                                                <label for="<?= "fec$i" ?>"><?= $lang->translation("Fecha") ?></label>
                                                <input <?= !$canEdit ? 'disabled' : '' ?> class="form-control" type="date" id="<?= "fec$i" ?>" value="<?= $_value->{"fec{$i}"} ?>" />
                                            </div>
                                        </div>
                                    <?php endfor ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

</body>

</html>