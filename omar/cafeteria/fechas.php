<?php
require_once '../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
	['Hasta', 'Until'],
	['Desde', 'From'],
	['Selección de fechas', 'Dates selection'],
	['Regresar', 'Go back']
]);

$school = new School(Session::id());
$year = $school->info('year2');

$resultado3 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
	<?php
	$title = $lang->translation('Selección de fechas');
	Route::includeFile('/cafeteria/includes/layouts/header.php');
	?>
</head>

<body>
	<div class="style1">

		<div class="container-lg mt-lg-3 mb-5 px-0">
			<h1 class="text-center mb-3 mt-5"><?= $lang->translation('Selección de fechas') ?></h1>
			<div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
				<form action="<?= $_GET['pdf'] ?>.php" method="post" target="_blank">
					<div class="form-group">
						<label for="fecha1"><?= $lang->translation('Desde') ?>:</label>
						<input type="date" class="form-control" name="fecha1" id="fecha1" value="<?= date('Y-m-d') ?>">
					</div>
					<div class="form-group">
						<label for="fecha2"><?= $lang->translation('Hasta') ?>:</label>
						<input type="date" class="form-control" name="fecha2" id="fecha2" value="<?= date('Y-m-d') ?>">
					</div>
					<?php if ($_GET['pdf'] === 'info_cuadre') : ?>
						<div class="form-check">
							<label class="form-check-label">
								<input type="radio" class="form-check-input" name="opcion" value="1" checked>
								Detallada
							</label>
						</div>
						<div class="form-check">
							<label class="form-check-label">
								<input type="radio" class="form-check-input" name="opcion" value="2">
								Resumen
							</label>
						</div>
					<?php endif ?>
					<div>
						<a class="btn btn-link" href="./menu.php"><?= $lang->translation('Regresar') ?></a>
						<button type="submit" class="btn btn-primary"><?= $lang->translation('Continuar') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>

</body>

</html>