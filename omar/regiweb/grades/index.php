<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\Lang;

Session::is_logged();
$teacher = new Teacher(Session::id());
$classes = $teacher->classes();


/* ------------------------------- Transaltion ------------------------------ */
$TRANS = [
    "es" => [
        "PAGE_TITLE" => 'Cursos',
        "OPTION1" => 'Entrada de asistencias',
        "OPTION2" => 'Informe de ausencias diaria',
        "OPTION3" => 'Informe de asistencias por curso',
        "OPTION4" => 'Preescolar',
    ],
    "en" => [
        "PAGE_TITLE" => 'Grades',
        "OPTION1" => 'Assists entry',
        "OPTION2" => 'Daily absence report',
        "OPTION3" => 'Attendance report by course',
        "OPTION4" => 'Preschool',
    ]
];

Lang::addTranslation($TRANS);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = Lang::translation('PAGE_TITLE');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center mb-3 mt-5"><?= Lang::translation('PAGE_TITLE') ?></h1>
        <div class="jumbotron bg-secondary shadow-sm py-3">
            <div class="row row-cols-1 row-cols-md-2">
                <div class="col mb-3">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= Lang::translation('OPTION1') ?></a>
                </div>
                <div class="col mb-3">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= Lang::translation('OPTION2') ?></a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= Lang::translation('OPTION3') ?></a>
                </div>
                <div class="col">
                    <a href="#" class="btn btn-outline-light btn-block btn-lg"><?= Lang::translation('OPTION4') ?></a>
                </div>
            </div>
        </div>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form action="<?= Route::url('/regiweb/grades/enterGrades.php') ?>" method="post">
                <div class="mx-auto" style="width: 20rem;">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="class"><?= Lang::$grade ?></label>
                        </div>
                        <select name="class" class="custom-select" id="class" required>
                            <option value="" selected><?= Lang::$select . "..." ?></option>
                            <?php foreach ($classes as $class) : ?>
                                <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tri"><?= Lang::$trimester ?></label>
                        </div>
                        <select name="tri" class="custom-select" id="tri" required>
                            <option value="" selected><?= Lang::$select . "..." ?></option>
                            <option value="Trimestre-1">Trimestre 1</option>
                            <option value="Trimestre-2">Trimestre 2</option>
                            <option value="Trimestre-3">Trimestre 3</option>
                            <option value="Trimestre-4">Trimestre 4</option>
                            <option value="Verano">Verano</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="tra"><?= Lang::plural(Lang::$page) ?></label>
                        </div>
                        <select name="tra" class="custom-select" id="tra" required>
                            <option value="" selected><?= Lang::$select . "..." ?></option>
                            <option value="Notas">Notas</option>
                            <option value="Pruebas-Cortas">Pruebas Cortas</option>
                            <option value="Trab-Diarios">Trabajos Diarios</option>
                            <?php if ($teacher->info('etd') === 'SI') : ?>
                                <option value="Trab-Diarios2">Trabajos Diarios 2</option>
                            <?php endif ?>
                            <option value="Trab-Libreta">Trabajos de Libreta</option>
                            <?php if ($teacher->info('etd') === 'SI') : ?>
                                <option value="Trab-Libreta2">Trabajos de Libreta 2</option>
                            <?php endif ?>
                            <option value="Cond-Asis">Conducta y Asistencia</option>
                            <option value="Ex-Final">Examen Final</option>
                            <option value="V-Nota">V. Nota</option>
                        </select>
                    </div>
                    <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= Lang::$continue ?>">
                </div>
            </form>

        </div>




    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>