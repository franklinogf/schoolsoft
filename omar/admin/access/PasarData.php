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
    ['Catálogo', 'Catalogue'],
    ['Cursos', 'Courses'],
    ['Presupuesto', 'Budget'],
    ['Costos', 'Costs'],
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
   if ($_POST['Ckb1']=='1')
      {
      $courses = DB::table('cursos')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
        DB::table('cursos')->insert([
          'year' => $_POST['year'],
          'curso' => $course->curso,
          'maestro' => $course->maestro,
          'desc1' => $course->desc1,
          'desc2' => $course->desc2,
          'credito' => $course->credito,
          'peso' => $course->peso,
          'id' => $course->id,
          'entrada' => $course->entrada,
          'salida' => $course->salida,
          'dias' => $course->dias,
          'ava' => $course->ava,
          'valor' => $course->valor,
          'verano' => $course->verano,
        ]);
        }
      }

   if ($_POST['Ckb2']=='1')
      {
      $courses = DB::table('materias')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
        DB::table('materias')->insert([
          'year' => $_POST['year'],
          'grado' => $course->grado,
          'des1' => $course->des1,
          'des2' => $course->des2,
          'des3' => $course->des3,
          'des4' => $course->des4,
          'des5' => $course->des5,
          'des6' => $course->des6,
          'des7' => $course->des7,
          'des8' => $course->des8,
          'des9' => $course->des9,
          'des10' => $course->des10,
          'des11' => $course->des11,
          'des12' => $course->des12,
          'des13' => $course->des13,
          'des14' => $course->des14,
          'des15' => $course->des15,
          'des16' => $course->des16,
          'des17' => $course->des17,
          'des18' => $course->des18,
          'des19' => $course->des19,
          'des20' => $course->des20,
          'des21' => $course->des21,
          'des22' => $course->des22,
          'des23' => $course->des23,
          'des24' => $course->des24,
          'des25' => $course->des25,
          'des26' => $course->des26,
          'des27' => $course->des27,
          'des28' => $course->des28,
          'des29' => $course->des29,
          'des30' => $course->des30,
          'des31' => $course->des31,
          'des32' => $course->des32,
          'des33' => $course->des33,
          'des34' => $course->des34,
          'des35' => $course->des35,
          'des36' => $course->des36,
          'des37' => $course->des37,
          'des38' => $course->des38,
          'des39' => $course->des39,
          'des40' => $course->des40,
          'curso1' => $course->curso1,
          'curso2' => $course->curso2,
          'curso3' => $course->curso3,
          'curso4' => $course->curso4,
          'curso5' => $course->curso5,
          'curso6' => $course->curso6,
          'curso7' => $course->curso7,
          'curso8' => $course->curso8,
          'curso9' => $course->curso9,
          'curso10' => $course->curso10,
          'curso11' => $course->curso11,
          'curso12' => $course->curso12,
          'curso13' => $course->curso13,
          'curso14' => $course->curso14,
          'curso15' => $course->curso15,
          'curso16' => $course->curso16,
          'curso17' => $course->curso17,
          'curso18' => $course->curso18,
          'curso19' => $course->curso19,
          'curso20' => $course->curso20,
          'curso21' => $course->curso21,
          'curso22' => $course->curso22,
          'curso23' => $course->curso23,
          'curso24' => $course->curso24,
          'curso25' => $course->curso25,
          'curso26' => $course->curso26,
          'curso27' => $course->curso27,
          'curso28' => $course->curso28,
          'curso29' => $course->curso29,
          'curso30' => $course->curso30,
          'curso31' => $course->curso31,
          'curso32' => $course->curso32,
          'curso33' => $course->curso33,
          'curso34' => $course->curso34,
          'curso35' => $course->curso35,
          'curso36' => $course->curso36,
          'curso37' => $course->curso37,
          'curso38' => $course->curso38,
          'curso39' => $course->curso39,
          'curso40' => $course->curso40,
        ]);
        }
      }

   if ($_POST['Ckb3']=='1')
      {
      $courses = DB::table('presupuesto')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
        DB::table('presupuesto')->insert([
          'year' => $_POST['year'],
          'codigo' => $course->codigo,
          'descripcion' => $course->descripcion,
          'cantidad' => $course->cantidad,
          'costo' => $course->costo,
        ]);
        }
      }

   if ($_POST['Ckb4']=='1')
      {
      $courses = DB::table('costos')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
        DB::table('costos')->insert([
          'year' => $_POST['year'],
          'codigo' => $course->codigo,
          'grado' => $course->grado,
          'descripcion' => $course->descripcion,
          'costo' => $course->costo,
          'activo' => $course->activo,
          'pf' => $course->pf,
          'esn' => $course->esn,
          'm1' => $course->m1,
          'm2' => $course->m2,
          'm3' => $course->m3,
          'm4' => $course->m4,
          'm5' => $course->m5,
          'm6' => $course->m6,
          'm7' => $course->m7,
          'm8' => $course->m8,
          'm9' => $course->m9,
          'm10' => $course->m10,
          'm11' => $course->m11,
          'm12' => $course->m12,
        ]);
        }
      }

   if ($_POST['Ckb5']=='1')
      {
      $courses = DB::table('year')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
    $thisCourse = DB::table('year')->where([
          ['ss', $course->ss], 
          ['year', $_POST['year']],
          ])->update([
          'tipo' => $course->tipo,
        ]);
        }
      }

   if ($_POST['Ckb6']=='1')
      {
      $courses = DB::table('year')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
        {
    $thisCourse = DB::table('year')->where([
          ['ss', $course->ss], 
          ['year', $_POST['year']],
          ])->update([
          'balance_a' => $course->balance_a,
          'cantidad' => $course->cantidad,
        ]);
        }
      }



    
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
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" method="POST">
                    <?php if (isset($_POST['search'])) : ?>
                        <input type="hidden" name="courseId" value="<?= $_REQUEST['course'] ?>">
                    <?php endif ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb1" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Catálogo") ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb2" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Cursos") ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb3" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Presupuesto") ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb4" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Costos") ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb5" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Fotos") ?></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <input name="Ckb6" type="checkbox" style="width: 25px; height: 25px" value="1" />
                                <label for="curso" class="newStyle1"><?= $lang->translation("Balances cafetería") ?></label>
                            </div>
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