<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
Server::is_post();
$_class = $_POST['class'];
$_trimester = $_POST['tri'];
$_report = $_POST['tra'];

$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$optionCppd = $teacher->info('cppd') === 'Si';
$sumTrimester = $teacher->info('sutri') === 'NO'; //NO === SI
$_trimesterNumber = (int) substr($_trimester, -1);
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
    $_value = DB::table('valores')->where('id', $_valueId)->first();
}

$gradeInfo = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $year],
])->first();
$optionLetter = $gradeInfo->letra === "ON";

// only this school
if (__ONLY_CBTM__) {
    $gradeInfo->nota_por = '1';
}

$_schoolInfo = [
    'Trimestre-1' => [
        'number' => 1,
        'dates' => ['ft1', 'ft2'],
        'end' => 'sie1'
    ],
    'Trimestre-2' => [
        'number' => 2,
        'dates' => ['ft3', 'ft4'],
        'end' => 'sie2'
    ],
    'Trimestre-3' => [
        'number' => 3,
        'dates' => ['ft5', 'ft6'],
        'end' => 'sie3'
    ],
    'Trimestre-4' => [
        'number' => 4,
        'dates' => ['ft7', 'ft8'],
        'end' => 'sie4'
    ],
    'Verano' => [
        'number' => 5,
        'dates' => ['fechav1', 'fechav2']
    ]
];
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
    if (__ONLY_CBTM__) {
        $columns = [
            'es' => ['Bono', 'Promedio', 'T-Diario', 'T-Libreta', 'P-Cor'],
            'en' => ['Bonus', 'Average', 'DW', 'HW', 'Quiz'],
            'text' => [false, '60%', '10%', '10%', '20%']
        ];
    } else {
        $columns = [
            'es' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
            'en' => ['Bonus', 'DW', 'HW', 'Quiz'],
            'text' => [false, false, false, false]
        ];
    }

    $_info = [
        "Notas" => [
            'table' => 'padres',
            'title' => 'Notas',
            'columns' => $columns,
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'totalAverage' => 'average1',
                'grades' => [1, 10],
                'values' => [
                    'tdia' => 'td1',
                    'tlib' => 'tl1',
                    'pcor' => 'pc1',
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'totalAverage' => 'average2',
                'grades' => [11, 20],
                'values' => [
                    'tdia' => 'td2',
                    'tlib' => 'tl2',
                    'pcor' => 'pc2',
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'totalAverage' => 'average3',
                'grades' => [21, 30],
                'values' => [
                    'tdia' => 'td3',
                    'tlib' => 'tl3',
                    'pcor' => 'pc3',
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]
            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'totalAverage' => 'average4',
                'grades' => [31, 40],
                'values' => [
                    'tdia' => 'td4',
                    'tlib' => 'tl4',
                    'pcor' => 'pc4',
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]
            ]
        ],
        "Notas2" => [
            'table' => 'padres7',
            'title' => 'Notas 2',
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'totalAverage' => 'average1',
                'grades' => [1, 10],
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'totalAverage' => 'average2',
                'grades' => [11, 20],
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'totalAverage' => 'average3',
                'grades' => [21, 30],
            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'totalAverage' => 'average4',
                'grades' => [31, 40],
            ]
        ],
        "Pruebas-Cortas" => [
            'table' => 'padres4',
            'title' => 'Pruebas Cortas',
            'columns' => [
                'es' => ['Nota 10'],
                'en' => ['Grade 10']
            ],
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 10],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [11, 20],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [21, 30],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]

            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'grades' => [31, 40],
                'values' => [
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]

            ]
        ],
        "Trab-Diarios" => [
            'table' => 'padres2',
            'title' => 'Trabajos Diarios',
            'columns' => [
                'es' => ['Nota 10'],
                'en' => ['Grade 10']
            ],
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 10],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [11, 20],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [21, 30],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]

            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'grades' => [31, 40],
                'values' => [
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]

            ]
        ],
        "Trab-Diarios2" => [
            'table' => 'padres5',
            'title' => 'Trabajos Diarios 2',
            'columns' => [
                'es' => ['Nota 10'],
                'en' => ['Grade 10']
            ],
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 10],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [11, 20],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [21, 30],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]

            ],
            'Trimestre-4' => [
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
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 10],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [11, 20],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [21, 30],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]

            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'grades' => [31, 40],
                'values' => [
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]

            ]
        ],
        "Trab-Libreta2" => [
            'table' => 'padres6',
            'title' => 'Trabajos de libreta 2',
            'columns' => [
                'es' => ['Nota 10'],
                'en' => ['Grade 10']
            ],
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 10],
                'values' => [
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ],
            'Trimestre-2' => [
                'totalGrade' => 'nota2',
                'grades' => [11, 20],
                'values' => [
                    'tpa' => 'tpa2',
                    'tdp' => 'por2'
                ]
            ],
            'Trimestre-3' => [
                'totalGrade' => 'nota3',
                'grades' => [21, 30],
                'values' => [
                    'tpa' => 'tpa3',
                    'tdp' => 'por3'
                ]

            ],
            'Trimestre-4' => [
                'totalGrade' => 'nota4',
                'grades' => [31, 40],
                'values' => [
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]

            ]
        ],
        "Cond-Asis" => [
            'table' => 'padres',
            'title' => 'Conducta y Asistencia',
            'Trimestre-1' => ['con1', 'aus1', 'tar1', 'de1'],
            'Trimestre-2' => ['con2', 'aus2', 'tar2', 'de2'],
            'Trimestre-3' => ['con3', 'aus3', 'tar3', 'de3'],
            'Trimestre-4' => ['con4', 'aus4', 'tar4', 'de4']
        ],
        "Ex-Final" => [
            'table' => 'padres',
            'title' => 'Examen Final',
            'Trimestre-2' => 'ex1',
            'Trimestre-4' => 'ex2'
        ],
        "V-Nota" => [
            'table' => 'padres',
            'title' => 'Notas de verano',
            'columns' => [
                'es' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
                'en' => ['Bonus', 'DW', 'HW', 'Quiz']
            ],
            'Verano' => [
                'totalGrade' => 'nota1',
                'grades' => [1, 7],
                'others' => ['con1', 'aus1', 'tar1'],
                'values' => [
                    'tdia' => 'td1',
                    'tlib' => 'tl1',
                    'pcor' => 'pc1',
                    'tpa' => 'tpa1',
                    'tdp' => 'por1'
                ]
            ]
        ]
    ];
}

$_dates = $_schoolInfo[$_trimester]['dates'];
$_end = isset($_schoolInfo[$_trimester]['end']) ? $_schoolInfo[$_trimester]['end'] : null;
$_options = isset($_info[$_report][$_trimester]) ? $_info[$_report][$_trimester] : null;
$_values = isset($_options['values']) ? $_options['values'] : null;
$_columns = null;
if (isset($_info[$_report]['columns'])) {
    $_columns = isset($_info[$_report]['columns'][__LANG]) ? $_info[$_report]['columns'][__LANG] : $_info[$_report]['columns'];
}
$_trimesterNumber = $_schoolInfo[$_trimester]['number'];
$_thisReport = $_info[$_report];
$students = new Student();
$students = $students->findByClass($_class, $_thisReport['table'], $_report === 'V-Nota' ? true : false);


// functions
function findValue($table, $student)
{
    global $_class;
    global $year;
    global $_values;
    return DB::table($table)->select($_values['tdp'])->where([
        ['curso', $_class],
        ['ss', $student->ss],
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
function findTotal($type, $student)
{
    global $_info;
    global $_report;
    $tpaTotal = 0;
    global $_trimesterNumber;
    if ($_trimesterNumber === 2 || $_trimesterNumber === 4) {
        $lastTrimester = $_trimesterNumber - 1;
        $t = $_info[$_report]["Trimestre-$lastTrimester"]['values'][$type];
        $tpaTotal += (int) $student->{$t};
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
                        <p class="text-monospace"><?= $lang->translation("Trimestre:") ?> <span class="badge badge-info"><?= str_replace('Trimestre', $lang->translation("Trimestre"), str_replace('-', ' ', $_trimester)) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Total de estudiantes:") ?> <span class="badge badge-info"><?= sizeof($students) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Entrando notas a:") ?> <span class="badge badge-info"><?= $lang->translation($_thisReport['title']) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Fecha de inicio:") ?> <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[0]), true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Fecha de cierre:") ?> <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[1]), true) ?></span>
                        </p>
                    </div>
                    <div class="col">
                        <p class="text-monospace"><?= $lang->translation("Tipo de nota:") ?> <span class="badge badge-info"><?= $gradeInfo->nota_por === "1" ? $lang->translation("Porciento") : $lang->translation("Suma") ?></span>
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
                                <input type="checkbox" class="custom-control-input" id="letra" value="<?= $letterNumber ?>" <?= ($gradeInfo->letra === "ON") ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="letra"><?= $lang->translation("Pasar a letras") ?></label>
                            </div>
                            <small><?= $lang->translation("Está opción se aplica en la columna") ?>
                                <b><?= $_report === 'Notas' ? $lang->translation("Nota") . '-9' : $lang->translation("Nota") . '-7' ?></b>
                                <?= $lang->translation("exclusivamente.") ?></small>
                        </div>
                        <div class="col mt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="pal" value="ON" <?= ($gradeInfo->pal === "ON") ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="pal"><?= $lang->translation("Conversión") ?></label>
                            </div>
                            <small><?= $lang->translation("Está opción es para convertir de numero a letra.") ?></small>
                        </div>
                        <?php if ($_end): ?>
                            <?php if ($teacher->info('sie') === 'Si' && $teacher->info('sieab') === '4'): ?>
                                <div class="col mt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="<?= $_end ?>" value='X' <?= ($gradeInfo->{$_end} === "X") ? 'checked=""' : '' ?> disabled>
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
            <input type="hidden" name="report" id="report" value="<?= $_report ?>">
            <input type="hidden" name="trimester" id="trimester" value="<?= $_trimester ?>">
            <input type="hidden" name="table" id="table" value="<?= $_thisReport['table'] ?>">
            <input type="hidden" name="subject" id="subject" value="<?= $_class ?>">
            <input type="hidden" name="optionCppd" id="optionCppd" value="<?= $optionCppd ?>">
            <input type="hidden" name="valueA" id="valueA" value="<?= $teacher->info('vala') ?>">
            <input type="hidden" name="valueB" id="valueB" value="<?= $teacher->info('valb') ?>">
            <input type="hidden" name="valueC" id="valueC" value="<?= $teacher->info('valc') ?>">
            <input type="hidden" name="valueD" id="valueD" value="<?= $teacher->info('vald') ?>">
            <input type="hidden" name="valueF" id="valueF" value="<?= $teacher->info('valf') ?>">
            <input type="hidden" name="sumTrimester" id="sumTrimester" value="<?= $sumTrimester ?>">
            <div class="table-responsive my-3 shadow">
                <?php if ($_report === 'Notas' || $_report === 'V-Nota' || $_report === 'Pruebas-Cortas' || $_report === 'Trab-Diarios' || $_report === 'Trab-Diarios2' || $_report === 'Trab-Libreta' || $_report === 'Trab-Libreta2'): ?>
                    <!-- Required hidden inputs -->
                    <input type="hidden" name="gradeStart" id="gradeStart" value="<?= $_options['grades'][0] ?>">
                    <input type="hidden" name="tpa" id="tpa" value="<?= $_values['tpa'] ?>">
                    <input type="hidden" name="tdp" id="tdp" value="<?= $_values['tdp'] ?>">
                    <input type="hidden" name="totalGrade" id="totalGrade" value="<?= $_options['totalGrade'] ?>">
                    <input type="hidden" name="optionLetter" id="optionLetter" value="<?= $optionLetter ? $letterNumber : 0 ?>">
                    <?php if (__ONLY_CBTM__): ?>
                        <input type="hidden" name="totalAverage" id="totalAverage" value="<?= $_options['totalAverage'] ?>">
                    <?php endif ?>

                    <?php if ($_report === 'Notas' || $_report === 'V-Nota' && !$optionCppd): ?>
                        <input type="hidden" name="tdia" id="tdia" value="<?= $_values['tdia'] ?>">
                        <input type="hidden" name="tlib" id="tlib" value="<?= $_values['tlib'] ?>">
                        <input type="hidden" name="pcor" id="pcor" value="<?= $_values['pcor'] ?>">
                    <?php endif ?>

                    <table class="table table-sm table-hover bg-white">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                                </th>
                                <?php
                                $amountOfGrades = $_report === 'V-Nota' ? 7 : ($_options['grades'][1]) - ($_options['grades'][0]);
                                for ($i = 1; $i <= $amountOfGrades; $i++): ?>
                                    <th scope="col"><?= $lang->translation("Nota") . " {$i}" ?></th>
                                <?php endfor ?>
                                <?php if ($_columns !== null): ?>
                                    <?php foreach ($_columns as $index => $column): ?>
                                        <th scope="col">
                                            <?= $column ?>
                                            <?php if ($_info[$_report]['columns']['text'][$index]): ?>
                                                <span style="font-size: 15px;" class="text-muted"><?= $_info[$_report]['columns']['text'][$index] ?></span>
                                            <?php endif ?>
                                        </th>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <th scope="col"><?= $lang->translation("TPA") ?></th>
                                <th scope="col"><?= $lang->translation("TDP") ?></th>
                                <th scope="col"><?= $lang->translation("Nota") ?></th>
                                <?php if ($_report === 'V-Nota'): ?>
                                    <th scope="col"><?= $lang->translation("Conducta") ?></th>
                                    <th scope="col"><?= $lang->translation("Ausencias") ?></th>
                                    <th scope="col"><?= $lang->translation("Tardanzas") ?></th>
                                <?php endif ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <th scope="row">
                                        <?= $index + 1 ?>
                                        <?php
                                        // important information for the values
                                        // Only on Notas and when "Cambiar Porciento a Punto Decimal" is not activated
                                        if ($_report === 'Notas' && !$optionCppd):
                                            if ($gradeInfo->nota_por === "2") {
                                                $_student = findValue($_info['Trab-Diarios']['table'], $student);
                                                $tdia = $_student->{$_values['tdp']};
                                                $_student = findValue($_info['Trab-Libreta']['table'], $student);
                                                $tlib = $_student->{$_values['tdp']};
                                                $_student = findValue($_info['Pruebas-Cortas']['table'], $student);
                                                $pcor = $_student->{$_values['tdp']};
                                            } else {
                                                if (__ONLY_CBTM__) {
                                                    $tdia = $student->{$_values['tdia']} ? '10' : '';
                                                    $tlib = $student->{$_values['tlib']} ? '10' : '';
                                                    $pcor = $student->{$_values['pcor']} ? '20' : '';
                                                } else {
                                                    $tdia = $student->{$_values['tdia']} ? '100' : '';
                                                    $tlib = $student->{$_values['tlib']} ? '100' : '';
                                                    $pcor = $student->{$_values['pcor']} ? '100' : '';
                                                }
                                            }
                                            ?>
                                            <?php if ($sumTrimester && ($_trimesterNumber === 2 || $_trimesterNumber === 4)): ?>
                                                <input type="hidden" class="_tpaTotal" name="tpaTotal" id="tpaTotal" value="<?= findTotal('tpa', $student) ?>">
                                                <input type="hidden" class="_tdpTotal" name="tdpTotal" id="tdpTotal" value="<?= findTotal('tdp', $student) ?>">
                                            <?php endif ?>
                                            <?php if ($_report === 'Notas' && __ONLY_CBTM__): ?>
                                                <input type="hidden" name="peso-<?= $student->ss ?>" class='_peso' value="<?= $student->peso ?>">
                                                <!-- Get the grades from Notas2 -->
                                                <?php
                                                $nota2Grades = DB::table("padres7")->where([
                                                    ['ss', $student->ss],
                                                    ['curso', $_class],
                                                    ['year', $year]
                                                ])->first();
                                                $nota2Values = DB::table('valores')
                                                    ->where([
                                                        ['curso', $_class],
                                                        ['trimestre', $_trimester],
                                                        ['nivel', "Notas2"],
                                                        ['year', $year]
                                                    ])->first();
                                                $_nota2Grade = $_nota2value = 0;
                                                for ($i = $_options['grades'][0]; $i <= $_options['grades'][1]; $i++) {
                                                    if ($nota2Grades->{"not$i"} != '' && $nota2Values->{"val$i"} != '') {
                                                        $_nota2Grade += $nota2Grades->{"not$i"};
                                                        $_nota2Value += $nota2Values->{"val$i"};
                                                    }
                                                }
                                                ?>

                                                <input type="hidden" class="_nota2Grade" value="<?= $_nota2Grade ?>">
                                                <input type="hidden" class="_nota2Value" value="<?= $_nota2Value ?>">
                                            <?php endif ?>
                                            <input type="hidden" class="_tdia" value="<?= $tdia ?>">
                                            <input type="hidden" class="_tlib" value="<?= $tlib ?>">
                                            <input type="hidden" class="_pcor" value="<?= $pcor ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                    </th>
                                    <td>
                                        <?= utf8_decode("$student->apellidos $student->nombre"); ?>
                                    </td>
                                    <?php for ($i = $_options['grades'][0]; $i <= $_options['grades'][1]; $i++): ?>
                                        <td><input class="form-control form-control-sm text-center grade" type="text" name="<?= "grade-$student->ss" ?>" value="<?= $student->{"not{$i}"} ?>" disabled>
                                        </td>
                                    <?php endfor ?>
                                    <?php if (__ONLY_CBTM__ && $_report === 'Notas'): ?>
                                        <td><input class="form-control-plaintext text-center totalAverage" readonly type="text" name="totalAverage-<?= $student->ss ?>" value=<?= $student->{$_options['totalAverage']} ?>></td>
                                    <?php endif ?>
                                    <?php if ($_report === 'V-Nota' && !$optionCppd): ?>
                                        <td><input class="form-control form-control-sm text-center grade" type="text" name="<?= "grade-$student->ss" ?>" value="<?= $student->not10 ?>" disabled></td>
                                    <?php endif ?>
                                    <?php if ($_values !== null): ?>
                                        <?php foreach ($_values as $fileName => $value):
                                            if (__ONLY_CBTM__ && $_report === 'Notas') {
                                                if ($fileName === 'tdia' || $fileName === 'tlib' || $fileName === 'pcor') {
                                                    $r = [
                                                        'tdia' => 'Trab-Diarios',
                                                        'tlib' => 'Trab-Libreta',
                                                        'pcor' => 'Pruebas-Cortas',
                                                    ];
                                                    $_student = findValueFor($_info[$r[$fileName]]['table'], $student);
                                                    $val = $_student->{$_info[$r[$fileName]][$_trimester]['totalGrade']};
                                                    // echo "$name= $val";
                                                } else {
                                                    $val = $student->{$value};
                                                }
                                            } else {
                                                $val = $student->{$value};
                                            }
                                            ?>
                                            <td><input class="form-control-plaintext text-center <?= $fileName ?>" readonly type="text" name="<?= $fileName . "-$student->ss" ?>" value=<?= $val ?>></td>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <td><input class="form-control-plaintext text-center totalGrade" readonly type="text" name="totalGrade-<?= $student->ss ?>" value=<?= $student->{$_options['totalGrade']} ?>></td>
                                    <?php if ($_report === 'V-Nota'): ?>
                                        <td><input class="form-control text-center" type="text" name="con-<?= $student->ss ?>" value=<?= $student->{$_options['others'][0]} ?>></td>
                                        <td><input class="form-control text-center" type="text" name="asis-<?= $student->ss ?>" value=<?= $student->{$_options['others'][1]} ?>></td>
                                        <td><input class="form-control text-center" type="text" name="tar-<?= $student->ss ?>" value=<?= $student->{$_options['others'][2]} ?>></td>
                                    <?php endif ?>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php elseif ($_report === 'Notas2'):
                    $amountOfGrades = 10; ?>
                    <input type="hidden" name="gradeStart" id="gradeStart" value="<?= $_options['grades'][0] ?>">
                    <table class="table table-sm table-hover bg-white">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                                </th>
                                <?php for ($i = 1; $i <= $amountOfGrades; $i++): ?>
                                    <th scope="col"><?= $lang->translation("Nota") . " {$i}" ?></th>
                                <?php endfor ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <th scope="row">
                                        <?= $index + 1 ?>
                                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                    </th>
                                    <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                    <?php for ($i = $_options['grades'][0]; $i <= $_options['grades'][1]; $i++): ?>
                                        <td><input class="form-control form-control-sm text-center grade" type="text" name="<?= "grade-$student->ss" ?>" value="<?= $student->{"not{$i}"} ?>" disabled>
                                        </td>
                                    <?php endfor ?>
                                <?php endforeach ?>
                        </tbody>
                    </table>
                <?php elseif ($_report === 'Cond-Asis'): ?>
                    <table class="table table-sm table-hover bg-white">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                                </th>
                                <th scope="col"><?= $lang->translation("Conducta") ?></th>
                                <th scope="col"><?= $lang->translation("Ausencias") ?></th>
                                <th scope="col"><?= $lang->translation("Tardanzas") ?></th>
                                <th scope="col"><?= $lang->translation("Deméritos") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student): ?>
                                <tr>
                                    <th scope="row">
                                        <?= $index + 1 ?>
                                        <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                    </th>
                                    <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                    <td><input class="form-control form-control-sm text-center" type="text" name="<?= "con-{$student->ss}" ?>" value="<?= $student->{$_options[0]} ?>"></td>
                                    <td><input class="form-control form-control-sm text-center" type="text" name="<?= "aus-{$student->ss}" ?>" value="<?= $student->{$_options[1]} ?>"></td>
                                    <td><input class="form-control form-control-sm text-center" type="text" name="<?= "tar-{$student->ss}" ?>" value="<?= $student->{$_options[2]} ?>"></td>
                                    <td><input class="form-control form-control-sm text-center" type="text" name="<?= "de-{$student->ss}" ?>" value="<?= $student->{$_options[3]} ?>"></td>
                                <?php endforeach ?>
                        </tbody>
                    </table>
                <?php elseif ($_report === 'Ex-Final'): ?>
                    <?php if ($_options !== null): ?>
                        <?php if (__ONLY_CBTM__): ?>
                            <input type="hidden" name="exGrade" value="<?= substr($students[0]->grado, 0, 2) ?>" />
                        <?php endif ?>
                        <table class="table table-sm table-hover bg-white">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" style="width: 19rem;"><?= $lang->translation("Nombre del estudiante") ?>
                                    </th>
                                    <th scope="col"><?= $lang->translation("Nota del Examen Final") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <th scope="row">
                                            <?= $index + 1 ?>
                                            <input type="hidden" name="ss" value="<?= $student->ss ?>">
                                        </th>
                                        <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                        <td><input class="form-control form-control-sm text-center w-auto mx-auto" type="text" name="<?= "ex-{$student->ss}" ?>" value="<?= $student->{$_options} ?>"></td>
                                    <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <h1 class="display-3 text-center">
                            <?= $lang->translation("Los examenes finales solo estan en el trimestre 2 y trimestre 4") ?>
                        </h1>
                    <?php endif ?>
                <?php endif ?>

                <!-- <button type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button> -->
                <?php if ($_options !== null): ?>
                    <?php if ((Util::date() <= $teacher->info($_dates[1]) && Util::date() >= $teacher->info($_dates[0])) && $teacher->fechas && ($teacher->tri === $_trimesterNumber || $teacher->tri === 5)): ?>
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
        <?php if ($_report !== 'Notas' && $_report !== 'V-Notas' && $_report !== 'Ex-Final' && $_report !== 'Cond-Asis'): ?>
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
                        <input type="hidden" id="valueId" value="<?= $_value->id ?>">
                        <div class="card-body">
                            <div class="form-row">
                                <?php $cant = ($_report === 'Ex-Final') ? 1 : $amountOfGrades ?>
                                <?php for ($i = 1; $i <= $cant; $i++): ?>
                                    <div class="form-row col-12 col-md-8 mb-2">
                                        <div class="form-group col-12">
                                            <label for="<?= "tema$i" ?>"><?= $lang->translation("Tema") ?>     <?= $i ?></label>
                                            <input class="form-control" type="text" id="<?= "tema$i" ?>" value="<?= $_value->{"tema{$i}"} ?>" />
                                        </div>
                                        <div class="form-group col-4 col-md-2 text-center">
                                            <label for="<?= "val$i" ?>"><?= $lang->translation("Valor") ?></label>
                                            <input class="form-control text-center" type="text" id="<?= "val$i" ?>" data-value="<?= $_value->{"val{$i}"} ?>" value="<?= $_value->{"val{$i}"} ?>" />
                                        </div>
                                        <div class="form-group col-8 col-md-3">
                                            <label for="<?= "fec$i" ?>"><?= $lang->translation("Fecha") ?></label>
                                            <input class="form-control" type="date" id="<?= "fec$i" ?>" value="<?= $_value->{"fec{$i}"} ?>" />
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

</body>

</html>