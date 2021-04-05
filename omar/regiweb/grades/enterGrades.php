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
$_value = DB::table('valores')
    ->where([
        ['curso', $_class],
        ['trimestre', $_trimester],
        ['nivel', $_report],
        ['year', $teacher->info('year')]
    ])->first();


$gradeInfo = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $teacher->info('year')],

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
    ]
];

$_info = [
    "Notas" => [
        'table' => 'padres',
        'columns' => ['Bono', 'T-Diario', 'T-Libreta', 'P-Cor'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tdia' => 'td1',
                'tlib' => 'tl1',
                'pcor' => 'tpc1',
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tdia' => 'td2',
                'tlib' => 'tl2',
                'pcor' => 'tpc2',
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tdia' => 'td3',
                'tlib' => 'tl3',
                'pcor' => 'tpc3',
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]
        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tdia' => 'td4',
                'pcor' => 'tpc4',
                'tlib' => 'tl4',
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]
        ]
    ],
    "Pruebas-Cortas" => [
        'table' => 'padres4',
        'columns' => ['Nota 10'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]

        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]

        ]
    ],
    "Trab-Diarios" => [
        'table' => 'padres2',
        'columns' => ['Nota 10'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]

        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]

        ]
    ],
    "Trab-Diarios2" => [
        'table' => 'padres5',
        'columns' => ['Nota 10'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]

        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]

        ]
    ],
    "Trab-Libreta" => [
        'table' => 'padres3',
        'columns' => ['Nota 10'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]

        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]

        ]
    ],
    "Trab-Libreta2" => [
        'table' => 'padres6',
        'columns' => ['Nota 10'],
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'grades' => [1, 10],
            'values' => [
                'tpa' => 'tpa1',
                'por' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'grades' => [11, 20],
            'values' => [
                'tpa' => 'tpa2',
                'por' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'grades' => [21, 30],
            'values' => [
                'tpa' => 'tpa3',
                'por' => 'por3'
            ]

        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'grades' => [31, 40],
            'values' => [
                'tpa' => 'tpa4',
                'por' => 'por4'
            ]

        ]
    ],
    "Cond-Asis" => [
        'table' => 'padres',
        'Trimestre-1' => ['con1', 'aus1', 'tar1', 'de1'],
        'Trimestre-2' => ['con2', 'aus2', 'tar2', 'de2'],
        'Trimestre-3' => ['con3', 'aus3', 'tar3', 'de3'],
        'Trimestre-4' => ['con4', 'aus4', 'tar4', 'de4']
    ],
    "Ex-Final" => [
        'table' => 'padres',
        'Trimestre-2' => 'ex1',
        'Trimestre-4' => 'ex2'
    ],
    "Ex-Final" => [
        'table' => 'padres',
        'Trimestre-1' => 'ex1',
        'Trimestre-2' => 'ex1',
        'Trimestre-3' => 'ex1',
        'Trimestre-4' => 'ex2'
    ],
];

$_dates = $_schoolInfo[$_trimester]['dates'];
$_end = $_schoolInfo[$_trimester]['end'];
$_options = isset($_info[$_report][$_trimester]) ? $_info[$_report][$_trimester] : null;
$_values = isset($_options['values']) ? $_options['values'] : null;
$_columns = isset($_info[$_report]['columns']) ? $_info[$_report]['columns'] : null;
$_trimesterNumber = $_schoolInfo[$_trimester]['number'];


$students = new Student();
$students = $students->findByClass($_class, $_info[$_report]['table']);
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
                        <p class="text-monospace">Total de estudiantes: <span class="badge badge-info"><?= sizeof($teacher->homeStudents()) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Entrando notas a: <span class="badge badge-info"><?= $_report ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Fecha de inicio: <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[0]), true) ?></span></p>
                    </div>
                    <div class="col">
                        <p class="text-monospace">Fecha de cierre: <span class="badge badge-info"><?= Util::formatDate($teacher->info($_dates[1]), true) ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($_report === 'Notas') : ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div class="row row-cols-1">
                        <div class="col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customCheckbox1" value="ON" <?= ($gradeInfo->letra === "ON") ? 'checked=""' : '' ?>>
                                <label class="custom-control-label" for="customCheckbox1">Pasar a letras</label>
                            </div>
                            <small>Está opción se aplica en la columna <b>Nota-9</b> exclusivamente.</small>
                        </div>
                        <div class="col mt-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customCheckbox2" value="ON">
                                <label class="custom-control-label" for="customCheckbox2">Conversión</label>
                            </div>
                            <small>Está opción es para convertir de numero a letra.</small>
                        </div>
                        <?php if ($teacher->info('sie') === 'Si' && $teacher->info('sieab') === '4') : ?>
                            <div class="col mt-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="customCheckbox3" value='X' <?= ($gradeInfo->{$_end} === "X") ? 'checked=""' : '' ?>>
                                    <label class="custom-control-label" for="customCheckbox3">Aviso Terminar</label>
                                </div>
                                <small>Cuando termine el trimestre marque está Opción.</small>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        <?php elseif ($_report === 'Pruebas-Cortas' || $_report === 'Trab-Diarios' || $_report === 'Trab-Diarios2' || $_report === 'Trab-Libreta' || $_report === 'Trab-Libreta2') : ?>
            <div class="card border-secondary mt-2">
                <div class="card-body">
                    <div class="row row-cols-1">
                        <div class="col">
                            <h4>¿Quieres que estas notas sean?</h4>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio1" class="custom-control-input" name="customRadio" value="1" <?= ($gradeInfo->nota_por === "1") ? 'checked=""' : '' ?>>
                                <label class="custom-control-label" for="customRadio1">Porciento</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio2" class="custom-control-input" name="customRadio" value="2" <?= ($gradeInfo->nota_por === "2") ? 'checked=""' : '' ?>>
                                <label class="custom-control-label" for="customRadio2">Suma</label>
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
            <?php if ($_report === 'Notas' || $_report === 'Pruebas-Cortas' || $_report === 'Trab-Diarios' || $_report === 'Trab-Diarios2' || $_report === 'Trab-Libreta' || $_report === 'Trab-Libreta2') : ?>
                <table class="table table-sm table-hover bg-white">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" style="width: 19rem;">Nombre del estudiante</th>
                            <?php for ($i = 1; $i <= 9; $i++) : ?>
                                <th scope="col"><?= "Nota {$i}" ?></th>
                            <?php endfor ?>
                            <?php if ($_columns !== null) : ?>
                                <?php foreach ($_columns as $column) : ?>
                                    <th scope="col"><?= $column ?></th>
                                <?php endforeach ?>
                            <?php endif ?>
                            <th scope="col">TPA</th>
                            <th scope="col">%</th>
                            <th scope="col">Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student) : ?>
                            <tr>
                                <th scope="row"><?= $index + 1 ?></th>
                                <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                                <?php for ($i = $_options['grades'][0]; $i < $_options['grades'][1]; $i++) : ?>
                                    <td><input class="form-control form-control-sm text-center" type="text" name="<?= "grade{$i}" ?>" value=<?= $student->{"not{$i}"} ?>></td>
                                <?php endfor ?>
                                <td><input class="form-control form-control-sm text-center" type="text" name="<?= "grade{$_options['grades'][1]}" ?>" value=<?= $student->{"not{$_options['grades'][1]}"} ?>></td>
                                <?php if ($_values !== null) : ?>
                                    <?php foreach ($_values as $name => $value) : ?>
                                        <td><input class="form-control-plaintext text-center" readonly type="text" name="<?= $name ?>" value=<?= $student->{$value} ?>></td>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <td><input class="form-control-plaintext text-center" readonly type="text" name="totalGrade" value=<?= $student->{$_options['totalGrade']} ?>></td>
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

            <?php if ($_options !== null) : ?>
                <?php if (Util::date() < $teacher->info($_dates[1]) && $teacher->fechas === 1 && ($teacher->tri === $_trimesterNumber || $teacher->tri === 5)) : ?>
                    <button class="btn btn-primary btn-lg d-block mx-auto my-3">Guardar</button>
                <?php else : ?>
                    <h4 class="text-center text-danger">Lo Sentimos, La fecha Ha Vencido o la Selección del trimestre es equivocada. Intentelo de Nuevo o Comuniquese con la Administración.</h4>
                <?php endif ?>
            <?php endif ?>
        </div>
        <!-- end Students list -->
        <h2 class="text-center text-info mb-0">*Recuerde ir a la pagina de notas y darle a grabar para tener los promédios correctos.*</h2>
        <div class="accordion mx-3 mb-5 mt-2" id="valuesAccordion">
            <div class="card">
                <div class="card-header bg-secondary" id="clubsHead">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left text-light font-weight-bold" type="button" data-toggle="collapse" data-target="#clubs" aria-expanded="true" aria-controls="clubs">
                            Valores
                        </button>
                    </h2>
                </div>
                <div id="clubs" class="collapse" aria-labelledby="clubsHead" data-parent="#valuesAccordion">
                    <div class="card-body">
                        <div class="form-row">
                            <?php $cant = ($_report === 'Ex-Final') ? 1 : 10 ?>
                            <?php for ($i = 1; $i <= $cant; $i++) : ?>
                                <div class="form-row col-12 mb-2">
                                    <div class="form-group col-8">
                                        <label for="<?= "tema$i" ?>">Tema</label>
                                        <input class="form-control" type="text" name="<?= "tema$i" ?>" id="<?= "tema$i" ?>" value="<?= $_value->{"tema{$i}"} ?>" />
                                    </div>
                                    <div class="form-group col-1 text-center">
                                        <label for="<?= "val$i" ?>">Valor</label>
                                        <input class="form-control text-center" type="text" name="<?= "val$i" ?>" id="<?= "val$i" ?>" value="<?= $_value->{"val{$i}"} ?>" />
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="<?= "fec$i" ?>">Fecha</label>
                                        <input class="form-control" type="date" name="<?= "fec$i" ?>" id="<?= "fec$i" ?>" value="<?= $_value->{"fec{$i}"} ?>" />
                                    </div>
                                </div>
                            <?php endfor ?>
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