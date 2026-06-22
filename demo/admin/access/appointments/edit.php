<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use App\Models\Appointments\AppointmentEvent;
use App\Services\SchoolService;

Session::is_logged();

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    Route::redirect('/access/appointments/index.php');
}

$event = AppointmentEvent::with(['slots' => fn($q) => $q->withCount('appointment')])
    ->find($eventId);

if (!$event) {
    Route::redirect('/access/appointments/index.php');
}

$allGrades = SchoolService::getAllGrades()->toArray();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Editar Evento de Citas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }

        .slots-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .slot-badge {
            padding: 1rem;
            border-radius: 0.25rem;
            text-align: center;
            background-color: #e9ecef;
        }

        .slot-badge strong {
            display: block;
            font-size: 1.5rem;
            color: #495057;
        }

        .slot-badge small {
            color: #6c757d;
        }

        .grades-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
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

        .alert-warning-conflict {
            border-left: 4px solid #ffc107;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Editar Evento de Citas') ?></h1>

        <div class="mx-auto" style="max-width: 800px;">
            <div class="mb-3">
                <a class="btn btn-outline-primary" href="./index.php"><?= __('Volver') ?></a>
            </div>

            <!-- Información de slots -->
            <div class="info-card">
                <strong><?= __('Resumen de Slots') ?></strong>
                <div class="slots-summary mt-3">
                    <div class="slot-badge">
                        <strong id="totalSlots"><?= $event->slots->count() ?></strong>
                        <small><?= __('Slots Totales') ?></small>
                    </div>
                    <div class="slot-badge">
                        <strong id="reservedSlots">0</strong>
                        <small><?= __('Citas Reservadas') ?></small>
                    </div>
                    <div class="slot-badge">
                        <strong id="availableSlots"><?= $event->slots->count() ?></strong>
                        <small><?= __('Slots Disponibles') ?></small>
                    </div>
                </div>
            </div>

            <!-- Formulario de edición -->
            <form id="editEventForm" class="shadow-lg p-4">
                <input type="hidden" name="id" value="<?= $event->id ?>">

                <div class="form-group">
                    <label for="editEventName"><?= __('Nombre del Evento') ?> *</label>
                    <input type="text" class="form-control" id="editEventName" name="name"
                        value="<?= htmlspecialchars($event->name) ?>" required>
                </div>

                <div class="form-group">
                    <label for="editEventDate"><?= __('Fecha') ?> *</label>
                    <input type="date" class="form-control" id="editEventDate" name="date"
                        value="<?= $event->date ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="editStartTime"><?= __('Hora Inicio') ?> *</label>
                            <input type="time" class="form-control" id="editStartTime" name="start_time"
                                value="<?= $event->start_time ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="editEndTime"><?= __('Hora Fin') ?> *</label>
                            <input type="time" class="form-control" id="editEndTime" name="end_time"
                                value="<?= $event->end_time ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="editBreakStartTime"><?= __('Break - Hora Inicio') ?></label>
                            <input type="time" class="form-control" id="editBreakStartTime" name="break_start_time"
                                value="<?= $event->break_start_time ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="editBreakEndTime"><?= __('Break - Hora Fin') ?></label>
                            <input type="time" class="form-control" id="editBreakEndTime" name="break_end_time"
                                value="<?= $event->break_end_time ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="editSlotDuration"><?= __('Duración Slot (minutos)') ?> *</label>
                    <input type="number" class="form-control" id="editSlotDuration" name="slot_duration"
                        value="<?= $event->slot_duration ?>" min="5" max="120" step="5" required>
                </div>

                <div class="form-group">
                    <label><?= __('Grados') ?> *</label>
                    <div class="grades-grid">
                        <?php foreach ($allGrades as $grade): ?>
                            <div class="grade-checkbox">
                                <input type="checkbox" id="editGrade_<?= htmlspecialchars($grade) ?>"
                                    name="grades" value="<?= htmlspecialchars($grade) ?>"
                                    <?= in_array($grade, $event->grades ?? []) ? 'checked' : '' ?>>
                                <label for="editGrade_<?= htmlspecialchars($grade) ?>">
                                    <?= htmlspecialchars($grade) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="editIsActive" name="is_active"
                            <?= $event->is_active ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="editIsActive">
                            <?= __('Evento Activo') ?>
                        </label>
                    </div>
                </div>

                <!-- Alerta de conflicto -->
                <div id="conflictAlert" class="alert alert-warning alert-warning-conflict" style="display: none;">
                    <strong><?= __('⚠️ Conflicto de Regeneración') ?></strong>
                    <p id="conflictMessage"></p>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary" id="updateEventBtn"><?= __('Actualizar') ?></button>
                    <button type="button" class="btn btn-danger" id="deleteEventBtn"><?= __('Eliminar Evento') ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        const eventId = <?php echo $event->id; ?>;
        const currentGrades = <?php echo json_encode($event->grades ?? []); ?>;
        let occupiedGradesForDate = [];

        $(document).ready(function() {
            // Validar conflictos cuando cambia la fecha
            $('#editEventDate').on('change', function() {
                checkDateConflicts();
            });

            // Validar grados iniciales
            checkDateConflicts();

            calculateSlotStats();
        });

        function checkDateConflicts() {
            const date = $('#editEventDate').val();
            if (!date) {
                enableAllGrades();
                return;
            }

            fetch(`./includes/check-date-conflict.php?date=${encodeURIComponent(date)}&exclude_event=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    occupiedGradesForDate = data.occupied_grades || [];
                    updateGradesAvailability();

                    if (data.has_conflict) {
                        showWarning(`<?= __('Estos grados ya tienen evento el ') ?> ${date}: ${occupiedGradesForDate.join(', ')}`);
                    } else {
                        hideWarning();
                    }
                })
                .catch(error => {
                    console.error('Error checking conflicts:', error);
                });
        }

        function updateGradesAvailability() {
            $('input[name="grades"]').each(function() {
                const grade = $(this).val();
                // Un grado está ocupado si:
                // - Ya tiene evento en otra fecha (occupiedGradesForDate)
                // - Y NO es uno de los grados actuales del evento (currentGrades)
                const isOccupied = occupiedGradesForDate.includes(grade) && !currentGrades.includes(grade);

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
            hideWarning();
        }

        function showWarning(message) {
            $('#conflictAlert').find('#conflictMessage').text(message);
            $('#conflictAlert').show();
        }

        function hideWarning() {
            $('#conflictAlert').hide();
        }

        function calculateSlotStats() {
            // Aquí iría lógica para contar citas reservadas si es necesario
            // Por ahora, solo mostramos el total de slots
            const totalSlots = <?php echo $event->slots->count(); ?>;
            $('#totalSlots').text(totalSlots);
            $('#availableSlots').text(totalSlots);
        }

        $('#editEventForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const payload = Object.fromEntries(formData.entries());

            // Convertir grades a array desde checkboxes
            const selectedGrades = $('input[name="grades"]:checked').map(function() {
                return this.value;
            }).get();

            payload.grades = selectedGrades;
            payload.is_active = $('#editIsActive').is(':checked');

            if (!payload.grades || payload.grades.length === 0) {
                Toast.fire({
                    icon: 'warning',
                    title: '<?= __('Selecciona al menos un grado') ?>'
                });
                return;
            }

            const btn = $('#updateEventBtn');
            btn.prop('disabled', true).html('<?= __('Actualizando...') ?>');

            $.ajax({
                url: './includes/update.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(response) {
                    $('#conflictAlert').hide();
                    const message = response.regenerated ?
                        `<?= __('Evento actualizado. Slots regenerados:') ?> ${response.slot_count}` :
                        response.message;

                    Toast.fire({
                        icon: 'success',
                        title: message
                    });

                    // Recargar datos del evento
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    if (response.conflict) {
                        $('#conflictMessage').text(response.error);
                        $('#conflictAlert').show();
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.error || '<?= __('Error al actualizar evento') ?>'
                        });
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('<?= __('Actualizar') ?>');
                }
            });
        });

        $('#deleteEventBtn').on('click', function() {
            ConfirmationAlert.fire({
                title: '<?= __('¿Estás seguro?') ?>',
                text: '<?= __('¿Deseas eliminar este evento? Se eliminarán todos los slots.') ?>',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementar delete si es necesario
                    console.log('Delete event:', <?= $event->id ?>);
                }
            });
        });
    </script>
</body>

</html>