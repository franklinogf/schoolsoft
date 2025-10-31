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
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-center"><?= $lang->translation('Pagos diarios') ?></h5>
            </div>
            <div class="card-body">
                <form id="TarjetaNotas" name="TarjetaNotas" method="POST" target="_blank" action="<?= Route::url('/admin/billing/pdf/pagos_diario_inf.php') ?>">
                    <?php if (Session::get('createGrades')) : ?>
                        <div class="alert alert-primary" role="alert">
                            <i class="fa-solid fa-square-check"></i>
                            <?= Session::get('gradesReports', true) ?>
                        </div>
                    <?php endif ?>

                    <!-- Date Range Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ft1" class="form-label font-weight-bold"><?= $lang->translation('Fecha desde:') ?></label>
                                <input type="date" class="form-control" name="ft1" id="ft1" value="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ft2" class="form-label font-weight-bold"><?= $lang->translation('Fecha hasta:') ?></label>
                                <input type="date" class="form-control" name="ft2" id="ft2" value="<?= date("Y-m-d") ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Selection Section -->
                    <div class="form-group mb-4">
                        <label for="codigo" class="form-label font-weight-bold"><?= $lang->translation('Selección') ?></label>
                        <select id="codigo" name="codigo" class="form-control">
                            <option value='Todos'><?= $lang->translation('Todos') ?></option>
                            <?php foreach ($presupuesto as $pres) { ?>
                                <option value='<?= $pres->codigo ?>'>
                                    <?= $pres->descripcion ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div> <!-- Payment Methods Section -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-3"><?= $lang->translation('Métodos de Pago') ?></h6>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="efe" name="efe" value="1" checked="checked">
                                    <label class="custom-control-label" for="efe"><?= $lang->translation('Efectivo') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="tar" name="tar" value="4" checked="checked">
                                    <label class="custom-control-label" for="tar"><?= $lang->translation('Tarjeta de Crédito') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="che" name="che" value="2" checked="checked">
                                    <label class="custom-control-label" for="che"><?= $lang->translation('Cheque') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="gir" name="gir" value="5" checked="checked">
                                    <label class="custom-control-label" for="gir"><?= $lang->translation('Giro') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ath" name="ath" value="3" checked="checked">
                                    <label class="custom-control-label" for="ath"><?= $lang->translation('ATH') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="nom" name="nom" value="6" checked="checked">
                                    <label class="custom-control-label" for="nom"><?= $lang->translation('Nomina') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="pay" name="pay" value="10" checked="checked">
                                    <label class="custom-control-label" for="pay"><?= $lang->translation('Paypal') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="bac" name="bac" value="11" checked="checked">
                                    <label class="custom-control-label" for="bac"><?= $lang->translation('Beca') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ban" name="ban" value="7" checked="checked">
                                    <label class="custom-control-label" for="ban"><?= $lang->translation('Banco') ?></label>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="telp" name="telp" value="9" checked="checked">
                                    <label class="custom-control-label" for="telp"><?= $lang->translation('Tele. Pago') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="pdir" name="pdir" value="8" checked="checked">
                                    <label class="custom-control-label" for="pdir"><?= $lang->translation('Pago Directo') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="athm" name="athm" value="12" checked="checked">
                                    <label class="custom-control-label" for="athm"><?= $lang->translation('ATH Movil') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cac" name="cac" value="13" checked="checked">
                                    <label class="custom-control-label" for="cac"><?= $lang->translation('C. a Cuenta') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="vt1" name="vt1" value="14" checked="checked">
                                    <label class="custom-control-label" for="vt1"><?= $lang->translation('V. Term.') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ac" name="ac" value="15" checked="checked">
                                    <label class="custom-control-label" for="ac"><?= $lang->translation('Acuden-C') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="av" name="av" value="16" checked="checked">
                                    <label class="custom-control-label" for="av"><?= $lang->translation('Acuden-V') ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="va" name="va" value="17" checked="checked">
                                    <label class="custom-control-label" for="va"><?= $lang->translation('VA Prog') ?></label>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Options Section -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bash" class="form-label font-weight-bold"><?= $lang->translation('Bash') ?></label>
                                <select id="bash" name="bash" class="form-control">
                                    <option value='0'>0</option>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='4'>4</option>
                                    <option value='5'>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="caja" class="form-label font-weight-bold"><?= $lang->translation('Caja') ?></label>
                                <select id="caja" name="caja" class="form-control">
                                    <option value='0'>0</option>
                                    <option value='1'>1</option>
                                    <option value='2'>2</option>
                                    <option value='3'>3</option>
                                    <option value='4'>4</option>
                                    <option value='5'>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pagos" class="form-label font-weight-bold"><?= $lang->translation('Tipo de Reporte') ?></label>
                                <select id="pagos" name="pagos" class="form-control">
                                    <option value='A'><?= $lang->translation('Detallado') ?></option>
                                    <option value='B'><?= $lang->translation('Resumen fecha') ?></option>
                                    <option value='C'><?= $lang->translation('Resumen código') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button name='create' type="submit" class="btn btn-primary btn-lg px-5">
                            <?= $lang->translation('Continuar') ?>
                        </button>
                    </div>
                </form>
            </div>
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