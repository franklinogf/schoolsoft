<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Student;
use App\Services\PlacetoPayPaymentProcessor;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$status = $_GET['status'] ?? null;
$statusMessage = $_GET['message'] ?? null;
$statusAmount = $_GET['amount'] ?? null;


$colegio = Admin::primaryAdmin();

$year = $colegio->year;
$minAmount = $colegio->deposito_minimo;

$students = Student::byId(Session::id())->get();

$family = Family::find(Session::id());
$defaultMobile = $family ? ($family->cel_m ?: $family->cel_p) : '';
$defaultEmail = $family ? ($family->email_m ?: $family->email_p) : '';

// Re-checks against PlacetoPay, so a resolved payment doesn't keep warning
$pendingPayment = (new PlacetoPayPaymentProcessor())->pendingFor(Session::id());


$oneStudent =  count($students) === 1 ? true : false;

if ($oneStudent) {
    $estu = $students[0];
}


?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">


<head>
    <?php
    $title = __('placetopay.deposit.title');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('placetopay.deposit.title') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-7">

                <?php if ($pendingPayment): ?>
                    <div class="alert alert-warning">
                        <?= __('placetopay.pending.warning', ['reference' => $pendingPayment->reference]) ?>
                        <a href="../placetopay/result.php?reference=<?= urlencode($pendingPayment->reference) ?>" class="alert-link"><?= __('placetopay.pending.view_detail') ?></a>
                    </div>
                <?php endif; ?>

                <?php if (!$oneStudent) : ?>
                    <div class="card mb-3">
                        <div class="card-header"><?= __('placetopay.deposit.select_student') ?></div>
                        <div id="students" class="list-group list-group-flush">
                            <?php foreach ($students as $estu): ?>
                                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-student-id="<?= $estu->mt ?>">
                                    <span class="name"><?= "$estu->nombre $estu->apellidos" ?></span>
                                    <span class="badge bg-success rounded-pill">$<?= $estu->cantidad ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif ?>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('placetopay.deposit.details') ?></span>
                        <?php if ($oneStudent): ?>
                            <span class="badge bg-success rounded-pill">$<?= $estu->cantidad ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between small mb-3">
                            <a href="../placetopay/history.php"><?= __('placetopay.history.link') ?> &raquo;</a>
                            <a href="./history.php"><?= __('placetopay.deposit.history_link') ?> &raquo;</a>
                        </div>
                        <form id="depositForm" class="needs-validation" novalidate method="post" action="./includes/start.php">
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="money" class="form-label"><?= __('placetopay.deposit.amount') ?> <?= $oneStudent ? __('placetopay.deposit.amount_to', ['name' => "$estu->nombre $estu->apellidos"]) : '' ?></label>
                                    <input type="text" class="form-control form-control-lg" id="money" required dir="rtl">
                                    <small class="text-muted"><?= __('placetopay.deposit.min_amount', ['amount' => '$' . $minAmount]) ?></small>
                                    <!-- Obligatorio para el deposito minimo en el JS -->
                                    <input type="hidden" id="minAmount" value="<?= $minAmount ?>">
                                    <div class="invalid-feedback">
                                        <?= __('placetopay.deposit.min_amount_invalid', ['amount' => '$' . $minAmount]) ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="form-row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="first-name" class="form-label"><?= __('Nombre') ?></label>
                                    <input type="text" class="form-control justText" id="first-name" name="first_name" required>
                                    <div class="invalid-feedback">
                                        <?= __('placetopay.validation.first_name') ?>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="last-name" class="form-label"><?= __('placetopay.form.last_name') ?></label>
                                    <input type="text" class="form-control justText" id="last-name" name="last_name" required>
                                    <div class="invalid-feedback">
                                        <?= __('placetopay.validation.last_name') ?>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="email" class="form-label"><?= __('Email') ?></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars((string) $defaultEmail) ?>" placeholder="you@example.com" required>
                                    <div class="invalid-feedback">
                                        <?= __('placetopay.validation.email') ?>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="mobile" class="form-label"><?= __('Celular') ?></label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars((string) $defaultMobile) ?>" placeholder="7875551234" pattern="\+?[\d\s\(\)\-]{10,20}" required>
                                    <div class="invalid-feedback">
                                        <?= __('placetopay.validation.mobile') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1" required>
                                <label class="custom-control-label" for="terms">
                                    <?= __('placetopay.form.accept_terms') ?> <a href="../placetopay/terms.php" target="_blank" rel="noopener"><?= __('placetopay.form.terms_link') ?></a>
                                </label>
                                <div class="invalid-feedback">
                                    <?= __('placetopay.validation.terms') ?>
                                </div>
                            </div>

                            <button class="w-100 btn btn-primary btn-lg mt-2 pagar" type="submit" id="pagar"><?= __('placetopay.form.pay_with_placetopay') ?></button>

                            <!-- campos ocultos usados por start.php -->
                            <input type="hidden" id="cuenta" name="account_id" value="<?= Session::id() ?>">
                            <input type="hidden" id="student_id" name="student_id" value="<?= $oneStudent ? $estu->mt : '' ?>">
                            <input type="hidden" id="amount" name="amount" value="">
                        </form>
                    </div>
                </div>
                <!-- End Payment -->
            </div>
        </div>
    </div>

    <?php
    $jqMask = true;
    ?>
    <script>
        const depositLang = <?= json_encode([
            'error' => __('Error'),
            'selectStudent' => __('placetopay.deposit.select_student_error'),
            'amountTo' => __('placetopay.deposit.amount') . ' ' . __('placetopay.deposit.amount_to'),
            'processing' => __('placetopay.form.processing'),
        ]) ?>;
    </script>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <?php if ($status === 'success'): ?>
        <script>
            Alert.fire(<?= json_encode(__('placetopay.deposit.success')) ?>, <?= json_encode(__('placetopay.deposit.new_balance', ['amount' => '$' . ($statusAmount ?? '')])) ?>, 'success')
        </script>
    <?php elseif ($status === 'error'): ?>
        <script>
            Alert.fire(<?= json_encode(__('Error')) ?>, <?= json_encode($statusMessage ?? __('placetopay.deposit.failed')) ?>, 'error')
        </script>
    <?php endif; ?>

</body>

</html>