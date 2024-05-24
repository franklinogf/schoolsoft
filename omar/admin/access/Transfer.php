<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ['Transferir datos', 'Transfer data'],
    ['Opción', 'Option'],
    ['Selección', 'Selection'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Fotos', 'Photos'],
    ['Balances cafetería', 'Cafeteria balances'],
    ['Año para transferir datos', 'Year to transfer data'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Transferir', 'Transfer'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Atrás', 'Go back'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);
$school = new School(Session::id());

$years = DB::table('year')->select("DISTINCT year")->get();


if (isset($_REQUEST['pasar'])) {
    $courses = DB::table('padres')->where([
        ['year', $_POST['year']],
        ])->get();
    foreach ($courses as $course)
        {
        DB::table('acumulativa')->insert([
          'year' => $_POST['year'],
          'curso' => $course->curso,
          'ss' => $course->ss,
          'desc1' => $course->descripcion,
          'desc2' => $course->desc2,
          'credito' => $course->credito,
          'peso' => $course->peso,
          'orden' => $course->orden,
          'apellidos' => $course->apellidos,
          'nombre' => $course->nombre,
          'grado' => $course->grado,
          'ava' => $course->ava,
          'val' => $course->valor,
          'verano' => $course->verano,
          'au1' => $course->aus1,
          'au2' => $course->aus2,
          'au3' => $course->aus3,
          'au4' => $course->aus4,
          'ta1' => $course->tar1,
          'ta2' => $course->tar2,
          'ta3' => $course->tar3,
          'ta4' => $course->tar4,
          'con1' => $course->con1,
          'con2' => $course->con2,
          'con3' => $course->con3,
          'con4' => $course->con4,
          ]);
   if ($_POST['sem']=='A'){$sem=$course->sem1;$se='sem1';}
   if ($_POST['sem']=='B'){$sem=$course->sem2;$se='sem2';}
    $thisCourse = DB::table('acumulativa')->where([
          ['ss', $course->ss], 
          ['curso', $course->curso], 
          ['year', $_POST['year']],
          ])->update([
          $se => $sem,
          'desc1' => $course->descripcion,
          'desc2' => $course->desc2,
          'credito' => $course->credito,
        ]);

       }
//          '' => $course->,
//          'sem1' => $course->sem1,
//          'sem2' => $course->sem2,




    
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Transferir datos');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
	<style type="text/css">
	.newStyle1 {
		font-size: medium;
		z-index: auto;
		position: relative;
	}
	</style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Transferir datos') ?></h1>
        <button onclick="history.back()" class="btn btn-secondary mb-2"><?= $lang->translation("Atrás") ?></button>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" method="POST">
                    <?php if (isset($_POST['search'])) : ?>
                        <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <label for="curso" class="newStyle1"><?= $lang->translation("Opción") ?></label>
                            </div>
                        <select id="sem" name="sem" class="form-control" required>
                            <option value=''><?= $lang->translation("Selección") ?></option>
                            <option value='A'><?= $lang->translation("Semestre 1") ?></option>
                            <option value='B'><?= $lang->translation("Semestre 2") ?></option>
                        </select>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label for="teacherId"><?= $lang->translation("Año para transferir datos") ?></label>
                                <select class="form-control selectpicker w-100" name="year" id="year" data-live-search="true" required>
                                    <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                    <?php foreach ($years as $year) : ?>
                                        <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary" name="pasar" type="submit"><?= $lang->translation('Transferir') ?></button>
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