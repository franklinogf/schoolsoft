<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;
use App\Models\StoreOrder;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;

Session::is_logged();

if (! school_is('cbl', 'demo')) {
    Route::forbidden();
}

$status = $_GET['status'] ?? null;
$statusMessage = $_GET['message'] ?? null;

$colegio = Admin::primaryAdmin();
$isClosed = $colegio->actmen === 'No';

$students = Student::byId(Session::id())->get();

$family = Family::find(Session::id());
$defaultMobile = $family ? ($family->cel_m ?: $family->cel_p) : '';
$defaultEmail = $family ? ($family->email_m ?: $family->email_p) : '';

// Re-checks against PlacetoPay, so a resolved payment doesn't keep warning
$pendingPayment = (new PlacetoPayPaymentProcessor())->pendingFor(Session::id());

$receipts = StoreOrder::where('accountID', Session::id())
    ->where('shopping', 2)
    ->where('paid', 1)
    ->orderByDesc('date')
    ->get();
?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('placetopay.account_credit.title');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('placetopay.account_credit.title') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-7">

                <?php if ($pendingPayment): ?>
                    <div class="alert alert-warning">
                        <?= __('placetopay.pending.warning', ['reference' => $pendingPayment->reference]) ?>
                        <a href="../placetopay/result.php?reference=<?= urlencode($pendingPayment->reference) ?>" class="alert-link"><?= __('placetopay.pending.view_detail') ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($isClosed): ?>
                    <div class="alert alert-info"><?= __('placetopay.account_credit.closed') ?></div>
                <?php else: ?>
                    <div class="card mb-3">
                        <div class="card-header"><?= __('placetopay.account_credit.students') ?></div>
                        <div class="card-body">
                            <div class="small mb-3">
                                <a href="../placetopay/history.php"><?= __('placetopay.history.link') ?> &raquo;</a>
                            </div>
                            <form id="creditForm" class="needs-validation" novalidate method="post" action="./includes/start.php">
                                <?php foreach ($students as $estu): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= htmlspecialchars($estu->profilePicture) ?>" alt="" class="rounded mr-3" width="56" height="56" style="object-fit:cover">
                                        <div class="flex-grow-1">
                                            <label for="amount-<?= $estu->mt ?>" class="mb-1 font-weight-bold"><?= htmlspecialchars("$estu->nombre $estu->apellidos") ?></label>
                                            <input type="text" class="form-control credit-amount" id="amount-<?= $estu->mt ?>" name="amounts[<?= $estu->mt ?>]" placeholder="<?= __('placetopay.account_credit.amount_for') ?> $0.00" dir="rtl">
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-3">
                                    <strong><?= __('placetopay.account_credit.total') ?></strong>
                                    <strong class="h4 mb-0" id="total">$0.00</strong>
                                </div>

                                <hr class="my-3">

                                <div class="form-row">
                                    <div class="form-group col-12 col-md-6">
                                        <label for="first-name" class="form-label"><?= __('Nombre') ?></label>
                                        <input type="text" class="form-control justText" id="first-name" name="first_name" required>
                                        <div class="invalid-feedback"><?= __('placetopay.validation.first_name') ?></div>
                                    </div>

                                    <div class="form-group col-12 col-md-6">
                                        <label for="last-name" class="form-label"><?= __('placetopay.form.last_name') ?></label>
                                        <input type="text" class="form-control justText" id="last-name" name="last_name" required>
                                        <div class="invalid-feedback"><?= __('placetopay.validation.last_name') ?></div>
                                    </div>

                                    <div class="form-group col-12 col-md-6">
                                        <label for="email" class="form-label"><?= __('Email') ?></label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars((string) $defaultEmail) ?>" placeholder="you@example.com" required>
                                        <div class="invalid-feedback"><?= __('placetopay.validation.email') ?></div>
                                    </div>

                                    <div class="form-group col-12 col-md-6">
                                        <label for="mobile" class="form-label"><?= __('Celular') ?></label>
                                        <input type="tel" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars((string) $defaultMobile) ?>" placeholder="7875551234" pattern="\+?[\d\s\(\)\-]{10,20}" required>
                                        <div class="invalid-feedback"><?= __('placetopay.validation.mobile') ?></div>
                                    </div>
                                </div>

                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1" required>
                                    <label class="custom-control-label" for="terms">
                                        <?= __('placetopay.form.accept_terms') ?> <a href="../placetopay/terms.php" target="_blank" rel="noopener"><?= __('placetopay.form.terms_link') ?></a>
                                    </label>
                                    <div class="invalid-feedback"><?= __('placetopay.validation.terms') ?></div>
                                </div>

                                <button class="w-100 btn btn-primary btn-lg mt-2 pagar" type="submit"><?= __('placetopay.form.pay_with_placetopay') ?></button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($receipts->isNotEmpty()): ?>
                    <div class="card">
                        <div class="card-header"><?= __('placetopay.account_credit.receipts') ?></div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($receipts as $receipt): ?>
                                <a href="./finished.php?id=<?= urlencode($receipt->trxID) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><?= $receipt->date->format('Y-m-d') ?> &middot; <?= htmlspecialchars($receipt->trxID) ?></span>
                                    <span class="badge badge-success badge-pill">$<?= number_format((float) $receipt->total, 2) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php $jqMask = true; ?>
    <script>
        const creditLang = <?= json_encode([
                                'error' => __('Error'),
                                'amountError' => __('placetopay.account_credit.select_amount_error'),
                                'processing' => __('placetopay.form.processing'),
                            ]) ?>;
    </script>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <?php if ($status === 'error'): ?>
        <script>
            Alert.fire(<?= json_encode(__('Error')) ?>, <?= json_encode($statusMessage ?? __('placetopay.errors.start_failed')) ?>, 'error')
        </script>
    <?php endif; ?>
</body>

</html>