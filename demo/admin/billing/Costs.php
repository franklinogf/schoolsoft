<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
	["Costos por grado", "Costs per grade"],
	['Grabar', 'Save'],
	['Código', 'Code'],
	['Editar', 'Edit'],
	['Borrar', 'Delete'],
	['Debe de llenar todos los campos', 'You must fill all fields'],
	['Lista de codigos', 'Codes list'],
	['Descripción', 'Description'],
	['Activo', 'Active'],
	['Costos', 'Costs'],
	['Opciones', 'Options'],
	['Agosto', 'August'],
	['Septiembre', 'September'],
	['Octubre', 'October'],
	['Noviembre', 'November'],
	['Diciembre', 'December'],
	['Enero', 'January'],
	['Febrero', 'February'],
	['Marzo', 'March'],
	['Abril', 'Abril'],
	['Mayo', 'May'],
	['Junio', 'June'],
	['Julio', 'July'],
	['Grados', 'Grades'],
	['Matri/Junio', 'Regis/June'],
	['Por Familia', 'Per Family'],
	['Estu. Nuevo', 'New Student'],
	['Grado', 'Grade'],
	['Selección', 'Selection'],
	['Si', 'Yes'],
	['No', 'No'],
	['Cambiar estado', 'Change Status'],
	['Guardar cambios', 'Save Changes'],
	['E', 'I'],
	['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],



]);

$school = new School(Session::id());
$year = $school->info('year2');

if (isset($_POST['data'])) {
	$costos = explode('~', $_POST['data']);
	foreach ($costos as $costo) {
		list($id, $activo, $grado) = explode(',', $costo);
		$thisCourse = DB::table('costos')->whereRaw("codigo = '$id' and grado = '$grado' and year = '$year'")->update([
				'activo' => $activo,
			]);
	}
}

if (isset($_POST['borra'])) {
	DB::table('costos')->where('mt', $_POST['mt1'])->delete();
}
$add2 = $_POST['add2'] ?? 0;
if (isset($_POST['add']) and $add2 == 2) {
	list($r1, $r2) = explode(", ", $_POST['desc']);
	DB::table('costos')->insert([
		'codigo' => $r1,
		'grado' => $_POST['grado'],
		'descripcion' => $r2,
		'costo' => $_POST['costo'],
		'activo' => $_POST['activo'],
		'm8' => $_POST['m8'] ?? '',
		'm9' => $_POST['m9'] ?? '',
		'm10' => $_POST['m10'] ?? '',
		'm11' => $_POST['m11'] ?? '',
		'm12' => $_POST['m12'] ?? '',
		'm1' => $_POST['m1'] ?? '',
		'm2' => $_POST['m2'] ?? '',
		'm3' => $_POST['m3'] ?? '',
		'm4' => $_POST['m4'] ?? '',
		'm5' => $_POST['m5'] ?? '',
		'm6' => $_POST['m6'] ?? '',
		'm7' => $_POST['m7'] ?? '',
		'esn' => $_POST['esn'] ?? '',
		'pf' => $_POST['pf'] ?? '',
		'year' => $year,
	]);
$add2=0;
}

if (isset($_POST['add']) and $add2 == 1) {
	list($r1, $r2) = explode(", ", $_POST['desc']);
	$thisCourse = DB::table('costos')->where('mt', $_POST['mt1'])->update([
		'codigo' => $r1,
		'grado' => $_POST['grado'],
		'descripcion' => $r2,
		'costo' => $_POST['costo'],
		'activo' => $_POST['activo'],
		'm8' => $_POST['m8'] ?? '',
		'm9' => $_POST['m9'] ?? '',
		'm10' => $_POST['m10'] ?? '',
		'm11' => $_POST['m11'] ?? '',
		'm12' => $_POST['m12'] ?? '',
		'm1' => $_POST['m1'] ?? '',
		'm2' => $_POST['m2'] ?? '',
		'm3' => $_POST['m3'] ?? '',
		'm4' => $_POST['m4'] ?? '',
		'm5' => $_POST['m5'] ?? '',
		'm6' => $_POST['m6'] ?? '',
		'm7' => $_POST['m7'] ?? '',
		'esn' => $_POST['esn'] ?? '',
		'pf' => $_POST['pf'] ?? '',
	]);
$add2=0;

}

//$add2=0;
if (isset($_POST['cambiar'])) {
	$reg4 = DB::table('costos')->where('mt', $_POST['mt1'])->first();
	$add2 = 1;
}
if (isset($_POST['new'])) {
//	$reg4 = DB::table('costos')->where('mt', $_POST['mt1'])->first();
	$add2 = 2;
}

$resultado3 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();
$resultado2 = DB::table('costos')->where('year', $year)->orderBy('grado, codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<script type="text/javascript" src="../../../jv/masked_input_1.js"></script>
<script type="text/javascript" src="../../../jv/masked_input_ex.js"></script>
<script language="JavaScript">
	function confirmar(mensaje) {
		return confirm(mensaje);
	}

	function supports_input_placeholder() {
		var i = document.createElement('input');
		return 'placeholder' in i;
	}

	if (!supports_input_placeholder()) {
		var fields = document.getElementsByTagName('INPUT');
		for (var i = 0; i < fields.length; i++) {
			if (fields[i].hasAttribute('placeholder')) {
				fields[i].defaultValue = fields[i].getAttribute('placeholder');
				fields[i].onfocus = function() {
					if (this.value == this.defaultValue) this.value = '';
				}
				fields[i].onblur = function() {
					if (this.value == '') this.value = this.defaultValue;
				}
			}
		}
	}
	document.oncontextmenu = function() {
		return false
	}
</script>

<head>
	<?php
	$title = $lang->translation('Costos por grado');
	Route::includeFile('/admin/includes/layouts/header.php');
	?>
</head>


<body>
	<?php
	Route::includeFile('/admin/includes/layouts/menu.php');
	?>
	<div class="container-lg mt-lg-3 mb-5 px-0">
		<h1 class="text-center mb-3 mt-5"><?= $lang->translation('Costos por grado') ?></h1>
		<div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
			<div class="div">
				<button class="btn btn-primary" style="width:200px" name="cambiar" id="activar"><?= $lang->translation('Cambiar estado') ?></button>
				<form id="cost" name="cost" method="post">
					<input type=hidden name='data' id='data' value='88 '>
				</form>

				<form id="new" name="new" method="post">
   				    <button class="btn btn-primary" style="width:200px" name="new" type="submit"><?= $lang->translation('Nuevo') ?></button>
				</form>


			</div>



			<form method="post" action="">
				<div class="style11">
					<table align="center" cellpadding="2" cellspacing="0" style="width: 750px">
						<tr>
							<td style="width: 75">
								<center><strong><?= $lang->translation('Grados') ?></strong></center>
							</td>
							<td style="width: 75">
								<center><strong><?= $lang->translation('Código') ?></strong></center>
							</td>
							<td style="width: 150">
								<center><strong><?= $lang->translation('Descripción') ?></strong></center>
							</td>
							<td style="width: 75">
								<center><strong><?= $lang->translation('Activo') ?></strong></center>
							</td>
							<td style="width: 75">
								<center><strong><?= $lang->translation('Costos') ?></strong></center>
							</td>
							<td style="width: 400">
								<center><strong><?= $lang->translation('Opciones') ?></strong></center>
							</td>
						</tr>
						<?php foreach ($resultado2 as $row2): ?>

							<form method="post">

								<tr>
									<td class="style9" style="width: 75">
										<?= $row2->grado ?>
									</td>
									<td class="style4">
										<center>
											<?= $row2->codigo ?></center>
									</td>
									<td class="style2">
										<?= $row2->descripcion ?>
									</td>
									<td class="style4 activo">
										<center>
											<?= $lang->translation($row2->activo) ?></center>
									</td>
									<td class="">
										<center>
											<?= $row2->costo ?></center>
									</td>
									<td class="style4" style="width: 400">
										<center>
											<strong>
												<input class="btn btn-danger" name="borra" style="width: 90px;" type="submit" formnovalidate value="<?= $lang->translation('Borrar') ?>" onclick="return confirmar('<?= $lang->translation('Estás seguro que desea eliminar el costo?') ?>')" />
												&nbsp;
												<input class="btn btn-primary" name="cambiar" style="width: 90px" type="submit" formnovalidate value="<?= $lang->translation('Editar') ?>" /></strong>
										</center>
									</td>
								</tr>
								<input type=hidden name=nn value='<?= $row2->codigo ?? '' ?>'>
								<input type=hidden name=nn1 value='<?= $row2->grado ?? '' ?>'>
								<input type=hidden name=mt1 value='<?= $row2->mt ?? 0 ?>'>
								<input type=hidden name=add2 value='<?= $add2 ?> '>
							</form>



						<?php endforeach ?>

						<tr>
							<td><strong><?= $lang->translation('Grado') ?></strong></td>
							<td><strong><?= $lang->translation('Código') ?></strong></td>
							<td><strong><?= $lang->translation('Descripción') ?></strong></td>
							<td><strong><?= $lang->translation('Activo') ?></strong></td>
							<td><strong><?= $lang->translation('Costos') ?></strong></td>
							<td><strong><?= $lang->translation('Opciones') ?></strong></td>
						</tr>
						<?php if ($add2 > 0): ?>
							<tr>
								<td class="style4">
									<input id="ex-2" maxlength="5" name="grado" size="5" type="text" placeholder="  -  " required value="<?= $reg4->grado ?? '' ?>" />
								</td>
								<td class="style4">
									<?= $reg4->codigo ?? '' ?>
								</td>
								<td class="style9">
									<select name="desc" required style="width: 190px">
										<option value="Selección"><?= $lang->translation('Selección') ?></option>
										<?php
										if ($add2 == 1) {
											echo '<option selected="">' . $reg4->codigo . ', ' . $reg4->descripcion . '</option>';
										}
										foreach ($resultado3 as $row3) {
											echo '<option>' . $row3->codigo . ', ' . $row3->descripcion . '</option>';
										}
										?>
									</select>
								</td>
								<td class="style4">

									<select name="activo" style="width: 46px">
										<option <?= $reg4->activo ?? 'No' === 'No' ? 'selected=""' : '' ?> value="No">No</option>
										<option <?= $reg4->activo ?? 'Si' === 'Si' ? 'selected=""' : '' ?> value="Si"><?= $lang->translation('Si') ?></option>
									</select>
								</td>
								<td class="style4">
									<input id="ex-99" name="costo" class="text" size="7" type="text" maxlength="7" placeholder="$999.99" required value="<?= $reg4->costo ?? '' ?>" />
								</td>
								<td class="style4">
									<strong>

										<input type=hidden name=nn0 value=''>
										<input type=hidden name=nn11 value=''>
										<input type=hidden name=mt value='<?= $reg4->mt ?? 0 ?>'>
										<input type=hidden name=add2 value='<?= $add2 ?>'>
									</strong>
								</td>
							</tr>
							<tr>
								<td class="style7">&nbsp;</td>
								<td class="style7">&nbsp;</td>
								<td class="style7">&nbsp;</td>
								<td class="style7">&nbsp;</td>
								<td class="style7">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
					</table>
					<table align="center" cellpadding="2" cellspacing="0" style="width: 600px">
						<tr>
							<td>
								<center><strong><?= $lang->translation('Agosto') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Septiembre') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Octubre') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Noviembre') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Diciembre') ?></strong></center>
							</td>
						</tr>
						<tr>
							<td class="style4">
								<center>
									<input <?= ($reg4->m8 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m8" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
     							<input <?= ($reg4->m9 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m9" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
     							<input <?= ($reg4->m10 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m10" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m11 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m11" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m12 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m12" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
						</tr>
						<tr>
							<td>
								<center><strong><?= $lang->translation('Enero') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Febrero') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Marzo') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Abril') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Mayo') ?></strong></center>
							</td>
						</tr>
						<tr>
							<td class="style4">
								<center>
									<input <?= ($reg4->m1 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m1" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m2 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m2" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m3 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m3" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m4 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m4" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m5 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m5" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
						</tr>
						<tr>
							<td>
								<center><strong><?= $lang->translation('Matri/Junio') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Julio') ?></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Por Familia') ?></strong></center>
							</td>
							<td>
								<center><strong></strong></center>
							</td>
							<td>
								<center><strong><?= $lang->translation('Estu. Nuevo') ?></strong></center>
							</td>
						</tr>
						<tr>
							<td class="style4">
								<center>
									<input <?= ($reg4->m6 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m6" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->m7 ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="m7" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->pf ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="pf" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
							<td class="style4">&nbsp;</td>
							<td class="style4">
								<center>
									<input <?= ($reg4->esn ?? '' === 'Si') ? 'checked="checked"' : '' ?> name="esn" type="checkbox" value="Si" style="height: 25px; width: 25px">
								</center>
							</td>
						</tr>
					<?php endif ?>
					<tr>
						<td class="style7">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td class="style7">&nbsp;</td>
						<td class="style7" style="width: 116px">&nbsp;</td>
					</tr>
					</table>
					<strong>
						<center>

							<br>

							<input class="btn btn-primary" name="add" style="width: 130px" type="submit" value="<?= $lang->translation('Grabar') ?>" />


						</center>
					</strong><br />
				</div>
			</form>

		</div>

	</div>
	<?php
	$jqMask = true;
	Route::includeFile('/includes/layouts/scripts.php', true);
	?>


	<script type="text/javascript">
		$(document).ready(function() {
			$("#activar").click(function(event) {
				if ($('#activar').prop('name') == 'cambiar') {
					$('.activo').each(function(index, el) {
						var $val = $.trim($(this).text());
						var $sel1 = '';
						var $sel2 = '';
						var t = '';
						$t = '<?= $lang->translation("E") ?>';
						if ($val == 'No') {
							$sel2 = 'selected=""'
						}
						if ($val === "No") {
							$sel2 = 'selected=""'
						}
						var $id = $('.activo').eq(index).prev().prev().text();
						$id = $.trim($id);
						$(this).html('<select name="' + $id + '"><option ' + $sel1 + ' value="Si"><?= $lang->translation("Si") ?></option><option ' + $sel2 + ' value="No">No</option></select>');
					});
					if ($t == 'E') {
						$('#activar').text('Guardar cambios').prop('name', 'guardar');
					}
					if ($t == 'I') {
						$('#activar').text('Save Changes').prop('name', 'guardar');
					}
				} else if ($('#activar').prop('name') == 'guardar') {
					console.log("GUARDAR");
					var $activos = '';
					$('.activo').each(function(index, el) {
						if (index != 0) $activos += '~';
						$activos += $(this).find('select').prop('name') + ',' + $(this).find('select').val() + ',' + $(this).prev().prev().prev().text().trim();
					});
					console.log($activos);
					$.post('costoActivar.php', {
						'costos': $activos
					}, function(data, textStatus, xhr) {

						//                alert($activos);
						var form = document.getElementById("cost");
						document.getElementById("data").value = $activos;
						form.submit();

						console.log(data);
						$('.activo select').each(function(index, el) {
							var $val = $(this).val();
							$(this).parent().text($val);
						});


						$('#activar').text('Cambiar estado').prop('name', 'cambiar');
					});

				}
			});
		});
	</script>
</body>

</html>