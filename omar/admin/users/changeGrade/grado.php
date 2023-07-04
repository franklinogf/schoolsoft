<?
session_start();
$id=$_SESSION['id1'];
$usua=$_SESSION['usua1'];
$idioma = 'Es';

if ($usua == "")
   {
   exit;
   }
if ($idioma == "Es") {
	$btn = "Buscar";
	$btn2 = "Cambiar grado";
	$header1 = "Grado";
	$header2 = "Estudiantes";
}else{
	$btn = "Search";
	$header1 = "Grade";
	$header2 = "Students";
	$btn2 = "Change grade";
}
  require_once('../control.php');
$e = mysql_query("SELECT * FROM colegio where usuario='$usua'",$con);
$cole = mysql_fetch_object($e);
$year = $cole->year2;



if (isset($_POST['cambiar'])) {
	$estudiantes = mysql_query("SELECT * FROM year WHERE activo='' and year='$year' AND grado='{$_POST['gra']}'  ORDER BY apellidos",$con);
	while ($row  = mysql_fetch_object($estudiantes)) {
		if ($_POST["estu$row->mt"] != $_POST['gra']) {
			mysql_query("UPDATE year SET grado = '{$_POST["estu$row->mt"]}' WHERE mt='$row->mt'",$con);	
            $gra = $_POST["estu$row->mt"];
            mysql_query("update pagos set grado='$gra' where ss = '$row->ss' AND year='$year'");
            }
        $q = "update padres set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update padres1 set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update padres2 set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update padres3 set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update padres4 set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update padres5 set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update asisdia set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $q = "update asispp set grado='{$_POST["estu$row->mt"]}' where ss = '$row->ss' AND year='$year'";
        mysql_query($q);
        $gra = $_POST["estu$row->mt"];
        mysql_query("update pagos set grado='$gra' where ss = '$row->ss' AND year='$year'");
		
	}

	$_POST['grado'] = $_POST['gra'];

}
$grados = mysql_query("SELECT distinct grado FROM year where activo='' and year = '$year' ORDER BY grado");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" type="text/css" href="../../jv/botones.css" />
	<link rel="stylesheet" href="datatable/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="label.css">

<style type="text/css">
.gris {
	text-align: center;
	font-size: large;
	background-color: #CCCCCC;
}
.color {
	text-align: center;
	background-color: #ffffcc;
}
.centrar{
margin:0 auto;
width:700px;
}

.margen{
	margin-top: 30px;
}
.gra {
    width: 50px;
    text-align: center;
}
</style>
<script language="Javascript">
// document.oncontextmenu = function(){return false}
</script>
	<meta charset="UTF-8">
	<title>Cambiar grado</title>
</head>
<body>
	<form method="POST">
		<table class="centrar margen">
			<thead>
				<tr class="gris">
					<th colspan="2"><?php echo $header1 ?> <?php echo $year ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="color">
					<td>
						<select style="width: 150px;" required="" name="grado" id="grado">
							<option value="">Seleccionar</option>
							<?php while ($row = mysql_fetch_object($grados)): ?>
								<option <?php echo ($_POST['grado'] == $row->grado)?"selected=''":''; ?> value="<?php echo $row->grado ?>"> <?php echo $row->grado ?></option>
							<?php endwhile ?>
						</select>
					</td>
					<td>
						<input name="buscar" class="myButton" style="width:200px" type="submit" value="<?php echo $btn ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
<!-- Lista de estudiantes -->
	<?php if (isset($_POST['grado'])): ?>
		<?php $estudiantes = mysql_query("SELECT * FROM year WHERE activo='' and  year='$year' AND grado='{$_POST['grado']}'  ORDER BY apellidos",$con); ?>
		<form  method="POST">
		<input type="hidden" name="gra" value="<?php echo $_POST['grado'] ?>">
		<div class="container margen">
			<table id="tabla" class="display compact" width="100%">
			    <thead>
			        <tr style="text-align:left;">
			            <th><?php echo "$header2 $header1 {$_POST['grado']}"; ?></th>	
			            <th></th>	           
			        </tr>
			    </thead>
			    <tbody>  
			    <?php while($estu = mysql_fetch_object($estudiantes)):?>    	
					<tr>		       					       		
						<td><?php echo strtoupper("$estu->apellidos $estu->nombre");?></td>
						<td>
							<input type="text" class="gra" required="" name="estu<?php echo $estu->mt ?>" value="<?php echo $estu->grado ?>">
						</td>
					</tr>             
			   <?php endwhile; ?>
			    </tbody>
			</table>
				<table class="centrar">
					<tbody>
						<tr>
							<td class="gris">
								<input name="cambiar" class="myButton" style="width:200px" type="submit" value="<?php echo $btn2 ?>">
							</td>
						</tr>
					</tbody>
				</table>

		</form>		
		</div>
	<?php endif ?>
<script type="text/javascript" src="/js/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="js/jquery.mask.js"></script>
<script type="text/javascript" src="datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(function() {
		$('.gra').mask('XX-XX', {
		    translation: {
		      'X': {
		        pattern: /[0-9a-zA-Z]/
		      }
		    }
		  });
		idioma = '<?php echo $idioma; ?>';

		if (idioma === "Es") {
			zeroRecords = "No hay estudiantes, presione continuar";
			info = "Hay _TOTAL_ estudiantes";
			infoEmpty = "No hay estudiantes";
			search = "Buscar:";
			infoFiltered = "(filtrados de un total de _MAX_ )";
		}else{
			zeroRecords = "There are no students, press continue";
			info = "There are _TOTAL_ students";
			infoEmpty = "There are no students";
			search = "Search:";
			infoFiltered = "(filtrates from a total of _MAX_ )";
		}
		//funcion para poner las primera letras de los nombres en mayuscula
	function eachWord(str){
	    	return str.replace(/\w\S*/g, function(txt){
	    	return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	    	});
			}
	 			

			$('#tabla').DataTable({
				ordering:false,
		     	"scrollY": "400px",
		        "paging": false,
		        "language": {            
		            "zeroRecords": zeroRecords,
		            "info": info,
		            "infoEmpty": infoEmpty,
		             "search": search,
		              "infoFiltered": infoFiltered
	        }   	
		    });
	    });
</script>
</body>
</html>
