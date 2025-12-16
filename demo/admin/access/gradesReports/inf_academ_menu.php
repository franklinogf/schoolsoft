<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();


$lang = new Lang([
    ['Informes Académicos', 'Academic Reports'],
    ['Informe de aprovechamiento académico', 'Academic achievement report'],
    ['Maestro', 'Teacher'],
    ['Semestre', 'Semester'],
    ['Opción', 'Option'],
    ['Continuar', 'Continue'],
    ['Semestre 1', 'Semester 1'],
    ['Semestre 2', 'Semester 2'],
    ['Trimestre 1', 'Quarter 1'],
    ['Trimestre 2', 'Quarter 2'],
    ['Trimestre 3', 'Quarter 3'],
    ['Trimestre 4', 'Quarter 4'],
    ['Notas para Sumar', 'Notes to Add'],
    ['Firmas', 'Signature'],
    ['Grados Separados:', 'Separate grades:'],
    ['Atrás', 'Go back'],
    ['Grado', 'Grade'],
    ['Notas para ver:', 'Notes to see:'],
    ['Maestro', 'Maestro'],
    ['Padre/encargado', 'Parent/Guardian'],
    ['Nota porciento', 'Percentage score'],
    ['Promedio final', 'Final average'],
    ['CURSOS A MEJORAR', 'COURSES TO IMPROVE'],
    ['INFORME DE DEFICIENCIA', 'DEFICIENCY REPORT'],
    

    
]);
$school = new School(Session::id());
$year = $school->info('year2');

$res = DB::table('padres')->select("DISTINCT profesor, id")->where([
        ['year', $school->info('year2')],
        ['profesor', '<>', '']
    ])->orderBy('profesor')->get();

?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<head>
    <?php
    $title = $lang->translation('Informes Académicos');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
	<style>
		table{
			margin:auto;
		}
		table th{
			background-color: #CCC;
		}
		table td{
			background-color: #FFFFCC;
		}
		.myButton{
			width: 80%;
		}
	</style>
</head>
<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Informe de aprovechamiento académico') ?>
        </h1>
        <div class="container bg-white shadow-lg py-3 rounded">

<form action="pdf/inf_academ.php" method="POST" target="infAcadem">
		<table id="TABLA">
			<thead>
				<tr>
					<th><?= $lang->translation('Maestro') ?></th>
					<th><?= $lang->translation('Semestre') ?></th>
					<th><?= $lang->translation('Opción') ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name="profe" id="profe">
                            <?php foreach ($res as $profe): ?>
								<option value="<?= $profe->id ?>"><?= "$profe->profesor" ?></option>
                            <?php endforeach ?>
						</select>
					</td>
					<td>
						<select name="sem" id="sem">
							<option value="nota1"><?= $lang->translation('Trimestre 1') ?></option>
							<option value="nota2"><?= $lang->translation('Trimestre 2') ?></option>
							<option value="nota3"><?= $lang->translation('Trimestre 3') ?></option>
							<option value="nota4"><?= $lang->translation('Trimestre 4') ?></option>
							<option value="sem1"><?= $lang->translation('Semestre 1') ?></option>
							<option value="sem2"><?= $lang->translation('Semestre 2') ?></option>
							<option value="final"><?= $lang->translation('Final') ?></option>
						</select>
					</td>
					<td>
						<select name="opt" id="opt">
							<option value="2"><?= $lang->translation('Nota porciento') ?></option>
							<option value="1"><?= $lang->translation('Nota decimal') ?></option>
						</select>
					</td>

				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th align="center" colspan="3">
						<input type="submit" class="btn btn-primary d-block mx-auto" value="Aceptar">
					</th>
				</tr>
			</tfoot>
		</table>
	</form>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>
</html>