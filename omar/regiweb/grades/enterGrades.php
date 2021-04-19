<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();
Server::is_post();
$_class = $_POST['class'];
$_trimester = $_POST['tri'];
$_report = $_POST['tra'];

$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$cppd = $teacher->info('cppd') === 'Si';
$_value = DB::table('valores')
    ->where([
        ['curso', $_class],
        ['trimestre', $_trimester],
        ['nivel', $_report],
        ['year', $year]
    ])->first();


$gradeInfo = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $year],
])->first();

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
if ($cppd) {
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

    $_info = [
        "Notas" => [
            'table' => 'padres',
            'title' => 'Notas',
            'columns' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
            'Trimestre-1' => [
                'totalGrade' => 'nota1',
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
                'grades' => [31, 40],
                'values' => [
                    'tdia' => 'td4',
                    'pcor' => 'pc4',
                    'tlib' => 'tl4',
                    'tpa' => 'tpa4',
                    'tdp' => 'por4'
                ]
            ]
        ],
        "Pruebas-Cortas" => [
            'table' => 'padres4',
            'title' => 'Pruebas Cortas',
            'columns' => ['Nota 10'],
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
            'columns' => ['Nota 10'],
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
            'columns' => ['Nota 10'],
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
            'columns' => ['Nota 10'],
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
            'columns' => ['Nota 10'],
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
            'columns' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
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
$_columns = isset($_info[$_report]['columns']) ? $_info[$_report]['columns'] : null;
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
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Entrada de notas";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <!-- Required hidden inputs -->
    <input type="hidden" id="report" value="<?= $_report ?>">
    <input type="hidden" id="cppd" value="<?= $cppd ?>">
    <input type="hidden" id="valueA" value="<?= $teacher->info('vala') ?>">
    <input type="hidden" id="valueB" value="<?= $teacher->info('valb') ?>">
    <input type="hidden" id="valueC" value="<?= $teacher->info('valc') ?>">
    <input type="hidden" id="valueD" value="<?= $teacher->info('vald') ?>">
    <input type="hidden" id="valueF" value="<?= $teacher->info('valf') ?>">

    <div class="container-lg mt-lg-3 px-0">
        <div class="card border-info">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col">
                        <p class="text-monospace">Curso: <span class="badge badge-info"><?= $_class ?> </span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Trimestre: <span class="badge badge-info"><?= $_trimester ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Total de estudiantes: <span class="badge badge-info"><?= sizeof($students) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Entrando notas a: <span class="badge badge-info"><?= $_thisReport['title'] ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Fecha de inicio: <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[0]), true) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Fecha de cierre: <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[1]), true) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Tipo de nota: <span class="badge badge-info"><?= $gradeInfo->nota_por === "1" ? 'Porciento' : 'Suma' ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_report === 'Notas' || $_report === 'V-Nota') : ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div class="row row-cols-1">
                        <div class="col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="letter" value="ON" <?= ($gradeInfo->letra === "ON") ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="letter">Pasar a letras</label>
                            </div>
                            <small>Está opción se aplica en la columna <b><?= $_report === 'Notas' ? 'Nota-9' : 'Nota-7' ?></b> exclusivamente.</small>
                        </div>
                        <div class="col mt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="convert" value="ON" disabled>
                                <label class="custom-control-label" for="convert">Conversión</label>
                            </div>
                            <small>Está opción es para convertir de numero a letra.</small>
                        </div>
                        <?php if ($_end) : ?>
                            <?php if ($teacher->info('sie') === 'Si' && $teacher->info('sieab') === '4') : ?>
                                <div class="col mt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="finish" value='X' <?= ($gradeInfo->{$_end} === "X") ? 'checked=""' : '' ?> disabled>
                                        <label class="custom-control-label" for="finish">Aviso Terminar</label>
                                    </div>
                                    <small>Cuando termine el trimestre marque está Opción.</small>
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div class="row row-cols-1">
                        <div class="col">
                            <h4>¿Quieres que estas notas sean?</h4>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="noteType1" class="custom-control-input" name="noteType" value="1" <?= $gradeInfo->nota_por === "1" ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="noteType1">Porciento</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="noteType2" class="custom-control-input" name="noteType" value="2" <?= $gradeInfo->nota_por === "2" ? 'checked=""' : '' ?> disabled>
                                <label class="custom-control-label" for="noteType2">Suma</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>

    <div class="container-fluid">
        <!-- Students list -->
        <div class="table-responsive my-3 shadow">
            <?php if ($_report === 'Notas' || $_report === 'V-Nota' || $_report === 'Pruebas-Cortas' || $_report === 'Trab-Diarios' || $_report === 'Trab-Diarios2' || $_report === 'Trab-Libreta' || $_report === 'Trab-Libreta2') : ?>
                <div class="loading text-center mb-3">
                    <span class="spinner-border spinner-border-lg" role="status" aria-hidden="true"></span>
                </div>
                <form action="<?= Route::url('/regiweb/grades/includes/enterGrades.php') ?>" method="POST">
                    <table class="table table-sm table-hover bg-white">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col" style="width: 19rem;">Nombre del estudiante</th>
                                <?php
                                $amountOfGrades = $_report === 'V-Nota' ? 7 : ($_options['grades'][1]) - ($_options['grades'][0]);
                                for ($i = 1; $i <= $amountOfGrades; $i++) :
                                ?>
                                    <th scope="col"><?= "Nota {$i}" ?></th>
                                <?php endfor ?>
                                <?php if ($_columns !== null) : ?>
                                    <?php foreach ($_columns as $column) : ?>
                                        <th scope="col"><?= $column ?></th>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <th scope="col">TPA</th>
                                <th scope="col">TDP</th>
                                <th scope="col">Nota</th>
                                <?php if ($_report === 'V-Nota') : ?>
                                    <th scope="col">Conducta</th>
                                    <th scope="col">Ausencias</th>
                                    <th scope="col">Tardanzas</th>
                                <?php endif ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $student) : ?>
                                <tr>
                                    <th scope="row">
                                        <?= $index + 1 ?>
                                        <?php
                                        // important information for the values
                                        // Only on Notas and when "Cambiar Porciento a Punto Decimal" is not activated
                                        if ($_report === 'Notas' && !$cppd) :
                                            if ($gradeInfo->nota_por === "2") {
                                                $_student =  findValue('padres2', $student);
                                                $tdia = $_student->{$_values['tdp']};
                                                $_student =  findValue('padres6', $student);
                                                $tlib = $_student->{$_values['tdp']};
                                                $_student =  findValue('padres4', $student);
                                                $pcor = $_student->{$_values['tdp']};
                                            } else {
                                                $tdia = $student->{$_values['tdia']} ? '100' : '';
                                                $tlib = $student->{$_values['tlib']} ? '100' : '';
                                                $pcor = $student->{$_values['pcor']} ? '100' : '';
                                            }
                                        ?>
                                            <input type="hidden" class="_tdia" value="<?= $tdia ?>">
                                            <input type="hidden" class="_tlib" value="<?= $tlib ?>">
                                            <input type="hidden" class="_pcor" value="<?= $pcor ?>">
                                        <?php endif; ?>
                                    </th>
                                    <td>
                                        <?= utf8_decode("$student->apellidos $student->nombre"); ?>
                                        <input type="hidden" name="ss[]" value="<?= $student->ss ?>">
                                    </td>
                                    <?php for ($i = $_options['grades'][0]; $i <= $_options['grades'][1]; $i++) : ?>
                                        <td><input class="form-control form-control-sm text-center grade" type="text" name="<?= "grade[$student->ss][]" ?>" value="<?= $student->{"not{$i}"} ?>" disabled></td>
                                    <?php endfor ?>
                                    <?php if ($_report === 'V-Nota' && !$cppd) : ?>
                                        <td><input class="form-control form-control-sm text-center grade" type="text" name="<?= "grade[$student->ss][]" ?>" value="<?= $student->not10 ?>" disabled></td>
                                    <?php endif ?>
                                    <?php if ($_values !== null) : ?>
                                        <?php foreach ($_values as $name => $value) : ?>
                                            <td><input class="form-control-plaintext text-center <?= $name ?>" readonly type="text" name="<?= $name . "[$student->ss]" ?>" value=<?= $student->{$value} ?>></td>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <td><input class="form-control-plaintext text-center totalGrade" readonly type="text" name="totalGrade[<?= $student->ss ?>]" value=<?= $student->{$_options['totalGrade']} ?>></td>
                                    <?php if ($_report === 'V-Nota') : ?>
                                        <td><input class="form-control text-center" type="text" name="con[<?= $student->ss ?>]" value=<?= $student->{$_options['others'][0]} ?>></td>
                                        <td><input class="form-control text-center" type="text" name="asis[<?= $student->ss ?>]" value=<?= $student->{$_options['others'][1]} ?>></td>
                                        <td><input class="form-control text-center" type="text" name="tar[<?= $student->ss ?>]" value=<?= $student->{$_options['others'][2]} ?>></td>
                                    <?php endif ?>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                <?php elseif ($_report === 'Cond-Asis') : ?>
                    <div class="container">
                        <table class="table table-sm table-hover bg-white">
                            <thead class="thead-dark text-center">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col" style="width: 19rem;">Nombre del estudiante</th>
                                    <th scope="col">Conducta</th>
                                    <th scope="col">Ausencias</th>
                                    <th scope="col">Tardanzas</th>
                                    <th scope="col">Deméritos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student) : ?>
                                    <tr>
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                        <td><input class="form-control form-control-sm text-center" type="text" name="<?= "" ?>" value=<?= $student->{$_options[0]} ?>></td>
                                        <td><input class="form-control form-control-sm text-center" type="text" name="<?= "" ?>" value=<?= $student->{$_options[1]} ?>></td>
                                        <td><input class="form-control form-control-sm text-center" type="text" name="<?= "" ?>" value=<?= $student->{$_options[2]} ?>></td>
                                        <td><input class="form-control form-control-sm text-center" type="text" name="<?= "" ?>" value=<?= $student->{$_options[3]} ?>></td>
                                    <?php endforeach ?>
                            </tbody>
                        </table>

                    </div>
                <?php elseif ($_report === 'Ex-Final') : ?>
                    <div class="container">
                        <?php if ($_options !== null) : ?>
                            <table class="table table-sm table-hover bg-white">
                                <thead class="thead-dark text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col" style="width: 19rem;">Nombre del estudiante</th>
                                        <th scope="col">Nota del Examen Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $student) : ?>
                                        <tr>
                                            <th scope="row"><?= $index + 1 ?></th>
                                            <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                            <td><input class="form-control form-control-sm text-center w-auto mx-auto" type="text" name="<?= "" ?>" value=<?= $student->{$_options} ?>></td>
                                        <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <h1 class="display-3 text-center">Los examenes finales solo estan en el Trimestre 2 y Trimestre 4</h1>
                            <button class="btn btn-primary d-block mx-auto mb-3" onclick="javascript:history.back()">Volver</button>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <!-- <button type="submit" class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button> -->
                </form>
                <?php if ($_options !== null) : ?>
                    <?php if (Util::date() < $teacher->info($_dates[1]) && $teacher->fechas === 1 && ($teacher->tri === $_trimesterNumber || $teacher->tri === 5)) : ?>
                        <button class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button>
                    <?php else : ?>
                        <h4 class="text-center text-danger">Lo Sentimos, La fecha Ha Vencido o la Selección del trimestre es equivocada. Intentelo de Nuevo o Comuniquese con la Administración.</h4>
                    <?php endif ?>
                <?php endif ?>
        </div>
        <!-- end Students list -->
        <?php if ($_report !== 'Notas' || $_report !== 'V-Notas' || $_report !== 'Ex-Final' || $_report !== 'Cond-Asis') : ?>
            <h2 class="text-center text-info mb-0">*Recuerde ir a la pagina de notas y darle a grabar para tener los promédios correctos.*</h2>
        <?php endif ?>
        <!-- Values -->
        <div class="container my-5">
            <div class="accordion" id="valuesAccordion">
                <div class="card">
                    <div class="card-header bg-secondary" id="valuesHead">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left text-light font-weight-bold" type="button" data-toggle="collapse" data-target="#values" aria-expanded="true" aria-controls="values">
                                Valores
                            </button>
                        </h2>
                    </div>
                    <div id="values" class="collapse" aria-labelledby="valuesHead" data-parent="#valuesAccordion">
                        <input type="hidden" id="valueId" value="<?= $_value->id ?>">
                        <div class="card-body">
                            <div class="form-row">
                                <?php $cant = ($_report === 'Ex-Final') ? 1 : $amountOfGrades ?>
                                <?php for ($i = 1; $i <= $cant; $i++) : ?>
                                    <div class="form-row col-12 mb-2">
                                        <div class="form-group col-8">
                                            <label for="<?= "tema$i" ?>">Tema <?= $i ?></label>
                                            <input class="form-control" type="text" id="<?= "tema$i" ?>" value="<?= $_value->{"tema{$i}"} ?>" />
                                        </div>
                                        <div class="form-group col-1 text-center">
                                            <label for="<?= "val$i" ?>">Valor</label>
                                            <input class="form-control text-center" type="text" id="<?= "val$i" ?>" data-value="<?= $_value->{"val{$i}"} ?>" value="<?= $_value->{"val{$i}"} ?>" />
                                        </div>
                                        <div class="form-group col-3">
                                            <label for="<?= "fec$i" ?>">Fecha</label>
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