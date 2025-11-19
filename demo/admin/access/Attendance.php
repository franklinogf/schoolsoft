<?php
require_once __DIR__ . '/../../app.php';

use Classes\Controllers\Teacher;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Cambio de fechas', 'Change dates'],
    ['Fechas para los cierres de los cuatrimestres', 'Dates for the closures of the four-month periods'],
    ['Primer semestre', 'First semester'],
    ['Segundo semestre', 'Second semester'],
    ['Cuatrimestre', 'Quarter'],
    ['Verano', 'Summer'],
    ['Inicio', 'Start'],
    ['Cierre', 'End'],
    ['Apellidos', 'Last name'],
    ['Nombre', 'Name'],
    ['Todos', 'All'],
    ['Guardar', 'Save'],
    ['Salón Hogar', 'Home Room'],
    ['Aplicar por asistencia', 'Apply for assistance'],
    ['Grados', 'Degrees'],
    ['Materias', 'Subjects'],
    ['Asistencia diaria por', 'Daily assistance by'],
    ['', ''],
    ['', ''],
]);
$school = new School();

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Cambio de fechas');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Fechas para los cierres de los cuatrimestres') ?></h1>
        <div class="container bg-white shadow-lg p-3 rounded">
            <form method="POST" action="<?= Route::url('/admin/access/includes/Attendance.php') ?>">
                <div class="row">
                    <div class="col-6 mb-2">
                        <h3 class="text-center"><?= $lang->translation("Primer semestre") ?></h3>
                    </div>
                    <div class="col-6 mb-2">
                        <h3 class="text-center"><?= $lang->translation("Segundo semestre") ?></h3>
                    </div>
                    <div class="col-6 border p-2">
                        <div class="row">
                            <div class="col-12">
                                <p><?= $lang->translation('Cuatrimestre') . ' 1' ?></p>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater1_start"><?= $lang->translation('Inicio') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater1_start" id="quater1_start" value="<?= $colegio->asis1 ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater1_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater1_end" id="quater1_end" value="<?= $colegio->asis2 ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <p><?= $lang->translation('Cuatrimestre') . ' 2' ?></p>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater2_start"><?= $lang->translation('Inicio') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater2_start" id="quater2_start" value="<?= $colegio->asis3 ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater2_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater2_end" id="quater2_end" value="<?= $colegio->asis4 ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 border p-2">
                        <div class="row">
                            <div class="col-12">
                                <p><?= $lang->translation('Cuatrimestre') . ' 3' ?></p>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater3_start"><?= $lang->translation('Inicio') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater3_start" id="quater3_start" value="<?= $colegio->asis5 ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater3_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater3_end" id="quater3_end" value="<?= $colegio->asis6 ?>">
                                </div>
                            </div>
                            <div class="col-12">
                                <p><?= $lang->translation('Cuatrimestre') . ' 4' ?></p>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater4_start"><?= $lang->translation('Inicio') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater4_start" id="quater4_start" value="<?= $colegio->asis7 ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater4_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater4_end" id="quater4_end" value="<?= $colegio->asis8 ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 border p-2 mt-2">
                        <div class="row">
                            <div class="col-12 col-lg-2">
                                <p><?= $lang->translation('Asistencia diaria por') ?></p>
                            </div>
                            <select class="form-control" name="asist" id="asist" required style="width: 140px">
                                <option <?= $colegio->asist == '1' ? 'selected=""' : '' ?> value="1"><?= $lang->translation("Salón Hogar") ?></option>
                                <option <?= $colegio->asist == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("Grados") ?></option>
                                <option <?= $colegio->asist == '3' ? 'selected=""' : '' ?> value="3"><?= $lang->translation("Materias") ?></option>
                            </select><span lang="en-us">&nbsp;&nbsp;&nbsp;
                            </span>&nbsp;<div class="col-12 col-lg-2">
                                <p><?= $lang->translation('Aplicar por asistencia') ?></p>
                            </div>
                            <select class="form-control" name="asis" id="asis" required style="width: 140px">
                                <option <?= $colegio->asis == 'A' ? 'selected=""' : '' ?> value="A"><?= utf8_encode($lang->translation("Diaria")) ?></option>
                                <option <?= $colegio->asis == 'B' ? 'selected=""' : '' ?> value="B"><?= $lang->translation("Trimestral") ?></option>
                            </select>
                            <div class="col-12 col-lg-5">
                            </div>
                            <div class="col-12 col-lg-5">
                            </div>
                        </div>
                    </div>
                </div>


                <input name="save" class="btn btn-primary mx-auto d-block mt-2" type="submit" value="<?= $lang->translation("Continuar") ?>">

            </form>
        </div>

    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>