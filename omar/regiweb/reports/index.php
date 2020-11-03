<?php
require_once '../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$classes = $teacher->classes();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = "Informes";
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/regiweb/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 px-0">
        <h1 class="text-center my-5">Seleccionar informe</h1>
        <form action="<?= Route::url('/regiweb/reports/report.php') ?>" method="post" target="_blank">
            <div class="mx-auto" style="width: 20rem;">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="class">Curso</label>
                    </div>
                    <select name="class" class="custom-select" id="class" required>
                        <option value="" selected>Seleccione...</option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= $class->curso ?>"><?= "$class->curso - $class->desc1" ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tri">Trimestre</label>
                    </div>
                    <select name="tri" class="custom-select" id="tri" required>
                        <option value="" selected>Seleccione...</option>
                        <option value="Trimestre-1">Trimestre 1</option>
                        <option value="Trimestre-2">Trimestre 2</option>
                        <option value="Trimestre-3">Trimestre 3</option>
                        <option value="Trimestre-4">Trimestre 4</option>
                        <option value="Verano">Verano</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="tra">Informe</label>
                    </div>
                    <select name="tra" class="custom-select" id="tra" required>
                        <option value="" selected>Seleccione...</option>
                        <option value="Notas">Notas</option>
                        <option value="Notas-2">Notas 2</option>
                        <option value="Trab-Diarios">Trabajos Diarios</option>
                        <option value="Trab-Libreta">Trabajos de Libreta</option>
                        <option value="Pruebas-Cortas">Pruebas Cortas</option>
                        <option value="Semestre-1">Semestre 1</option>
                        <option value="Semestre-2">Semestre 2</option>
                        <option value="V-Nota">V. Nota</option>
                        <option value="Finales">Finales</option>
                        <option value="Sem-Por-1">Semestre por 1</option>
                        <!-- <option value="Sem-Por-2">Semestre por 2</option> NO EXISTE-->
                        <option value="Notas-Porciento">Notas en porciento</option>
                        <option value="Notas-P-Decimal">Notas en punto decimal</option>
                        <!-- <option value="Inf. Academico">Informe academico</option> NO EXISTE -->
                        <!-- <option value="Informe acumulativo de notas">Informe acumulativo de notas</option> NO EXISTE -->
                    </select>
                </div>
                <input class="btn btn-primary mx-auto d-block" type="submit" value="Ver informe">
            </div>
        </form>

    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>