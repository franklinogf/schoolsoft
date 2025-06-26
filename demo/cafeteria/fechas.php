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
	<link rel="stylesheet" href="css/fechas.css">
</head>

<body>
	<!-- Hero Section -->
	<div class="hero-section bg-primary text-white py-4 mb-4">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-8">
					<h1 class="mb-0">
						<i class="fas fa-calendar-alt mr-2"></i>
						<?= $lang->translation('Selección de fechas') ?>
					</h1>
				</div>
				<div class="col-md-4 text-md-right mt-3 mt-md-0">
					<a href="./menu.php" class="btn btn-outline-light">
						<i class="fas fa-arrow-left mr-1"></i>
						<?= $lang->translation('Regresar') ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-md-8 col-lg-6">
				<div class="date-selection-card card shadow-lg border-0">
					<div class="card-header bg-gradient-primary text-white">
						<h5 class="card-title mb-0">
							<i class="fas fa-calendar-check mr-2"></i>
							Configurar período de consulta
						</h5>
					</div>
					<div class="card-body p-4">
						<form action="<?= $_GET['pdf'] ?>.php" method="post" target="_blank" class="needs-validation" novalidate>
							<!-- Date Range Section -->
							<div class="date-range-section mb-4">
								<h6 class="section-title text-muted mb-3">
									<i class="fas fa-calendar-week mr-1"></i>
									Rango de fechas
								</h6>

								<div class="row">
									<div class="col-md-6 mb-3">
										<label for="fecha1" class="form-label font-weight-medium">
											<i class="fas fa-calendar-day mr-1 text-success"></i>
											<?= $lang->translation('Desde') ?>:
										</label>
										<input type="date"
											class="form-control form-control-lg"
											name="fecha1"
											id="fecha1"
											value="<?= date('Y-m-d') ?>"
											required>
										<div class="invalid-feedback">
											Por favor selecciona una fecha de inicio válida.
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<label for="fecha2" class="form-label font-weight-medium">
											<i class="fas fa-calendar-day mr-1 text-danger"></i>
											<?= $lang->translation('Hasta') ?>:
										</label>
										<input type="date"
											class="form-control form-control-lg"
											name="fecha2"
											id="fecha2"
											value="<?= date('Y-m-d') ?>"
											required>
										<div class="invalid-feedback">
											Por favor selecciona una fecha de fin válida.
										</div>
									</div>
								</div>

								<div class="date-validation-message" id="dateValidation" style="display: none;">
									<div class="alert alert-warning" role="alert">
										<i class="fas fa-exclamation-triangle mr-1"></i>
										La fecha de inicio debe ser anterior o igual a la fecha de fin.
									</div>
								</div>
							</div>

							<?php if ($_GET['pdf'] === 'info_cuadre') : ?>
								<!-- Report Type Section -->
								<div class="report-options-section mb-4">
									<h6 class="section-title text-muted mb-3">
										<i class="fas fa-file-alt mr-1"></i>
										Tipo de reporte
									</h6>

									<div class="option-cards">
										<div class="custom-control custom-radio option-card mb-3">
											<input type="radio"
												class="custom-control-input"
												name="opcion"
												value="1"
												id="opcion1"
												checked>
											<label class="custom-control-label" for="opcion1">
												<div class="option-content">
													<div class="option-icon">
														<i class="fas fa-list-ul text-primary"></i>
													</div>
													<div class="option-text">
														<strong>Detallada</strong>
														<small class="text-muted d-block">
															Incluye todos los detalles y transacciones
														</small>
													</div>
												</div>
											</label>
										</div>

										<div class="custom-control custom-radio option-card mb-3">
											<input type="radio"
												class="custom-control-input"
												name="opcion"
												value="2"
												id="opcion2">
											<label class="custom-control-label" for="opcion2">
												<div class="option-content">
													<div class="option-icon">
														<i class="fas fa-chart-bar text-info"></i>
													</div>
													<div class="option-text">
														<strong>Resumen</strong>
														<small class="text-muted d-block">
															Vista general con totales agregados
														</small>
													</div>
												</div>
											</label>
										</div>
									</div>
								</div>
							<?php endif ?>

							<!-- Action Buttons -->
							<div class="action-buttons d-flex justify-content-between flex-wrap">
								<a class="btn btn-outline-secondary btn-lg" href="./menu.php">
									<i class="fas fa-times mr-1"></i>
									Cancelar
								</a>
								<button type="submit" class="btn btn-success btn-lg" id="generateReport">
									<i class="fas fa-file-pdf mr-1"></i>
									Generar reporte
								</button>
							</div>
						</form>
					</div>
				</div>

				<!-- Quick Actions -->
				<div class="quick-actions mt-4">
					<div class="row">
						<div class="col-md-4 mb-2">
							<button type="button" class="btn btn-outline-primary btn-block" onclick="setToday()">
								<i class="fas fa-calendar-day mr-1"></i>
								Hoy
							</button>
						</div>
						<div class="col-md-4 mb-2">
							<button type="button" class="btn btn-outline-primary btn-block" onclick="setThisWeek()">
								<i class="fas fa-calendar-week mr-1"></i>
								Esta semana
							</button>
						</div>
						<div class="col-md-4 mb-2">
							<button type="button" class="btn btn-outline-primary btn-block" onclick="setThisMonth()">
								<i class="fas fa-calendar mr-1"></i>
								Este mes
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	</div>

	<?php
	Route::includeFile('/includes/layouts/scripts.php', true);
	?>
</body>

</html>