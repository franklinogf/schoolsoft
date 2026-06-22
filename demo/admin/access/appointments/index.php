<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use App\Services\SchoolService;

Session::is_logged();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Eventos de Citas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .table-responsive {
            border-radius: 0.25rem;
        }

        .badge-pill {
            padding: 0.5rem 1rem;
        }

        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= __('Eventos de Citas - Padres') ?></h1>

        <!-- Botón para crear evento -->
        <div class="mx-auto mb-3" style="max-width: 1000px;">
            <a class="btn btn-primary" href="./create.php">
                <i class="fas fa-plus"></i> <?= __('Crear Evento') ?>
            </a>
        </div>

        <!-- Tabla de eventos -->
        <div class="mx-auto table-responsive" style="max-width: 1000px;">
            <table class="table table-striped table-hover" id="eventsTable">
                <caption><?= __('Lista de eventos de citas') ?></caption>
                <thead class="table-dark">
                    <tr>
                        <th><?= __('Nombre') ?></th>
                        <th><?= __('Fecha') ?></th>
                        <th><?= __('Horario') ?></th>
                        <th><?= __('Grados') ?></th>
                        <th><?= __('Slots') ?></th>
                        <th><?= __('Estado') ?></th>
                        <th><?= __('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody id="eventsTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted"><?= __('Cargando...') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <script>
        $(document).ready(function() {
            loadEvents();
        });

        function loadEvents() {
            fetch('./includes/load.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = $('#eventsTableBody');
                    tbody.empty();

                    if (!Array.isArray(data) || data.length === 0) {
                        tbody.html(`<tr><td colspan="7" class="text-center text-muted"><?= __('No hay eventos') ?></td></tr>`);
                        return;
                    }

                    data.forEach(event => {
                        const gradesList = Array.isArray(event.grades) ? event.grades.join(', ') : '';
                        const statusBadge = event.is_active ?
                            '<span class="badge badge-success"><?= __('Activo') ?></span>' :
                            '<span class="badge badge-secondary"><?= __('Inactivo') ?></span>';

                        const row = `
                            <tr>
                                <td>${event.name}</td>
                                <td>${event.date}</td>
                                <td>${event.start_time} - ${event.end_time}</td>
                                <td>${gradesList}</td>
                                <td><span class="badge badge-info">${event.slot_count}</span></td>
                                <td>${statusBadge}</td>
                                <td class="action-buttons">
                                    <a class="btn btn-outline-primary btn-sm" href="./edit.php?id=${event.id}" title="<?= __('Editar') ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm deleteEventBtn" data-event-id="${event.id}" title="<?= __('Eliminar') ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    // Bind delete buttons
                    $('.deleteEventBtn').on('click', function() {
                        deleteEvent($(this).data('event-id'));
                    });
                })
                .catch(error => {
                    Toast.fire({
                        icon: 'error',
                        title: '<?= __('Error al cargar eventos') ?>'
                    });
                    console.error('Load events error:', error);
                });
        }

        function deleteEvent(eventId) {
            ConfirmationAlert.fire({
                title: '<?= __('¿Estás seguro?') ?>',
                text: '<?= __('¿Deseas eliminar este evento?') ?>',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementar delete si es necesario
                    alert('<?= __('Función de eliminación no implementada en este demo') ?>');
                    console.log('Delete event:', eventId);
                }
            });
        }
    </script>
</body>

</html>