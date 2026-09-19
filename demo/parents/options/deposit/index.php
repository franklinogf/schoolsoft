<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Student;
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


$oneStudent =  count($students) === 1 ? true : false;

if ($oneStudent) {
    $estu = $students[0];
}


?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">


<head>
    <?php
    $title = __('Deposito Cafetería');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('Deposito Cafetería') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-7">

                <?php if (!$oneStudent) : ?>
                    <div class="card mb-3">
                        <div class="card-header"><?= __('Seleccionar el estudiante al que se le quiere hacer el deposito') ?></div>
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
                        <span><?= __('Detalles del deposito') ?></span>
                        <?php if ($oneStudent): ?>
                            <span class="badge bg-success rounded-pill">$<?= $estu->cantidad ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <a href="./history.php" class="d-block text-end small mb-3">
                            <?= __('Ver historial de depositos') ?> &raquo;
                        </a>
                        <form id="depositForm" class="needs-validation" novalidate method="post" action="./includes/start.php">
                            <div class="form-row">
                                <div class="form-group col-12">
                                    <label for="money" class="form-label"><?= __('Cantidad a depositar') ?> <?= $oneStudent ? __('a :name', ['name' => "$estu->nombre $estu->apellidos"]) : '' ?></label>
                                    <input type="text" class="form-control form-control-lg" id="money" required dir="rtl">
                                    <small class="text-muted"><?= __('La cantidad minima es de :amount', ['amount' => '$' . $minAmount]) ?></small>
                                    <!-- Obligatorio para el deposito minimo en el JS -->
                                    <input type="hidden" id="minAmount" value="<?= $minAmount ?>">
                                    <div class="invalid-feedback">
                                        <?= __('Por favor introduzca una cantidad igual o mayor a :amount', ['amount' => '$' . $minAmount]) ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="form-row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="first-name" class="form-label"><?= __('Nombre') ?></label>
                                    <input type="text" class="form-control justText" id="first-name" name="first_name" required>
                                    <div class="invalid-feedback">
                                        <?= __('El nombre es obligatorio.') ?>
                                    </div>
                                </div>

                                <div class="form-group col-12 col-md-6">
                                    <label for="last-name" class="form-label"><?= __('Apellido') ?></label>
                                    <input type="text" class="form-control justText" id="last-name" name="last_name" required>
                                    <div class="invalid-feedback">
                                        <?= __('El apellido es obligatorio.') ?>
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="email" class="form-label"><?= __('Email') ?></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                                    <div class="invalid-feedback">
                                        <?= __('Por favor introduzca un correo electronico valido.') ?>
                                    </div>
                                </div>
                            </div>

                            <button class="w-100 btn btn-primary btn-lg mt-2 pagar" type="submit" id="pagar" <?= $oneStudent ? '' : 'disabled' ?>><?= __('Pagar con PlacetoPay') ?></button>

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
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

    <?php if ($status === 'success'): ?>
        <script>
            Alert.fire(<?= json_encode(__('¡Deposito exitoso!')) ?>, <?= json_encode(__('El nuevo saldo es :amount', ['amount' => '$' . ($statusAmount ?? '')])) ?>, 'success')
        </script>
    <?php elseif ($status === 'error'): ?>
        <script>
            Alert.fire(<?= json_encode(__('Error')) ?>, <?= json_encode($statusMessage ?? __('No se pudo procesar el deposito.')) ?>, 'error')
        </script>
    <?php endif; ?>

</body>

</html>