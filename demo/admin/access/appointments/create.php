<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use App\Services\SchoolService;

Session::is_logged();

$allGrades = SchoolService::getAllGrades()->toArray();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Crear Evento de Citas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .grades-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .grade-checkbox {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .grade-checkbox:hover {
            background-color: #e9ecef;
            border-color: #007bff;
        }

        .grade-checkbox input[type="checkbox"]:checked+label {
            font-weight: bold;
            color: #007bff;
        }

        .grade-checkbox input[type="checkbox"] {
            margin-right: 0.5rem;
            cursor: pointer;
        }

        .grade-checkbox label {
            margin: 0;
            cursor: pointer;
            flex: 1;
        }

        .grade-checkbox input[type="checkbox"]:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .grade-checkbox.disabled-grade {
            background-color: #f5f5f5;
            border-color: #dc3545;
            opacity: 0.6;
        }

        .grade-checkbox.disabled-grade label {
            cursor: not-allowed;
            color: #6c757d;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .error-message {
            display: none;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 0.25rem;
        }

        .success-message {
            display: none;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #28a745;
            background-color: #d4edda;
            color: #155724;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <div class="mx-auto" style="max-width: 900px;">
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="./index.php"><?= __('Volver') ?></a>
            </div>

            <h1 class="text-center mb-5 mt-5"><?= __('Crear Evento de Citas') ?></h1>

            <div id="errorMessage" class="error-message"></div>
            <div id="successMessage" class="success-message"></div>

            <form id="createEventForm" class="shadow-lg p-5">
                <!-- Nombre y Fecha -->
                <div class="form-section">
                    <h4><?= __('Información General') ?></h4>

                    <div class="form-group">
                        <label for="eventName"><?= __('Nombre del Evento') ?> *</label>
                        <input type="text" class="form-control" id="eventName" name="name" required>
                        <small class="form-text text-muted"><?= __('Ej: Reunión de Padres - Junio') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="eventDate"><?= __('Fecha') ?> *</label>
                        <input type="date" class="form-control" id="eventDate" name="date" required>
                    </div>
                </div>

                <!-- Horarios -->
                <div class="form-section">
                    <h4><?= __('Horarios') ?></h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="startTime"><?= __('Hora Inicio') ?> *</label>
                                <input type="time" class="form-control" id="startTime" name="start_time" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="endTime"><?= __('Hora Fin') ?> *</label>
                                <input type="time" class="form-control" id="endTime" name="end_time" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="breakStartTime"><?= __('Break - Hora Inicio') ?></label>
                                <input type="time" class="form-control" id="breakStartTime" name="break_start_time">
                                <small class="form-text text-muted"><?= __('Opcional') ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="breakEndTime"><?= __('Break - Hora Fin') ?></label>
                                <input type="time" class="form-control" id="breakEndTime" name="break_end_time">
                                <small class="form-text text-muted"><?= __('Opcional') ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slotDuration"><?= __('Duración de cada slot (minutos)') ?> *</label>
                        <input type="number" class="form-control" id="slotDuration" name="slot_duration" value="30"
                            min="5" max="120" step="5" required>
                        <small class="form-text text-muted"><?= __('Tiempo reservado para cada cita') ?></small>
                    </div>
                </div>

                <!-- Grados -->
                <div class="form-section">
                    <h4><?= __('Selecciona los Grados') ?> *</h4>
                    <div class="grades-grid">
                        <?php foreach ($allGrades as $grade): ?>
                            <div class="grade-checkbox">
                                <input type="checkbox" id="grade_<?= htmlspecialchars($grade) ?>"
                                    name="grades" value="<?= htmlspecialchars($grade) ?>">
                                <label for="grade_<?= htmlspecialchars($grade) ?>">
                                    <?= htmlspecialchars($grade) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="form-text text-muted d-block"><?= __('Debes seleccionar al menos un grado') ?></small>
                </div>

                <!-- Botones -->
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-secondary" href="./index.php"><?= __('Cancelar') ?></a>
                    <button type="submit" class="btn btn-primary" id="createEventBtn"><?= __('Crear Evento') ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        let occupiedGradesForDate = [];

        $(document).ready(function() {
            // Validar conflictos cuando cambia la fecha
            $('#eventDate').on('change', function() {
                checkDateConflicts();
            });

            $('#createEventForm').on('submit', function(e) {
                e.preventDefault();

                // Validar grados seleccionados
                const selectedGrades = $('input[name="grades"]:checked').map(function() {
                    return this.value;
                }).get();

                if (selectedGrades.length === 0) {
                    showError('<?= __('Debes seleccionar al menos un grado') ?>');
                    return;
                }

                // Preparar payload
                const payload = {
                    name: $('#eventName').val(),
                    date: $('#eventDate').val(),
                    start_time: $('#startTime').val(),
                    end_time: $('#endTime').val(),
                    break_start_time: $('#breakStartTime').val() || null,
                    break_end_time: $('#breakEndTime').val() || null,
                    slot_duration: parseInt($('#slotDuration').val()),
                    grades: selectedGrades
                };

                // Validar campos requeridos
                if (!payload.name || !payload.date || !payload.start_time || !payload.end_time) {
                    showError('<?= __('Por favor completa todos los campos requeridos') ?>');
                    return;
                }

                const btn = $('#createEventBtn');
                btn.prop('disabled', true).html('<?= __('Creando...') ?>');
                hideMessages();

                $.ajax({
                    url: './includes/store.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        showSuccess(response.message || '<?= __('Evento creado exitosamente') ?>');
                        setTimeout(() => {
                            window.location.href = './index.php';
                        }, 1500);
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.error || '<?= __('Error al crear el evento') ?>';
                        showError(error);
                        btn.prop('disabled', false).html('<?= __('Crear Evento') ?>');
                    }
                });
            });
        });

        function checkDateConflicts() {
            const date = $('#eventDate').val();
            if (!date) {
                enableAllGrades();
                return;
            }

            fetch(`./includes/check-date-conflict.php?date=${encodeURIComponent(date)}`)
                .then(response => response.json())
                .then(data => {
                    occupiedGradesForDate = data.occupied_grades || [];
                    updateGradesAvailability();

                    if (data.has_conflict) {
                        showError(`<?= __('Estos grados ya tienen evento el ') ?> ${date}: ${occupiedGradesForDate.join(', ')}`);
                    } else {
                        hideMessages();
                    }
                })
                .catch(error => {
                    console.error('Error checking conflicts:', error);
                });
        }

        function updateGradesAvailability() {
            $('input[name="grades"]').each(function() {
                const grade = $(this).val();
                const isOccupied = occupiedGradesForDate.includes(grade);

                $(this).prop('disabled', isOccupied);
                $(this).closest('.grade-checkbox').toggleClass('disabled-grade', isOccupied);

                if (isOccupied && $(this).is(':checked')) {
                    $(this).prop('checked', false);
                }
            });
        }

        function enableAllGrades() {
            occupiedGradesForDate = [];
            $('input[name="grades"]').prop('disabled', false);
            $('.grade-checkbox').removeClass('disabled-grade');
            hideMessages();
        }

        function showError(message) {
            $('#errorMessage').text(message).show();
            $('#successMessage').hide();
        }

        function showSuccess(message) {
            $('#successMessage').text(message).show();
            $('#errorMessage').hide();
        }

        function hideMessages() {
            $('#errorMessage').hide();
            $('#successMessage').hide();
        }
    </script>
</body>

</html>