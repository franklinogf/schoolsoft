<?php
require '../session.php';
$ID = $_SESSION['id'];
$tipo = $_SESSION['tipo'];
$result = mysql_query("SELECT year FROM colegio WHERE usuario='administrador'",$con);
$row = mysql_fetch_assoc($result);
$year = $row['year'];
$result = mysql_query("SELECT DISTINCT curso,descripcion FROM padres WHERE id='$ID' AND year='$year'",$con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Foro</title>
	<link rel="stylesheet" href="../../../datatable/css/jquery.dataTables.min.css">	
	<link rel="stylesheet" href="datatable.css">
	<link rel="stylesheet" href="/css/botones.css">
	
	<style>
#titulo{margin-top: 20px;}
	
.container {
  width: 50%;
  margin: 20px auto;
  font-family: 'Helvetica Neue', Arial, Helvetica, sans-serif;
}
</style>
</head>
<body>
	<?php $activo='4'; require("menu.php"); ?>	
	<div class="container">
		<?php $active='1'; require("informessubmenu.php"); ?>
		<form id="cursoForm" action="cursospdf.php" method="POST" target="Cursos">
			<table class="display" id="tabla">
				<thead>
				 	<tr style="text-align:left;">
			        	<th style="width: 1px;"><input type="checkbox" id="imprimirTodos"></th>
			            <th>Cursos</th>		            		                     
			        </tr>
				</thead>
				<tbody>
				<?php while($row = mysql_fetch_assoc($result)): ?>
					<tr>
						<td style="text-align:center;"><input value="<?php echo $row['curso']; ?>" type="checkbox" name="imprimir[]"></td>
						<td><?php echo $row['curso']." - ".$row['descripcion']; ?></td>
					</tr>
				<?php endwhile; ?>	
				</tbody>
				<tfoot>
			    	<tr>
				    	<td align="center" colspan="2">
					    	<a class="myButton" href="" id="continuar">Continuar</a>
				    	</td>		    		
			    	</tr>
			  </tfoot>		
			</table>
		</form>	
	</div>
	<script type="text/javascript" src="../../../js/jquery-2.1.1.min.js"></script>		
	<script type="text/javascript" src="../../../datatable/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
			$(document).ready(function() {


				$('#tabla').DataTable({
						ordering:false,
				     	"scrollY": "400px",
				        "paging": false,
				        "language": {            
			            "zeroRecords": "No hay cursos",
			            "info": "Hay _TOTAL_ cursos",
			            "infoEmpty": "No hay cursos",
			             "search":    "Buscar" 
			        }   	
				    });


				$("#imprimirTodos").click(function(event) {
	if($(this).prop("checked")){
		$("input[name='imprimir[]']").each(function(index, el) {
			$(this).prop('checked', true);
		});
	}else{
		$("input[name='imprimir[]']").each(function(index, el) {
			$(this).prop('checked', false);
		});
	}
});

$("input[name='imprimir[]']").click(function(event) {
	$("input[name='imprimir[]']").each(function(index, el) {
		if(!$(this).prop('checked')){
			$("#imprimirTodos").prop('checked', false);
			return false;
		}else{
			$("#imprimirTodos").prop('checked', true);
		}
	});
});
//marcar checkbox cuando se clicque una fila en la tabla
$("#tabla tbody tr").click(function(event) {
	var index = $("#tabla tbody tr").index($(this));
	var check = $("input[name='imprimir[]']").eq(index);
	check.click();
});

//END seleccionar todos

//continuar debe de haber seleccionado al menos uno
	$("#continuar").click(function(event) {
		event.preventDefault();
		var haySeleccionado = false;
		$("input[name='imprimir[]']").each(function(index, el) {
			if($(this).prop('checked')){
				haySeleccionado = true;
				return false;
			}
		});
		if (haySeleccionado) {
			$("#cursoForm").submit();
			$("#imprimirTodos").prop('checked', false);
			$("input[name='imprimir[]']").each(function(index, el) {
				$(this).prop('checked', false);
			});
		}else{
			alert("DEBE DE SELECCIONAR AL MENOS UN CURSO");
		};
		
	});
	
			});
	</script>
</body>
</html>