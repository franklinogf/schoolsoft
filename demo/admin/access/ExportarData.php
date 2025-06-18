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
    ['Exportación de data a Excel', 'Data export to Excel'],
    ['Selección de Base de datos', 'Database Selection'],
    ['Seleccióna el año', 'Select the year'],
    ['Selección', 'Selection'],
    ['Padres', 'Parents'],
    ['Estudiantes', 'Students'],
    ['Año para transferir datos', 'Year to transfer data'],
    ['Notas', 'Grades'],
    ['Pagos', 'Payments'],
    ['Transferir', 'Transfer'],
    ['Documentos de estudiantes', 'Student documents'],
    ['Padres/Estudiantes', 'Parents/Students'],
    ['Comedor escolar', 'School cafeteria'],
    ['Convertir', 'Convert'],
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
              }
      }

   if ($_POST['Ckb2']=='1')
      {
      $courses = DB::table('materias')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
              {
              }
      }

   if ($_POST['Ckb3']=='1')
      {
      $courses = DB::table('presupuesto')->where([
        ['year', $school->info('year2')]
      ])->get();
      foreach ($courses as $course)
              {
              }
      }

    
}

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Exportación de data a Excel');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
	<script language="Javascript" type="text/javascript">
		document.oncontextmenu = function() {
			return false
		};

		function reci11() {
			var now = new Date();
			var time = now.getTime();
			time += 1800 * 1000;
			now.setTime(time);

			var miVariablea = document.dataex.tabla.value;
			document.cookie = 'variable9=' + miVariablea + '; expires=' + now.toGMTString() + '; path=/';
			var miVariableb = document.dataex.year.value;
			document.cookie = 'variable10=' + miVariableb + '; expires=' + now.toGMTString() + '; path=/';
		}
	</script>
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
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Exportación de data a Excel') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form class="mt-3" id="form" name="dataex" method="post">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group col-6 px-20">
                                <label for="curso" class="newStyle1"><?= $lang->translation("Selección de Base de datos") ?></label>

                                <select name="tabla" id="tabla">
                                    <option value=""><?= $lang->translation("Selección") ?></option>
                                    <option value="year"><?= $lang->translation("Estudiantes") ?></option>
                                    <option value="madre"><?= $lang->translation("Padres") ?></option>
                                    <option value="padres"><?= $lang->translation("Notas") ?></option>
                                    <option value="pagos"><?= $lang->translation("Pagos") ?></option>
                                    <option value="docu_estudiantes"><?= $lang->translation("Documentos de estudiantes") ?></option>
                                    <option value="asis_alimentaria"><?= $lang->translation("Padres/Estudiantes") ?></option>
                                    <option value="comedor"><?= $lang->translation("Comedor escolar") ?></option>
                                </select>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="form-group">
                                <label for="teacherId"><?= $lang->translation("Seleccióna el año") ?></label>
                                <select class="form-control selectpicker w-100" name="year" id="year" data-live-search="true" required>
                                    <option value=""><?= $lang->translation("Seleccionar") ?></option>
                                    <?php foreach ($years as $year) : ?>
                                        <option <?= $school->info('year2') == $year->year ? 'selected' : '' ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-danger" id="trans" name="trans" type="submit" onmouseover="reci11()" ><?= $lang->translation('Convertir') ?></button>
                            
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
			$(function() {
			
				$("#trans").click(function(e) {					
					e.preventDefault();
					if ($("#tabla").val() === 'asis_alimentaria') {
						window.open('asis_alimentaria.php','_new')
//						$("#form").prop('action', 'asis_alimentaria.php','_new');
//						$("#form").submit();
					}else if($("#tabla").val() === 'comedor'){
						window.open('comedor.php','_new')
//						$("#form").prop('action', 'comedor.php','_new');
//						$("#form").submit();
					}
					else{
						window.open('excel.php','_new')
					}
				})

				
			})

    </script>

</body>

</html>

<?
//            				<input class="myButton" id="trans" name="trans" type="button" value="Convertir" style="width: 155px; height: 27px;"  onmouseover="reci11()" />



?>
