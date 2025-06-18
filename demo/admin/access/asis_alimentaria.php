<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase0\DB;
use Classes\Controllers\School;

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

$tabla =  $_COOKIE["variable9"];
$year  =  $_COOKIE["variable10"];

header("Content-type: application/vnd.ms-excel" ); 
header("Content-Disposition: attachment; filename=Asistencia Alimentaria.xls" );
session_start();

function CalcularEdad($fecha)
{
	list($Y, $m, $d) = explode("-", $fecha);
	return (date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

	<meta charset="UTF-8">
	<title>Asistencia Alimentaria</title>
	<style type="text/css">
		.style1 {
			text-align: center;
			font-size: large;
		}

		.style2 {
			text-align: center;
		}

		.style3 {
			background-color: #CCCCCC;
		}

		.style4 {
			background-color: #FFFFCC;
			text-align: center;
		}

		.text {
			mso-number-format: "\@";
			/*force text*/
		}
	</style>

</head>

<body>

	<?php
      $students = DB::table('year')->where([
        ['year', $year],
        ['codigobaja', 0]
      ])->get();
	
	?>
	<table id="estudiantes" align="center" cellpadding="2" border="1" cellspacing="0">
		<thead>
			<tr class="style1">
				<th>SEG_SOC_ESTUDIANTE</th>
				<th>NUMERO_ESTUDIANTE</th>
				<th>NOMBRE</th>
				<th>INICIAL</th>
				<th>APELLIDO_PATERNO</th>
				<th>APELLIDO_MATERNO</th>
				<th>SEXO</th>
				<th>FECHA_NACIMIENTO</th>
				<th>CIUDADANIA</th>
				<th>ESTADO_CIVIL</th>
				<th>NOMBRE_PADRE_O_ENCARGADO</th>
				<th>INCAPACIDAD</th>
				<th>CODIGO_DE_ESCOLARIDAD</th>
				<th>ESCUELA</th>
				<th>MUNICIPIO ESCUELA</th>
				<th>ASISTE_REGULARIDAD</th>
				<th>TELEFONO</th>
				<th>EMAIL</th>
				<th>DSC_DIR1_POSTAL</th>
				<th>DSC_DIR2_POSTAL</th>
				<th>ZIP_CODE_POSTAL1</th>
				<th>ZIP_CODE_POSTAL2</th>
				<th>CIUDAD_POSTAL</th>
				<th>DSC_DIR1_RESIDENCIAL</th>
				<th>DSC_DIR2_RESIDENCIAL</th>
				<th>ZIP_CODE_RESIDENCIAL1</th>
				<th>ZIP_CODE_RESIDENCIAL2</th>
				<th>CIUDAD_RESIDENCIAL</th>

			</tr>
		</thead>
		<tbody>
			<?php
			
      foreach ($students as $estu)
              {
			
            $madre = DB::table('madre')->where([
              ['id', $estu->id]
            ])->first();
			
			?>
				<tr>
					<td align="center"><?= $estu->ss ?></td>
					<td align="center"><?= $estu->id ?></td>
					<td><?= utf8_encode($estu->nombre) ?></td>
					<td><?= utf8_encode($estu->nombre[0]) ?></td>
					<?php $apellidos = explode(" ",$estu->apellidos) ?>
					<td><?= utf8_encode($apellidos[0]) ?></td>
					<td><?= utf8_encode($apellidos[1]) ?></td>
					<td align="center"><?= ($estu->genero === 'M' || $estu->genero === '2') ? 'M' : 'F' ?></td>
					<td align="center"><?= ($estu->fecha !== '0000-00-00') ? $estu->fecha : '' ?></td>
					<td></td>
					<td></td>
					<td><?= ($madre->madre !== "") ? utf8_encode($madre->madre) : utf8_encode($madre->padre) ?></td>
					<td></td>
					<td  class="text"><?= $estu->grado ?></td>
					<td><?= $cole->colegio ?></td>
					<td><?= $cole->pueblo1 ?></td>
					<td></td>
					<?php $tel = ($madre->tel_m !== "") ? $madre->tel_m : $madre->tel_p  ?>
					<td><?= ($tel !== '(___)___-____') ? $tel : '' ?></td>
					<td><?= ($madre->email_m !== "") ? $madre->email_m : $madre->email_p ?></td>
					<td><?= $madre->dir1 ?></td>
					<td><?= $madre->dir3 ?></td>
					<td></td>
					<td class="text"><?= $madre->zip1 ?></td>
					<td><?= $madre->pueblo1 ?></td>
					<td><?= $madre->dir2 ?></td>
					<td><?= $madre->dir4 ?></td>
					<td></td>
					<td class="text"><?= $madre->zip2 ?></td>
					<td><?= $madre->pueblo2 ?></td>
				</tr>				
			<?php } ?>
		</tbody>
	</table>

</body>

</html>