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
    ['Ordenar cursos', 'Order courses'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
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
$school = new School(Session::id());
$year = $school->info('year2');

$teachers = new Teacher;
if (isset($_REQUEST['Grabar'])) {
    $cur = "%".$_POST['curso']."%";
    $thisCourse = DB::table('padres')->where([
        ['curso', 'LIKE', $cur],
        ['year', $year],
    ])->update([
        'orden' => $_POST['orden'],
    ]);
}

$courses = DB::table('padres')->select("distinct grado, curso, descripcion, orden")->where([
          ['year', $year],
        ])->orderBy('orden, curso')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Ordenar cursos');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Ordenar cursos') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" method="POST">
                    <?php if (isset($_POST['search'])) : ?>
                        <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-6 px-0">
                                <label for="curso"><?= $lang->translation("Entre el codigo del curso") ?></label>
                                <input type="text" value='' name='curso' id="curso" required>
                                <label for="curso"><?= $lang->translation("Posición del curso") ?></label>
                                <input type="text" value='' name='orden' id="orden" required>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-danger" name="Grabar" type="submit" onclick="return confirmar('<?= $lang->translation('Estás seguro que quieres cambiar la posición del curso?') ?>')"><?= $lang->translation('Grabar') ?></button>
                        </div>

                        <div class="col-12 text-center">
                        </div>
                        <div>
                        	<br>
							<table cellspacing="1" style="width: 69%">
								<tr>
									<td style="width: 150px"><?= $lang->translation("Posición") ?></td>
									<td style="width: 125px"><?= $lang->translation("Cursos") ?></td>
									<td style="width: 500px"><?= $lang->translation("Descripción") ?></td>
								</tr>
                        <?php foreach ($courses as $course) : ?>
								<tr>
									<td style="width: 150px"><?= $course->orden ?></td>
									<td style="width: 125px"><?= $course->curso ?></td>
									<td style="width: 500px"><?= $course->descripcion ?></td>
								</tr>
                        <?php endforeach ?>
							</table>
                        
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