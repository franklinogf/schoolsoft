<?php

require_once __DIR__ . '/../../../app.php';

use App\Enums\DepositPaymentTypeEnum;
use App\Models\Deposit;
use Classes\Route;
use Classes\Session;

Session::is_logged();

$deposits = Deposit::with('student')
    ->where('id', Session::id())
    ->orderByDesc('date')
    ->get();

?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Historial de depositos');
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>

    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-4"><?= __('Historial de depositos') ?></h1>

        <div class="row justify-content-center">
            <div class="col-12 col-lg-9">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><?= __('Depositos realizados') ?></span>
                        <a href="./index.php" class="btn btn-sm btn-outline-primary"><?= __('Hacer un deposito') ?></a>
                    </div>

                    <?php if ($deposits->isEmpty()): ?>
                        <div class="card-body">
                            <p class="text-center text-muted mb-0"><?= __('No tienes depositos registrados.') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th><?= __('Fecha') ?></th>
                                        <th><?= __('Estudiante') ?></th>
                                        <th><?= __('Tipo de pago') ?></th>
                                        <th><?= __('Referencia') ?></th>
                                        <th class="text-end"><?= __('Cantidad') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($deposits as $deposito): ?>
                                        <?php $tipo = DepositPaymentTypeEnum::tryFrom((string) $deposito->tipoDePago); ?>
                                        <tr>
                                            <td>
                                                <?= $deposito->date->format('d/m/Y') ?>
                                                <div class="text-muted small"><?= $deposito->date->format('h:i A') ?></div>
                                            </td>
                                            <td><?= $deposito->student ? "{$deposito->student->nombre} {$deposito->student->apellidos}" : ($deposito->ss ?? '') ?></td>
                                            <td><span class="badge bg-secondary"><?= $tipo ? $tipo->label() : ($deposito->tipoDePago ?? '') ?></span></td>
                                            <td><?= $deposito->referencia ?? '' ?></td>
                                            <td class="text-end fw-bold">$<?= number_format((float) $deposito->cantidad, 2) ?></td>
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

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
</body>

</html>
