<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use App\Enums\AppointmentMemberEnum;
use App\Models\Appointments\AppointmentEvent;

Session::is_logged();

$events = AppointmentEvent::query()->active()
    ->whereTodayOrAfter('date')
    ->orderBy('date')
    ->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Citas con Maestros');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
    <style>
        .cascade-form {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.25rem;
            margin-bottom: 1.5rem;
        }

        .cascade-form .form-group {
            margin-bottom: 1rem;
        }

        .cascade-form label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .cascade-form select,
        .cascade-form input {
            width: 100%;
        }

        .loading {
            display: none;
            text-align: center;
            color: #6c757d;
            margin: 1rem 0;
        }

        .loading.show {
            display: block;
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        #slotsContainer {
            display: block;
            margin-top: 1rem;
        }

        .teacher-block .slots-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .slot-card {
            display: flex;
            align-items: center;
            padding: 0;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            overflow: hidden;
        }

        .slot-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.25);
        }

        .slot-card input[type="radio"] {
            display: none;
        }

        .slot-card input[type="radio"]:checked+.slot-content,
        .slot-card input[type="checkbox"]:checked+.slot-content {
            background-color: #007bff;
            color: white;
        }

        .slot-card input[type="checkbox"] {
            display: none;
        }

        .slot-content {
            padding: 1rem;
            flex: 1;
            transition: background-color 0.2s ease;
        }

        .teacher-block .slot-content {
            padding: 0.55rem 0.7rem;
        }

        .slot-time {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .teacher-block .slot-time {
            font-size: 0.92rem;
            margin-bottom: 0;
            line-height: 1.2;
        }

        .error-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 0.25rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #28a745;
            background-color: #d4edda;
            color: #155724;
            border-radius: 0.25rem;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        .hidden {
            display: none;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section h4 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: #495057;
        }

        .teacher-block {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .teacher-name {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .teacher-subjects {
            color: #6c757d;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .teacher-note {
            margin-top: 0.75rem;
        }

        .teacher-member {
            margin-top: 0.5rem;
        }

        .teacher-existing {
            margin-bottom: 0.6rem;
            padding: 0.45rem 0.6rem;
            border-left: 3px solid #17a2b8;
            background: #eef9fc;
            color: #0c5460;
            font-size: 0.86rem;
            border-radius: 0.2rem;
        }

        .slot-card.conflict {
            border-color: #dc3545;
            box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.25);
        }

        .teacher-member-select.conflict {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.1rem rgba(220, 53, 69, 0.2);
        }

        #timeConflictHint {
            color: #dc3545;
            margin-top: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .teacher-block .slots-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container mt-lg-3 mb-5 px-0">
        <div class="mx-auto" style="max-width: 900px;">
            <h1 class="text-center mb-4 mt-5"><?= __('Citas con Maestros') ?></h1>

            <div id="errorMessage" class="error-message"></div>
            <div id="successMessage" class="success-message"></div>

            <form id="appointmentForm" class="shadow-lg p-4">
                <!-- Paso 1: Seleccionar Evento -->
                <div class="form-section">
                    <div class="cascade-form">
                        <div class="form-group">
                            <label for="eventSelect"><?= __('Evento (Fecha)') ?> *</label>
                            <select id="eventSelect" name="event" class="form-control" required>
                                <option value=""><?= __('Selecciona un evento...') ?></option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event->id ?>">(<?= $event->date ?>) <?= $event->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="loading" id="loadingEvents">
                                <small><?= __('Cargando eventos...') ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Seleccionar Estudiante -->
                <div class="form-section hidden" id="studentSection">
                    <div class="cascade-form">
                        <div class="form-group">
                            <label for="studentSelect"><?= __('Estudiante') ?> *</label>
                            <select id="studentSelect" name="student" class="form-control" required>
                                <option value=""><?= __('Selecciona un estudiante...') ?></option>
                            </select>
                            <div class="loading" id="loadingStudents">
                                <small><?= __('Cargando estudiantes...') ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paso 3: Profesores y Slots Disponibles -->
                <div class="form-section hidden" id="slotsSection">
                    <div class="cascade-form">
                        <div class="form-group">
                            <label><?= __('Selecciona una o varias ventanas de tiempo') ?> *</label>
                            <div class="loading show" id="loadingSlots">
                                <small><?= __('Cargando profesores y slots disponibles...') ?></small>
                            </div>
                            <div class="slots-grid" id="slotsContainer">
                            </div>
                            <div id="timeConflictHint" class="hidden"></div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <?= __('Reservar Cita') ?>
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <?= __('Limpiar') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        const memberOptions = <?= json_encode(array_map(static fn(AppointmentMemberEnum $member): array => [
                                    'value' => $member->value,
                                    'label' => __($member->getLabel()),
                                ], AppointmentMemberEnum::cases()), JSON_UNESCAPED_UNICODE) ?>;

        const memberLabelByValue = memberOptions.reduce((acc, item) => {
            acc[item.value] = item.label;
            return acc;
        }, {});

        $(document).ready(function() {
            setupEventHandlers();

        });

        function setupEventHandlers() {
            $('#eventSelect').on('change', function() {
                const eventId = $(this).val();
                resetFlowFromEvent();

                if (eventId) {
                    showStudentSection();
                    loadStudents(eventId);
                } else {
                    hideStudentSection();
                    hideSlotSection();
                }
            });

            $('#studentSelect').on('change', function() {
                const studentId = $(this).val();
                if (studentId) {
                    const eventId = $('#eventSelect').val();
                    loadTeachersWithSlots(eventId, studentId);
                } else {
                    hideSlotSection();
                }
            });

            $(document).on('change', 'input[name="slot_ids[]"]', function() {
                const current = $(this);

                // Allow only one selected slot per teacher.
                if (current.is(':checked')) {
                    const teacherId = current.data('teacher-id');
                    $(`input[name="slot_ids[]"][data-teacher-id="${teacherId}"]`)
                        .not(current)
                        .prop('checked', false);
                }

                updateSubmitButton();
            });

            $(document).on('change', '.teacher-member-select', function() {
                updateSubmitButton();
            });

            $('#appointmentForm').on('submit', function(e) {
                e.preventDefault();
                bookAppointment();
            });

            $('#appointmentForm').on('reset', function() {
                setTimeout(() => {
                    hideMessages();
                    resetFlowFromEvent();
                    hideStudentSection();
                    hideSlotSection();
                }, 0);
            });
        }


        function loadStudents(eventId) {
            showLoading('#loadingStudents');
            const params = new URLSearchParams({
                event_id: eventId
            });
            fetch(`./includes/get-students.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    const select = $('#studentSelect');
                    select.empty().append('<option value=""><?= __("Selecciona un estudiante...") ?></option>');
                    if (data.success && data.students && data.students.length > 0) {
                        data.students.forEach(student => {
                            select.append(`<option value="${student.id}">${student.name} (${student.grade})</option>`);
                        });
                    } else {
                        showError('<?= __("No hay estudiantes disponibles para este evento") ?>');
                        hideSlotSection();
                    }
                })
                .catch(error => {
                    showError('<?= __("Error al cargar estudiantes") ?>');
                    console.error(error);
                })
                .finally(() => hideLoading('#loadingStudents'));
        }

        function loadTeachersWithSlots(eventId, studentId) {
            showLoading('#loadingSlots');
            showSlotSection();
            clearSlots();

            const params = new URLSearchParams({
                event_id: eventId,
                student_id: studentId
            });
            fetch(`./includes/get-teachers-slots.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    const container = $('#slotsContainer');
                    container.empty();

                    if (data.success && data.teachers && data.teachers.length > 0) {
                        let renderedSlots = 0;

                        data.teachers.forEach(teacher => {
                            const subjects = (teacher.subjects && teacher.subjects.length > 0) ?
                                teacher.subjects.join(', ') :
                                teacher.subject;

                            const existingSelection = teacher.existing_selection || null;
                            const existingMemberLabel = existingSelection && existingSelection.member ?
                                (memberLabelByValue[existingSelection.member] || existingSelection.member) :
                                '';
                            const existingInfoHtml = existingSelection ?
                                `<div class="teacher-existing">${escapeHtml('<?= __('Ya tiene una cita con este profesor') ?>')}: ${escapeHtml(existingMemberLabel)} · ${escapeHtml(existingSelection.start_time)} - ${escapeHtml(existingSelection.end_time)}</div>` :
                                '';

                            const initialMember = existingSelection && existingSelection.member ? existingSelection.member : '';
                            const initialNote = existingSelection && existingSelection.note ? existingSelection.note : '';

                            let teacherHtml = `
                                <div class="teacher-block">
                                    <div class="teacher-name">${escapeHtml(teacher.name)}</div>
                                    <div class="teacher-subjects">${escapeHtml(subjects || '<?= __("Sin materias") ?>')}</div>
                                    ${existingInfoHtml}
                                    <div class="teacher-member">
                                        <label class="mb-1" for="teacher-member-${teacher.id}"><?= __('¿Quién hará la cita con este profesor?') ?></label>
                                        <select id="teacher-member-${teacher.id}" class="form-control teacher-member-select" data-teacher-id="${teacher.id}">
                                            ${buildMemberOptionsHtml(initialMember)}
                                        </select>
                                    </div>
                                    <div class="slots-grid">
                            `;

                            if (teacher.slots && teacher.slots.length > 0) {
                                teacher.slots.forEach(slot => {
                                    renderedSlots++;
                                    teacherHtml += `
                                        <label class="slot-card">
                                            <input type="checkbox" name="slot_ids[]" value="${slot.id}" data-teacher-id="${teacher.id}" data-start-time="${escapeHtml(slot.start_time)}" data-end-time="${escapeHtml(slot.end_time)}" ${slot.selected ? 'checked' : ''}>
                                            <div class="slot-content">
                                                <div class="slot-time">${escapeHtml(slot.start_time)} - ${escapeHtml(slot.end_time)}</div>
                                            </div>
                                        </label>
                                    `;
                                });
                            } else {
                                teacherHtml += `<small class="text-muted"><?= __("Sin slots disponibles") ?></small>`;
                            }

                            teacherHtml += `
                                    </div>
                                    <div class="teacher-note">
                                        <label class="mb-1" for="teacher-note-${teacher.id}"><?= __('Nota para este profesor (opcional)') ?></label>
                                        <textarea
                                            id="teacher-note-${teacher.id}"
                                            class="form-control teacher-note-input"
                                            data-teacher-id="${teacher.id}"
                                            rows="2"
                                            maxlength="500"
                                            placeholder="<?= __('Escribe una nota breve para este profesor...') ?>">${escapeHtml(initialNote)}</textarea>
                                    </div>
                                </div>
                            `;

                            container.append(teacherHtml);
                        });

                        if (renderedSlots === 0) {
                            showError('<?= __("No hay slots disponibles para este estudiante") ?>');
                        }
                    } else {
                        showError('<?= __("No hay profesores disponibles para este estudiante") ?>');
                        hideSlotSection();
                    }
                })
                .catch(error => {
                    showError('<?= __("Error al cargar profesores y slots") ?>');
                    console.error(error);
                    hideSlotSection();
                })
                .finally(() => {
                    hideLoading('#loadingSlots');
                    hasTimeConflictByMember(true);
                    updateSubmitButton();
                });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildMemberOptionsHtml(selectedValue) {
            const options = [`<option value="">${escapeHtml('<?= __('Selecciona miembro...') ?>')}</option>`];

            memberOptions.forEach(option => {
                const selectedAttr = option.value === selectedValue ? 'selected' : '';
                options.push(`<option value="${escapeHtml(option.value)}" ${selectedAttr}>${escapeHtml(option.label)}</option>`);
            });

            return options.join('');
        }

        function getSelectedTeacherIds() {
            const ids = $('input[name="slot_ids[]"]:checked').map(function() {
                return String($(this).data('teacher-id'));
            }).get();

            return [...new Set(ids)];
        }

        function hasMembersForSelectedTeachers() {
            const selectedTeacherIds = getSelectedTeacherIds();
            if (selectedTeacherIds.length === 0) {
                return false;
            }

            return selectedTeacherIds.every(teacherId => {
                return $(`.teacher-member-select[data-teacher-id="${teacherId}"]`).val();
            });
        }

        function hasTimeConflictByMember(applyHighlight = false) {
            const conflictsByKey = {};
            const seen = {};
            let hasConflict = false;

            $('input[name="slot_ids[]"]:checked').each(function() {
                const slot = $(this);
                const teacherId = String(slot.data('teacher-id'));
                const member = $(`.teacher-member-select[data-teacher-id="${teacherId}"]`).val();
                const startTime = String(slot.data('start-time') || '');

                if (!member || !startTime) {
                    return;
                }

                const key = `${member}|${startTime}`;
                if (!conflictsByKey[key]) {
                    conflictsByKey[key] = [];
                }

                conflictsByKey[key].push({
                    teacherId,
                    slot,
                });

                if (seen[key] && seen[key] !== teacherId) {
                    hasConflict = true;
                }

                seen[key] = teacherId;
            });

            if (applyHighlight) {
                $('.slot-card').removeClass('conflict');
                $('.teacher-member-select').removeClass('conflict');

                Object.values(conflictsByKey).forEach(group => {
                    if (group.length <= 1) {
                        return;
                    }

                    group.forEach(item => {
                        item.slot.closest('.slot-card').addClass('conflict');
                        $(`.teacher-member-select[data-teacher-id="${item.teacherId}"]`).addClass('conflict');
                    });
                });

                const hint = $('#timeConflictHint');
                if (hasConflict) {
                    hint.text('<?= __("El mismo miembro no puede seleccionar la misma hora con diferentes profesores") ?>');
                    hint.removeClass('hidden');
                } else {
                    hint.addClass('hidden').text('');
                }
            }

            return hasConflict;
        }

        function bookAppointment() {
            const eventId = $('#eventSelect').val();
            const studentId = $('#studentSelect').val();
            const slotIds = $('input[name="slot_ids[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            const selectedTeacherIds = getSelectedTeacherIds();
            const membersByTeacher = {};
            selectedTeacherIds.forEach(teacherId => {
                membersByTeacher[teacherId] = $(`.teacher-member-select[data-teacher-id="${teacherId}"]`).val() || '';
            });

            const notesByTeacher = {};
            selectedTeacherIds.forEach(teacherId => {
                const note = ($(`.teacher-note-input[data-teacher-id="${teacherId}"]`).val() || '').trim();
                if (teacherId) {
                    notesByTeacher[teacherId] = note;
                }
            });

            if (!eventId || !studentId || slotIds.length === 0 || !hasMembersForSelectedTeachers()) {
                showError('<?= __("Por favor completa todos los campos") ?>');
                return;
            }

            if (hasTimeConflictByMember(true)) {
                showError('<?= __("El mismo miembro no puede seleccionar la misma hora con diferentes profesores") ?>');
                return;
            }

            const btn = $('#submitBtn');
            btn.prop('disabled', true).html('<?= __("Reservando...") ?>');
            hideMessages();

            const payload = {
                event_id: eventId,
                student_id: studentId,
                slot_ids: slotIds,
                members_by_teacher: membersByTeacher,
                notes_by_teacher: notesByTeacher
            };

            $.ajax({
                url: './includes/book-appointments.php',
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(response) {
                    if (response.success) {
                        const createdCount = response.count || slotIds.length;
                        showSuccess(response.message || `<?= __("Citas reservadas exitosamente") ?> (${createdCount})`);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showError(response.message || '<?= __("Error al reservar la cita") ?>');
                        btn.prop('disabled', false).html('<?= __("Reservar Cita") ?>');
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || '<?= __("Error al reservar la cita") ?>';
                    showError(error);
                    btn.prop('disabled', false).html('<?= __("Reservar Cita") ?>');
                }
            });
        }

        function showStudentSection() {
            $('#studentSection').removeClass('hidden');
        }

        function hideStudentSection() {
            $('#studentSection').addClass('hidden');
            clearStudents();
        }

        function showSlotSection() {
            $('#slotsSection').removeClass('hidden');
        }

        function hideSlotSection() {
            $('#slotsSection').addClass('hidden');
            clearSlots();
        }

        function clearStudents() {
            $('#studentSelect').empty().append('<option value=""><?= __("Selecciona un estudiante...") ?></option>');
            hideSlotSection();
        }

        function clearSlots() {
            $('input[name="slot_ids[]"]').prop('checked', false);
            $('#slotsContainer').empty();
            updateSubmitButton();
        }

        function resetFlowFromEvent() {
            clearStudents();
            clearSlots();
            updateSubmitButton();
        }

        function updateSubmitButton() {
            const hasSelectedSlots = $('input[name="slot_ids[]"]:checked').length > 0;
            const hasConflict = hasTimeConflictByMember(true);
            const isComplete = $('#eventSelect').val() &&
                $('#studentSelect').val() &&
                hasSelectedSlots &&
                hasMembersForSelectedTeachers() &&
                !hasConflict;
            $('#submitBtn').prop('disabled', !isComplete);
        }

        function showLoading(selector) {
            $(selector).addClass('show');
        }

        function hideLoading(selector) {
            $(selector).removeClass('show');
        }

        function showError(message) {
            hideMessages();
            $('#errorMessage').text(message).addClass('show');
            window.scrollTo(0, 0);
        }

        function showSuccess(message) {
            hideMessages();
            $('#successMessage').text(message).addClass('show');
            window.scrollTo(0, 0);
        }

        function hideMessages() {
            $('#errorMessage').removeClass('show');
            $('#successMessage').removeClass('show');
        }
    </script>
</body>

</html>