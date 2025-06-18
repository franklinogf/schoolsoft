<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Pagos diarios', 'Daily payments'],
    ['Fecha desde:', 'Date from:'],
    ['Fecha hasta:', 'Date until:'],
    ['Atrás', 'Go back'],
    ['Todos', 'All'],
    ['Efectivo', 'Cash'],
    ['Tarjeta de Crédito', 'Credit card'],
    ['Cheque', 'Checks'],
    ['Giro', 'Money order'],
    ['ATH', 'ATH'],
    ['Nomina', 'Payroll'],
    ['Paypal', 'Paypal'],
    ['Banco', 'Bank'],
    ['Pago Directo', 'Direct payment'],
    ['Tele. Pago', 'Phone. Pay'],
    ['Beca', 'Scholarship'],
    ['ATH Movil', 'ATH Movil'],
    ['Detallado', 'Detailed'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Selección', 'Selection'],
    ['Bash', 'Bash'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
    ['Resumen fecha', 'Summary date'],
    ['Resumen código', 'Code summary'],
    ['Caja', 'Cash register'],
    ['', ''],
    ['', ''],
    ['', ''],
]);
$school = new School(Session::id());
$year = $school->info('year2');

$grades = $school->allGrades();

$re = $school->info('tar');
$in1 = '';
$in2 = '';
$in3 = '';
$in4 = '';
$in5 = '';
$in6 = '';
$in7 = '';
$in8 = '';
$in9 = '';
$in10 = '';
$in11 = '';
$in12 = '';
$in13 = '';
$in14 = '';
$in15 = '';
$in16 = '';
$in17 = '';
$in18 = '';
$in19 = '';
$in20 = '';
if ($re == '1') {
    $in1 = 'selected';
}
if ($re == '2') {
    $in2 = 'selected';
}
if ($re == '3') {
    $in3 = 'selected';
}

$mensaj = DB::table('codigos')->orderBy('codigo')->get();

$presupuesto = DB::table('presupuesto')->where([
    ['year', $year]
])->orderBy('codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script language="JavaScript">
    function activarTrimestre() {
        var dis = document.TarjetaNotas.tarjeta.value;
        if (dis == '2') {
            document.TarjetaNotas.tri.disabled = false;
        } else {
            document.TarjetaNotas.tri.disabled = true;
        }

    }
</script>

<head>
    <?php
    $title = $lang->translation('Pagos diarios');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Pagos diarios') ?>
        </h1>
        <a href="<?= Route::url('/admin/billing/') ?>" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></a>
        <div class="container bg-white shadow-lg py-3 rounded">
            <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/billing/pdf/pagos_diario_inf.php') ?>">
                <div class="mx-auto" style="max-width: 550px;">
                    <?php if (Session::get('createGrades')) : ?>
                        <div class="alert alert-primary col-6 mx-auto mt-1" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Fecha desde:') ?>
                            </label>
                        </div>
                        <input type="date" class="form-control" name="ft1" id="ft1" value="<?= date("Y-m-d") ?>">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Fecha hasta:') ?>
                            </label>
                        </div>
                        <input type="date" class="form-control" name="ft2" id="ft2" value="<?= date("Y-m-d") ?>">

                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="grade">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        </div>
                        <select id="codigo" name="codigo" class="form-control">
                            <option value='Todos'><?= $lang->translation('Todos') ?></option>
                            <?php foreach ($presupuesto as $pres) { ?>
                                <option value='<?= $pres->codigo ?>'>
                                    <?= $pres->descripcion ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Efectivo') ?>
                            </label>
                        </div>
                        <input id="efe" name="efe" type="checkbox" style="height: 30px; width: 30px" value="1" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Tarjeta de Crédito') ?>
                            </label>
                        </div>
                        <input id="tar" name="tar" type="checkbox" style="height: 30px; width: 30px" value="4" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Cheque') ?>
                            </label>
                        </div>
                        <input id="che" name="che" type="checkbox" style="height: 30px; width: 30px" value="2" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Giro') ?>
                            </label>
                        </div>
                        <input id="gir" name="gir" type="checkbox" style="height: 30px; width: 30px" value="5" checked="checked" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('ATH') ?>
                            </label>
                        </div>
                        <input id="ath" name="ath" type="checkbox" style="height: 30px; width: 30px" value="3" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Nomina') ?>
                            </label>
                        </div>
                        <input id="nom" name="nom" type="checkbox" style="height: 30px; width: 30px" value="6" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Paypal') ?>
                            </label>
                        </div>
                        <input id="pay" name="pay" type="checkbox" style="height: 30px; width: 30px" value="10" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Beca') ?>
                            </label>
                        </div>
                        <input id="bac" name="bac" type="checkbox" style="height: 30px; width: 30px" value="11" checked="checked" />
                    </div>


                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Banco') ?>
                            </label>
                        </div>
                        <input id="ban" name="ban" type="checkbox" style="height: 30px; width: 30px" value="7" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Tele. Pago') ?>
                            </label>
                        </div>
                        <input id="telp" name="telp" type="checkbox" style="height: 30px; width: 30px" value="9" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('Pago Directo') ?>
                            </label>
                        </div>
                        <input id="pdir" name="pdir" type="checkbox" style="height: 30px; width: 30px" value="8" checked="checked" />
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="option">
                                <?= $lang->translation('ATH Movil') ?>
                            </label>
                        </div>
                        <input id="athm" name="athm" type="checkbox" style="height: 30px; width: 30px" value="12" checked="checked" />
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Bash') ?>
                            </label>
                        </div>
                        <select id="bash" name="bash" class="form-control" style="width: 30px">
                            <option value='0'>0</option>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                            <option value='5'>5</option>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="mensaje">
                                <?= $lang->translation('Caja') ?>
                            </label>
                        </div>
                        <select id="caja" name="caja" class="form-control" style="width: 30px">
                            <option value='0'>0</option>
                            <option value='1'>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                            <option value='5'>5</option>
                        </select>
                        <div class="input-group-prepend">
                            <label class="input-group-text" for="Comentario">
                                <?= $lang->translation('Selección') ?>
                            </label>
                        </div>
                        <select id="pagos" name="pagos" class="form-control" style="width: 301px">
                            <option value='A'><?= $lang->translation('Detallado') ?></option>
                            <option value='B'><?= $lang->translation('Resumen fecha') ?></option>
                            <option value='C'><?= $lang->translation('Resumen código') ?></option>
                        </select>
                    </div>







                    <button name='create' type="submit" class="btn btn-primary d-block mx-auto">
                        <?= $lang->translation('Continuar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>
<script language="JavaScript">
    var dis = document.TarjetaNotas.tarjeta.value;
    if (dis == '2') {
        document.TarjetaNotas.tri.disabled = false;
    } else {
        document.TarjetaNotas.tri.disabled = true;
    }
</script>

</html>