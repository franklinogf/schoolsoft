<?php
require_once __DIR__ . '/../app.php';

use Classes\Route;
use App\Models\Admin;

$school = Admin::primaryAdmin();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $school->colegio ?> &mdash; <?= __('Calendario') ?></title>
    <link rel="icon" href="<?= school_logo() ?>">
    <?= Route::bootstrapCSS() ?>
    <?php Route::css('/css/main.css', true) ?>
    <?php Route::fontawasome() ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js"></script>
    <style>
        #calendar {
            max-width: 1180px;
            margin-inline: auto;
            padding-block: 20px;
        }
    </style>
</head>

<body>
    <header class="hero-header bg-dark py-4">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center flex-wrap">
                <img class="img-fluid" src="<?= school_logo() ?>" alt="School Logo" width="<?= school_config('app.logo.size.home') ?>">
                <h1 class="display-4 text-white text-center ml-3"><?= $school->colegio ?></h1>
            </div>
        </div>
    </header>

    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><?= __('Calendario') ?></h2>
            <a href="<?= Route::url('/') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i><?= __('Volver al menú principal') ?>
            </a>
        </div>

        <div id="calendar"></div>
    </main>

    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="event-description" class="mb-2"></p>
                    <p id="event-location" class="text-muted mb-2"></p>
                    <p id="event-start" class="mb-1 small"></p>
                    <p id="event-end" class="mb-0 small"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Cerrar') ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true) ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                nowIndicator: true,
                themeSystem: 'bootstrap',
                initialView: 'dayGridMonth',
                selectable: false,
                editable: false,
                droppable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
                },
                validRange: {
                    start: new Date(new Date().getFullYear(), new Date().getMonth(), 1)
                },
                events: './includes/load.php',
                eventDidMount: function(info) {
                    if (!info.event.extendedProps.description) return;
                    $(info.el).tooltip({
                        title: info.event.extendedProps.description,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventClick: function({
                    event
                }) {
                    const fmt = (iso) => iso ? new Date(iso).toLocaleString(__LANG) : '';

                    document.getElementById('eventModalLabel').textContent = event.title;

                    const desc = event.extendedProps.description;
                    const loc = event.extendedProps.location;

                    const descEl = document.getElementById('event-description');
                    descEl.textContent = desc || '';
                    descEl.hidden = !desc;

                    const locEl = document.getElementById('event-location');
                    locEl.textContent = loc ? '📍 ' + loc : '';
                    locEl.hidden = !loc;

                    document.getElementById('event-start').textContent = event.start ? '<?= __('Comienza') ?>: ' + fmt(event.start) : '';
                    const endEl = document.getElementById('event-end');
                    endEl.textContent = event.end ? '<?= __('Termina') ?>: ' + fmt(event.end) : '';
                    endEl.hidden = !event.end;

                    $('#eventModal').modal('show');
                }
            });

            calendar.render();
        });
    </script>
</body>

</html>