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
            <form method="POST" action="<?= Route::url('/admin/access/includes/changeDates.php') ?>">
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
                                    <input type="date" class="form-control" name="quater1_start" id="quater1_start" value="<?= $school->info('ft1') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater1_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater1_end" id="quater1_end" value="<?= $school->info('ft2') ?>">
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
                                    <input type="date" class="form-control" name="quater2_start" id="quater2_start" value="<?= $school->info('ft3') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater2_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater2_end" id="quater2_end" value="<?= $school->info('ft4') ?>">
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
                                    <input type="date" class="form-control" name="quater3_start" id="quater3_start" value="<?= $school->info('ft5') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater3_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater3_end" id="quater3_end" value="<?= $school->info('ft6') ?>">
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
                                    <input type="date" class="form-control" name="quater4_start" id="quater4_start" value="<?= $school->info('ft7') ?>">
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="quater4_end"><?= $lang->translation('Cierre') ?></label>
                                    </div>
                                    <input type="date" class="form-control" name="quater4_end" id="quater4_end" value="<?= $school->info('ft8') ?>">
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
        <?php if (Session::get('changeDates')) : ?>
            <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                <i class="fa-solid fa-square-check"></i> <?= Session::get('changeDates', true) ?>
            </div>
        <?php endif ?>


        <div class="container mt-5">
            <div class="table_wrap">
                <table class="dataTable table table-sm table-pointer table-striped table-hover cell-border shadow">
                    <thead class="bg-gradient-primary bg-primary border-0">
                        <tr>
                            <th style="width: 1px;"></th>
                            <th><?= $lang->translation("Apellidos") ?></th>
                            <th><?= $lang->translation("Nombre") ?></th>
                            <th><?= $lang->translation("Trimestre") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher) : ?>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input check" type="checkbox" data-id="<?= $teacher->id ?>" <?= ($teacher->fechas) ? 'checked=""' : '' ?> value="<?= $teacher->id ?>">
                                        <label class="custom-control-label" for="<?= $teacher->id ?>"></label>
                                    </div>
                                </td>
                                <td><?= $teacher->nombre ?></td>
                                <td><?= $teacher->apellidos ?></td>
                                <td>
                                    <select data-id="<?= $teacher->id ?>" class="form-control tri">
                                        <option <?= ($teacher->tri == '') ? 'selected=""' : '' ?> value=""></option>
                                        <option <?= ($teacher->tri == '1') ? 'selected=""' : '' ?> value="1"><?= $lang->translation("Trimestre") ?> 1</option>
                                        <option <?= ($teacher->tri == '2') ? 'selected=""' : '' ?> value="2"><?= $lang->translation("Trimestre") ?> 2</option>
                                        <option <?= ($teacher->tri == '3') ? 'selected=""' : '' ?> value="3"><?= $lang->translation("Trimestre") ?> 3</option>
                                        <option <?= ($teacher->tri == '4') ? 'selected=""' : '' ?> value="4"><?= $lang->translation("Trimestre") ?> 4</option>
                                        <option <?= ($teacher->tri == '5') ? 'selected=""' : '' ?> value="5"><?= $lang->translation("Todos") ?></option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".custom-control-input").on('change', function() {
                const id = $(this).data('id')
                const value = $(this).prop('checked')
                console.log(id, value);

                $.post(includeThisFile(), {
                    check: id,
                    value,
                }, function(data, textStatus, xhr) {
                    console.log('data:', data);
                });
            });

            $('.form-control.tri').on('click', function(e) {
                e.preventDefault()
                e.stopPropagation()
            });
            $('.form-control.tri').on('change', function(e) {
                const select = $(this)
                const id = select.data('id')
                const value = select.val()
                console.log(id, value);

                $.post(includeThisFile(), {
                    changeTrimester: id,
                    value,
                }, function(data, textStatus, xhr) {
                    // animateCSs(select, 'pulse')

                });
            });

        });
    </script>

</body>

</html>