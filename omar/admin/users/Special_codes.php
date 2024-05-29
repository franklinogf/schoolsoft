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
    ['Pantalla de Códigos Especiales', 'Special Codes Screen'],
    ['Recibo', 'Receipt'],
    ['Descripción', 'Description'],
    ['Estudiantes Nuevos:', 'New Students:'],
    ['Código', 'Code'],
    ['Costo', 'Cost'],
    ['Mes', 'Month'],
    ['Manual', 'Manually'],
    ['Automático', 'Automatic'],
    ['Recargo mensual', 'Monthly surcharge'],
    ['Día Vence', 'Due Day'],
    ['Código Cargo de CHK devuelto', 'CHK Charge Code Returned'],
    ['Cantidad', 'Amount'],
    ['Cantidad minima deposito:', 'Minimum deposit amount:'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);
//$years = DB::table('year')->select("DISTINCT year")->get();
$school = new School(Session::id());


//$teachers = new Teacher;
//$teachers = $teachers->all();
if (isset($_REQUEST['save'])) {
//    $teacher = new Teacher($_POST['teacherId']);
    $thisCourse = DB::table('colegio')->where('usuario', 'administrador')->update([
        'can_min' => $_POST['can_min'],
        'codc1' => $_POST['cdc1'],
        'codc2' => $_POST['cdc2'],
        'dia_vence' => $_POST['dv'],
        'esn' => $_POST['esn'],
        'esncodigo' => $_POST['esncodigo'],
        'esnmes' => $_POST['esnmes'],
        'rec' => $_POST['rec'],
        'chk' => $_POST['chk'],
    ]);
}

$thisCourse = DB::table('colegio')->where('usuario', 'administrador')->first();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Pantalla de Códigos Especiales');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Pantalla de Códigos Especiales') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-0 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" method="POST">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="credito"><?= $lang->translation("Estudiantes Nuevos:") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="credito"><?= $lang->translation("Código") ?></label>
                                <input type="text" value='<?= $thisCourse->esncodigo ?>' class="form-control" name='esncodigo' id="esncodigo" style="width: 90px">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Costo") ?></label>
                                <input type="text" value='<?= $thisCourse->esn ?>' class="form-control --float" name='esn' id="esn" style="width: 90px">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Mes") ?></label>
                                <input type="text" value='<?= $thisCourse->esnmes ?>' class="form-control" name='esnmes' id="esnmes" style="width: 90px">
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-4">
                                <label for="ava"><?= $lang->translation("Recibo") ?></label>
                                <select class="form-control" name="rec" id="rec" required>
                                    <option <?= $thisCourse->rec == '1' ? 'selected=""' : '' ?> value="1"><?= $lang->translation("Automático") ?></option>
                                    <option <?= $thisCourse->rec == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("Manual") ?></option>
                                </select>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Recargo mensual") ?></label>
                                <input type="text" value='<?= $thisCourse->chk ?>' class="form-control" name='chk' id="chk" style="width: 90px">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Día Vence") ?></label>
                                <input type="text" value='<?= $thisCourse->dia_vence ?>' class="form-control" name='dv' id="dv" style="width: 90px">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Código Cargo de CHK devuelto") ?></label>
                                <input type="text" value='<?= $thisCourse->codc1 ?>' class="form-control" name='cdc1' id="cdc1" style="width: 90px">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="peso"><?= $lang->translation("Cantidad") ?></label>
                                <input type="text" value='<?= $thisCourse->codc2 ?>' class="form-control --float" name='cdc2' id="cdc2" style="width: 90px">
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="entrada"><?= $lang->translation("Cantidad minima deposito:") ?></label>
                                <input type="text" name='can_min' id="can_min" value='<?= $thisCourse->can_min ?>' class="form-control --float">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="salida"><?= $lang->translation("") ?></label>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="save" type="submit"><?= $lang->translation('Guardar') ?></button>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="salida"><?= $lang->translation("") ?></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
    <script>
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
        $(document).ready(function() {

            $('.--float').mask("00.00").change(function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2))
                } else {
                    $(this).val('00.00')
                }
            });
            $('.--decimal').mask("0.00").change(function() {
                if ($(this).val()) {
                    $(this).val(parseFloat($(this).val()).toFixed(2))
                } else {
                    $(this).val('0.00')
                }
            });
            $('#ava').change(function(e) {
                e.preventDefault();
                if ($(this).val() == 'No') {
                    $('#valor,#verano').attr('disabled', true)
                } else {
                    $('#valor,#verano').attr('disabled', false)

                }
            });
            $('.--float,#ava').change();
        });
    </script>

</body>

</html>