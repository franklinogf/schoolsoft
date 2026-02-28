<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;

?>
<?php Route::includeFile('/admin/includes/layouts/head.php'); ?>
<style>
    #calendar {
        max-width: 1180px;
        margin-inline: auto;
        padding-block: 20px;
    }

    .color-preview {
        display: inline-block;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1px solid #ced4da;
        margin-left: 8px;
        vertical-align: middle;
    }
</style>

<div id="calendar"></div>

<div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable"" role=" document">
        <form class="modal-content" id="create-event-form">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Crear evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="create-event-title">Título</label>
                    <input type="text" id="create-event-title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="create-event-description">Descripción</label>
                    <textarea id="create-event-description" name="description" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="create-event-location">Ubicación</label>
                    <input type="text" id="create-event-location" name="location" class="form-control">
                </div>

                <div class="form-group">
                    <label for="create-event-color">Color <span id="create-event-color-preview" class="color-preview"></span></label>
                    <select id="create-event-color" name="color" class="form-control">
                        <option value="#007bff">Azul</option>
                        <option value="#28a745">Verde</option>
                        <option value="#dc3545">Rojo</option>
                        <option value="#ffc107">Amarillo</option>
                        <option value="#17a2b8">Celeste</option>
                        <option value="#6c757d">Gris</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="create-event-start">Inicio</label>
                    <input type="datetime-local" id="create-event-start" name="start" class="form-control" required>
                </div>

                <div class="form-group mb-0">
                    <label for="create-event-end">Fin</label>
                    <input type="datetime-local" id="create-event-end" name="end" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="create-event">Crear</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable"" role=" document">
        <form class="modal-content" id="update-event-form">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Editar evento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="event-id" name="id">

                <div class="form-group">
                    <label for="event-title">Título</label>
                    <input type="text" id="event-title" name="title" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="event-description">Descripción</label>
                    <textarea id="event-description" name="description" class="form-control" rows="2"></textarea>
                </div>

                <div class="form-group">
                    <label for="event-location">Ubicación</label>
                    <input type="text" id="event-location" name="location" class="form-control">
                </div>

                <div class="form-group">
                    <label for="event-color">Color <span id="event-color-preview" class="color-preview"></span></label>
                    <select id="event-color" name="color" class="form-control">
                        <option value="#007bff">Azul</option>
                        <option value="#28a745">Verde</option>
                        <option value="#dc3545">Rojo</option>
                        <option value="#ffc107">Amarillo</option>
                        <option value="#17a2b8">Celeste</option>
                        <option value="#6c757d">Gris</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="event-start">Inicio</label>
                    <input type="datetime-local" id="event-start" name="start" class="form-control" required>
                </div>

                <div class="form-group mb-0">
                    <label for="event-end">Fin</label>
                    <input type="datetime-local" id="event-end" name="end" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="delete-event">Eliminar</button>
                <button type="submit" class="btn btn-primary" id="update-event">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script src="
https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js
"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let activeEvent = null;
        let selectedRange = null;
        const defaultEventColor = '#007bff';
        const eventColors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6c757d'];

        function normalizeEventColor(color) {
            return eventColors.includes(color) ? color : defaultEventColor;
        }

        function updateColorPreview(selectId, previewId) {
            const selectedColor = normalizeEventColor(document.getElementById(selectId).value);
            document.getElementById(previewId).style.backgroundColor = selectedColor;
        }

        function toDateTimeLocal(dateValue) {
            if (!dateValue) {
                return '';
            }

            const date = new Date(dateValue);
            const timezoneOffset = date.getTimezoneOffset() * 60000;

            return new Date(date.getTime() - timezoneOffset).toISOString().slice(0, 16);
        }

        const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            nowIndicator: true,
            themeSystem: 'bootstrap',
            initialView: 'dayGridMonth',
            selectable: true,
            editable: false,
            droppable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,list'
            },
            events: './includes/load.php',
            eventDidMount: function(info) {
                if (!info.event.extendedProps.description) {
                    return;
                }
                $(info.el).tooltip({
                    title: info.event.extendedProps.description || '',
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },

            select: function(info) {
                selectedRange = info;
                document.getElementById('create-event-title').value = '';
                document.getElementById('create-event-description').value = '';
                document.getElementById('create-event-location').value = '';
                document.getElementById('create-event-color').value = defaultEventColor;
                updateColorPreview('create-event-color', 'create-event-color-preview');
                document.getElementById('create-event-start').value = toDateTimeLocal(info.start);
                document.getElementById('create-event-end').value = toDateTimeLocal(info.end);
                $('#createEventModal').modal('show');
            },

            eventClick: function({
                event
            }) {
                activeEvent = event;

                document.getElementById('event-id').value = event.id || '';
                document.getElementById('event-title').value = event.title || '';
                document.getElementById('event-description').value = event.extendedProps.description || '';
                document.getElementById('event-location').value = event.extendedProps.location || '';
                document.getElementById('event-color').value = normalizeEventColor(event.backgroundColor || event.extendedProps.color);
                updateColorPreview('event-color', 'event-color-preview');
                document.getElementById('event-start').value = toDateTimeLocal(event.start);
                document.getElementById('event-end').value = toDateTimeLocal(event.end);

                $('#eventModal').modal('show');
            }

        });

        calendar.render();

        updateColorPreview('create-event-color', 'create-event-color-preview');
        updateColorPreview('event-color', 'event-color-preview');

        document.getElementById('create-event-color').addEventListener('change', function() {
            updateColorPreview('create-event-color', 'create-event-color-preview');
        });

        document.getElementById('event-color').addEventListener('change', function() {
            updateColorPreview('event-color', 'event-color-preview');
        });

        document.getElementById('create-event-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());
            payload.color = normalizeEventColor(payload.color);

            if (!payload.title || !payload.start) {
                return;
            }

            fetch('./includes/store.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(function(response) {
                if (!response.ok) {
                    return response.json().then(function(error) {
                        alert(error.error || 'Error al crear el evento. Por favor, inténtalo de nuevo.');
                    });
                }
                $('#createEventModal').modal('hide');
                calendar.unselect();
                calendar.refetchEvents();
                selectedRange = null;
            })
        });

        $('#createEventModal').on('hidden.bs.modal', function() {
            calendar.unselect();
            selectedRange = null;
        });

        document.getElementById('update-event-form').addEventListener('submit', function(event) {
            event.preventDefault();

            if (!activeEvent) {
                return;
            }

            const formData = new FormData(event.currentTarget);
            const payload = Object.fromEntries(formData.entries());
            payload.color = normalizeEventColor(payload.color);

            fetch('./includes/udpate.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(function(response) {
                if (!response.ok) {
                    return response.json().then(function(error) {
                        alert(error.error || 'Error al actualizar el evento. Por favor, inténtalo de nuevo.');
                    });
                }
                const updatedTitle = payload.title;
                const updatedDescription = payload.description;
                const updatedLocation = payload.location;
                const updatedColor = payload.color;
                const updatedStart = payload.start;
                const updatedEnd = payload.end;

                activeEvent.setProp('title', updatedTitle);
                activeEvent.setExtendedProp('description', updatedDescription);
                activeEvent.setExtendedProp('location', updatedLocation);
                activeEvent.setExtendedProp('color', updatedColor);
                activeEvent.setProp('backgroundColor', updatedColor);
                activeEvent.setProp('borderColor', updatedColor);

                if (updatedStart) {
                    activeEvent.setStart(updatedStart);
                }

                activeEvent.setEnd(updatedEnd || null);

                $('#eventModal').modal('hide');
            });
        });

        document.getElementById('delete-event').addEventListener('click', function() {
            if (!activeEvent) {
                return;
            }

            fetch('./includes/destroy.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: document.getElementById('event-id').value
                })
            }).then(function(response) {
                if (!response.ok) {
                    return response.json().then(function(error) {
                        alert(error.error || 'Error al eliminar el evento. Por favor, inténtalo de nuevo.');
                    });
                }
                activeEvent.remove();
                activeEvent = null;
                $('#eventModal').modal('hide');
            });
        });
    });
</script>

<?php Route::includeFile('/admin/includes/layouts/footer.php'); ?>