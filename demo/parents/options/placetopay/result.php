<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\PlacetoPaySession;
use Classes\Route;
use Classes\Session;

/**
 * Summary shown after coming back from PlacetoPay Web Checkout, for every
 * outcome (approved, rejected, cancelled by the buyer, pending). The buyer
 * may come back without an active session, so this page doesn't require
 * login; only the reference, amount and status are shown in that case.
 */
$reference = (string) ($_GET['reference'] ?? '');
$error = $_GET['error'] ?? null;
$session = $reference !== '' ? PlacetoPaySession::findByReference($reference) : null;
$isOwner = $session && Session::id() && (string) Session::id() === (string) $session->account_id;
$canResume = $isOwner && $session->canResume();

$backUrl = match ($session?->payable_type) {
    'store_order' => '../stores/index.php',
    default => '../deposit/index.php',
};

$title = __('placetopay.result.title');
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php Route::includeFile('/parents/includes/layouts/header.php'); ?>
</head>

<body>
    <?php if (Session::is_logged(false)) Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('placetopay.result.title') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <?php if (! $session): ?>
                    <div class="alert alert-danger"><?= __('placetopay.errors.session_not_found') ?></div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><?= __('placetopay.result.transaction') ?></span>
                            <span class="badge <?= $session->status->getBadgeClass() ?> p-2"><?= $session->status->getLabel() ?></span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?= __('Referencia') ?></span>
                                <strong><?= htmlspecialchars($session->reference) ?></strong>
                            </li>
                            <?php if ($isOwner && $session->description): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted"><?= __('placetopay.result.concept') ?></span>
                                    <span class="text-right"><?= htmlspecialchars($session->description) ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?= __('placetopay.result.amount') ?></span>
                                <strong>$<?= number_format($session->amount, 2) ?> <?= htmlspecialchars($session->currency) ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?= __('Estado') ?></span>
                                <span><?= $session->status->getLabel() ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?= __('Fecha') ?></span>
                                <span><?= $session->updated_at->format('d/m/Y h:i A') ?></span>
                            </li>
                            <?php if ($message = $session->statusMessage()): ?>
                                <li class="list-group-item">
                                    <small class="text-muted"><?= htmlspecialchars($message) ?></small>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <?php if ($canResume): ?>
                            <div class="card-body pb-0">
                                <div class="alert alert-warning mb-0">
                                    <p><?= __('placetopay.result.resume_notice') ?></p>
                                    <a href="<?= htmlspecialchars($session->process_url) ?>" class="btn btn-warning btn-block w-100">
                                        <?= __('placetopay.result.resume') ?>
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($session->status->isPending()): ?>
                            <div class="card-body pb-0">
                                <div class="alert alert-warning mb-0">
                                    <?= __('placetopay.result.pending_notice') ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="card-body pb-0">
                                <div class="alert alert-danger mb-0"><?= htmlspecialchars($error) ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex justify-content-between">
                            <a href="<?= $backUrl ?>" class="btn btn-outline-primary"><?= __('Volver') ?></a>
                            <a href="./history.php" class="btn btn-primary"><?= __('placetopay.history.link') ?></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>