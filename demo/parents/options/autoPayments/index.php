<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$year = Admin::primaryAdmin()->year;
$students = Student::query()->where('id', Session::id())->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Pagos Automaticos');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <style>
        .step.locked { opacity: .5; pointer-events: none; }
        .step-number { display: inline-block; width: 1.6rem; height: 1.6rem; line-height: 1.6rem; text-align: center; border-radius: 50%; background: #1f4e79; color: #fff; margin-right: .25rem; }
    </style>
    <?php Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('Pagos Automaticos') ?></h1>

        <div class="row">
            <div class="col-12 col-lg-4 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Autopagos Guardados') ?></span>
                        <button type="button" id="newAutopay" class="btn btn-sm btn-primary"><?= __('Nuevo') ?></button>
                    </div>
                    <div id="autopayList" class="list-group list-group-flush"></div>
                </div>
            </div>

            <div class="col-12 col-lg-8">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <span id="formTitle"><?= __('Nuevo Autopago') ?></span>
                            <span id="stateBadge" class="badge badge-secondary ml-2 d-none"></span>
                        </span>
                        <span>
                            <button type="button" id="pauseAutopay" class="btn btn-sm btn-outline-secondary d-none"><?= __('Pausar') ?></button>
                            <button type="button" id="resumeAutopay" class="btn btn-sm btn-outline-success d-none"><?= __('Reanudar') ?></button>
                            <button type="button" id="deleteAutopay" class="btn btn-sm btn-outline-danger d-none"><?= __('Eliminar') ?></button>
                        </span>
                    </div>
                    <div id="stateNotice" class="alert alert-danger rounded-0 mb-0 d-none" role="alert"></div>
                </div>

                <div class="card mb-3 step" id="step1">
                    <div class="card-header"><span class="step-number">1</span> <?= __('Datos y cargos') ?> <span class="step-done text-success d-none">&#10003;</span></div>
                    <div class="card-body">
                        <input type="hidden" id="autoPayId" value="">

                        <div class="form-row align-items-end">
                            <div class="form-group col-12 col-md-7">
                                <label for="email" class="form-label"><?= __('Email') ?></label>
                                <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                            </div>
                            <div id="dayOfPaymentWrapper" class="form-group col-12 col-md-5">
                                <label for="dayOfPayment"><?= __('Dia de pago automatico') ?></label>
                                <input class="form-control" type="number" id="dayOfPayment" min="1" max="30" step="1" required>
                            </div>
                        </div>
                        <button type="button" id="saveHeader" class="btn btn-primary"><?= __('Guardar') ?></button>

                        <div id="itemsSection" class="mt-4">
                            <h6><?= __('Agregar Cargo') ?></h6>
                            <div class="form-row">
                                <div class="form-group col-12 col-md-4">
                                    <label for="itemStudent"><?= __('Estudiante') ?></label>
                                    <select id="itemStudent" class="form-control">
                                        <option value=""></option>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?= $student->mt ?>"><?= "{$student->apellidos} {$student->nombre}" ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-4">
                                    <label for="itemBudget"><?= __('Codigo') ?></label>
                                    <select id="itemBudget" class="form-control">
                                        <option value=""><?= __('Seleccione estudiante primero') ?></option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-4">
                                    <label for="itemAmount"><?= __('Cantidad (desde Pagos)') ?></label>
                                    <input id="itemAmount" class="form-control" type="number" min="0" step="0.01" readonly>
                                </div>
                            </div>
                            <button type="button" id="addItem" class="btn btn-outline-primary"><?= __('Guardar Cargo') ?></button>

                            <div class="d-flex justify-content-between align-items-center mt-4 mb-1">
                                <h6 class="mb-0"><?= __('Renglones del Autopago') ?></h6>
                                <span><?= __('Total') ?>: $<strong id="totalAmount">0.00</strong></span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th><?= __('Estudiante') ?></th>
                                            <th><?= __('Codigo') ?></th>
                                            <th><?= __('Descripcion') ?></th>
                                            <th class="text-right"><?= __('Cantidad') ?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted"><?= __('No hay renglones guardados') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 step" id="step2">
                    <div class="card-header"><span class="step-number">2</span> <?= __('Tarjeta') ?> <span class="step-done text-success d-none">&#10003;</span></div>
                    <div class="card-body">
                        <div id="cardStatus" class="alert alert-secondary d-none" role="alert"></div>
                        <button type="button" id="registerCard" class="btn btn-outline-primary d-none"><?= __('Registrar tarjeta') ?></button>
                        <p id="step2Locked" class="text-muted mb-0"><?= __('Guarde primero el paso 1.') ?></p>
                    </div>
                </div>

                <div class="card mb-3 step" id="step3">
                    <div class="card-header"><span class="step-number">3</span> <?= __('Confirmar') ?> <span class="step-done text-success d-none">&#10003;</span></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-3" id="summary">
                            <li><?= __('Tarjeta') ?>: <strong id="sumCard">-</strong></li>
                            <li><?= __('Dia de pago automatico') ?>: <strong id="sumDay">-</strong></li>
                            <li><?= __('Renglones') ?>: <strong id="sumItems">0</strong></li>
                            <li><?= __('Total estimado') ?>: $<strong id="sumTotal">0.00</strong></li>
                        </ul>
                        <p class="text-muted small"><?= __('El monto final se calcula el dia del cobro segun sus balances pendientes. Recibira un aviso 3 dias antes.') ?></p>
                        <button type="button" id="activateAutopay" class="btn btn-success" disabled><?= __('Activar pago automatico') ?></button>
                        <p id="step3Hint" class="text-muted small mt-2 mb-0"></p>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header"><?= __('Historial de cobros') ?></div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th><?= __('Fecha') ?></th>
                                    <th><?= __('Mes') ?></th>
                                    <th><?= __('Resultado') ?></th>
                                    <th><?= __('Autorizacion') ?></th>
                                    <th><?= __('Referencia') ?></th>
                                </tr>
                            </thead>
                            <tbody id="historyBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted"><?= __('Seleccione un autopago') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerCardModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="modal-content" method="post" action="./includes/start.php">
                <div class="modal-header">
                    <h5 class="modal-title"><?= __('Registrar tarjeta') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted"><?= __('Sera redirigido a PlacetoPay para registrar su tarjeta de forma segura. No se realizara ningun cargo ahora.') ?></p>
                    <input type="hidden" name="autoPayId" id="registerAutoPayId" value="">
                    <div class="form-group">
                        <label for="first_name"><?= __('Nombre') ?></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name"><?= __('Apellidos') ?></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="mobile"><?= __('Celular') ?></label>
                        <input type="tel" class="form-control" id="mobile" name="mobile" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" required>
                        <label class="form-check-label" for="terms">
                            <?= __('Acepto los') ?> <a href="../placetopay/terms.php" target="_blank"><?= __('terminos y condiciones') ?></a>
                            <?= __('y autorizo los cobros automaticos mensuales a la tarjeta registrada.') ?>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= __('Cancelar') ?></button>
                    <button type="submit" class="btn btn-primary"><?= __('Continuar') ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.autoPaymentsMeta = {
            accountId: <?= (int) Session::id() ?>,
            year: '<?= $year ?>'
        };
    </script>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>
</body>

</html>