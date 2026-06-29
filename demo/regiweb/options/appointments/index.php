<?php
require_once __DIR__ . '/../../../app.php';

use App\Enums\AppointmentStatusEnum;
use Classes\Route;
use Classes\Session;

Session::is_logged();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Citas con Padres');
    Route::includeFile('/regiweb/includes/layouts/header.php');
    ?>
    <style>
        .page-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            padding: 1rem;
        }

        .status-pill {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-booked {
            background: #cff4fc;
            color: #055160;
        }

        .status-done {
            background: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .status-no-show {
            background: #e2e3e5;
            color: #41464b;
        }

        .status-actions {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .message-box {
            display: none;
            border-radius: 0.25rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .message-box.show {
            display: block;
        }

        .message-error {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
            color: #842029;
        }

        .message-success {
            border-left: 4px solid #198754;
            background: #d1e7dd;
            color: #0f5132;
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 1.2rem;
        }

        .info-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #495057;
            margin-bottom: 0.4rem;
        }

        .chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
        }

        .chip {
            display: inline-block;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 600;
            background: #eef4ff;
            border: 1px solid #d6e3ff;
            color: #1f3a6d;
        }

        .chip-sm {
            padding: 0.15rem 0.5rem;
            font-size: 0.75rem;
        }

        .section-hidden {
            display: none;
        }

        .subject-group {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .subject-group-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 0.9rem;
            font-weight: 700;
            color: #334155;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/regiweb/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <div class="mx-auto" style="max-width: 1200px;">
            <h1 class="text-center mb-4 mt-5"><?= __('Citas con Padres') ?></h1>

            <div id="errorMessage" class="message-box message-error"></div>
            <div id="successMessage" class="message-box message-success"></div>

            <div class="page-card mb-3">
                <label for="eventSelect" class="mb-2 font-weight-bold"><?= __('Evento') ?></label>
                <select id="eventSelect" class="form-control">
                    <option value=""><?= __('Selecciona un evento...') ?></option>
                </select>
                <small id="eventsLoading" class="text-muted d-none"><?= __('Cargando eventos...') ?></small>

                <div id="eventGradesWrap" class="mt-3 section-hidden">
                    <div class="info-title"><?= __('Grados del evento') ?></div>
                    <div id="eventGrades" class="chip-list"></div>
                </div>
            </div>

            <div id="subjectsCard" class="page-card mb-3 section-hidden">
                <div class="info-title"><?= __('Materias') ?></div>
                <div id="subjectsList" class="chip-list"></div>
            </div>

            <div class="page-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h5 class="mb-2 mb-md-0"><?= __('Citas') ?></h5>
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm" type="button" disabled>
                        <?= __('Actualizar') ?>
                    </button>
                </div>

                <div id="subjectGroupsContainer" class="empty-state">
                    <?= __('Selecciona un evento para ver las citas') ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        const API_URL = './includes/index.php';
        const BOOKED_STATUS = '<?= AppointmentStatusEnum::BOOKED->value ?>';

        const ACTIONS = [{
                status: '<?= AppointmentStatusEnum::DONE->value ?>',
                label: '<?= AppointmentStatusEnum::DONE->getLabel() ?>',
                className: 'btn-success'
            },
            {
                status: '<?= AppointmentStatusEnum::CANCELLED->value ?>',
                label: '<?= AppointmentStatusEnum::CANCELLED->getLabel() ?>',
                className: 'btn-danger'
            },
            {
                status: '<?= AppointmentStatusEnum::NO_SHOW->value ?>',
                label: '<?= AppointmentStatusEnum::NO_SHOW->getLabel() ?>',
                className: 'btn-secondary'
            }
        ];

        $(document).ready(function() {
            bindHandlers();
            loadEvents();
        });

        function bindHandlers() {
            $('#eventSelect').on('change', function() {
                const eventId = $(this).val();
                $('#refreshBtn').prop('disabled', !eventId);

                if (!eventId) {
                    resetEventContext();
                    renderEmpty('<?= __('Selecciona un evento para ver las citas') ?>');
                    return;
                }

                loadAppointments(eventId);
            });

            $('#refreshBtn').on('click', function() {
                const eventId = $('#eventSelect').val();
                if (eventId) {
                    loadAppointments(eventId);
                }
            });

            $(document).on('click', '.status-btn', function() {
                const appointmentId = Number($(this).data('appointment-id'));
                const status = String($(this).data('status'));
                if (!appointmentId || !status) {
                    return;
                }

                updateStatus(appointmentId, status);
            });
        }

        function loadEvents() {
            toggleLoading('#eventsLoading', true);
            hideMessages();

            $.ajax({
                url: API_URL,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'getEvents'
                },
                success: function(response) {
                    if (!response.success) {
                        showError(response.message || '<?= __('No se pudieron cargar los eventos') ?>');
                        return;
                    }

                    const select = $('#eventSelect');
                    select.empty().append(`<option value=""><?= __('Selecciona un evento...') ?></option>`);

                    const events = response.events || [];
                    events.forEach(event => {
                        select.append(`<option value="${escapeHtml(event.id)}">(${escapeHtml(event.date)}) ${escapeHtml(event.name)}</option>`);
                    });

                    if (events.length === 0) {
                        renderEmpty('<?= __('No hay eventos activos o futuros disponibles') ?>');
                    }
                },
                error: function() {
                    showError('<?= __('Error al cargar eventos') ?>');
                },
                complete: function() {
                    toggleLoading('#eventsLoading', false);
                }
            });
        }

        function loadAppointments(eventId) {
            hideMessages();
            resetEventContext();
            renderLoading();

            $.ajax({
                url: API_URL,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'getAppointmentsByEvent',
                    event_id: eventId,
                },
                success: function(response) {
                    if (!response.success) {
                        showError(response.message || '<?= __('No se pudieron cargar las citas') ?>');
                        renderEmpty('<?= __('No fue posible cargar las citas del evento') ?>');
                        return;
                    }

                    renderEventGrades(response.event?.grades || []);
                    renderInvolvedSubjects(response.subjects || []);
                    renderAppointments(response.appointments || []);
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || '<?= __('Error al cargar citas') ?>';
                    showError(message);
                    resetEventContext();
                    renderEmpty('<?= __('Error al consultar las citas') ?>');
                }
            });
        }

        function resetEventContext() {
            $('#eventGradesWrap').addClass('section-hidden');
            $('#eventGrades').empty();
            $('#subjectsCard').addClass('section-hidden');
            $('#subjectsList').empty();
        }

        function renderLoading() {
            $('#subjectGroupsContainer').html(`
                <div class="empty-state"><?= __('Cargando citas...') ?></div>
            `);
        }

        function renderEmpty(message) {
            $('#subjectGroupsContainer').html(`
                <div class="empty-state">${escapeHtml(message)}</div>
            `);
        }

        function renderEventGrades(grades) {
            const wrap = $('#eventGradesWrap');
            const container = $('#eventGrades');
            container.empty();

            if (!Array.isArray(grades) || !grades.length) {
                wrap.removeClass('section-hidden');
                container.append(`<span class="chip"><?= __('Sin grados configurados') ?></span>`);
                return;
            }

            grades.forEach(grade => {
                container.append(`<span class="chip">${escapeHtml(grade)}</span>`);
            });

            wrap.removeClass('section-hidden');
        }

        function renderInvolvedSubjects(subjects) {
            const card = $('#subjectsCard');
            const container = $('#subjectsList');
            container.empty();

            if (!Array.isArray(subjects) || !subjects.length) {
                card.removeClass('section-hidden');
                container.append(`<span class="chip"><?= __('No hay subjects involucradas para este evento') ?></span>`);
                return;
            }

            subjects.forEach(subject => {
                container.append(`<span class="chip">${escapeHtml(subject)}</span>`);
            });

            card.removeClass('section-hidden');
        }

        function renderAppointments(appointments) {
            const container = $('#subjectGroupsContainer');
            container.empty();

            if (!Array.isArray(appointments) || !appointments.length) {
                renderEmpty('<?= __('No hay citas en este evento para este profesor') ?>');
                return;
            }

            let rowsHtml = '';
            appointments.forEach(item => {
                const isBooked = item.status_value === BOOKED_STATUS;
                const actionsHtml = ACTIONS.map(action => {
                    const disabled = isBooked ? '' : 'disabled';
                    return `
                        <button
                            type="button"
                            class="btn ${action.className} btn-sm status-btn"
                            data-appointment-id="${escapeHtml(item.id)}"
                            data-status="${escapeHtml(action.status)}"
                            ${disabled}
                        >
                            ${escapeHtml(action.label)}
                        </button>
                    `;
                }).join('');

                rowsHtml += `
                    <tr data-appointment-id="${escapeHtml(item.id)}">
                        <td>${escapeHtml(item.parent_name)}</td>
                        <td>${escapeHtml(item.student_name)}</td>
                        <td>${escapeHtml(item.student_grade)}</td>
                        <td>
                            <div class="chip-list">
                                ${(item.subjects || []).map(s => `<span class="chip chip-sm">${escapeHtml(s)}</span>`).join('')}
                            </div>
                        </td>
                        <td>${escapeHtml(item.time_range)}</td>
                        <td>${escapeHtml(item.family_member_label)}</td>
                        <td>${escapeHtml(item.notes || '<?= __('Sin notas') ?>')}</td>
                        <td>
                            <span class="status-pill ${statusClassName(item.status_value)}" data-status-label data-status-value="${escapeHtml(item.status_value)}">
                                ${escapeHtml(item.status_label)}
                            </span>
                        </td>
                        <td>
                            <div class="status-actions">
                                ${actionsHtml}
                            </div>
                        </td>
                    </tr>
                `;
            });

            container.html(`
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th><?= __('Padres') ?></th>
                                <th><?= __('Estudiante') ?></th>
                                <th><?= __('Grado') ?></th>
                                <th><?= __('Materias') ?></th>
                                <th><?= __('Horario') ?></th>
                                <th><?= __('Miembro') ?></th>
                                <th><?= __('Notas') ?></th>
                                <th><?= __('Estado') ?></th>
                                <th><?= __('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>${rowsHtml}</tbody>
                    </table>
                </div>
            `);
        }

        function updateStatus(appointmentId, status) {
            hideMessages();

            const rows = $(`tr[data-appointment-id="${appointmentId}"]`);
            const buttons = $(`.status-btn[data-appointment-id="${appointmentId}"]`);
            buttons.prop('disabled', true);

            $.ajax({
                url: API_URL,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'updateStatus',
                    appointment_id: appointmentId,
                    status: status,
                },
                success: function(response) {
                    if (!response.success) {
                        showError(response.message || '<?= __('No fue posible actualizar el estado') ?>');
                        const isBooked = (String(rows.first().find('[data-status-label]').attr('data-status-value') || '') === BOOKED_STATUS);
                        buttons.prop('disabled', !isBooked);
                        return;
                    }

                    rows.find('[data-status-label]')
                        .text(response.status_label)
                        .attr('data-status-value', response.status_value)
                        .removeClass('status-booked status-done status-cancelled status-no-show')
                        .addClass(statusClassName(response.status_value));

                    buttons.prop('disabled', true);
                    showSuccess(response.message || '<?= __('Estado actualizado correctamente') ?>');
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || '<?= __('Error al actualizar estado') ?>';
                    showError(message);
                    const isBooked = (String(rows.first().find('[data-status-label]').attr('data-status-value') || '') === BOOKED_STATUS);
                    buttons.prop('disabled', !isBooked);
                }
            });
        }

        function statusClassName(statusValue) {
            if (statusValue === '<?= AppointmentStatusEnum::BOOKED->value ?>') {
                return 'status-booked';
            }
            if (statusValue === '<?= AppointmentStatusEnum::DONE->value ?>') {
                return 'status-done';
            }
            if (statusValue === '<?= AppointmentStatusEnum::CANCELLED->value ?>') {
                return 'status-cancelled';
            }
            return 'status-no-show';
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function toggleLoading(selector, show) {
            $(selector).toggleClass('d-none', !show);
        }

        function showError(message) {
            $('#successMessage').removeClass('show').text('');
            $('#errorMessage').addClass('show').text(message);
            window.scrollTo(0, 0);
        }

        function showSuccess(message) {
            $('#errorMessage').removeClass('show').text('');
            $('#successMessage').addClass('show').text(message);
            window.scrollTo(0, 0);
        }

        function hideMessages() {
            $('#errorMessage').removeClass('show').text('');
            $('#successMessage').removeClass('show').text('');
        }
    </script>
</body>

</html>