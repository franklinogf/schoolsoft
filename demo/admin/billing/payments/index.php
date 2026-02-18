<?php
require_once __DIR__ . '/../../../app.php';

use App\Enums\AdminPermission;
use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$user = Admin::user(Session::id())->first();

if (!$user->hasPermissionTo(AdminPermission::ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS)) {
    Route::forbidden();
}

$lang = new Lang([
    ["Pagos", "Payments"],
    ["Selección", "Selection"],
    ['estudiante', 'student'],
    ['Buscar información', 'Search information'],
]);

$students = Student::all();
$months = __LANG === 'es' ?
    ['Julio' => '07', 'Agosto' => '08', 'Septiembre' => '09', 'Octubre' => '10', 'Noviembre' => '11', 'Diciembre' => '12', 'Enero' => '01', 'Febrero' => '02', 'Marzo' => '03', 'Abril' => '04', 'Mayo' => '05', 'Junio' => '06']
    : ['July' => '07', 'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12', 'January' => '01', 'February' => '02', 'March' => '03', 'April' => '04', 'May' => '05', 'June' => '06'];
$currentMonth = $_GET['month'] ?? date('m');
$school = Admin::primaryAdmin();
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
    '15' => 'Acuden-Contigo',
    '16' => 'Acuden-Vales',
    '17' => 'VA Prog',
    '18' => 'Colegio',
    '19' => 'Pago APP',
];

$deposits = [
    0 => 'Depósitos Cafetería',
    1 => '-1 Compras de Camisas',
    2 => '-2 Abono de Mensualidad',
    3 => '-3 Abono de Matrícula',
    4 => '-4 Compras de Inflables',
];


$codes = Manager::table('presupuesto')->where("year", $year)->orderBy('codigo')->get();

$adminUsers = Admin::query()->select('usuario')->get();
$depositTypes = [
    1 => "Cash",
    2 => "Donación",
    3 => "Intercambio LT",
    4 => "Recompensa",
    5 => "Correción",
    10 => 'Correción ACH',
    11 => 'Correción Tarjeta',
    6 => "Devolución Balance",
    // 7 => "Borrar",
    8 => "Balance",
    9 => "Otros",
    12 => "Pago a través de oficina",
];
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
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<body>
    <?php
Route::includeFile('/admin/includes/layouts/menu.php');
?>
    <div class="container-md mt-md-3 mb-md-5 px-2">
        <h1 class="text-center my-3"><?= $lang->translation("Pagos") ?></h1>
        <form method="GET">
            <select class="form-control selectpicker" style="width: 100%;" name="accountId" data-live-search="true" required>
                <option value=""><?= $lang->translation("Seleccionar") . ' ' . $lang->translation('estudiante') ?></option>
                <?php foreach ($students as $student): ?>
                <option <?= isset($_REQUEST['accountId']) && $_REQUEST['accountId'] == $student->id ? 'selected=""' : '' ?>
                    value="<?= $student->id ?>"><?= "$student->apellidos $student->nombre ($student->id)" ?></option>
                <?php endforeach ?>
            </select>
            <button class="btn btn-primary btn-sm btn-block mt-2" type="submit"><?= $lang->translation("Buscar información") ?></button>
        </form>

        <?php if (isset($_REQUEST['accountId'])):
            $accountId = $_REQUEST['accountId'];
            $parent = Family::with('kids')->find($accountId);
            $accountStudents = $parent->kids;
            $paymentsQuery = $parent->charges()
                ->orderByRaw("id,grado desc,ss,codigo,rec")
                ->get();

            $debtData = $paymentsData = [];
            $rec = 0;
            foreach ($paymentsQuery as $row) {
                $month = date('m', strtotime($row->fecha_d));
                $debtData[$month][] = $row;
                if ($row->pago > 0 and $row->rec != $rec) {
                    $paymentsData[] = $row;
                    $rec = $row->rec;
                }
            }

            $paymentsData = collect($paymentsData)->sortByDesc('fecha_p')->sortByDesc('hora_p')->values()->all();

            ?>
        <input type="hidden" id="accountId" value="<?= $accountId ?>">
        <!-- students -->

        <div class="my-4">
            <div class="d-inline-flex align-items-center mb-2">
                <h2>Estudiantes en esta cuenta</h2>
                <button id="paymentPromiseButton" class="btn btn-sm btn-secondary ml-2" data-toggle="modal" data-target="#paymentPromiseModal">
                    Promesa de pago <span class="badge badge-light"><?= $parent->total_pagar !== '' && floatval($parent->total_pagar) > 0 ? 'Si' : 'No' ?></span>
                </button>
            </div>
            <div class="card-deck">
                <?php foreach ($accountStudents as $student): ?>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= "$student->apellidos $student->nombre ($student->grado)" ?></h5>
                        <button data-id="<?= $student->mt ?>" class="btn btn-sm btn-primary depositBtn">Deposito <span><?= number_format($student->cantidad, 2) ?></span></button>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>

        <div id="monthsButtons" class="row row-cols-3 row-cols-lg-6 justify-content-around mb-2">
            <?php foreach ($months as $fileName => $number): ?>
            <div class="col mb-1">
                <button data-month="<?= $number ?>"
                    class="btn w-100 <?= $currentMonth === $number ? 'active' : '' ?>"><?= $fileName ?></button>
            </div>
            <?php endforeach ?>
        </div>

        <div id="paymentButtons" class="row row-cols-3 row-cols-lg-6 justify-content-around mb-2">

            <div class="col mb-1">
                <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#paymentModal">Hacer un pago</button>
            </div>
            <?php if ($user->hasPermissionTo(AdminPermission::ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_ADD)): ?>
            <div class="col mb-1">
                <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#addChargeModal">Añadir cargo</button>
            </div>
            <?php endif ?>
            <div class="col mb-1">
                <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#statementModal">Estado de cuenta</button>
            </div>
            <div class="col mb-1">
                <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#paymentReceiptModal">Recibo de pago</button>
            </div>
            <div class="col mb-1">
                <button class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#expiredModal">Vencido</button>
            </div>
            <div class="col mb-1">
                <button id="latePaymentButton" class="btn btn-secondary w-100 h-100" data-toggle="modal" data-target="#latePaymentModal">Pago moroso</button>
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
                <?php foreach ($months as $fileName => $number): ?>
                <tbody id="table<?= $number ?>" class="<?= $currentMonth !== $number ? 'hidden' : '' ?> monthTable">
                    <?php if (isset($debtData[$number])): ?>
                    <?php foreach ($debtData[$number] as $charge): ?>
                    <tr data-id="<?= $charge->codigo ?>" data-ss="<?= $charge->ss ?>" data-grade="<?= $charge->grado ?>"
                        data-description="<?= $charge->desc1 ?>" data-debt="<?= $charge->deuda ?>" data-payment="<?= $charge->pago ?>">
                        <th scope="row"><?= $charge->codigo ?></th>
                        <td><?= $charge->grado ?></td>
                        <td><?= $charge->desc1 ?></td>
                        <td><?= $charge->fecha_d ?></td>
                        <td class="text-right debt"><?= number_format($charge->deuda, 2) ?></td>
                        <td class="text-right payment"><?= number_format($charge->pago, 2) ?></td>
                        <td><?= $charge->fecha_p === '0000-00-00' ? '' : $charge->fecha_p ?></td>
                        <td><?= $charge->tdp !== '' ? $paymentTypes[$charge->tdp] : '' ?></td>
                        <td><?= $charge->rec !== 0 ? $charge->rec : '' ?></td>
                        <td class="text-right">
                            <?php if ($user->hasPermissionTo(AdminPermission::ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_DELETE)): ?>
                            <i data-id="<?= $charge->mt ?>" role="button" class="delete fa-solid fa-trash text-danger pointer-cursor"></i>
                            <?php endif ?>
                            <?php if ($user->hasPermissionTo(AdminPermission::ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_CHANGE)): ?>
                            <i data-id="<?= $charge->mt ?>" role="button"
                                class="<?= $charge->fecha_p !== '0000-00-00' ? 'editPayment' : 'editCharge' ?> fa-solid fa-pen-to-square text-info pointer-cursor"></i>
                            <?php endif ?>
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
    <div data-account-id="<?= $accountId ?>" class="modal fade" id="paymentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
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
                    <input type="hidden" id="paymentAccountId" name="accountId" value="<?= $accountId ?>">
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
                                <?php if ($school->rec == '1'): ?>
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
                                <select class="form-control" <?= $school->caja != 0 ? 'disabled' : '' ?> id="bash" name="bash">
                                    <?php for ($i = 0; $i <= 10; $i++): ?>
                                    <option <?= $school->rec == $i ? 'selected' : '' ?>
                                        value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                                <?php if ($school->caja != 0): ?>
                                <input type="hidden" class="form-control" name="bash" value="<?= $school->caja ?>" />
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
    <?php if ($user->hasPermissionTo(AdminPermission::ACCOUNTS_RECEIVABLE_ENTER_PAYMENTS_ADD)): ?>
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
                <form id="addChargeForm" method="POST" action="<?= Route::url('/admin/billing/payments/includes/addCharge.php') ?>">
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
                                    <?php foreach ($months as $fileName => $monthNumber): ?>
                                    <option value="<?= $monthNumber ?>"><?= $fileName ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="allMonths" name="allMonths">
                                    <label class="custom-control-label w-100" for="allMonths">T.L.M</label>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="amount">Cantidad a pagar</label>
                                <input type="text" class="form-control" id="amount" name="amount" required />
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif ?>
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
                <form method="post" action="<?= Route::url('/admin/billing/payments/includes/editCharge.php') ?>">
                    <input type="hidden" id="editChargeId" name="id">

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editChargeDate">Fecha posteo</label>
                            <input type="date" class="form-control" id="editChargeDate" name="date" />
                        </div>

                        <div class="form-group">
                            <label for="editChargeTo">Aplicar a</label>
                            <select class="form-control" id="editChargeTo" name="chargeTo">
                                <?php foreach ($accountStudents as $student): ?>
                                <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre ($student->grado)" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editChargeCode">Codigo</label>
                            <select class="form-control" id="editChargeCode" name="code">
                                <?php foreach ($codes as $code): ?>
                                <option value="<?= $code->codigo ?>"><?= $code->descripcion ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editChargeDescription">Descripción</label>
                            <input type="text" class="form-control" id="editChargeDescription" name="description" />
                        </div>
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label for="editChargeAmount">Cantidad a pagar</label>
                                <input type="text" class="form-control" id="editChargeAmount" name="amount" />
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaymentModalLabel">Editar pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="<?= Route::url('/admin/billing/payments/includes/editPayment.php') ?>">
                    <input type="hidden" id="editPaymentId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label for="editPaymentBash">Bash</label>
                                        <select class="form-control" <?= $school->caja != 0 ? 'disabled' : '' ?> id="editPaymentBash" name="bash">
                                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                            <option <?= $school->rec == $i ? 'selected' : '' ?>
                                                value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor ?>
                                        </select>
                                        <?php if ($school->caja != 0): ?>
                                        <input type="hidden" class="form-control" name="bash" value="<?= $school->caja ?>" />
                                        <?php endif ?>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="editPaymentChargeDate">Fecha posteo</label>
                                        <input type="date" class="form-control" id="editPaymentChargeDate" name="charge_date" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="editPaymentTo">Aplicar a</label>
                                    <select class="form-control" id="editPaymentTo" name="chargeTo">
                                        <?php foreach ($accountStudents as $student): ?>
                                        <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre ($student->grado)" ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="receiptNumber">Recibo</label>
                                    <input class="form-control" id="editPaymentReceiptNumber" name="receipt_number" />
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-6">
                                        <label for="editPaymentPaymentDate">Fecha del pago</label>
                                        <input type="date" class="form-control" id="editPaymentPaymentDate" name="payment_date" />
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="editPaymentDescription">Descripción</label>
                                        <input type="text" class="form-control" id="editPaymentDescription" name="description" />
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-4">
                                        <label for="editPaymentAmount">Cantidad a pagar</label>
                                        <input type="text" class="form-control" id="editPaymentAmount" name="amount" />
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="editPaymentPaymentType">Tipo de pago</label>
                                        <select class="form-control" id="editPaymentPaymentType" name="paymentType">
                                            <?php foreach ($paymentTypes as $id => $label): ?>
                                            <option value="<?= $id ?>"><?= $label ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="editPaymentChkNum">No. de CHK</label>
                                        <input type="text" class="form-control" data-mask="0#" id="editPaymentChkNum" name="chkNum" />
                                    </div>
                                </div>

                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="editPaymentComment">Razón del cambio</label>
                                    <textarea class="form-control" name="comment" id="editPaymentComment" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="editPaymentChangeDate">Fecha del cambio</label>
                                    <input type="text" class="form-control-plaintext" id="editPaymentChangeDate" readonly>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="editPaymentReturnedCheck" name="returnedCheck" value="true">
                                        <label class="custom-control-label w-100" for="editPaymentReturnedCheck">Cheque devuelto</label>
                                    </div>
                                </div>
                                <div class="form-group hidden">
                                    <label for="editPaymentCode">Codigo</label>
                                    <select class="form-control" id="editPaymentCode" name="code">
                                        <?php foreach ($codes as $code): ?>
                                        <option value="<?= $code->codigo ?>"><?= $code->descripcion ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-4">
                                        <label for="editPaymentUser">Cobrador</label>
                                        <input type="text" class="form-control-plaintext" id="editPaymentUser" readonly>
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="editPaymentDate">Fecha</label>
                                        <input type="text" class="form-control-plaintext" id="editPaymentDate" readonly>
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="editPaymentTime">Hora</label>
                                        <input type="text" class="form-control-plaintext" id="editPaymentTime" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Editar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deposit Modal -->
    <div class="modal fade" id="depositModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Deposito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="minDepositForm" novalidate>
                        <label for="minDeposit">Cantidad minima</label>
                        <div class="input-group mb-3">
                            <input type="text" id="minDeposit" required name="deposit" class="form-control" placeholder="10.00" aria-label="Cantidad minima" aria-describedby="minDepositBtn">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="submit" id="minDepositBtn">Actualizar</button>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex">
                        <p class="mb-0">Poner balance en "0"</p>
                        <button id="deleteDeposit" type="button" class="btn btn-warning btn-sm ml-2"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <form id="depositForm" method="POST" novalidate action="<?= Route::url('/admin/billing/payments/includes/deposit.php') ?>">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="depositType">Tipo de deposito</label>
                                <select class="form-control" name="type" id="depositType">
                                    <?php foreach ($depositTypes as $id => $label): ?>
                                    <option value="<?= $id ?>"><?= $label ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="depositAmount">Cantidad a Depositar</label>
                                <input type="text" class="form-control" name="amount" id="depositAmount" placeholder="10.00">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="depositDate">Fecha</label>
                                <input type="date" class="form-control" name="date" id="depositDate" disabled>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="depositTime">Hora</label>
                                <input type="time" class="form-control" name="time" id="depositTime" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="depositDate">Depositas a:</label>
                            </div>

                            <div class="form-group col-12 col-md-6">

                                <select class="form-control" name="type2" id="deposits" required>
                                    <?php foreach ($deposits as $id => $label): ?>
                                    <option value="<?= $id ?>"><?= $label ?></option>
                                    <?php endforeach ?>
                                </select>

                            </div>


                        </div>
                        <div class="form-group">
                            <label for="depositOther">Otra descripción</label>
                            <input type="text" class="form-control" name="other" id="depositOther" disabled>
                        </div>
                        <table class="table table-sm text-center">
                            <thead>
                                <tr>
                                    <th>Disponible</th>
                                    <th>Nueva</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="availableDeposit"></td>
                                    <td id="newDeposit"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Depositar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Late payment Modal -->
    <div class="modal fade" id="latePaymentModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="latePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="latePaymentModalLabel">Pago moroso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="latePaymentForm" method="POST">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="latePaymentObservationType">Tipo de observación</label>
                                <select class="form-control" name="observationType" id="latePaymentObservationType">
                                    <option value=""></option>
                                    <option value="Si">Cheque devuelto</option>
                                    <option value="2">Documentos</option>
                                    <option value="3">Otros</option>
                                </select>

                            </div>
                            <div class="col-12 col-md-6">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="latePaymentAlert" name="alert">
                                    <label class="custom-control-label w-100" for="latePaymentAlert">Alerta no aceptar cheques</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="latePaymentAditionalInfo">Información adicional</label>
                            <textarea class="form-control" name="info" id="latePaymentAditionalInfo"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Payment Promise Modal -->
    <div class="modal fade" id="paymentPromiseModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="paymentPromiseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentPromiseModalLabel">Promesa de pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="paymentPromiseForm" method="POST">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="paymentPromiseDate">Fecha</label>
                                <input type="date" class="form-control" name="date" id="paymentPromiseDate" required />
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="paymentPromiseExpirationDate">Fecha de expiración</label>
                                <input type="date" class="form-control" name="expirationDate" id="paymentPromiseExpirationDate" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="paymentPromiseDescription">Descripción de la promesa de pago</label>
                            <textarea class="form-control" name="description" id="paymentPromiseDescription" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="paymentPromiseAmount">Cantidad acordada</label>
                            <input type="text" class="form-control" name="amount" id="paymentPromiseAmount" required />
                        </div>
                        <div class="form-group">
                            <label for="paymentPromiseTime">Tiempo acordado</label>
                            <input type="text" class="form-control" name="time" id="paymentPromiseTime" required />
                        </div>
                        <div class="form-group">
                            <label for="paymentPromiseNewAmount">Nuevos cargos</label>
                            <input type="text" class="form-control" name="newAmount" id="paymentPromiseNewAmount" required />
                        </div>
                        <div class="form-group">
                            <label for="paymentPromiseTotal">Total a pagar</label>
                            <input type="text" class="form-control" name="total" id="paymentPromiseTotal" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button id="paymentPromiseDelete" type="button" class="btn btn-danger">Borrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Expired Modal -->
    <div class="modal fade" id="expiredModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="expiredModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="expiredModalLabel">Cargos vencidos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="expiredTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th class="text-right">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right">Total a pagar <span class="font-weight-bold" id="expiredTotal"></span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Statement Modal -->
    <div class="modal fade" id="statementModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="statementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statementModalLabel">Estado de cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="statementForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="statementType">Tipo de transacción</label>
                            <select class="form-control" name="type" id="statementType" required>
                                <option value="1">Todas las transacciones</option>
                                <option value="2">Solamente pagos</option>
                                <option value="3">Balance del mes</option>
                                <option value="4">Transacciones por código</option>
                                <option value="5">Depositos entrados</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="statementEmail">Email</label>
                            <select class="form-control" name="email" id="statementEmail">
                                <option value="">Selección</option>
                                <?php if ($parent->email_m && $parent->email_p): ?>
                                <option value="<?= "$parent->email_m,$parent->email_p" ?>">Ambos</option>
                                <?php endif ?>
                                <?php if ($parent->email_m): ?>
                                <option><?= $parent->email_m ?></option>
                                <?php endif ?>
                                <?php if ($parent->email_p): ?>
                                <option><?= $parent->email_p ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="statementNewEmail">Nuevo email</label>
                            <input type="text" class="form-control" name="newEmail" id="statementNewEmail">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Continuar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Payment Receipt Modal -->
    <div class="modal fade" id="paymentReceiptModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="paymentReceiptModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentReceiptModalLabel">Recibo de pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="paymentReceiptForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="paymentReceiptType">Tipo de recibo</label>
                            <select class="form-control" name="type" id="paymentReceiptType" required>
                                <option value="1">Recibo 1</option>
                                <option value="2">Recibo 2</option>
                                <option value="3">Recibo 3</option>
                                <option value="4">Recibo 4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paymentReceiptTransaction">Transacción</label>
                            <select class="form-control" name="transaction" id="paymentReceiptTransaction" required>
                                <?php foreach ($paymentsData as $payment): ?>
                                <option value="<?= $payment->mt ?>"><?= "$payment->fecha_p, $payment->rec" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paymentReceiptEmail">Email</label>
                            <select class="form-control" name="email" id="paymentReceiptEmail">
                                <option value="">Selección</option>
                                <?php if ($parent->email_m && $parent->email_p): ?>
                                <option value="ambos">Ambos</option>
                                <?php endif ?>
                                <?php if ($parent->email_m): ?>
                                <option><?= $parent->email_m ?></option>
                                <?php endif ?>
                                <?php if ($parent->email_p): ?>
                                <option><?= $parent->email_p ?></option>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paymentReceiptNewEmail">Nuevo email</label>
                            <input type="text" class="form-control" name="newEmail" id="paymentReceiptNewEmail">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Continuar</button>
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