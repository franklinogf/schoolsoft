<?php
require '../session.php';


$ID = $_SESSION['id'];
$result = mysql_query("SELECT id,nombre,apellidos FROM year WHERE mt='$ID'", $con);
$estudiante = mysql_fetch_object($result);
$ESTUD = "$estudiante->nombre $estudiante->apellidos";
$result = mysql_query("SELECT year FROM colegio WHERE usuario='administrador'", $con);
$row = mysql_fetch_assoc($result);
$year = $row['year'];
 
$foro_entrada = mysql_query("SELECT e.id,e.titulo,c.curso,c.desc1,d.fecha,d.hora FROM detalle_foro_entradas as d
				INNER JOIN foro_entradas AS e ON e.id = d.entrada_id 
				INNER JOIN padres AS p ON p.curso = e.curso
				INNER JOIN cursos AS c ON c.curso = p.curso 
				INNER JOIN year AS y ON y.ss = p.ss
				WHERE y.mt ='$ID' AND c.year='$year' AND p.year='$year' and e.estado = 'a'
				ORDER BY d.fecha DESC,d.hora DESC LIMIT 1", $con);
$not = mysql_fetch_object($foro_entrada);


$result = mysql_query("SELECT p.curso,c.desc1,c.id FROM `year` as e 
	INNER JOIN padres as p on p.ss = e.ss 
	INNER JOIN cursos as c on c.curso = p.curso	
	WHERE e.mt ='$ID' AND p.year='$year' AND c.year='$year'
	ORDER BY p.curso", $con);
$array = array();
$hoy = date("Y-m-d");
while ($row = mysql_fetch_object($result)) {

	$rs = mysql_query("SELECT d.fec_out,d.id_documento,d.curso,c.desc1,d.titulo FROM tbl_documentos as d
	INNER JOIN cursos as c on c.curso = d.curso	
	WHERE d.curso='$row->curso' AND d.id2='$row->id' AND c.year='$year' AND d.fec_out >= '$hoy'
	ORDER BY d.fec_out DESC", $con);

	while ($row2 = mysql_fetch_object($rs)) {
		//		echo "SELECT *,COUNT(*) as enviado FROM tareas_enviadas WHERE id_tarea='$row2->id_documento' AND fecha >= '$hoy' AND year='$year'; <br>";
		$ra = mysql_query("SELECT *,COUNT(*) as enviado FROM tareas_enviadas WHERE id_tarea='$row2->id_documento' AND fecha >= '$hoy' AND year='$year'", $con);
		$add = mysql_fetch_object($ra);
		if ($add->enviado == 0) {
			array_push($array, array(
				"id" => $row2->id_documento,
				"curso" => $row2->curso,
				"desc" => $row2->desc1,
				"titulo" => $row2->titulo,
				"cierre" => $row2->fec_out
			));
		}
	}
}
$tareas = json_decode(json_encode($array));

$result = mysql_query("SELECT * FROM foro_mensajes WHERE id_e='$ID' AND leido_e='no'", $con);
$mensajes = mysql_num_rows($result);
$mensaje = "mensajes";
if ($mensajes == 1) {
	$mensaje = "mensaje";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Estado</title>
	<link rel="stylesheet" href="../../../datatable/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="datatable.css">
	<style>
		.container {
			width: 60%;
			margin: 0 auto;
			font-family: sans-serif;
		}

		#nombre {
			text-align: center;
		}

		#fecha {
			text-align: right;
		}

		#notificacion {

			font-size: large;
		}

		.not {
			color: rgb(165, 165, 165);
			font-style: italic;
			font-variant: small-caps;
		}

		#tabla tr {
			pointer-events: auto !important;
		}
	</style>
</head>

<body>

	<body>
		<?php $activo = '0';
		require('menu.php'); ?>

		<div class="container">
			<h1 id="nombre">Bienvenid@ <?php echo $ESTUD; ?></h1>
			<h3 id="fecha"><?php echo $hoy; ?> <span id="hora"><?php echo date("H:i:s"); ?></span></h3>
			<h3><a href="mensajes.php">Tienes <?php echo "$mensajes $mensaje"; ?> sin leer</a></h3>
			<? if (mysql_num_rows($foro_entrada) > 0) : ?>
				<p id="notificacion">
					El ultimo comentario se ha hecho en el tema <span class="not"><?php echo "\"$not->titulo\""; ?></span>
					del curso <?php echo "$not->curso ($not->desc1) el $not->fecha a las $not->hora"; ?>
					<a href="verentrada.php?entrada=<?php echo $not->id; ?>">Click aqui para ir al tema</a>
				</p>
			<? endif ?>
			<?php if (sizeof($tareas) > 0) : ?>
				<table id="tabla" class="display">
					<thead>
						<tr>
							<th>Tareas</th>
							<th>Fecha cierre</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($tareas as $tarea) : ?>
							<tr>
								<td><?php echo "$tarea->curso - $tarea->desc1 ($tarea->titulo)"; ?></td>
								<td align="center"><?php echo $tarea->cierre; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<h2>No hay tareas pendientes</h2>
			<?php endif; ?>
		</div>
		<script type="text/javascript" src="../../../js/jquery-2.1.1.min.js"></script>
		<script type="text/javascript" src="../../../datatable/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {

				$('#tabla').DataTable({
					ordering: false,
					"scrollY": "200px",
					"paging": false,
					"language": {
						"zeroRecords": "No hay tareas para enviar",
						"info": "Hay _TOTAL_ tareas para enviar",
						"infoEmpty": "No hay tareas para enviar",
						"search": "Buscar"
					}
				});
				$("#tabla tbody tr").click(function(event) {
					location.href = "tareas.php";
				});
				var $reloj = function() {
					var $ahora = new Date();
					var $horas = $ahora.getHours();
					var $minutos = $ahora.getMinutes();
					var $segundos = $ahora.getSeconds();
					var $HORA = $horas + ":" + $minutos + ":" + $segundos;
					hora.innerHTML = $HORA;
				}
				$reloj();
				setInterval($reloj, 1000);

			});
		</script>
	</body>

</html>