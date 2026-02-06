<?php
require_once __DIR__ . '/../../app.php';

use App\Enums\QuincenalTrimesterEnum;
use App\Models\Admin;
use App\Models\Classes;
use App\Models\Teacher;
use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;


Session::is_logged();
Server::is_post();
$_class = $_POST['class'];
$_trimester = QuincenalTrimesterEnum::from($_POST['tri']);
$_report = $_POST['tra'];



$teacher = Teacher::find(Session::id());
$school = Admin::primaryAdmin();
$year = $school->year();
$optionCppd = $school->cppd === 'Si';
$sumTrimester = $school->sutri === 'NO'; //NO === SI

// Diferent option for these schools


if (
    !$_value = DB::table('valores')
        ->where([
            ['curso', $_class],
            ['trimestre', $_trimester],
            ['nivel', $_report],
            ['year', $year]
        ])->first()
) {
    $_valueId = DB::table('valores')->insertGetId([
        'curso' => $_class,
        'trimestre' => $_trimester,
        'nivel' => $_report,
        'year' => $year
    ]);
    $_value = DB::table('valores')->where('mt', $_valueId)->first();
}

$gradeInfo = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $year],
])->first();

$optionLetter = !$gradeInfo ? false : $gradeInfo->letra === "ON";


$_schoolInfo = school_is('cdls') ? [
    QuincenalTrimesterEnum::FIRST_S1->value => [
        'number' => 1,
        'dates' => ['ft3', 'ft4'],
        'end' => 'sie1'
    ],
    QuincenalTrimesterEnum::SECOND_S1->value => [
        'number' => 2,
        'dates' => ['ft5', 'ft6'],
        'end' => 'sie2'
    ],
    QuincenalTrimesterEnum::THIRD_S1->value => [
        'number' => 3,
        'dates' => ['ft7', 'ft8'],
        'end' => 'sie3'
    ],
    QuincenalTrimesterEnum::FIRST_S2->value => [
        'number' => 1,
        'dates' => ['ft11', 'ft12'],
        'end' => 'sie1'
    ],
    QuincenalTrimesterEnum::SECOND_S2->value => [
        'number' => 2,
        'dates' => ['ft13', 'ft14'],
        'end' => 'sie2'
    ],
    QuincenalTrimesterEnum::THIRD_S2->value => [
        'number' => 3,
        'dates' => ['ft15', 'ft16'],
        'end' => 'sie3'
    ],
    QuincenalTrimesterEnum::FINAL_S1->value => [
        'number' => 4,
        'dates' => ['ft17', 'ft17'],
        'end' => 'sie4'
    ],
    QuincenalTrimesterEnum::FINAL_S2->value => [
        'number' => 4,
        'dates' => ['ft18', 'ft18'],
        'end' => 'sie4'
    ]
] : [
    QuincenalTrimesterEnum::FIRST_S1->value => [
        'number' => 1,
        'dates' => ['ft1', 'ft2'],
        'end' => 'sie1'
    ],
    QuincenalTrimesterEnum::SECOND_S1->value => [
        'number' => 2,
        'dates' => ['ft3', 'ft4'],
        'end' => 'sie2'
    ],
    QuincenalTrimesterEnum::THIRD_S1->value => [
        'number' => 3,
        'dates' => ['ft5', 'ft6'],
        'end' => 'sie3'
    ],
    QuincenalTrimesterEnum::FOURTH_S1->value => [
        'number' => 4,
        'dates' => ['ft7', 'ft8'],
        'end' => 'sie4'
    ],
    QuincenalTrimesterEnum::FINAL_S1->value => [
        'number' => 4,
        'dates' => ['ft17', 'ft17'],
        'end' => 'sie4'
    ]
];

function generateColumn(string $header, string $col, bool $readonly = false, ?string $class = null, ?string $text = null)
{
    return [
        'header' => $header,
        'column' => $col,
        'readonly' => $readonly,
        'class' => $class,
        'text' => $text,
    ];
}

function generateGradeColumns(string $colNumber, ?string $bonusCol = null)
{
    $bonus = $bonusCol ?? 'not' . $colNumber . '0';
    return [
        generateColumn(__('Bono'), $bonus, class: 'bonus'),
        generateColumn(__('T-Diario'), 'td' . $colNumber, true, 'tdia'),
        generateColumn(__('T-Libreta'), 'tl' . $colNumber, true, 'tlib'),
        generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
        generateColumn(__('TDP'), 'por' . $colNumber, true, 'tdp'),
        generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
        generateColumn(__('DIC'), 'dip' . $colNumber),
        generateColumn(__('Obs'), 'con' . $colNumber),
    ];
}

function generateDHColumns(string $colNumber)
{
    $bonus = 'not' . $colNumber . '0';
    return [
        generateColumn(__('Nota') . ' 10', $bonus, false, 'grade'),
        generateColumn(__('TPA'), 'tpa' . $colNumber, true, 'tpa'),
        generateColumn('%', 'por' . $colNumber, true, 'tdp'),
        generateColumn(__('Nota'), 'nota' . $colNumber, true, 'totalGrade'),
    ];
}

$_info = school_is('cdls') ? [
    "Notas" => [
        'title' => 'Notas',
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'table' => 'padres',
            'grades' => [11, 19],
            "columns" => generateGradeColumns('2'),
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'table' => 'padres',
            'grades' => [21, 29],
            "columns" => generateGradeColumns('3'),

        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'table' => 'padres',
            'grades' => [31, 39],
            "columns" => generateGradeColumns('4'),
        ],
        QuincenalTrimesterEnum::FINAL_S1->value => [
            'table' => 'padres',
            'columns' => [
                generateColumn(__('Nota'), 'examen')
            ]

        ],
        QuincenalTrimesterEnum::FIRST_S2->value => [
            'table' => 'padres4',
            'grades' => [11, 19],
            "columns" => generateGradeColumns('2'),
        ],
        QuincenalTrimesterEnum::SECOND_S2->value => [
            'table' => 'padres4',
            'grades' => [21, 29],
            "columns" => generateGradeColumns('3'),

        ],
        QuincenalTrimesterEnum::THIRD_S2->value => [
            'table' => 'padres4',
            'grades' => [31, 39],
            "columns" => generateGradeColumns('4'),
        ],
        QuincenalTrimesterEnum::FINAL_S2->value => [
            'table' => 'padres4',
            'columns' => [
                generateColumn(__('Nota'), 'examen')
            ]
        ]
    ],
    "Trab-Diarios" => [
        'title' => 'Trabajos Diarios',
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'table' => 'padres2',
            'number' => '2',
            'columns' => generateDHColumns('2'),
            'grades' => [11, 19],
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'table' => 'padres2',
            'number' => '3',
            'columns' => generateDHColumns('3'),
            'grades' => [21, 29],
        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'table' => 'padres2',
            'number' => '4',
            'columns' => generateDHColumns('4'),
            'grades' => [31, 39],
        ],
        QuincenalTrimesterEnum::FIRST_S2->value => [
            'table' => 'padres5',
            'number' => '2',
            'columns' => generateDHColumns('2'),
            'grades' => [11, 19],
        ],
        QuincenalTrimesterEnum::SECOND_S2->value => [
            'table' => 'padres5',
            'number' => '3',
            'columns' => generateDHColumns('3'),
            'grades' => [21, 29],
        ],
        QuincenalTrimesterEnum::THIRD_S2->value => [
            'table' => 'padres5',
            'number' => '4',
            'columns' => generateDHColumns('4'),
            'grades' => [31, 39],
        ]
    ],
    "Trab-Libreta" => [
        'title' => 'Trabajos de libreta',
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'table' => 'padres3',
            'number' => '2',
            'columns' => generateDHColumns('2'),
            'grades' => [11, 19],
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'table' => 'padres3',
            'number' => '3',
            'columns' => generateDHColumns('3'),
            'grades' => [21, 29],
        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'table' => 'padres3',
            'number' => '4',
            'columns' => generateDHColumns('4'),
            'grades' => [31, 39],
        ],
        QuincenalTrimesterEnum::FIRST_S2->value => [
            'table' => 'padres6',
            'number' => '2',
            'columns' => generateDHColumns('2'),
            'grades' => [11, 19],
        ],
        QuincenalTrimesterEnum::SECOND_S2->value => [
            'table' => 'padres6',
            'number' => '3',
            'columns' => generateDHColumns('3'),
            'grades' => [21, 29],
        ],
        QuincenalTrimesterEnum::THIRD_S2->value => [
            'table' => 'padres6',
            'number' => '4',
            'columns' => generateDHColumns('4'),
            'grades' => [31, 39],
        ]
    ],
] : [
    "Notas" => [
        'title' => 'Notas',
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'table' => 'padres',
            'grades' => [1, 9],
            "columns" => generateGradeColumns('1'),
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'table' => 'padres',
            'grades' => [11, 19],
            "columns" => generateGradeColumns('2'),
        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'table' => 'padres',
            'grades' => [21, 29],
            "columns" => generateGradeColumns('3'),
        ],
        QuincenalTrimesterEnum::FOURTH_S1->value => [
            'table' => 'padres',
            'grades' => [31, 39],
            "columns" => generateGradeColumns('4'),
        ],
        QuincenalTrimesterEnum::FINAL_S1->value => [
            'table' => 'padres',
            'columns' => [
                generateColumn(__('Nota'), 'examen')
            ]
        ],
        QuincenalTrimesterEnum::FIRST_S2->value => [
            'table' => 'padres4',
            'grades' => [1, 9],
            "columns" => generateGradeColumns('1'),
        ],
        QuincenalTrimesterEnum::SECOND_S2->value => [
            'table' => 'padres4',
            'grades' => [11, 19],
            "columns" => generateGradeColumns('2'),
        ],
        QuincenalTrimesterEnum::THIRD_S2->value => [
            'table' => 'padres4',
            'grades' => [21, 29],
            "columns" => generateGradeColumns('3'),
        ],
        QuincenalTrimesterEnum::FOURTH_S2->value => [
            'table' => 'padres4',
            'grades' => [31, 39],
            "columns" => generateGradeColumns('4'),
        ],
        QuincenalTrimesterEnum::FINAL_S2->value => [
            'table' => 'padres4',
            'columns' => [
                generateColumn(__('Nota'), 'examen')
            ]
        ],
    ],
    "Trab-Diarios" => [
        'table' => 'padres2',
        'title' => 'Trabajos Diarios',
        'columns' => [
            'es' => ['Nota 10'],
            'en' => ['Grade 10']
        ],
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'tdp' => 'por1'
            ]
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'tdp' => 'por2'
            ]
        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'tdp' => 'por3'
            ]

        ],
        QuincenalTrimesterEnum::FOURTH_S1->value => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'tdp' => 'por4'
            ]

        ]
    ],

    "Trab-Libreta" => [
        'table' => 'padres3',
        'title' => 'Trabajos de libreta',
        'columns' => [
            'es' => ['Nota 10'],
            'en' => ['Grade 10']
        ],
        QuincenalTrimesterEnum::FIRST_S1->value => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'tdp' => 'por1'
            ]
        ],
        QuincenalTrimesterEnum::SECOND_S1->value => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'tdp' => 'por2'
            ]
        ],
        QuincenalTrimesterEnum::THIRD_S1->value => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'tdp' => 'por3'
            ]

        ],
        QuincenalTrimesterEnum::FOURTH_S1->value => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'tdp' => 'por4'
            ]

        ]
    ]
];

$_thisReport = $_info[$_report];
$_dates = $_schoolInfo[$_trimester->value]['dates'];
$_end = isset($_schoolInfo[$_trimester->value]['end']) ? $_schoolInfo[$_trimester->value]['end'] : null;
$_options = isset($_thisReport[$_trimester->value]) ? $_thisReport[$_trimester->value] : null;
$_values = isset($_options['values']) ? $_options['values'] : null;
$_columns = $_options['columns'] ?? null;


$_trimesterNumber = $_schoolInfo[$_trimester->value]['number'];

$_table = $_options['table'] ?? null;

$grades = Classes::query()->withoutGlobalScopes()->table($_table)->ofClass($_class)->where('year', $year)->orderBy('apellidos')->orderBy('nombre')->get();


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

function findValueFor(string $table, Classes $grade)
{
    global $_class;
    global $year;
    return DB::table($table)->where([
        ['curso', $_class],
        ['ss', $grade->ss],
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
    $title = $lang->translation('Entrada de notas');
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
                        <p class="text-monospace"><?= $lang->translation("Curso:") ?> <span class="badge badge-info"><?= $_class ?> </span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Trimestre:") ?> <span class="badge badge-info"><?= $_trimester->getLabel() ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Total de estudiantes:") ?> <span class="badge badge-info"><?= $grades->count() ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Entrando notas a:") ?> <span class="badge badge-info"><?= $lang->translation($_thisReport['title']) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Fecha de inicio:") ?> <span class="badge badge-info"><?= Util::formatDate($school->{$_dates[0]}, true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Fecha de cierre:") ?> <span class="badge badge-info"><?= Util::formatDate($school->{$_dates[1]}, true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Tipo de nota:") ?> <span class="badge badge-info"><?= $gradeInfo && $gradeInfo->nota_por === "1" ? $lang->translation("Porciento") : $lang->translation("Suma") ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($_report === 'Notas' || $_report === 'V-Nota'):
            $letterNumber = $_report === 'Notas' ? '9' : '7';
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
                                <b><?= $_report === 'Notas' ? $lang->translation("Nota") . '-9' : $lang->translation("Nota") . '-7' ?></b>
                                <?= $lang->translation("exclusivamente.") ?></small>
                        </div>

                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div class="row row-cols-1">
                        <div class="col">
                            <h4><?= $lang->translation("¿Quieres que estas notas sean?") ?></h4>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="noteType1" class="custom-control-input" name="noteType" value="1" <?= $gradeInfo->nota_por === "1" ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="noteType1"><?= $lang->translation("Porciento") ?></label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="noteType2" class="custom-control-input" name="noteType" value="2" <?= $gradeInfo->nota_por === "2" ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="noteType2"><?= $lang->translation("Suma") ?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
    <!-- loading spinner -->
    <div class="loading text-center my-3">
        <h3 class="font-weight-bolder"><?= $lang->translation("Calculando notas") ?></h3>
        <span class="spinner-border spinner-border-lg" role="status" aria-hidden="true"></span>
    </div>
    <div class="container-fluid">
        <!-- Students list -->
        <form id="form" action="<?= Route::url('/regiweb/grades/includes/enterGrades2.php') ?>" method="POST">
            <!-- Required hidden inputs -->
            <input type="hidden" name="report" id="report" value="<?= $_report ?>">
            <input type="hidden" name="trimester" id="trimester" value="<?= $_trimester->value ?>">
            <input type="hidden" name="table" id="table" value="<?= $_table ?>">
            <input type="hidden" name="subject" id="subject" value="<?= $_class ?>">
            <input type="hidden" name="optionCppd" id="optionCppd" value="<?= $optionCppd ?>">
            <input type="hidden" name="valueA" id="valueA" value="<?= $school->vala ?>">
            <input type="hidden" name="valueB" id="valueB" value="<?= $school->valb ?>">
            <input type="hidden" name="valueC" id="valueC" value="<?= $school->valc ?>">
            <input type="hidden" name="valueD" id="valueD" value="<?= $school->vald ?>">
            <input type="hidden" name="valueF" id="valueF" value="<?= $school->valf ?>">
            <input type="hidden" name="sumTrimester" id="sumTrimester" value="<?= $sumTrimester ?>">
            <input type="hidden" name="optionLetter" id="optionLetter" value="<?= $optionLetter ? $letterNumber ?? 0 : 0 ?>">
            <div class="table-responsive my-3 shadow">
                <table class="table table-sm table-hover bg-white">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th style="width: 5px;" scope="col">#</th>
                            <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                            </th>
                            <?php if (isset($_options['grades'])): ?>
                                <?php
                                $amountOfGrades = $_options['grades'][1] - $_options['grades'][0] + 1;
                                for ($i = 1; $i <= $amountOfGrades; $i++): ?>
                                    <th scope="col"><?= __("Nota") . " {$i}" ?></th>
                                <?php endfor ?>
                            <?php endif ?>
                            <?php if ($_columns !== null): ?>
                                <?php foreach ($_columns as $col): ?>
                                    <th scope="col">
                                        <?= $col['header'] ?>
                                        <?php if ($col['text'] !== null): ?>
                                            <span style="font-size: 15px;" class="text-muted"><?= $col['text'] ?></span>
                                        <?php endif ?>
                                    </th>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $index => $grade): ?>
                            <tr>
                                <th style="width: 5px;" scope="row">
                                    <?= $index + 1 ?>
                                    <?php
                                    // important information for the values
                                    // Only on Notas and when "Cambiar Porciento a Punto Decimal" is not activated
                                    if ($_report === 'Notas' && !$optionCppd && $_trimester !== QuincenalTrimesterEnum::FINAL_S1 && $_trimester !== QuincenalTrimesterEnum::FINAL_S2):
                                        if ($gradeInfo->nota_por === "2") {
                                            $_student = findValue($_info['Trab-Diarios'][$_trimester->value]['table'], $grade);
                                            $tdia = $_student ? $_student->{'por' . $_info['Trab-Diarios'][$_trimester->value]['number']} : null;
                                            $_student = findValue($_info['Trab-Libreta'][$_trimester->value]['table'], $grade);
                                            $tlib = $_student ? $_student->{'por' . $_info['Trab-Libreta'][$_trimester->value]['number']} : null;
                                        } else {
                                            $tdia = $grade->{$_values['tdia']} ? '100' : '';
                                            $tlib = $grade->{$_values['tlib']} ? '100' : '';
                                        }
                                    ?>
                                        <?php if ($sumTrimester && ($_trimesterNumber === 2 || $_trimesterNumber === 4)): ?>
                                            <input type="hidden" class="_tpaTotal" name="tpaTotal" id="tpaTotal" value="<?= findTotal('tpa', $grade) ?>">
                                            <input type="hidden" class="_tdpTotal" name="tdpTotal" id="tdpTotal" value="<?= findTotal('tdp', $grade) ?>">
                                        <?php endif ?>
                                        <input type="hidden" class="_tdia" value="<?= $tdia ?>">
                                        <input type="hidden" class="_tlib" value="<?= $tlib ?>">
                                    <?php endif; ?>
                                </th>
                                <td>
                                    <?= "$grade->apellidos $grade->nombre"; ?>
                                </td>
                                <?php if (isset($_options['grades'])): ?>
                                    <?php for ($i = $_options['grades'][0]; $i <= $_options['grades'][1]; $i++): ?>
                                        <td>
                                            <input class="form-control form-control-sm text-center grade" type="text" name="<?= "students[$grade->ss][not{$i}]" ?>" value="<?= $grade->{"not{$i}"} ?>" disabled>
                                        </td>
                                    <?php endfor ?>
                                <?php endif ?>
                                <?php if ($_columns !== null): ?>
                                    <?php foreach ($_columns as $col): ?>
                                        <td>
                                            <input class="text-center <?= $col['readonly'] ? 'form-control-plaintext' : 'form-control form-control-sm' ?> <?= $col['class'] ?: $col['column'] ?>" type="text" name="<?= "students[$grade->ss][{$col['column']}]" ?>" value="<?= $grade->{$col['column']} ?>" <?= $col['readonly'] ? 'readonly' : '' ?> <?= !$canEdit ? 'disabled' : '' ?>>
                                        </td>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>


                <!-- <button type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button> -->
                <?php if ($_options !== null): ?>
                    <?php if ($canEdit): ?>
                        <button id="save" type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3"><?= $lang->translation("Guardar") ?></button>
                    <?php else: ?>
                        <h4 class="text-center text-danger">
                            <?= $lang->translation("Lo Sentimos, La fecha Ha Vencido o la selección del trimestre es equivocada. Intentelo de Nuevo o Comuniquese con la Administración.") ?>
                        </h4>
                    <?php endif ?>
                <?php endif ?>
            </div>
        </form>
        <!-- end Students list -->
        <?php if ($_report !== 'Notas'): ?>
            <h2 class="text-center text-info mb-0">
                *<?= $lang->translation("Recuerda ir a la pagina de notas y darle a grabar para tener los promédios correctos.") ?>*
            </h2>
        <?php endif ?>

        <!-- Values -->
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
                        <input type="hidden" id="valueId" value="<?= $_value->mt ?>">
                        <div class="card-body">
                            <div class="form-row">
                                <?php $cant = $_trimester === QuincenalTrimesterEnum::FINAL_S1 || $_trimester === QuincenalTrimesterEnum::FINAL_S2 ? 1 : $amountOfGrades ?>
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

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script src='./js/enterGrades2.js'></script>

</body>

</html>