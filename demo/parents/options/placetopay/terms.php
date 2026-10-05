<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Services\PlacetoPayCheckout;
use Classes\Route;
use Classes\Session;

/**
 * Terms and conditions the parent must accept before being redirected to
 * PlacetoPay. Public (no login) so it can be opened from any payment form.
 */
$school = Admin::primaryAdmin();
$schoolName = $school->colegio ?? school_config('app.name', '');
$contactEmail = $school->correo ?? '';

$title = __('placetopay.terms.title');
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php Route::includeFile('/parents/includes/layouts/header.php'); ?>
</head>

<body>
    <?php if (Session::is_logged(false)) Route::includeFile('/parents/includes/layouts/menu.php'); ?>

    <div class="container-md mt-md-3 mb-md-5">
        <h1 class="text-center my-4"><?= __('placetopay.terms.title') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <p><?= __('placetopay.terms.intro', ['school' => htmlspecialchars($schoolName)]) ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.processing.title') ?></h5>
                        <p><?= __('placetopay.terms.processing.body') ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.buyer.title') ?></h5>
                        <p><?= __('placetopay.terms.buyer.body') ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.expiration.title') ?></h5>
                        <p><?= __('placetopay.terms.expiration.body', ['minutes' => PlacetoPayCheckout::EXPIRATION_MINUTES]) ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.pending.title') ?></h5>
                        <p><?= __('placetopay.terms.pending.body') ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.crediting.title') ?></h5>
                        <p><?= __('placetopay.terms.crediting.body') ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.refunds.title') ?></h5>
                        <p><?= __('placetopay.terms.refunds.body') ?></p>

                        <h5 class="mt-4"><?= __('placetopay.terms.privacy.title') ?></h5>
                        <p><?= __('placetopay.terms.privacy.body') ?></p>

                        <?php if ($contactEmail): ?>
                            <h5 class="mt-4"><?= __('placetopay.terms.contact.title') ?></h5>
                            <p><?= __('placetopay.terms.contact.body', ['email' => htmlspecialchars($contactEmail)]) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>
