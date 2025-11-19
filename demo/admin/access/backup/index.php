<?php

require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$years = Manager::table('year')->select('year')->orderByDesc('year')->distinct()->pluck('year')->toArray();
?>

<?php Route::includeFile('/admin/includes/layouts/head.php'); ?>

<div class="container text-center mt-5 pb-10">
	<h2><?= __('Descargar Backup') ?></h2>
	<div class="alert alert-warning" role="alert">
		<i class="fa fa-exclamation-triangle"></i>
		<strong><?= __('Nota') ?>:</strong> <?= __('Crear un nuevo backup eliminará el backup anterior.') ?>
	</div>

	<!-- Year Filter Section -->
	<div class="card mb-4 mx-auto" style="max-width: 500px;">
		<div class="card-body">
			<h5 class="card-title"><i class="fa fa-filter"></i> <?= __('Filtro de Año') ?></h5>
			<p class="text-muted small"><?= __('Seleccione un año específico para incluir solo los datos de ese año para las tablas que tienen una columna de año. Déjelo vacío para respaldar todos los datos.') ?></p>
			<div class="row align-items-center">
				<div class="col-md-8">
					<select class="form-control" id="yearFilter">
						<option value=""><?= __('Todos los años (Backup Completo)') ?></option>
						<?php foreach ($years as $year): ?>
							<option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-4">
					<span class="badge badge-info" id="yearBadge" style="display: none;">
						<i class="fa fa-info-circle"></i> <?= __('Filtrado') ?>
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class="alert alert-info d-none" role="alert" id="backgroundInfo">
		<i class="fa fa-info-circle"></i>
		<?= __('El backup se está ejecutando en segundo plano. Puedes navegar y continuar trabajando—tu backup estará listo cuando termine!') ?>
	</div>
	<button class="btn btn-primary mb-4" id="backupBtn"><?= __('Iniciar Backup') ?></button>
	<div id="statusText" class="mt-3 font-weight-bold"></div>
	<div class="progress mt-3" style="height: 24px;">
		<div id="bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
	</div>
	<div id="lastBackup" class="mt-4 text-muted">
		<?= __('Último backup') ?>: <span id="lastBackupTime"><?= __('Ninguno') ?></span>
		<br>
		<button class="btn btn-success btn-sm mt-2 d-none" id="downloadBtn">
			<i class="fa fa-download"></i> <?= __('Descargar Último Backup') ?>
		</button>
	</div>
</div>



<?php Route::includeFile('/admin/includes/layouts/footer.php'); ?>

<script>
	const btn = document.getElementById('backupBtn');
	const statusText = document.getElementById('statusText');
	const bar = document.getElementById('bar');
	const lastBackupTime = document.getElementById('lastBackupTime');
	const downloadBtn = document.getElementById('downloadBtn');
	const backgroundInfo = document.getElementById('backgroundInfo');
	const yearFilter = document.getElementById('yearFilter');
	const yearBadge = document.getElementById('yearBadge');

	// Translation strings
	const translations = {
		none: <?= json_encode(__('Ninguno')) ?>,
		starting: <?= json_encode(__('Iniciando...')) ?>,
		dumping: <?= json_encode(__('Exportando base de datos...')) ?>,
		zipping: <?= json_encode(__('Comprimiendo backup...')) ?>,
		complete: <?= json_encode(__('Backup completo! Descargando...')) ?>,
		error: <?= json_encode(__('Error al crear backup.')) ?>,
		startingBackup: <?= json_encode(__('Iniciando backup...')) ?>,
		resuming: <?= json_encode(__('Reanudando progreso del backup...')) ?>,
		completedAway: <?= json_encode(__('Tu backup se completó mientras estabas ausente!')) ?>,
		errorAway: <?= json_encode(__('El backup encontró un error mientras estabas ausente.')) ?>
	};

	let lastBackupId = null;
	let currentInterval = null;
	let currentBackupId = null;

	// Show badge when year is selected
	yearFilter.addEventListener('change', () => {
		if (yearFilter.value) {
			yearBadge.style.display = 'inline-block';
		} else {
			yearBadge.style.display = 'none';
		}
	});

	async function fetchLastBackup() {
		try {
			const resp = await fetch('check_status.php');
			const data = await resp.json();
			if (data.backup_id && data.status === 'done') {
				lastBackupId = data.backup_id;
				const timestamp = data.backup_id.replace('backup_', '').replace(/_/g, ':').replace('T', ' ');
				lastBackupTime.textContent = timestamp.replace(/:(\d\d)$/, ' $1');
				downloadBtn.classList.remove('d-none');
			} else {
				downloadBtn.classList.add('d-none');
			}
		} catch (e) {
			console.warn('No last backup found');
			downloadBtn.classList.add('d-none');
		}
	}

	downloadBtn.addEventListener('click', () => {
		if (lastBackupId) {
			window.location = `download.php?id=${lastBackupId}`;
		}
	});

	// Automatically save backup ID when user navigates away
	window.addEventListener('beforeunload', () => {
		if (currentBackupId && currentInterval) {
			sessionStorage.setItem('activeBackupId', currentBackupId);
		}
	});

	function startPolling(backupId) {
		currentBackupId = backupId;
		btn.disabled = true;

		// Show info message
		backgroundInfo.classList.remove('d-none');

		// Start polling immediately
		currentInterval = setInterval(async () => {
			const resp = await fetch(`check_status.php?id=${backupId}`);
			const sdata = await resp.json();

			switch (sdata.status) {
				case 'starting':
					bar.style.width = '10%';
					statusText.textContent = translations.starting;
					break;
				case 'dumping':
					bar.style.width = '50%';
					statusText.textContent = translations.dumping;
					break;
				case 'zipping':
					bar.style.width = '80%';
					statusText.textContent = translations.zipping;
					break;
				case 'done':
					bar.style.width = '100%';
					statusText.textContent = translations.complete;
					clearInterval(currentInterval);
					currentInterval = null;
					backgroundInfo.classList.add('d-none');
					sessionStorage.removeItem('activeBackupId');
					window.location = `download.php?id=${backupId}`;
					await fetchLastBackup(); // update display after download
					btn.disabled = false;
					break;
				case 'error':
					bar.style.width = '100%';
					bar.classList.remove('bg-success');
					bar.classList.add('bg-danger');
					statusText.textContent = translations.error;
					clearInterval(currentInterval);
					currentInterval = null;
					backgroundInfo.classList.add('d-none');
					sessionStorage.removeItem('activeBackupId');
					btn.disabled = false;
					break;
				default:
					clearInterval(currentInterval);
					currentInterval = null;
					backgroundInfo.classList.add('d-none');
					btn.disabled = false;
					break;
			}
		}, 2000);
	}

	btn.addEventListener('click', async () => {
		statusText.textContent = translations.startingBackup;
		bar.style.width = '10%';
		bar.classList.remove('bg-danger');
		bar.classList.add('bg-success');
		backgroundInfo.classList.add('d-none');

		const formData = new FormData();
		if (yearFilter.value) {
			formData.append('year', yearFilter.value);
		}
		console.log('Starting backup with year:', yearFilter.value);

		const res = await fetch('start_backup.php', {
			method: 'POST',
			body: formData
		});

		const data = await res.json();
		console.log('Backup started:', data);
		const backupId = data.backup_id;
		const createdAt = data.created_at;

		if (createdAt) {
			lastBackupTime.textContent = createdAt.replace(/_/g, ':').replace('T', ' ');
		}

		// Start polling
		startPolling(backupId);
	});

	// Check if there's an active backup when page loads
	async function checkActiveBackup() {
		const activeBackupId = sessionStorage.getItem('activeBackupId');
		if (activeBackupId) {
			try {
				const resp = await fetch(`check_status.php?id=${activeBackupId}`);
				const data = await resp.json();

				if (data.status === 'done') {
					// Backup completed while user was away
					sessionStorage.removeItem('activeBackupId');
					bar.style.width = '100%';
					statusText.textContent = '✓ ' + translations.completedAway;
					statusText.classList.add('text-success');
					await fetchLastBackup();
				} else if (data.status === 'error') {
					sessionStorage.removeItem('activeBackupId');
					bar.style.width = '100%';
					bar.classList.add('bg-danger');
					statusText.textContent = '✗ ' + translations.errorAway;
					statusText.classList.add('text-danger');
				} else {
					// Still in progress, automatically resume watching
					statusText.textContent = translations.resuming;
					statusText.classList.add('text-info');

					// Update progress bar based on current status
					switch (data.status) {
						case 'starting':
							bar.style.width = '10%';
							break;
						case 'dumping':
							bar.style.width = '50%';
							break;
						case 'zipping':
							bar.style.width = '80%';
							break;
					}

					// Automatically resume polling
					startPolling(activeBackupId);
				}
			} catch (e) {
				sessionStorage.removeItem('activeBackupId');
			}
		}
	}

	// Load last backup timestamp on page load
	fetchLastBackup();
	checkActiveBackup();
</script>