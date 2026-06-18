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
                        <span id="formTitle"><?= __('Nuevo Autopago') ?></span>
                        <button type="button" id="deleteAutopay" class="btn btn-sm btn-outline-danger d-none"><?= __('Eliminar') ?></button>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="autoPayId" value="">

                        <div class="form-row">
                            <div class="form-group col-12 col-md-8">
                                <label for="email" class="form-label"><?= __('Email') ?></label>
                                <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                            </div>
                        </div>

                        <h5 class="mt-2"><?= __('Metodo de pago') ?></h5>
                        <ul class="nav nav-pills mb-3" id="methods-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cardMethod-tab" data-toggle="pill" data-target="#cardMethod" type="button" role="tab" aria-controls="cardMethod" aria-selected="true"><?= __('Tarjeta') ?></button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="achMethod-tab" data-toggle="pill" data-target="#achMethod" type="button" role="tab" aria-controls="achMethod" aria-selected="false">ACH</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="cardMethod" role="tabpanel" aria-labelledby="cardMethod-tab">
                                <div class="form-row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="cc-name"><?= __('Nombre en la tarjeta') ?></label>
                                        <input type="text" class="form-control justText" id="cc-name" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="cc-number"><?= __('Numero de la tarjeta') ?></label>
                                        <input type="text" class="form-control" id="cc-number" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="cc-expiration"><?= __('Expiracion') ?></label>
                                        <input type="text" class="form-control" id="cc-expiration" placeholder="MM/YY" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="cc-cvv">CVV</label>
                                        <input type="password" class="form-control" id="cc-cvv" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="cc-zip"><?= __('Codigo Postal') ?></label>
                                        <input type="text" class="form-control zip" id="cc-zip" autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="achMethod" role="tabpanel" aria-labelledby="achMethod-tab">
                                <div class="form-row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="ach-name"><?= __('Nombre en la cuenta') ?></label>
                                        <input type="text" class="form-control justText" id="ach-name" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-6">
                                        <label for="ach-type"><?= __('Tipo de cuenta') ?></label>
                                        <select id="ach-type" class="form-control">
                                            <option value="">Selecciona</option>
                                            <option value="w"><?= __('Cuenta de cheques') ?></option>
                                            <option value="s"><?= __('Cuenta de ahorros') ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="ach-number"><?= __('Numero de cuenta') ?></label>
                                        <input type="text" class="form-control justNumber" id="ach-number" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="ach-route"><?= __('Numero de ruta') ?></label>
                                        <input type="text" class="form-control justNumber" id="ach-route" autocomplete="off">
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label for="ach-zip"><?= __('Codigo Postal') ?></label>
                                        <input type="text" class="form-control zip" id="ach-zip" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row align-items-center mt-2">
                            <div id="dayOfPaymentWrapper" class="col-12 col-md-5 mt-2 mt-md-0">
                                <label for="dayOfPayment" class="mb-1"><?= __('Dia de pago automatico') ?></label>
                                <input class="form-control" type="number" id="dayOfPayment" min="1" max="30" step="1" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="button" id="saveHeader" class="btn btn-primary"><?= __('Guardar Metodo de Pago') ?></button>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header"><?= __('Agregar Cargo') ?></div>
                    <div class="card-body">
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
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Renglones del Autopago') ?></span>
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
    </div>

    <script>
        window.autoPaymentsMeta = {
            accountId: <?= (int) Session::id() ?>,
            year: '<?= $year ?>'
        };
    </script>

    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>
</body>

</html>