<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
	["Códigos de Presupuesto", "Budget codes"],
	['Grabar', 'Save'],
	['Código', 'Code'],
	['Editar', 'Edit'],
	['Añadir', 'Add'],
	['Borrar', 'Delete'],
	['Debe de llenar todos los campos', 'You must fill all fields'],
	['Lista de codigos', 'Codes list'],
	['Descripción', 'Description'],
	['Precio', 'Price'],
	['Cantidad', 'Amount'],
	['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');
$add2 = $_GET['add2'] ?? 0;

if (isset($_POST['borra'])) {
	DB::table('presupuesto')->where('mt', $_POST['mt'])->delete();
}

if (isset($_POST['add']) and $add2 == 0) {
	DB::table('presupuesto')->insert([
		'codigo' => $_POST['codigo'],
		'descripcion' => $_POST['descripcion'],
		'cantidad' => $_POST['bajo_nivel'],
		'costo' => $_POST['sobre_nivel'],
		'year' => $year,
	]);
}

if (isset($_POST['add']) and $add2 == 1) {
	$thisCourse = DB::table('presupuesto')->where('mt', $_POST['mt'])->update([
		'codigo' => $_POST['codigo'],
		'descripcion' => $_POST['descripcion'],
		'cantidad' => $_POST['bajo_nivel'],
		'costo' => $_POST['sobre_nivel'],
	]);
}

$add2 = 0;
if (isset($_POST['cambiar'])) {
	$reg4 = DB::table('presupuesto')->where('mt', $_POST['mt'])->first();
	$add2 = 1;
}

$codes = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
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
	$title = $lang->translation('Códigos de Presupuesto');
	Route::includeFile('/admin/includes/layouts/header.php');
	?>
</head>

<body>
	<?php
	Route::includeFile('/admin/includes/layouts/menu.php');
	?> <div class="container-lg mt-lg-3 mb-5 px-0">
		<h1 class="text-center mb-4 mt-5"><?= $lang->translation('Códigos de Presupuesto') ?></h1>
		<div class="row justify-content-center">
			<div class="col-lg-10 col-xl-8">
				<div class="card shadow-lg">
					<div class="card-header bg-primary text-white">
						<h5 class="card-title mb-0"><?= $lang->translation('Lista de codigos') ?></h5>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover table-striped mb-0">
								<thead class="thead-dark">
									<tr>
										<th scope="col"><?= $lang->translation('Código') ?></th>
										<th scope="col"><?= $lang->translation('Descripción') ?></th>
										<th scope="col" class="text-right"><?= $lang->translation('Cantidad') ?></th>
										<th scope="col" class="text-right"><?= $lang->translation('Precio') ?></th>
										<th scope="col" class="text-center"><?= $lang->translation('Opciones') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($codes as $code): ?>
										<tr>
											<td><strong><?= $code->codigo ?></strong></td>
											<td><?= $code->descripcion ?></td>
											<td class="text-right"><?= number_format($code->cantidad, 2) ?></td>
											<td class="text-right">$<?= number_format($code->costo, 2) ?></td>
											<td class="text-center">
												<form method="post" class="d-inline">
													<input type="hidden" name="nn" value="<?= $code->codigo ?>">
													<input type="hidden" name="mt" value="<?= $code->mt ?>">
													<input type="hidden" name="add2" value="<?= $add2 ?>">
													<button type="submit" name="cambiar" class="btn btn-sm btn-outline-primary mr-1" title="<?= $lang->translation('Editar') ?>">
														<i class="fas fa-edit"></i>
													</button>
												</form>
												<form method="post" class="d-inline">
													<input type="hidden" name="nn" value="<?= $code->codigo ?>">
													<input type="hidden" name="mt" value="<?= $code->mt ?>">
													<input type="hidden" name="add2" value="<?= $add2 ?>">
													<button type="submit" name="borra" class="btn btn-sm btn-outline-danger"
														onclick="return confirmar('¿Está seguro que desea eliminar este código?')"
														title="<?= $lang->translation('Borrar') ?>">
														<i class="fas fa-trash"></i>
													</button>
												</form>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<!-- Add/Edit Form Card -->
				<div class="card shadow-lg mt-4">
					<div class="card-header bg-success text-white">
						<h5 class="card-title mb-0">
							<?= $add2 == 1 ? $lang->translation('Editar') . ' Código' : $lang->translation('Añadir') . ' Nuevo Código' ?>
						</h5>
					</div>
					<div class="card-body">
						<form method="post" action="budget.php?add2=<?= $add2 ?>">
							<div class="row">
								<div class="col-md-3">
									<div class="form-group">
										<label for="codigo" class="form-label font-weight-bold"><?= $lang->translation('Código') ?></label>
										<input type="text"
											class="form-control"
											id="codigo"
											name="codigo"
											maxlength="2"
											required
											value="<?= $add2 == 1 ? ($reg4->codigo ?? '') : '' ?>"
											placeholder="01">
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										<label for="descripcion" class="form-label font-weight-bold"><?= $lang->translation('Descripción') ?></label>
										<input type="text"
											class="form-control"
											id="descripcion"
											name="descripcion"
											maxlength="50"
											required
											value="<?= $add2 == 1 ? ($reg4->descripcion ?? '') : '' ?>"
											placeholder="<?= $lang->translation('Descripción') ?>">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="bajo_nivel" class="form-label font-weight-bold"><?= $lang->translation('Cantidad') ?></label>
										<input type="number"
											class="form-control"
											id="bajo_nivel"
											name="bajo_nivel"
											step="0.01"
											min="0"
											value="<?= $add2 == 1 ? ($reg4->cantidad ?? '') : '' ?>"
											placeholder="0.00">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="sobre_nivel" class="form-label font-weight-bold"><?= $lang->translation('Precio') ?></label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											<input type="number"
												class="form-control"
												id="sobre_nivel"
												name="sobre_nivel"
												step="0.01"
												min="0"
												value="<?= $add2 == 1 ? ($reg4->costo ?? '') : '' ?>"
												placeholder="0.00">
										</div>
									</div>
								</div>
							</div>

							<div class="text-center mt-3">
								<input type="hidden" name="nn0" value="<?= $add2 == 1 ? ($reg4->codigo ?? '') : '' ?>">
								<input type="hidden" name="mt" value="<?= $add2 == 1 ? ($reg4->mt ?? '') : '' ?>">
								<input type="hidden" name="add2" value="<?= $add2 ?>">
								<button type="submit" name="add" class="btn btn-success btn-lg px-4">
									<i class="fas fa-save mr-2"></i><?= $lang->translation('Grabar') ?>
								</button>
								<?php if ($add2 == 1): ?>
									<a href="budget.php" class="btn btn-secondary btn-lg px-4 ml-2">
										<i class="fas fa-times mr-2"></i>Cancelar
									</a>
								<?php endif ?>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$jqMask = true;
	Route::includeFile('/includes/layouts/scripts.php', true);
	?>

</body>

</html>