<?php
require_once '../../app.php';

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
]);
$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School();
$teachers = new Teacher();
$teachers = $teachers->all();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

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
                                    <input type="date" class="form-control" name="quater1_start" id="quater1_start" value="<?= $school->info('asis1') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater1_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater1_end" id="quater1_end" value="<?= $school->info('asis2') ?>">
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
                                    <input type="date" class="form-control" name="quater2_start" id="quater2_start" value="<?= $school->info('asis3') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater2_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater2_end" id="quater2_end" value="<?= $school->info('asis4') ?>">
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
                                    <input type="date" class="form-control" name="quater3_start" id="quater3_start" value="<?= $school->info('asis5') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater3_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater3_end" id="quater3_end" value="<?= $school->info('asis6') ?>">
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
                                    <input type="date" class="form-control" name="quater4_start" id="quater4_start" value="<?= $school->info('asis7') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater4_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater4_end" id="quater4_end" value="<?= $school->info('asis8') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 border p-2 mt-2">
                        <div class="row">
                            <div class="col-12 col-lg-2">
                                <p><?= $lang->translation('Verano') ?></p>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="summer_start"><?= $lang->translation('Inicio') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="summer_start" id="summer_start" value="<?= $school->info('fechav1') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="summer_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="summer_end" id="summer_end" value="<?= $school->info('fechav2') ?>">
                                </div>
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