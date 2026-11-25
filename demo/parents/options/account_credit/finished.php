<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Scopes\YearScope;
use App\Models\Student;
use App\Models\StoreOrder;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if (! school_is('cbl')) {
    Route::forbidden();
}

$order = StoreOrder::withoutGlobalScope(YearScope::class)
    ->where('trxID', (string) ($_GET['id'] ?? ''))
    ->where('accountID', Session::id())
    ->where('shopping', 2)
    ->where('paid', 1)
    ->first();

$items = $order?->items ?? collect();
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $order ? __('placetopay.account_credit.receipt', ['reference' => $order->trxID]) : __('placetopay.account_credit.title');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-7">
                <?php if (! $order): ?>
                    <div class="alert alert-danger mt-4"><?= __('placetopay.account_credit.receipt_not_found') ?></div>
                <?php else: ?>
                    <div class="text-center my-4">
                        <h1 class="h3 font-weight-bold"><?= __('placetopay.account_credit.receipt_title') ?></h1>
                        <h2 class="h5"><?= __('placetopay.account_credit.receipt', ['reference' => htmlspecialchars($order->trxID)]) ?></h2>
                        <span class="text-muted"><?= $order->date->format('Y-m-d H:i') ?></span>
                    </div>

                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($items as $item):
                                $student = Student::withoutGlobalScopes()->where('ss', $item->item_name)->where('year', $order->year)->first();
                            ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <?php if ($student): ?>
                                        <img src="<?= htmlspecialchars($student->profilePicture) ?>" alt="" class="rounded mr-3" width="64" height="64" style="object-fit:cover">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <strong><?= htmlspecialchars($student ? "$student->nombre $student->apellidos" : '') ?></strong>
                                        <div class="text-muted small"><?= __('placetopay.account_credit.amount_for') ?>: <strong>$<?= number_format((float) $item->price, 2) ?></strong></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="card-footer d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong>$<?= number_format((float) $order->total, 2) ?></strong>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="./index.php" class="btn btn-outline-secondary mt-3">&laquo; <?= __('placetopay.account_credit.back') ?></a>
            </div>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>
