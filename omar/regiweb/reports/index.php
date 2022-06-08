<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\Lang;

Session::is_logged();
$teacher = new Teacher(Session::id());
$classes = $teacher->classes();
$lang = new Lang([
    ["Informes","Reports"],
    ["Seleccionar informe","Select report"],
    ["Curso","Class"],
    ["Verano","Summer"],
    ["Informe","Report"],
    ["Ver informe","View report"],
    ["Semestre","Semester"],
    ["Finales","Final"],
    ["Semestre porciento","Semester porcent"],
    ["Notas en porciento","Grades in percent"],
    ["Notas en punto decimal","Decimal point grades"]
]);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Informes");
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center my-5"><?= $lang->translation("Seleccionar informe") ?></h1>
        <form action="<?= Route::url('/regiweb/reports/report.php') ?>" method="post" target="_blank">
            <div class="mx-auto" style="width: 20rem;">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="class"><?= $lang->translation("Curso") ?></label>
                    </div>
                    <select name="class" class="custom-select" id="class" required>
                        <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tri"><?= $lang->translation("Trimestre") ?></label>
                    </div>
                    <select name="tri" class="custom-select" id="tri" required>
                        <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                        <option value="Trimestre-1"><?= $lang->translation("Trimestre") ?> 1</option>
                        <option value="Trimestre-2"><?= $lang->translation("Trimestre") ?> 2</option>
                        <option value="Trimestre-3"><?= $lang->translation("Trimestre") ?> 3</option>
                        <option value="Trimestre-4"><?= $lang->translation("Trimestre") ?> 4</option>
                        <option value="Verano"><?= $lang->translation("Verano") ?></option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tra"><?= $lang->translation("Informe") ?></label>
                    </div>
                    <select name="tra" class="custom-select" id="tra" required>
                        <option value="" selected><?= $lang->translation("Seleccionar") ?></option>
                        <option value="Notas"><?= $lang->translation("Notas") ?></option>
                        <option value="Notas-2"><?= $lang->translation("Notas") ?> 2</option>
                        <option value="Trab-Diarios"><?= $lang->translation("Trabajos diarios") ?></option>
                        <option value="Trab-Libreta"><?= $lang->translation("Trabajos de libreta") ?></option>
                        <option value="Pruebas-Cortas"><?= $lang->translation("Pruebas cortas") ?></option>
                        <option value="Semestre-1"><?= $lang->translation("Semestre") ?> 1</option>
                        <option value="Semestre-2"><?= $lang->translation("Semestre") ?> 2</option>
                        <option value="V-Nota"><?= $lang->translation("Notas de verano") ?></option>
                        <option value="Finales"><?= $lang->translation("Finales") ?></option>
                        <option value="Sem-Por-1"><?= $lang->translation("Semestre porciento") ?></option>
                        <!-- <option value="Sem-Por-2">Semestre por 2</option> NO EXISTE-->
                        <option value="Notas-Porciento"><?= $lang->translation("Notas en porciento") ?></option>
                        <option value="Notas-P-Decimal"><?= $lang->translation("Notas en punto decimal") ?></option>
                        <!-- <option value="Inf. Academico">Informe academico</option> NO EXISTE -->
                        <!-- <option value="Informe acumulativo de notas">Informe acumulativo de notas</option> NO EXISTE -->
                    </select>
                </div>
                <input class="btn btn-primary mx-auto d-block" type="submit" value="<?= $lang->translation("Ver informe") ?>">
            </div>
        </form>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>