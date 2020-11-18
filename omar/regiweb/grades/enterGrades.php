<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
Server::is_post();
$teacher = new Teacher(Session::id());

$_class = $_POST['class'];
$_trimester = $_POST['tri'];
$_report = $_POST['tra'];

$_dates = [
    'Trimestre-1' => ['ft1', 'ft2'],
    'Trimestre-2' => ['ft3', 'ft4'],
    'Trimestre-3' => ['ft5', 'ft6'],
    'Trimestre-4' => ['ft7', 'ft8']
];
$_dates = $_dates[$_trimester];


$students = new Student();
$students = $students->findByClass($_class);
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

        <div class="card border-secondary mt-2">
            <div class="card-body">
                <div class="row row-cols-1">
                    <div class="col">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1">
                            <label class="custom-control-label" for="customSwitch1">Pasar a letras</label>
                        </div>
                        <small>Está opción se aplica en la columna <b>Nota-9</b> exclusivamente.</small>
                    </div>
                    <div class="col mt-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch2">
                            <label class="custom-control-label" for="customSwitch2">Conversión</label>
                        </div>
                        <small>Está opción es para convertir de numero a letra.</small>
                    </div>
                    <div class="col mt-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="customSwitch3">
                            <label class="custom-control-label" for="customSwitch3">Aviso Terminar</label>
                        </div>
                        <small>Cuando termine el trimestre marque está Opción.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Students list -->
        <h3 class="text-center mt-2">Estudiantes</h3>
        <div class="table-responsive my-3 shadow">
            <table class="table table-sm table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col" style="width: 19rem;">Nombre del estudiante</th>
                        <?php for ($i = 1; $i <= 9; $i++) : ?>
                            <th scope="col"><?= "Nota {$i}" ?></th>
                        <?php endfor ?>
                        <th scope="col">Bono</th>
                        <th scope="col">T-Diario</th>
                        <th scope="col">T-Libreta</th>
                        <th scope="col">P-Cor</th>
                        <th scope="col">TPA</th>
                        <th scope="col">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student) : ?>
                        <tr>
                            <th scope="row"><?= $index + 1 ?></th>
                            <td><?= utf8_decode("$student->apellidos $student->nombre"); ?></td>
                            <?php for ($i = 1; $i <= 9; $i++) : ?>
                                <td><input class="form-control form-control-sm" type="text" name="<?= "grade{$i}" ?>"></td>
                            <?php endfor ?>
                            <td><input class="form-control form-control-sm" type="text" name="<?= "bonus" ?>"></td>
                            <td><input class="form-control-plaintext" readonly type="text" name="<?= "tdia" ?>"></td>
                            <td><input class="form-control-plaintext" readonly type="text" name="<?= "tlib" ?>"></td>
                            <td><input class="form-control-plaintext" readonly type="text" name="<?= "pcor" ?>"></td>
                            <td><input class="form-control-plaintext" readonly type="text" name="<?= "tpa" ?>"></td>
                            <td><input class="form-control-plaintext" readonly type="text" name="<?= "grade" ?>"></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>