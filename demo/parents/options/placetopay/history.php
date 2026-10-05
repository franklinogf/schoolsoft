<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\PlacetoPaySession;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$payments = PlacetoPaySession::forAccount(Session::id())->history()->get();

$title = __('placetopay.history.title');
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php Route::includeFile('/parents/includes/layouts/header.php'); ?>
</head>

<body>
    <?php Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('placetopay.history.title') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <span><?= __('placetopay.history.subtitle') ?></span>
                    </div>

                    <?php if ($payments->isEmpty()): ?>
                        <div class="card-body">
                            <p class="text-center text-muted mb-0"><?= __('placetopay.history.empty') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th><?= __('Referencia') ?></th>
                                        <th><?= __('Fecha') ?></th>
                                        <th><?= __('placetopay.result.concept') ?></th>
                                        <th class="text-right"><?= __('placetopay.result.amount') ?></th>
                                        <th class="text-center"><?= __('Estado') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><a href="./result.php?reference=<?= urlencode($payment->reference) ?>"><?= htmlspecialchars($payment->reference) ?></a></td>
                                            <td>
                                                <?= $payment->created_at->format('d/m/Y') ?>
                                                <div class="text-muted small"><?= $payment->created_at->format('h:i A') ?></div>
                                            </td>
                                            <td><?= htmlspecialchars($payment->description ?? '') ?></td>
                                            <td class="text-right font-weight-bold">$<?= number_format($payment->amount, 2) ?> <?= htmlspecialchars($payment->currency) ?></td>
                                            <td class="text-center"><span class="badge <?= $payment->status->getBadgeClass() ?>"><?= $payment->status->getLabel() ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>
