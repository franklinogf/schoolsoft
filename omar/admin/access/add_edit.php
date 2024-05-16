<?
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Añadir/Editar Nota', 'Add/Edit Note'],
    ['Nombre del Estudiante', 'Student name'],
    ['Nuevo Estudiante', 'New student'],
    ['Buscar', 'Search'],
    ['Atrás', 'Back'],
    ['Nombre', 'Name'],
    ['Apellidos', 'Last name'],
    ['Seguro Social', 'Social Security'],
    ['Nota Sem-1', 'Sem-1 Note'],
    ['Nota Sem-2', 'Sem-2 Note'],
    ['Año', 'Year'],
    ['Grado', 'Grade'],
    ['Cursos', 'Courses'],
    ['Añadir', 'Add'],
    ['Guardar', 'Saved'],
    ['Desea borrarlo?', 'You want to delete it?'],
    ['', ''],
    ['', ''],
    
]);


$school = new School(Session::id());
$grados = $school->allGrades();
$years = DB::table('year')->select("DISTINCT year")->get();

$estudiantes = DB::table('year')->select("DISTINCT nombre, apellidos, ss")->orderBy('apellidos, nombre')->get();

?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<head>
<title>Añadir / Editar</title>
<link rel="stylesheet" type="text/css" href="../../css/botones.css" />
<script language="JavaScript">
function confirmar ( mensaje ) {
return confirm( mensaje );
}
</script> 
    <?php
    $title = $lang->translation('Transcripción de crédito');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5">
            <?= $lang->translation('Añadir/Editar Nota') ?>
        </h1>

<div class="container bg-white shadow-lg py-3 rounded">

<form method="post">

<table cellpadding="2" cellspacing="0" style="width: 50%" align="center">
	<tr>
		<th class="gris"><?= $lang->translation('Nombre del Estudiante') ?></th>
	</tr>
	<tr>
		<td class="color">
           <div class="input-group mb-3">
              <div class="input-group-prepend">
			<input class="btn btn-primary mx-auto" name="new" type="submit" value="<?= $lang->translation('Nuevo Estudiante') ?>" />
                        </div>
			<select name="estu" style="width: 300px">
			<?php 
           foreach ($estudiantes as $student) 
                   {
			?>
					<option  <?php echo ($student->ss == $_REQUEST['estu'])?'selected=""':'' ?> value="<?php echo $student->ss ?>"><?php echo utf8_decode("$student->apellidos $student->nombre") ?></option>
			<?php } ?>		
			</select>
		<input class="btn btn-primary mx-auto" name="buscar" type="submit" value="<?= $lang->translation('Buscar') ?>" />
                        </div>

	</td>
	</tr>

	<tr>
		<td class="gris">
			<a class="btn btn-primary mx-auto" href="acumulativa.php"><?= $lang->translation('Atrás') ?></a>
		</td>
	</tr>
	
</table>
</form>

<form method="POST" action="addedit_guardar.php">
<?php if (isset($_REQUEST['estu'])): ?>
	<?php 
			if (!isset($_POST['new'])) {
            $est = DB::table('year')->select("DISTINCT nombre, apellidos, ss")->where('ss', $_REQUEST['estu'])->orderBy('apellidos, nombre')->first();
			}
		
	?>
	<table cellpadding="2" cellspacing="0" style="width: 50%; margin-top: 20px;" align="center">
		<tr class="gris">
			<th><?= $lang->translation('Nombre') ?></th>
			<th><?= $lang->translation('Apellidos') ?></th>
			<th><?= $lang->translation('Seguro Social') ?></th>
		</tr>
		<tr class="color">
			<td>
				<?php if (isset($_POST['new'])): ?>
					<input type="text" name="nombre">
					<?php else: ?>
						<input type="hidden" name="nombre" value="<?php echo utf8_decode($est->nombre) ?>">
						<?php echo utf8_decode($est->nombre) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if (isset($_POST['new'])): ?>
					<input type="text" name="apellidos">
					<?php else: ?>
						<input type="hidden" name="apellidos" value="<?php echo utf8_decode($est->apellidos) ?>">
						<?php echo utf8_decode($est->apellidos) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if (isset($_POST['new'])): ?>
					<input required=""  data-mask="000-00-0000" type="text" name="ss" placeholder="000-00-0000" >
					<?php else: ?>
						<input type="hidden" name="ss" value="<?php echo $est->ss ?>">
						<?php echo $est->ss ?>
				<?php endif ?>
			</td>
			
		</tr>
	</table>
	<?php 
   $notas = DB::table('acumulativa')->where('ss', $_REQUEST['estu'])->orderBy('year, curso')->get();

	?>
	<table cellpadding="2" cellspacing="0" style="width: 50%;" align="center">
		<thead>
			<tr class="gris">
				<th><?= $lang->translation('Nota Sem-1') ?></th>
				<th><?= $lang->translation('Nota Sem-2') ?></th>
				<th><?= $lang->translation('Grado') ?></th>
				<th><?= $lang->translation('Cursos') ?></th>
				<th><?= $lang->translation('Año') ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody  id="notas" >
			<?php
           foreach ($notas as $not) 
                   {
			 ?>
				<?php 
                $cursos = DB::table('cursos')->select("DISTINCT curso, desc1")->orderBy('curso')->get();
                $grados = DB::table('year')->select("DISTINCT grado")->orderBy('grado')->get();
				?>
				<tr class="color">
					<td><input data-mask="000" type="text" name="nota1[]" size="5" value="<?php echo $not->sem1 ?>"></td>
					<td><input data-mask="000" type="text" name="nota2[]" size="5" value="<?php echo $not->sem2 ?>"></td>
					<td>
						<select required="" name="grado[]">
							<?php 
                          foreach ($grados as $grado) 
                                  {
							
							?>
								<option <?php echo ($not->grado==$grado->grado)?"selected=''":''; ?> value="<?php echo $grado->grado ?>"><?php echo "$grado->grado" ?></option>	
							<?php } ?>				
						</select>
					</td>
					<td>
						<select required="" name="curso[]">
							<?php 
                          foreach ($cursos as $curso) 
                                  {
							 ?>
								<option <?php echo ($not->curso==$curso->curso)?"selected=''":''; ?> value="<?php echo $curso->curso ?>"><?php echo "$curso->curso - $curso->desc1" ?></option>	
							<?php } ?>				
						</select>
					</td>
					<td>
						<input required=""  data-mask='00-00' size="5" type="text" name="year[]" placeholder="00-00"  value="<?php echo $not->year ?>">
					</td>
					<td>
						<input type="hidden" name="id[]" value="<?php echo $not->id ?>">						
						<a id="<?php echo $not->id ?>" href="#" class="del btn btn-primary mx-auto">-</a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div style="width: 50%; margin:0 auto">
		<a id="add" class="btn btn-primary mx-auto" href="#"><?= $lang->translation('Añadir') ?></a>		
		<center><input class="btn btn-primary mx-auto" type="submit" name="guardar" value="<?= $lang->translation('Guardar') ?>"></center>		
	</div>
	</form>

    </div>


<?php 

$cursos = DB::table('cursos')->select("DISTINCT curso, desc1")->orderBy('curso')->get();
$grados = DB::table('year')->select("DISTINCT grado")->orderBy('grado')->get();

?>
<?php endif ?>
<script type="text/javascript" src="../../../js/jquery.js"></script>
<script type="text/javascript" src="../../../js/jquery.mask.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		 /*$('tbody').on('mask', '.year', function(event) {
		 	event.preventDefault();
		 	
		 });*/
		$("#add").click(function(event) {
			$("#notas").append(
				'<tr class="color">'+
					'<td><input data-mask="000" type="text" name="nota1[]" size="5"></td>'+
					'<td><input data-mask="000" type="text" name="nota2[]" size="5"></td>'+
					'<td>'+
						'<select required="" name="grado[]">'+
							<?php 
                          foreach ($grados as $grado) 
                                  {
							
							?>
								'<option value="<?php echo $grado->grado ?>"><?php echo "$grado->grado" ?></option>	'+
							<?php } ?>
						'</select>'+
					'</td>'+
					'<td>'+
						'<select required="" name="curso[]">'+
							<?php 
                          foreach ($cursos as $curso) 
                                  {

							?>
								'<option value="<?php echo $curso->curso ?>"><?php echo "$curso->curso - $curso->desc1" ?></option>	'+
							<?php } ?>
						'</select>'+
					'</td>'+
					'<td>'+
						'<input required="" data-mask="00-00" size="5" type="text" name="year[]" placeholder="00-00">'+
					'</td>'+
					'<td>'+
						'<a href="#" class="del myButton">-</a>'+
					'</td>'+
				'</tr>');
			$.applyDataMask('input');
		});

		$('tbody').on('click', '.del', function(event) {
			event.preventDefault();	
			if (confirm('You want to delete it?')) {
				var $tr = $(this);
				var $id = $tr.attr('id');
				console.log($id);					
				if ($id != undefined) {					
					$.post('addedit_guardar.php', {'borrar':$id}, function(data, textStatus, xhr) {
						$tr.parents('tr').remove();					
					});
				}else{
					$tr.parents('tr').remove();					

				}

			}
		});


	});
</script>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>
