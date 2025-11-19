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
    ['Requisitos', 'Requirements'],
    ['Mensaje', 'Message'],
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
    $thisCourse = DB::table('requisitos')->update([
        'des1' => $_POST['des1'],
        'des2' => $_POST['des2'],
        'des3' => $_POST['des3'],
        'des4' => $_POST['des4'],
        'des5' => $_POST['des5'],
        'des6' => $_POST['des6'],
        'des7' => $_POST['des7'],
        'des8' => $_POST['des8'],
        'des9' => $_POST['des9'],
        'des10' => $_POST['des10'],
        'des11' => $_POST['des11'],
        'des12' => $_POST['des12'],
        'des13' => $_POST['des13'],
        'des14' => $_POST['des14'],
        'des15' => $_POST['des15'],
        'des16' => $_POST['des16'],
        'des17' => $_POST['des17'],
    ]);
}

$school = new School(Session::id());
$year = $school->info('year2');

$requisitos = DB::table('requisitos')->first();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Requisitos');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Requisitos') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-1 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" method="POST">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">1.</label>
                                <input type="text" value='<?= $requisitos->des1 ?>' class="form-control" name='des1' id="des1">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">2.</label>
                                <input type="text" value='<?= $requisitos->des2 ?>' class="form-control" name='des2' id="des2">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">3.</label>
                                <input type="text" value='<?= $requisitos->des3 ?>' class="form-control" name='des3' id="des3">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">4.</label>
                                <input type="text" value='<?= $requisitos->des4 ?>' class="form-control" name='des4' id="des4">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">5.</label>
                                <input type="text" value='<?= $requisitos->des5 ?>' class="form-control" name='des5' id="des5">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">6.</label>
                                <input type="text" value='<?= $requisitos->des6 ?>' class="form-control" name='des6' id="des6">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">7.</label>
                                <input type="text" value='<?= $requisitos->des7 ?>' class="form-control" name='des7' id="des7">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">8.</label>
                                <input type="text" value='<?= $requisitos->des8 ?>' class="form-control" name='des8' id="des8">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">9.</label>
                                <input type="text" value='<?= $requisitos->des9 ?>' class="form-control" name='des9' id="des9">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">10.</label>
                                <input type="text" value='<?= $requisitos->des10 ?>' class="form-control" name='des10' id="des10">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">11.</label>
                                <input type="text" value='<?= $requisitos->des11 ?>' class="form-control" name='des11' id="des11">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">12.</label>
                                <input type="text" value='<?= $requisitos->des12 ?>' class="form-control" name='des12' id="des12">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">13.</label>
                                <input type="text" value='<?= $requisitos->des13 ?>' class="form-control" name='des13' id="des13">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">14.</label>
                                <input type="text" value='<?= $requisitos->des14 ?>' class="form-control" name='des14' id="des14">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">15.</label>
                                <input type="text" value='<?= $requisitos->des15 ?>' class="form-control" name='des15' id="des15">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">16.</label>
                                <input type="text" value='<?= $requisitos->des16 ?>' class="form-control" name='des16' id="des16">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-12 px-0">
                                <label for="curso">Final</label>
                                <input type="text" value='<?= $requisitos->des17 ?>' class="form-control" name='des17' id="des17">
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