<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use App\Models\Admin;

Session::is_logged();
$school = Admin::user(Session::id())->first();

// Obtener todos los años disponibles
$years = DB::table('year')->select("DISTINCT year")->orderBy('year', 'DESC')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Copiar datos de un año a otro');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
    <style>
        .data-option {
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .data-option:hover {
            border-color: #007bff;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        }

        .data-option input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .data-option label {
            margin-bottom: 0;
            cursor: pointer;
            font-size: 1.05rem;
            font-weight: 500;
        }

        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .warning-box i {
            color: #ffc107;
            font-size: 24px;
        }

        .year-badge {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .copy-summary {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .copy-summary ul {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-4 mt-5">
            <i class="fas fa-copy"></i>
            <?= __('Copiar datos de un año a otro') ?>
        </h1>

        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-4 rounded" style="max-width: 800px;">

                <!-- Mensaje de resultado -->
                <div id="resultMessage" style="display: none;"></div>

                <div class="warning-box">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <div>
                            <h5 class="mb-2"><?= __('Advertencia') ?></h5>
                            <p class="mb-1"><?= __('Esta acción copiará la información seleccionada del año de origen al año de destino.') ?></p>
                            <p class="mb-0"><strong><?= __('Los datos existentes en el año de destino NO serán eliminados.') ?></strong></p>
                        </div>
                    </div>
                </div>

                <form method="POST" id="copyForm">
                    <div class="row">
                        <!-- Año de origen -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="source_year" class="font-weight-bold">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= __('Año de origen') ?>
                                </label>
                                <select class="form-control selectpicker" name="source_year" id="source_year" data-live-search="true" required>
                                    <option value=""><?= __('Seleccionar año') ?></option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= $year->year ?>" <?= $school->year() == $year->year ? 'selected' : '' ?>>
                                            <?= $year->year ?>
                                            <?= $school->year() == $year->year ? ' (' . __('Año actual') . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Año de destino -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dest_year" class="font-weight-bold">
                                    <i class="fas fa-calendar-check"></i>
                                    <?= __('Año de destino') ?>
                                </label>
                                <select class="form-control selectpicker" name="dest_year" id="dest_year" data-live-search="true" required>
                                    <option value=""><?= __('Seleccionar año') ?></option>
                                    <?php foreach ($years as $year): ?>
                                        <option value="<?= $year->year ?>">
                                            <?= $year->year ?>
                                            <?= $school->year() == $year->year ? ' (' . __('Año actual') . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3 font-weight-bold">
                        <i class="fas fa-list-check"></i>
                        <?= __('Seleccione los datos a copiar') ?>
                    </h5>

                    <div class="row">
                        <!-- Catálogo -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_catalog" id="opt_catalog" value="1">
                                    <label class="custom-control-label" for="opt_catalog">
                                        <i class="fas fa-book text-primary"></i>
                                        <?= __('Catálogo de cursos') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Cursos -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_courses" id="opt_courses" value="1">
                                    <label class="custom-control-label" for="opt_courses">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                        <?= __('Cursos (Materias)') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Presupuesto -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_budget" id="opt_budget" value="1">
                                    <label class="custom-control-label" for="opt_budget">
                                        <i class="fas fa-dollar-sign text-warning"></i>
                                        <?= __('Presupuesto') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Costos -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_costs" id="opt_costs" value="1">
                                    <label class="custom-control-label" for="opt_costs">
                                        <i class="fas fa-receipt text-danger"></i>
                                        <?= __('Costos') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Fotos -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_photos" id="opt_photos" value="1">
                                    <label class="custom-control-label" for="opt_photos">
                                        <i class="fas fa-camera text-info"></i>
                                        <?= __('Fotos') ?>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Balances -->
                        <div class="col-md-6">
                            <div class="data-option">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="opt_balances" id="opt_balances" value="1">
                                    <label class="custom-control-label" for="opt_balances">
                                        <i class="fas fa-utensils text-secondary"></i>
                                        <?= __('Balances de cafetería') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <button type="button" class="btn btn-secondary btn-block" onclick="window.location.href='<?= Route::url('/admin/access/index.php') ?>'">
                                <i class="fas fa-arrow-left"></i>
                                <?= __('Volver') ?>
                            </button>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button type="submit" class="btn btn-primary btn-block" name="copy_data">
                                <i class="fas fa-copy"></i>
                                <?= __('Copiar datos') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>

    <script>
        $(document).ready(function() {
            // Validación del formulario
            $('#copyForm').on('submit', function(e) {
                e.preventDefault();

                const sourceYear = $('#source_year').val();
                const destYear = $('#dest_year').val();
                const checkboxes = $('input[type="checkbox"]:checked').length;

                if (!sourceYear || !destYear) {
                    alert('<?= __('Debe seleccionar el año de origen y destino') ?>');
                    return false;
                }

                if (sourceYear === destYear) {
                    alert('<?= __('El año de origen y destino deben ser diferentes') ?>');
                    return false;
                }

                if (checkboxes === 0) {
                    alert('<?= __('Seleccione al menos un tipo de dato para copiar') ?>');
                    return false;
                }

                // Confirmación antes de copiar
                const confirmMsg = '<?= __('¿Está seguro que desea continuar?') ?>\n\n' +
                    '<?= __('Año de origen') ?>: ' + sourceYear + '\n' +
                    '<?= __('Año de destino') ?>: ' + destYear + '\n' +
                    '<?= __('Datos a copiar') ?>: ' + checkboxes;

                if (!confirm(confirmMsg)) {
                    return false;
                }

                // Mostrar loading
                const $btn = $(this).find('button[name="copy_data"]');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?= __('Procesando...') ?>');

                // Enviar formulario vía AJAX
                $.ajax({
                    url: 'copy_action.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        $btn.prop('disabled', false).html(originalText);

                        let messageHtml = '';
                        if (response.success) {
                            messageHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-check-circle"></i></strong>
                                    ${response.message}`;

                            if (response.copiedData && response.copiedData.length > 0) {
                                messageHtml += `
                                    <div class="mt-3">
                                        <strong><?= __('Se copiaron los siguientes datos:') ?></strong>
                                        <ul class="mt-2 mb-0">`;
                                response.copiedData.forEach(function(data) {
                                    messageHtml += `<li>${data}</li>`;
                                });
                                messageHtml += `</ul></div>`;
                            }

                            messageHtml += `
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;

                            // Reset form checkboxes
                            $('input[type="checkbox"]').prop('checked', false).trigger('change');
                        } else {
                            messageHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-triangle"></i></strong>
                                    ${response.message}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>`;
                        }

                        $('#resultMessage').html(messageHtml).show();

                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: $('#resultMessage').offset().top - 100
                        }, 500);
                    },
                    error: function(xhr, status, error) {
                        $btn.prop('disabled', false).html(originalText);

                        const messageHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle"></i></strong>
                                <?= __('Error al copiar los datos') ?>: ${error}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>`;

                        $('#resultMessage').html(messageHtml).show();
                    }
                });
            });

            // Resaltar la opción seleccionada
            $('input[type="checkbox"]').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).closest('.data-option').addClass('border-primary bg-light');
                } else {
                    $(this).closest('.data-option').removeClass('border-primary bg-light');
                }
            });
        });
    </script>
</body>

</html>