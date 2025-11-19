<?php

use Classes\DataBase\DB;
use Classes\Route;

require_once __DIR__ . '/../app.php';

$items = DB::table('inventario')->orderBy('articulo')->get();
$isAdding = isset($_GET['add']);
$item_id = $_GET['item'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
	<?php $title = "Inventario Cafetería" ?>
	<?php Route::includeFile("/cafeteria/includes/layouts/header.php"); ?>
	<style>
		/* Bootstrap 4.6 Enhanced Styles */
		.inventory-container {
			max-width: 500px;
			margin: 0 auto;
		}

		.card {
			border: none;
			box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
			transition: box-shadow 0.3s ease;
		}

		.card:hover {
			box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
		}

		.page-header {
			background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
			color: white;
			padding: 2rem 0;
			margin-bottom: 2rem;
			border-radius: 0 0 1rem 1rem;
		}

		.btn-group-actions {
			gap: 0.5rem;
		}

		.form-control:focus {
			border-color: #007bff;
			box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
		}

		.btn {
			transition: all 0.3s ease;
		}

		.btn:hover {
			transform: translateY(-1px);
		}

		.btn:active {
			transform: translateY(0);
		}

		.alert {
			border: none;
			border-radius: 0.5rem;
		}

		.form-group label {
			font-weight: 600;
			color: #495057;
		}

		.badge-required {
			background-color: #dc3545;
			font-size: 0.75rem;
		}

		.search-section {
			background-color: #f8f9fa;
			border-radius: 0.5rem;
			padding: 1.5rem;
			margin-bottom: 1.5rem;
		}

		.form-section {
			background-color: #ffffff;
			border-radius: 0.5rem;
			padding: 1.5rem;
		}

		@media (max-width: 576px) {
			.inventory-container {
				max-width: 100%;
				padding: 0 1rem;
			}

			.page-header {
				padding: 1.5rem 0;
			}

			.btn-group-actions {
				flex-direction: column;
			}

			.btn-group-actions .btn {
				margin-bottom: 0.5rem;
			}
		}
	</style>
</head>

<body class="bg-light">
	<!-- Page Header -->
	<div class="page-header">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center">
					<h1 class="display-4 mb-2">
						<i class="fas fa-boxes"></i> Inventario de Cafetería
					</h1>
					<p class="lead mb-0">Administración de artículos</p>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="inventory-container">
			<!-- Action Buttons -->
			<div class="d-flex justify-content-center btn-group-actions mb-4">
				<a class="btn btn-outline-secondary" href="./menu.php">
					<i class="fas fa-arrow-left"></i> Regresar al Menú
				</a>
				<?php if (!$isAdding): ?>
					<a class="btn btn-success" href="./inventario.php?add">
						<i class="fas fa-plus"></i> Agregar Artículo
					</a>
				<?php else: ?>
					<a class="btn btn-secondary" href="./inventario.php">
						<i class="fas fa-times"></i> Cancelar
					</a>
				<?php endif; ?>
			</div>

			<!-- Main Card -->
			<div class="card">
				<div class="card-header bg-primary text-white">
					<h5 class="card-title mb-0">
						<i class="fas fa-<?= $isAdding ? 'plus' : 'search' ?>"></i>
						<?= $isAdding ? 'Agregar Nuevo Artículo' : 'Buscar Artículo' ?>
					</h5>
				</div>
				<div class="card-body">
					<?php if (!$isAdding): ?>
						<!-- Search Section -->
						<div class="search-section">
							<form method="GET" class="needs-validation" novalidate>
								<div class="form-group">
									<label for="item" class="form-label">
										<i class="fas fa-search"></i> Seleccionar Artículo
										<span class="badge badge-required ml-1">Requerido</span>
									</label>
									<select name="item" id="item" class="form-control form-control-lg" required>
										<option value="">-- Seleccione un artículo --</option>
										<?php foreach ($items as $item) : ?>
											<option <?= $item_id !== null && intval($item_id) === $item->id ? 'selected' : '' ?>
												value="<?= $item->id ?>">
												<?= htmlspecialchars($item->articulo) ?>
											</option>
										<?php endforeach; ?>
									</select>
									<div class="invalid-feedback">
										Por favor seleccione un artículo.
									</div>
								</div>
								<button class="btn btn-primary btn-lg btn-block" type="submit">
									<i class="fas fa-search"></i> Buscar Artículo
								</button>
							</form>
						</div>
					<?php endif ?>
					<?php if ($item_id !== null || $isAdding):
						if (!$isAdding) {
							$item = DB::table('inventario')->where('id', $item_id)->first();
						}
					?>
						<!-- Form Section -->
						<div class="form-section">
							<?php if (!$isAdding && $item): ?>
								<div class="alert alert-info" role="alert">
									<i class="fas fa-info-circle"></i>
									<strong>Editando:</strong> <?= htmlspecialchars($item->articulo) ?>
								</div>
							<?php elseif ($isAdding): ?>
								<div class="alert alert-success" role="alert">
									<i class="fas fa-plus-circle"></i>
									<strong>Agregando nuevo artículo</strong>
								</div>
							<?php endif; ?>

							<form action="<?= Route::url('/cafeteria/includes/inventario.php') ?>" method="POST" class="needs-validation" novalidate>
								<?php if (!$isAdding) : ?>
									<input type="hidden" name="id" value="<?= $item->id ?>">
								<?php endif; ?>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="id2" class="form-label">
												<i class="fas fa-hashtag"></i> ID del Artículo
											</label>
											<input type="text"
												class="form-control"
												name="id2"
												id="id2"
												value="<?= htmlspecialchars($item->id2 ?? '') ?>"
												placeholder="Ej: CAF001">
											<small class="form-text text-muted">Identificador único del artículo</small>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label for="cbarra" class="form-label">
												<i class="fas fa-barcode"></i> Código de Barras
											</label>
											<input type="text"
												class="form-control"
												name="cbarra"
												id="cbarra"
												value="<?= htmlspecialchars($item->cbarra ?? '') ?>"
												placeholder="Código de barras">
											<small class="form-text text-muted">Código de barras del producto</small>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="articulo" class="form-label">
										<i class="fas fa-tag"></i> Nombre del Artículo
										<span class="badge badge-required ml-1">Requerido</span>
									</label>
									<input type="text"
										class="form-control form-control-lg"
										name="articulo"
										id="articulo"
										value="<?= htmlspecialchars($item->articulo ?? '') ?>"
										placeholder="Ej: Refresco de Cola"
										required>
									<div class="invalid-feedback">
										Por favor ingrese el nombre del artículo.
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label for="precio" class="form-label">
												<i class="fas fa-dollar-sign"></i> Precio
												<span class="badge badge-required ml-1">Requerido</span>
											</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">$</span>
												</div>
												<input type="number"
													step="0.01"
													min="0"
													class="form-control"
													name="precio"
													id="precio"
													value="<?= htmlspecialchars($item->precio ?? '') ?>"
													placeholder="0.00"
													required>
												<div class="invalid-feedback">
													Por favor ingrese un precio válido.
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label for="cantidad" class="form-label">
												<i class="fas fa-cubes"></i> Cantidad en Stock
												<span class="badge badge-required ml-1">Requerido</span>
											</label>
											<input type="number"
												min="0"
												class="form-control"
												name="cantidad"
												id="cantidad"
												value="<?= htmlspecialchars($item->cantidad ?? '') ?>"
												placeholder="0"
												required>
											<div class="invalid-feedback">
												Por favor ingrese la cantidad en stock.
											</div>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label for="minimo" class="form-label">
												<i class="fas fa-exclamation-triangle"></i> Stock Mínimo
											</label>
											<input type="number"
												min="0"
												class="form-control"
												name="minimo"
												id="minimo"
												value="<?= htmlspecialchars($item->minimo ?? '') ?>"
												placeholder="0">
											<small class="form-text text-muted">Cantidad mínima antes de alerta</small>
										</div>
									</div>
								</div>

								<!-- Action Buttons -->
								<div class="row mt-4">
									<div class="col-12">
										<button class="btn btn-success btn-lg btn-block mb-2" type="submit" name="<?= $isAdding ? 'add' : 'edit' ?>">
											<i class="fas fa-save"></i> <?= $isAdding ? 'Agregar Artículo' : 'Actualizar Artículo' ?>
										</button>
										<?php if (!$isAdding) : ?>
											<button class="btn btn-danger btn-lg btn-block" type="submit" name="delete"
												onclick="return confirm('¿Está seguro que desea eliminar este artículo? Esta acción no se puede deshacer.')">
												<i class="fas fa-trash"></i> Eliminar Artículo
											</button>
										<?php endif; ?>
									</div>
								</div>
							</form>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap JavaScript and Custom Scripts -->
	<?php Route::includeFile('/includes/layouts/scripts.php', true); ?>

	<script>
		// Bootstrap 4.6 Form Validation
		(function() {
			'use strict';

			// Fetch all forms with validation
			var forms = document.querySelectorAll('.needs-validation');

			// Loop over forms and prevent submission
			Array.prototype.slice.call(forms).forEach(function(form) {
				form.addEventListener('submit', function(event) {
					if (!form.checkValidity()) {
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});

			// Real-time validation
			var inputs = document.querySelectorAll('.form-control[required]');
			Array.prototype.slice.call(inputs).forEach(function(input) {
				input.addEventListener('blur', function() {
					if (this.checkValidity()) {
						this.classList.remove('is-invalid');
						this.classList.add('is-valid');
					} else {
						this.classList.remove('is-valid');
						this.classList.add('is-invalid');
					}
				});
			});

			// Auto-focus on first input when form is visible
			var firstInput = document.querySelector('.form-section .form-control');
			if (firstInput) {
				setTimeout(function() {
					firstInput.focus();
				}, 300);
			}

			// Format price input
			var priceInput = document.getElementById('precio');
			if (priceInput) {
				priceInput.addEventListener('input', function() {
					var value = parseFloat(this.value);
					if (!isNaN(value)) {
						this.value = value.toFixed(2);
					}
				});
			}

			// Enhance select2 for better UX (if available)
			if (typeof $.fn.select2 !== 'undefined') {
				$('#item').select2({
					placeholder: '-- Seleccione un artículo --',
					allowClear: true,
					theme: 'bootstrap4'
				});
			}

			// Add loading state to form submission
			var forms = document.querySelectorAll('form');
			Array.prototype.slice.call(forms).forEach(function(form) {
				form.addEventListener('submit', function() {
					var submitBtn = this.querySelector('[type="submit"]');
					if (submitBtn && this.checkValidity()) {
						var originalText = submitBtn.innerHTML;
						submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
						submitBtn.disabled = true;

						// Re-enable after 3 seconds as fallback
						setTimeout(function() {
							submitBtn.innerHTML = originalText;
							submitBtn.disabled = false;
						}, 3000);
					}
				});
			});
		})();
	</script>
</body>

</html>