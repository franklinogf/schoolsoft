<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Listado por descripción mensual', 'List by monthly description'],
    ['Código', 'Code'],
    ['Selección', 'Select'],
    ['Todos', 'All'],
    ['Mes', 'Month'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'April'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Orden', 'Order'],
    ['Apellidos', 'Last names'],
    ['Cuenta', 'Account'],
    ['Continuar', 'Continue'],
]);
$school = new School(Session::id());

$presupuestos = DB::table('presupuesto')->where('year', $school->info('year2'))->orderBy('codigo')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Listado por descripción mensual');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Listado por descripción mensual') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form method="POST" action="pdf/inf_desc_in2.php" target="_blank">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="codi"><?= $lang->translation('Código') ?></label>
                                <select class="form-control selectpicker w-100" name="codi" id="codi" data-live-search="true" required>
                                    <option value="0"><?= $lang->translation('Selección') ?></option>
                                    <option value="T"><?= $lang->translation('Todos') ?></option>
                                    <?php foreach ($presupuestos as $row3) : ?>
                                        <?php $v = array_values((array) $row3); ?>
                                        <option value="<?= $v[0] ?>"><?= $v[0] . ', ' . $v[1] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="mes"><?= $lang->translation('Mes') ?></label>
                                <select class="form-control" name="mes" id="mes" required>
                                    <option value="0"><?= $lang->translation('Mes') ?></option>
                                    <option value="01"><?= $lang->translation('Enero') ?></option>
                                    <option value="02"><?= $lang->translation('Febrero') ?></option>
                                    <option value="03"><?= $lang->translation('Marzo') ?></option>
                                    <option value="04"><?= $lang->translation('Abril') ?></option>
                                    <option value="05"><?= $lang->translation('Mayo') ?></option>
                                    <option value="06"><?= $lang->translation('Junio') ?></option>
                                    <option value="07"><?= $lang->translation('Julio') ?></option>
                                    <option value="08"><?= $lang->translation('Agosto') ?></option>
                                    <option value="09"><?= $lang->translation('Septiembre') ?></option>
                                    <option value="10"><?= $lang->translation('Octubre') ?></option>
                                    <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                    <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="orden"><?= $lang->translation('Orden') ?></label>
                                <select class="form-control" name="orden" id="orden" required>
                                    <option value=""><?= $lang->translation('Orden') ?></option>
                                    <option value="apellidos"><?= $lang->translation('Apellidos') ?></option>
                                    <option value="id"><?= $lang->translation('Cuenta') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="Submit1"><?= $lang->translation('Continuar') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
</body>

</html>