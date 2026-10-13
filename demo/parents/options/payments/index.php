<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Family;
use App\Services\FamilyChargesService;
use App\Services\PlacetoPayPaymentProcessor;
use Carbon\Carbon;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$status = $_GET['status'] ?? null;
$statusMessage = $_GET['message'] ?? null;

$family = Family::with('kids')->find(Session::id());
$defaultMobile = $family->cel_m ?: $family->cel_p;
$defaultEmail = $family->email_m ?: $family->email_p;

$charges = FamilyChargesService::charges($family->id);
$chargesByStudent = $charges->groupBy(fn($item) => $item['charge']->ss);
$pendingCharges = $charges->reject(fn($item) => $item['isPaid']);
$balanceDue = $pendingCharges->sum('balance');
$overdueCount = $pendingCharges->where('isOverdue', true)->count();

// Re-checks against PlacetoPay, so a resolved payment doesn't keep warning
$pendingPayment = (new PlacetoPayPaymentProcessor())->pendingFor(Session::id());

$money = fn(float $amount) => '$' . number_format($amount, 2);
$date = fn(?string $value) => $value ? Carbon::parse($value)->format('d/m/Y') : '';
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('placetopay.payments.title');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
    <style>
        .charge-row,
        .select-all-row {
            cursor: pointer;
            transition: background-color .15s ease-in-out;
        }

        .charge-row.is-selected {
            background-color: #eef5ff;
        }

        .charge-row .custom-control-label,
        .select-all-row .custom-control-label {
            cursor: pointer;
        }

        .charge-amount {
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .summary-balance {
            font-size: 2rem;
            font-weight: 600;
            line-height: 1.1;
        }

        .pay-bar {
            position: fixed;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            padding: .75rem 1rem calc(.75rem + env(safe-area-inset-bottom, 0px));
            background: #fff;
            box-shadow: 0 -.25rem .75rem rgba(0, 0, 0, .1);
        }

        @media (max-width: 991.98px) {
            body.has-pay-bar {
                padding-bottom: 5.5rem;
            }
        }

        @media (min-width: 992px) {
            .checkout-column {
                position: sticky;
                top: 1rem;
            }
        }
    </style>
</head>

<body class="<?= $pendingCharges->isNotEmpty() ? 'has-pay-bar' : '' ?>">
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-md-3 mb-md-5 px-2 px-md-3">
        <h1 class="text-center my-4"><?= __('placetopay.payments.title') ?></h1>

        <?php if ($pendingPayment): ?>
            <div class="alert alert-warning">
                <?= __('placetopay.pending.warning', ['reference' => $pendingPayment->reference]) ?>
                <a href="../placetopay/result.php?reference=<?= urlencode($pendingPayment->reference) ?>" class="alert-link"><?= __('placetopay.pending.view_detail') ?></a>
            </div>
        <?php endif; ?>

        <?php if ($charges->isEmpty()): ?>
            <div class="card text-center">
                <div class="card-body py-5">
                    <p class="lead mb-3"><?= __('placetopay.payments.empty') ?></p>
                    <a href="../placetopay/history.php"><?= __('placetopay.history.link') ?> &raquo;</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Summary -->
            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
                        <div class="text-muted small text-uppercase"><?= __('placetopay.payments.balance_due') ?></div>
                        <div class="summary-balance <?= $balanceDue > 0 ? '' : 'text-success' ?>"><?= $money($balanceDue) ?></div>
                        <div class="small">
                            <span class="text-muted"><?= trans_choice('placetopay.payments.pending_count', $pendingCharges->count()) ?></span>
                            <?php if ($overdueCount > 0): ?>
                                &middot; <span class="text-danger font-weight-bold"><?= trans_choice('placetopay.payments.overdue_count', $overdueCount) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="../placetopay/history.php" class="btn btn-outline-secondary btn-sm"><?= __('placetopay.history.link') ?></a>
                </div>
            </div>

            <?php if ($pendingCharges->isEmpty()): ?>
                <div class="alert alert-success"><?= __('placetopay.payments.all_paid') ?></div>
            <?php endif; ?>

            <form id="paymentsForm" class="needs-validation" novalidate method="post" action="./includes/start.php">
                <div class="row">
                    <!-- Charges by student -->
                    <div class="<?= $pendingCharges->isNotEmpty() ? 'col-lg-7' : 'col-12' ?>">
                        <?php foreach ($family->kids as $kid): ?>
                            <?php if (! $chargesByStudent->has($kid->ss)) continue; ?>
                            <?php
                            $kidCharges = $chargesByStudent->get($kid->ss);
                            $kidPending = $kidCharges->reject(fn($item) => $item['isPaid']);
                            $kidPaid = $kidCharges->filter(fn($item) => $item['isPaid']);
                            $kidKey = md5($kid->ss);
                            ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= "$kid->nombre $kid->apellidos" ?></strong>
                                        <span class="text-muted small ml-1">(<?= $kid->grado ?>)</span>
                                    </div>
                                    <?php if ($kidPending->isNotEmpty()): ?>
                                        <span class="badge badge-light border charge-amount"><?= $money($kidPending->sum('balance')) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?= __('placetopay.payments.paid') ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($kidPending->isNotEmpty()): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php if ($kidPending->count() > 1): ?>
                                            <li class="list-group-item py-2 bg-light select-all-row">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input selectAll" id="all_<?= $kidKey ?>" data-student="<?= $kidKey ?>">
                                                    <label class="custom-control-label small font-weight-bold" for="all_<?= $kidKey ?>"><?= __('placetopay.payments.select_all') ?></label>
                                                </div>
                                            </li>
                                        <?php endif; ?>
                                        <?php foreach ($kidPending as $item): ?>
                                            <?php $charge = $item['charge']; ?>
                                            <li class="list-group-item charge-row d-flex justify-content-between align-items-center">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input chargeCheckbox" type="checkbox" id="charge_<?= $charge->mt ?>" name="charges[]" value="<?= $charge->mt ?>" data-amount="<?= $item['balance'] ?>" data-student="<?= $kidKey ?>">
                                                    <label class="custom-control-label" for="charge_<?= $charge->mt ?>">
                                                        <?= htmlspecialchars($charge->desc1) ?>
                                                        <span class="d-block small text-muted">
                                                            <?= ucfirst(Carbon::parse($charge->fecha_d)->translatedFormat('F Y')) ?>
                                                            <?php if ($item['paid'] > 0): ?>
                                                                &middot; <?= __('placetopay.payments.partial_paid', ['amount' => $money($item['paid']), 'total' => $money($item['total'])]) ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="text-right ml-2">
                                                    <div class="font-weight-bold charge-amount"><?= $money($item['balance']) ?></div>
                                                    <?php if ($item['isOverdue']): ?>
                                                        <span class="badge badge-danger"><?= __('placetopay.payments.overdue') ?></span>
                                                    <?php else: ?>
                                                        <small class="text-muted"><?= __('placetopay.payments.due', ['date' => $date($charge->fecha_d)]) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if ($kidPaid->isNotEmpty()): ?>
                                    <div class="card-footer bg-white p-0">
                                        <button class="btn btn-link btn-block text-left text-muted small px-3" type="button" data-toggle="collapse" data-target="#paid_<?= $kidKey ?>" aria-expanded="false">
                                            <?= __('placetopay.payments.paid_section', ['count' => $kidPaid->count()]) ?> &#9662;
                                        </button>
                                        <ul class="list-group list-group-flush collapse <?= $kidPending->isEmpty() ? 'show' : '' ?>" id="paid_<?= $kidKey ?>">
                                            <?php foreach ($kidPaid as $item): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center text-muted">
                                                    <div>
                                                        <?= htmlspecialchars($item['charge']->desc1) ?>
                                                        <span class="d-block small"><?= ucfirst(Carbon::parse($item['charge']->fecha_d)->translatedFormat('F Y')) ?></span>
                                                    </div>
                                                    <div class="text-right ml-2">
                                                        <div class="charge-amount"><?= $money($item['paid']) ?></div>
                                                        <span class="badge badge-success">
                                                            <?= $item['paidDate'] ? __('placetopay.payments.paid_on', ['date' => $date($item['paidDate'])]) : __('placetopay.payments.paid') ?>
                                                        </span>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($pendingCharges->isNotEmpty()): ?>
                        <!-- Payer + checkout -->
                        <div class="col-lg-5">
                            <div class="card mb-3 checkout-column" id="checkoutCard">
                                <div class="card-header"><?= __('placetopay.payments.payer_details') ?></div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-12 col-md-6 col-lg-12 col-xl-6">
                                            <label for="first-name"><?= __('Nombre') ?></label>
                                            <input type="text" class="form-control justText" id="first-name" name="first_name" required>
                                            <div class="invalid-feedback"><?= __('placetopay.validation.first_name') ?></div>
                                        </div>
                                        <div class="form-group col-12 col-md-6 col-lg-12 col-xl-6">
                                            <label for="last-name"><?= __('placetopay.form.last_name') ?></label>
                                            <input type="text" class="form-control justText" id="last-name" name="last_name" required>
                                            <div class="invalid-feedback"><?= __('placetopay.validation.last_name') ?></div>
                                        </div>
                                        <div class="form-group col-12 col-md-6 col-lg-12">
                                            <label for="email"><?= __('Email') ?></label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars((string) $defaultEmail) ?>" placeholder="you@example.com" required>
                                            <div class="invalid-feedback"><?= __('placetopay.validation.email') ?></div>
                                        </div>
                                        <div class="form-group col-12 col-md-6 col-lg-12">
                                            <label for="mobile"><?= __('Celular') ?></label>
                                            <input type="tel" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars((string) $defaultMobile) ?>" placeholder="7875551234" pattern="\+?[\d\s\(\)\-]{10,20}" required>
                                            <div class="invalid-feedback"><?= __('placetopay.validation.mobile') ?></div>
                                        </div>
                                    </div>

                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="terms" name="terms" value="1" required>
                                        <label class="custom-control-label" for="terms">
                                            <?= __('placetopay.form.accept_terms') ?> <a href="../placetopay/terms.php" target="_blank" rel="noopener"><?= __('placetopay.form.terms_link') ?></a>
                                        </label>
                                        <div class="invalid-feedback"><?= __('placetopay.validation.terms') ?></div>
                                    </div>

                                    <div class="border-top pt-3 d-none d-lg-block">
                                        <div class="d-flex justify-content-between align-items-end mb-3">
                                            <div>
                                                <div class="text-muted small"><?= __('placetopay.payments.total') ?></div>
                                                <div class="h3 mb-0 charge-amount js-total">$0.00</div>
                                            </div>
                                            <small class="text-muted js-count"><?= trans_choice('placetopay.payments.selected_count', 0) ?></small>
                                        </div>
                                        <button class="btn btn-primary btn-lg btn-block pagar" type="submit"><?= __('placetopay.form.pay_with_placetopay') ?></button>
                                    </div>
                                    <small class="d-block text-muted mt-2"><?= __('placetopay.form.redirect_notice') ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($pendingCharges->isNotEmpty()): ?>
                    <!-- Mobile pay bar -->
                    <div class="pay-bar d-flex d-lg-none justify-content-between align-items-center">
                        <div>
                            <div class="h5 mb-0 charge-amount js-total">$0.00</div>
                            <small class="text-muted js-count"><?= trans_choice('placetopay.payments.selected_count', 0) ?></small>
                        </div>
                        <button class="btn btn-primary pagar" type="submit"><?= __('placetopay.form.pay') ?></button>
                    </div>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>

    <?php
    $jqMask = true;
    ?>
    <script>
        const paymentsLang = <?= json_encode([
            'error' => __('Error'),
            'selectCharge' => __('placetopay.payments.select_charge_error'),
            'processing' => __('placetopay.form.processing'),
            'selectedCount' => [
                trans_choice('placetopay.payments.selected_count', 0),
                trans_choice('placetopay.payments.selected_count', 1),
                trans_choice('placetopay.payments.selected_count', 2, ['count' => ':count']),
            ],
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
