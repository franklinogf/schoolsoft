<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();
$lang = new Lang([
    ['Ver todas las transaciones', 'See all transactions'],
    ['Activar o inactivar las cuentas de los padres del año', 'Activate or inactivate the accounts of the parents of the year'],
    ['Descripción', 'Descrption'],
    ['Fecha', 'Date'],
    ['Buscar', 'Search'],
    ['Selección', 'Selection'],
    ['Cerrar', 'Close'],
    ['Pagos', 'Pays'],
    ['Marcar para inactivar', 'Check to inactivate'],
    ['Mensaje para los inactivos', 'Message for inactive'],
    ['Cargo', 'Debts'],
    ['Control del bloqueo', 'Block control'],
    ['Código', 'Code'],
    ['Marcar para borrar', 'Mark to delete'],
    ['Fecha posteo', 'Post date'],
    ['Fecha pago', 'Pay date'],
    ['Grado', 'Grade'],
    ['Si', 'Yes'],
    ['Borrar', 'Delete'],


]);
$school = new School(Session::id());
$year = $school->info('year2');
$students = new Student();
$students = $students->all();
if (isset($_POST['pro']) and !empty($_POST['students'])) {
    $estudiantesSS = $_POST['students'];
    foreach ($estudiantesSS as $ss) {
        DB::table('pagos')->where('mt', $ss)->delete();
    }
}
$ss = $_POST['nombre'] ?? '';
$budgets = DB::table('pagos')->where([
    ['ss', $ss],
    ['year', $year]
])->orderBy('codigo, fecha_d')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Ver todas las transaciones');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::fontawasome();
    ?>
    <style type="text/css">
        .style1 {
            font-size: medium;
        }

        .dataTable_wrap per.row:first-child {
            display: none;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Ver todas las transaciones') . ' ' . $school->year() ?> </h1>
        <div class="container mt-5">
            <form name="ver" action="" method="post">

                <select name="nombre" style="width: 335px">
                    <option value="Selección"><?= $lang->translation('Selección') ?></option>
                    <?php foreach ($students as $student): ?>
                        <?php
                        $nom = '';
                        if ($student->ss == $_POST['nombre']) {
                            $nom = 'selected=""';
                        }
                        ?>
                        <option <?= $nom ?> value='<?= $student->ss ?>'>
                            <?= $student->apellidos . ' ' . $student->nombre . ' ' . $student->id ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <input class="btn btn-primary" style="width: 140px;" type="submit" value="<?= $lang->translation('Buscar') ?>" />
            </form>
            <div class="table_wrap">
                <small class="text-muted d-block"><b><span class="style1"><?= $lang->translation("Marcar para borrar") ?></span></b></small>
                <form id="form" action="" target="teacherID" method="POST">
                    <table class="dataTable table table-sm table-pointer table-striped table-hover cell-border shadow" style=" width: 1000px;">
                        <thead class="bg-gradient-primary bg-primary border-0">
                            <tr class="checkbox">
                                <th style=" width: 1px;">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input bg-success checkAll" type="checkbox" id="check1">
                                        <label class="custom-control-label" for="check1"></label>
                                    </div>
                                </th>
                                <th><?= $lang->translation("Código") ?></th>
                                <th><?= $lang->translation("Grado") ?></th>
                                <th><?= $lang->translation("Descripción") ?></th>
                                <th style=" width: 40px;"><?= $lang->translation("Cargo") ?></th>
                                <th><?= $lang->translation("Fecha posteo") ?></th>
                                <th><?= $lang->translation("Pagos") ?></th>
                                <th><?= $lang->translation("Fecha pago") ?></th>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($budgets as $budget) :
                            ?>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input id="<?= $budget->mt ?>" class="custom-control-input check" type="checkbox" data-id="<?= $budget->mt ?>" value="<?= $budget->mt ?>">
                                            <label class="custom-control-label" for="<?= $budget->mt ?>"></label>
                                        </div>
                                    </td>
                                    <td><?= $budget->codigo ?></td>
                                    <td><?= $budget->grado ?></td>
                                    <td><?= $budget->desc1 ?></td>
                                    <td style=" width: 40px;" align="right"><?= number_format($budget->deuda, 2) ?></td>
                                    <td><?= $budget->fecha_d ?></td>
                                    <td style=" width: 40px;" align="right"><?= number_format($budget->pago, 2) ?></td>
                                    <td><?= $budget->fecha_p ?></td>
                                    <td><?= $budget->id ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    <? echo "<input type=hidden name=nombre value='" . $_POST['nombre'] . "'/>"; ?>

                    <button type="submit" name="pro" class="btn btn-block btn-primary"><?= $lang->translation("Borrar") ?></button>
            </div>
            </form>
        </div>
    </div>

    <?php
    $DataTable = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

    <script>
        $(document).ready(function() {
            $("#form").submit(function(e) {
                tableDataToSubmit("#form", dataTable[0], 'students[]')
            });
        });
    </script>

</body>

</html>