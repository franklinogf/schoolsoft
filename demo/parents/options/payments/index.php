<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Family;
use App\Models\Payment;
use Carbon\Carbon;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$family = Family::with('kids')->find(Session::id());

$rows = Payment::query()
    ->where('id', $family->id)
    ->where('baja', '')
    ->get();

$chargesByStudent = $rows
    ->groupBy(fn($row) => "{$row->ss}-{$row->codigo}-{$row->fecha_d}")
    ->map(function ($group) {
        $charge = $group->first(fn($row) => $row->deuda > 0);
        if (!$charge) {
            return null;
        }
        $lastPaidDate = $group->filter(fn($row) => $row->pago > 0)->max('fecha_p');
        $totalCharge = $group->sum(fn($row) => (float) $row->deuda);
        $totalPaid = $group->sum(fn($row) => (float) $row->pago);
        $balance = $totalCharge - $totalPaid;
        return [
            'charge' => $charge,
            'amount' => $balance > 0.0 ? $balance : $totalPaid,
            'isPaid' => $balance <= 0.0,
            'paidDate' => $lastPaidDate,
        ];
    })
    ->filter()
    ->sortBy(fn($item) => $item['charge']->fecha_d)
    ->groupBy(fn($item) => $item['charge']->ss);
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Pagos");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __("Pagos") ?></h1>

        <?php if ($chargesByStudent->isEmpty()): ?>
            <p class="text-center"><?= __("No tienes cargos registrados") ?></p>
        <?php else: ?>
            <form id="paymentsForm">
                <?php foreach ($family->kids as $kid): ?>
                    <?php if ($chargesByStudent->has($kid->ss)): ?>
                        <div class="card mb-3">
                            <div class="card-header"><?= "$kid->nombre $kid->apellidos ($kid->grado)" ?></div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($chargesByStudent->get($kid->ss) as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input chargeCheckbox" type="checkbox" data-amount="<?= $item['amount'] ?>" id="charge_<?= $item['charge']->mt ?>" name="charges[]" value="<?= $item['charge']->mt ?>" <?= $item['isPaid'] ? 'disabled' : '' ?>>
                                            <label class="form-check-label" for="charge_<?= $item['charge']->mt ?>">
                                                <?= $item['charge']->desc1 ?> (<?= ucfirst(Carbon::parse($item['charge']->fecha_d)->translatedFormat('F Y')) ?>)
                                            </label>
                                        </div>
                                        <?php if ($item['isPaid']): ?>
                                            <span class="badge badge-success">
                                                <?= $item['paidDate'] ? __("Pagado el") . " {$item['paidDate']}" : __("Pagado") ?>
                                                (<?= number_format($item['amount'], 2) ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= number_format($item['amount'], 2) ?></span>
                                        <?php endif ?>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>
                <?php endforeach ?>

                <div class="d-flex justify-content-between align-items-center sticky-bottom bg-white py-2 border-top">
                    <strong><?= __("Total seleccionado") ?>: <span id="totalSelected">0.00</span></strong>
                    <button type="submit" id="payButton" class="btn btn-primary" disabled><?= __("Pagar seleccionados") ?></button>
                </div>
            </form>
        <?php endif ?>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>
