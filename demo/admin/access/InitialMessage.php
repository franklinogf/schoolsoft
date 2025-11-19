<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Mensaje inicial', 'Initial message'],
    ['Mensaje inicial derecha', 'Right initial message'],
    ['Mensaje inicial izquierda', 'Left initial message'],
    ['Entre el codigo del curso', 'Enter the course code'],
    ['Estás seguro que quieres cambiar la posición del curso?', 'Are you sure you want to change the course position?'],
    ['Posición del curso', 'Course position'],
    ['Cursos', 'Courses'],
    ['Posición', 'Position'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Grabar', 'Save'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
]);

if (isset($_REQUEST['Grabar'])) {
    $thisCourse = DB::table('colegio')->where([
        ['usuario', 'administrador'],
    ])->update([
        'men_ini' => $_POST['men_ini'],
        'men_nota' => $_POST['men_nota'],
    ]);
}

$school = new School(Session::id());
$year = $school->info('year2');
$men_ini = $school->info('men_ini');
$men_nota = $school->info('men_nota');

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Mensaje inicial');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensaje inicial') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 650px;">
                <form class="mt-3" method="POST">
                    <div class="row">
                        <div class="col-18">
                            <div class="form-group col-6 px-0">
                                <label for="curso"><?= $lang->translation("Mensaje inicial izquierda") ?></label>
                                <textarea cols="84" name="men_ini" rows="15"><?= $men_ini ?></textarea>
                                
                                <label for="curso"><?= $lang->translation("Mensaje inicial derecha") ?></label>
                                <textarea cols="84" name="men_nota" rows="15"><?= $men_nota ?></textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-danger" name="Grabar" type="submit" ><?= $lang->translation('Grabar') ?></button>
                        </div>

                        <div class="col-12 text-center">
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

            $('.--float').mask("0.00").change(function() {
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