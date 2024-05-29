<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Session;
use Classes\Lang;
use Classes\Controllers\Student;
use Classes\Controllers\School;
use Classes\DataBase\DB;

Session::is_logged();


$lang = new Lang([
    ["Pagos", "Payments"],
    ['estudiante', 'student'],
    ['Buscar información', 'Search information'],
]);

$students = new Student();
$months = __LANG === 'es' ?
    ['Julio' => '07', 'Agosto' => '08', 'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12', 'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04', 'Mayo' => '05', 'Junio' => '06']
    : ['July' => '07', 'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12', 'January' => '01', 'February' => '02', 'March' => '03', 'April' => '04', 'May' => '05', 'June' => '06'];
$currentMonth = $_GET['month'] ?: date('m');
$school = new School();
$year = $school->year();
$paymentTypes = [
    '1' => 'Efectivo',
    '2' => 'Cheque',
    '3' => 'ATH',
    '4' => 'Tarjeta Credito',
    '5' => 'Giro',
    '6' => 'Nomina',
    '7' => 'Banco',
    '8' => 'Pago Directo',
    '9' => 'Tele Pago',
    '10' => 'Paypal',
    '11' => 'Beca',
    '12' => 'ATH Movil',
    '13' => 'Credito a Cuenta',
    '14' => 'Virtual Terminal',
];
$codes = DB::table('presupuesto')->where([
    ["year", $year]
])->orderBy('codigo')->get();

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Pagos");
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    Route::fontawasome();
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Pagos") ?></h1>
        <form method="GET">
            <select class="form-control selectpicker w-100" name="accountId" data-live-search="true" required>
                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                <?php foreach ($students->All() as $student): ?>
                    <option <?= isset($_REQUEST['accountId']) && $_REQUEST['accountId'] == $student->id ? 'selected=""' : '' ?> value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                <?php endforeach ?>
            </select>
            <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar información") ?></button>
        </form>

        <?php if (isset($_REQUEST['accountId'])):
            $accountId = $_REQUEST['accountId'];
            $accountStudents = $students->findById($accountId);
            $paymentsQuery = DB::table('pagos')->where([
                ['id', $accountId],
                ['year', $year],
                ['baja', '']
            ])->get();

            $debtData = [];
            foreach ($paymentsQuery as $row) {
                $month = date('m', strtotime($row->fecha_d));
                $debtData[$month][] = $row;
            }
            ?>
            <div class="my-4">
                <h2>Estudiantes en esta cuenta</h2>
                <div class="card-deck">
                    <?php foreach ($accountStudents as $student): ?>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= "$student->apellidos $student->nombre ($student->grado)" ?></h5>
                                <button class="btn btn-sm btn-primary">Deposito <?= number_format($student->cantidad, 2) ?></button>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div id="monthsButtons" class="row row-cols-3 row-cols-lg-6 justify-content-around mb-2">
                <?php foreach ($months as $name => $number): ?>
                    <div class="col mb-1">
                        <button data-month="<?= $number ?>" class="btn w-100 <?= $currentMonth === $number ? 'active' : '' ?>"><?= $name ?></button>
                    </div>
                <?php endforeach ?>
            </div>
            <div id="paymentButtons" class="row row-cols-3 row-cols-lg-6 justify-content-around mb-2">
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#paymentModal">Hacer un pago</button>
                </div>
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#addChargeModal">Añadir cargo</button>
                </div>
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#statementModal">Estado de cuenta</button>
                </div>
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100">Recibo de pago</button>
                </div>
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100">Vencido</button>
                </div>
                <div class="col mb-1">
                    <button class="btn btn-secondary w-100 h-100">Pago moroso</button>
                </div>

            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Grado</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Deudas</th>
                            <th scope="col">Pagos</th>
                            <th scope="col">Fecha Pago</th>
                            <th scope="col">TDP</th>
                            <th scope="col">Rec.</th>
                            <td></td>
                        </tr>
                    </thead>
                    <?php foreach ($months as $name => $number): ?>
                        <tbody id="table<?= $number ?>" class="<?= $currentMonth !== $number ? 'hidden' : '' ?> monthTable">
                            <?php if ($debtData[$number]): ?>
                                <?php foreach ($debtData[$number] as $payment): ?>
                                    <tr data-id="<?= $payment->codigo ?>">
                                        <th scope="row"><?= $payment->codigo ?></th>
                                        <td><?= $payment->grado ?></td>
                                        <td><?= $payment->desc1 ?></td>
                                        <td><?= $payment->fecha_d ?></td>
                                        <td class="text-right debt"><?= number_format($payment->deuda, 2) ?></td>
                                        <td class="text-right payment"><?= number_format($payment->pago, 2) ?></td>
                                        <td><?= $payment->fecha_p === '0000-00-00' ? '' : $payment->fecha_p ?></td>
                                        <td><?= $paymentTypes[$payment->tdp] ?></td>
                                        <td><?= $payment->rec !== 0 ? $payment->rec : '' ?></td>
                                        <td class="text-right">
                                            <i data-id="<?= $payment->mt ?>" role="button" class="delete fa-solid fa-trash text-danger pointer-cursor"></i>
                                            <i data-id="<?= $payment->mt ?>" role="button" class="<?= $payment->deuda ? 'editCharge' : 'editPayment' ?> fa-solid fa-pen-to-square text-info pointer-cursor"></i>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>

                        </tbody>
                    <?php endforeach ?>
                    <tfoot class="table-primary">
                        <td colspan="4" class="text-right">Total:</td>
                        <th id="totalDebts" class="text-right"></th>
                        <th id="totalPayments" class="text-right"></th>
                        <td colspan="4">Balance: <span class="font-weight-bold" id="totalBalance"></span></td>

                    </tfoot>
                </table>
            </div>
        <?php endif ?>

    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Hacer un pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= Route::url('/admin/billing/payments/includes/makePayment.php') ?>">
                    <input type="hidden" id="monthToPay" name="monthToPay" value="<?= $currentMonth ?>">
                    <input type="hidden" id="accountId" name="accountId" value="<?= $accountId ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="paymentMode">Modo de pago</label>
                            <select class="form-control" id="paymentMode" name="paymentMode">
                                <option value="completo">Pago completo</option>
                                <option value="parcial">Pago parcial</option>
                            </select>
                        </div>
                        <div class="card p-2 my-2 hidden" id="parcialPaymentDebts">

                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="paymentType">Tipo de pago</label>
                                <select class="form-control" id="paymentType" name="paymentType">
                                    <?php foreach ($paymentTypes as $id => $label): ?>
                                        <option value="<?= $id ?>"><?= $label ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="chkNum">No. de CHK</label>
                                <input type="text" class="form-control" data-mask="0#" id="chkNum" name="chkNum" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="receiptType">No. de recibo</label>
                                <?php if ($school->info('rec') === '1'): ?>
                                    <select class="form-control" id="receiptType" name="receiptType">
                                        <option value="1">Recibo nuevo</option>
                                        <option value="2">El mismo recibo</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control" id="receiptNum" name="receiptNum" />
                                <?php endif ?>

                            </div>
                            <div class="form-group col-6">
                                <label for="bash">Bash</label>
                                <select class="form-control" <?= $school->info('caja') != 0 ? 'disabled' : '' ?> id="bash" name="bash">
                                    <?php for ($i = 0; $i <= 10; $i++): ?>
                                        <option <?= $school->info('rec') === $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                                <?php if ($school->info('caja') != 0): ?>
                                    <input type="hidden" class="form-control" name="bash" value="<?= $school->info('caja') ?>" />
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="paymentDate">Fecha</label>
                            <input type="date" class="form-control" id="paymentDate" name="paymentDate" value="<?= date('Y-m-d') ?>" />
                        </div>
                        <div>
                            <span>Cantidad a pagar</span>
                            <span class="badge badge-primary" id="paymentTotal">0.00</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="paymentButton" type="submit" class="btn btn-primary">Pagar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Charge Modal -->
    <div class="modal fade" id="addChargeModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addChargeModalLabel">Añadir cargo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="<?= Route::url('/admin/billing/payments/includes/addCharge.php') ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Codigo</label>
                            <select class="form-control" id="code" name="code">
                                <?php foreach ($codes as $code): ?>
                                    <option value="<?= $code->codigo ?>"><?= $code->descripcion ?></option>
                                <?php endforeach ?>
                            </select>
                            <input type="text" class="form-control" id="codeDescription" name="codeDescription" />
                        </div>
                        <div class="form-group">
                            <label for="chargeTo">Aplicar a</label>
                            <select class="form-control" id="chargeTo" name="chargeTo">
                                <?php foreach ($accountStudents as $student): ?>
                                    <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre ($student->grado)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>


                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="month">Mes para aplicar</label>
                                <select class="form-control" id="month" name="month">
                                    <?php foreach ($months as $name => $monthNumber): ?>
                                        <option value="<?= $monthNumber ?>"><?= $name ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="allMonths" name="allMonths">
                                    <label class="custom-control-label w-100" for="allMonths">T.L.M</label>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="amount">Cantidad a pagar</label>
                                <input type="text" class="form-control" data-mask="#,##0.00" data-mask-reverse="true" id="amount" name="amount" />
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Pagar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Charge Modal -->
    <div class="modal fade" id="editChargeModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editChargeModalLabel">Editar cargo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="PUT" action="<?= Route::url('/admin/billing/payments/includes/editCharge.php') ?>">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="chargeTo">Aplicar a</label>
                            <select class="form-control" id="chargeTo" name="chargeTo">
                                <?php foreach ($accountStudents as $student): ?>
                                    <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre ($student->grado)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="amount">Cantidad a pagar</label>
                                <input type="text" class="form-control" data-mask="#,##0.00" data-mask-reverse="true" id="amount" name="amount" />
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Pagar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    Route::selectPicker('js');
    Route::js('/js/jquery.mask.min.js', true);
    ?>
</body>

</html>