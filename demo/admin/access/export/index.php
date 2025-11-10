<?php

require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$years = Manager::table('year')->select('year')->orderByDesc('year')->distinct()->pluck('year')->toArray();
?>

<?php Route::includeFile('/admin/includes/layouts/head.php'); ?>

<style>
    .export-card {
        border-radius: 15px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .export-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        padding: 12px 15px;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Override Bootstrap 4 form-control styling */
    select.form-control {
        height: auto;
    }

    .btn-primary {
        border-radius: 25px;
        padding: 12px 40px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .progress {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .progress-bar {
        font-weight: 600;
        font-size: 14px;
    }

    .btn-success {
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }

    #progressText {
        font-size: 14px;
        font-weight: 500;
        color: #555;
    }

    .history-card {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .list-group-item {
        border: none;
        border-radius: 10px !important;
        margin-bottom: 10px;
        padding: 20px;
        background: #f8f9fa;
        transition: all 0.3s;
    }

    .list-group-item:hover {
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateX(5px);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .btn-group-vertical .btn {
        border-radius: 8px !important;
        margin-bottom: 5px;
        font-size: 13px;
        padding: 8px 15px;
        transition: all 0.2s;
    }

    .btn-group-vertical .btn:hover {
        transform: scale(1.05);
    }

    .btn-sm {
        border-radius: 20px !important;
        padding: 6px 15px;
    }

    .text-success {
        color: #11998e !important;
    }

    .icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        margin-right: 10px;
    }

    .export-icon {
        font-size: 24px;
    }
</style>

<div class="container mt-5 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card export-card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0 d-flex align-items-center">
                        <span class="icon-wrapper">
                            <i class="fas fa-file-export export-icon"></i>
                        </span>
                        Exportar Datos a Excel
                    </h2>
                </div>
                <div class="card-body p-4">
                    <form id="exportForm">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="tabla" class="form-label font-weight-bold">
                                    <i class="fas fa-table mr-2"></i>Seleccionar Tabla
                                </label>
                                <select class="form-control" id="tabla" name="tabla" required>
                                    <option value="">-- Seleccione una opción --</option>
                                    <option value="students">📚 Estudiantes</option>
                                    <option value="families">👨‍👩‍👧‍👦 Familias</option>
                                    <option value="grades">📝 Notas</option>
                                    <option value="payments">💰 Pagos</option>
                                    <option value="student_documents">📄 Documentos de Estudiantes</option>
                                    <option value="food_assistance">🍽️ Asistencia Alimentaria</option>
                                    <option value="cafeteria">☕ Cafetería</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="year" class="form-label font-weight-bold">
                                    <i class="fas fa-calendar-alt mr-2"></i>Seleccionar Año Escolar
                                </label>
                                <select class="form-control" id="year" name="year" required>
                                    <option value="">-- Seleccione un año --</option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" id="start" class="btn btn-primary btn-lg">
                                <i class="fas fa-rocket mr-2"></i>Iniciar Exportación
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div id="progressContainer" class="mt-5" style="display: none;">
                            <div class="text-center mb-3">
                                <div id="progressSpinner" class="spinner-grow text-primary" role="status" style="width: 50px; height: 50px;">
                                    <span class="sr-only">Procesando...</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 35px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    0%
                                </div>
                            </div>
                            <p id="progressText" class="text-center mt-3">Preparando exportación...</p>
                            <div class="text-center">
                                <button id="downloadBtn" class="btn btn-success btn-lg mt-2" style="display: none;">
                                    <i class="fas fa-download mr-2"></i>Descargar Archivo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Export History -->
            <div class="card history-card shadow-lg mt-5">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-history mr-2"></i>
                        Historial de Exportaciones
                    </h4>
                    <button class="btn btn-sm btn-light" onclick="loadExportHistory()">
                        <i class="fas fa-sync-alt mr-1"></i>Actualizar
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="exportHistory">
                        <p class="text-center">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let progressInterval;
    let exportId;

    // Load export history on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadExportHistory();

        // Check if there's an active export in localStorage
        const activeExport = localStorage.getItem('activeExport');
        if (activeExport) {
            const data = JSON.parse(activeExport);
            exportId = data.export_id;

            // Show progress bar
            document.getElementById('progressContainer').style.display = 'block';
            document.getElementById('start').disabled = true;

            // Resume checking progress
            checkProgress();
        }
    });

    function loadExportHistory() {
        fetch('list_exports.php')
            .then(response => response.json())
            .then(data => {
                console.log('Export history data:', data);
                renderExportHistory(data.exports || []);
            })
            .catch(error => {
                console.error('Error loading export history:', error);
                document.getElementById('exportHistory').innerHTML =
                    '<p class="text-danger">Error al cargar el historial</p>';
            });
    }

    function renderExportHistory(exports) {
        const container = document.getElementById('exportHistory');

        if (exports.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">No hay exportaciones disponibles</p>';
            return;
        }

        let html = '<div class="list-group">';

        exports.forEach(exp => {
            const statusBadge = exp.complete ?
                '<span class="badge badge-success">Completado</span>' :
                '<span class="badge badge-warning">En progreso</span>';

            const tableNames = {
                'students': 'Estudiantes',
                'families': 'Familias',
                'grades': 'Notas',
                'payments': 'Pagos',
                'student_documents': 'Documentos',
                'food_assistance': 'Asistencia',
                'cafeteria': 'Cafetería'
            };

            const tableName = tableNames[exp.table] || exp.table;
            const fileSize = (exp.filesize / 1024).toFixed(2) + ' KB';

            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <i class="fas fa-file-excel text-success"></i> 
                                ${tableName} - Año ${exp.year}
                                ${statusBadge}
                            </h6>
                            <p class="mb-1 small text-muted">
                                <i class="fas fa-clock"></i> ${exp.created_at}
                                ${exp.complete ? ` | <i class="fas fa-hdd"></i> ${fileSize}` : ''}
                            </p>
                            ${!exp.complete ? `
                                <div class="progress mt-2" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         style="width: ${exp.progress.toFixed(2)}%">${exp.progress.toFixed(2)}%</div>
                                </div>
                                <small class="text-muted">${exp.message}</small>
                            ` : ''}
                        </div>
                        <div class="btn-group-vertical ms-3">
                            ${exp.complete && exp.file_exists ? `
                                <button class="btn btn-sm btn-success mb-1" onclick="downloadExport('${exp.export_id}')">
                                    <i class="fas fa-download"></i> Descargar
                                </button>
                            ` : ''}
                            ${!exp.complete ? `
                                <button class="btn btn-sm btn-primary mb-1" onclick="resumeExport('${exp.export_id}')">
                                    <i class="fas fa-eye"></i> Ver Progreso
                                </button>
                            ` : ''}
                            <button class="btn btn-sm btn-danger" onclick="deleteExport('${exp.export_id}')">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    function downloadExport(id) {
        // Create a temporary link for download
        const link = document.createElement('a');
        link.href = `download_export.php?export_id=${id}`;
        link.download = ''; // This will use the filename from Content-Disposition header
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function resumeExport(id) {
        exportId = id;
        localStorage.setItem('activeExport', JSON.stringify({
            export_id: id
        }));

        document.getElementById('progressContainer').style.display = 'block';
        document.getElementById('downloadBtn').style.display = 'none';
        document.getElementById('start').disabled = true;

        checkProgress();
    }

    function deleteExport(id) {
        if (!confirm('¿Estás seguro de que deseas eliminar esta exportación?')) {
            return;
        }

        fetch('delete_export.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `export_id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadExportHistory();

                    // If it's the active export, clear it
                    const activeExport = localStorage.getItem('activeExport');
                    if (activeExport) {
                        const active = JSON.parse(activeExport);
                        if (active.export_id === id) {
                            localStorage.removeItem('activeExport');
                            clearInterval(progressInterval);
                            document.getElementById('progressContainer').style.display = 'none';
                            document.getElementById('start').disabled = false;
                        }
                    }
                } else {
                    alert('Error al eliminar la exportación: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error deleting export:', error);
                alert('Error al eliminar la exportación');
            });
    }

    document.getElementById('exportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const tabla = document.getElementById('tabla').value;
        const year = document.getElementById('year').value;

        if (!tabla || !year) {
            alert('Por favor seleccione una tabla y un año');
            return;
        }

        // Prevent double submission
        const startBtn = document.getElementById('start');
        if (startBtn.disabled) {
            console.log('Export already in progress, ignoring duplicate submit');
            return;
        }

        // Show progress bar and disable button immediately
        document.getElementById('progressContainer').style.display = 'block';
        document.getElementById('downloadBtn').style.display = 'none';
        startBtn.disabled = true;

        // Start export in background
        fetch('start_export.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `tabla=${encodeURIComponent(tabla)}&year=${encodeURIComponent(year)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    startBtn.disabled = false;
                    document.getElementById('progressContainer').style.display = 'none';
                    return;
                }

                exportId = data.export_id;
                console.log('Export started with ID:', exportId);

                // Save to localStorage for recovery
                localStorage.setItem('activeExport', JSON.stringify(data));

                // Start checking progress
                checkProgress();
            })
            .catch(error => {
                console.error('Error starting export:', error);
                alert('Error al iniciar la exportación');
                startBtn.disabled = false;
                document.getElementById('progressContainer').style.display = 'none';
            });
    });

    function checkProgress() {
        // Clear any existing interval first
        if (progressInterval) {
            clearInterval(progressInterval);
        }

        let checkCount = 0;
        progressInterval = setInterval(function() {
            checkCount++;
            const url = `progress.php?id=${exportId}&t=${Date.now()}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`Progress: ${data.progress}% - ${data.message}`);

                    if (data.error) {
                        console.error('Export encountered an error');
                        clearInterval(progressInterval);
                        localStorage.removeItem('activeExport');

                        // Show generic error message
                        document.getElementById('progressBar').style.width = '100%';
                        document.getElementById('progressBar').textContent = 'Error';
                        document.getElementById('progressBar').classList.remove('progress-bar-animated');
                        document.getElementById('progressBar').classList.add('bg-danger');
                        document.getElementById('progressText').textContent = 'Ocurrió un error al procesar la exportación';

                        // Re-enable start button after a delay
                        setTimeout(() => {
                            document.getElementById('start').disabled = false;
                            document.getElementById('progressContainer').style.display = 'none';
                            document.getElementById('progressBar').style.width = '0%';
                            document.getElementById('progressBar').textContent = '0%';
                            document.getElementById('progressBar').classList.add('progress-bar-animated');
                            document.getElementById('progressBar').classList.remove('bg-danger');
                        }, 3000);

                        // Refresh history
                        loadExportHistory();
                        return;
                    }

                    if (data.progress !== undefined) {
                        const percent = Math.round(data.progress);

                        document.getElementById('progressBar').style.width = percent + '%';
                        document.getElementById('progressBar').textContent = percent + '%';
                        document.getElementById('progressBar').setAttribute('aria-valuenow', percent);

                        if (data.message) {
                            document.getElementById('progressText').textContent = data.message;
                        }

                        if (data.complete) {
                            document.getElementById('progressSpinner').style.display = 'none';
                            console.log('Export completed!');
                            clearInterval(progressInterval);
                            localStorage.removeItem('activeExport');

                            document.getElementById('progressBar').classList.remove('progress-bar-animated');
                            document.getElementById('progressBar').classList.add('bg-success');
                            document.getElementById('progressText').textContent = '¡Exportación completada!';

                            // Re-enable the start button to allow new exports
                            document.getElementById('start').disabled = false;

                            // Show download button
                            const downloadBtn = document.getElementById('downloadBtn');
                            downloadBtn.style.display = 'inline-block';

                            // Remove previous onclick to avoid duplicates
                            downloadBtn.onclick = null;

                            downloadBtn.onclick = function(e) {
                                e.preventDefault();
                                // Create a temporary link for download
                                const link = document.createElement('a');
                                link.href = `download_export.php?export_id=${exportId}`;
                                link.download = ''; // This will use the filename from Content-Disposition header
                                link.style.display = 'none';
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);

                                // Reset UI immediately
                                setTimeout(() => {
                                    document.getElementById('progressContainer').style.display = 'none';
                                    document.getElementById('progressBar').style.width = '0%';
                                    document.getElementById('progressBar').textContent = '0%';
                                    document.getElementById('progressBar').classList.add('progress-bar-animated');
                                    document.getElementById('progressBar').classList.remove('bg-success');
                                    downloadBtn.style.display = 'none';
                                }, 500);
                            };

                            // Refresh history only once
                            loadExportHistory();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking progress:', error);
                });
        }, 500); // Check every 500ms
    }
</script>

<?php Route::includeFile('/admin/includes/layouts/footer.php'); ?>