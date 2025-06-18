<?php

require_once '../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use Classes\Route;
use Classes\Session;

Session::is_logged();


$colegio = Admin::primaryAdmin()->first();

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

    <div class="container">
        <h2 class="text-center mt-5">Seleccionar el estudiante al que se le quiere hacer el desposito</h2>

        <?php if (!$oneStudent) : ?>

            <div id="students" class="list-group my-5 mx-auto" style="width:30rem;">
                <?php foreach ($students as $estu): ?>
                    <button type="button" class="<?= $oneStudent ? 'active' : '' ?> list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-primary" aria-current="false" data-student-id="<?= $estu->mt ?>">
                        <span class="name"><?= "$estu->nombre $estu->apellidos" ?></span>
                        <span class="badge bg-success rounded-pill ">$<?= $estu->cantidad ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif ?>

        <div class="row">
            <div class="form-group col-12 col-md-8">
                <label for="money" class="form-label">Cantidad a depositar <?= $oneStudent ? "a $estu->nombre $estu->apellidos" : '' ?></label>
                <input type="text" class="form-control" id="money" required dir="rtl">
                <small class="text-muted">La cantidad minima es de $<?= $minAmount ?></small>
                <!-- Obligatorio para el deposito minimo en el JS -->
                <input type="hidden" id="minAmount" value="<?= $minAmount ?>">
                <div class="invalid-feedback">
                    Por favor introduzca una cantidad igual o mayor a $<?= $minAmount ?>
                </div>
            </div>


            <div class="form-group col-12 col-md-8">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                <div class="invalid-feedback">
                    Por favor introduzca un correo electronico valido.
                </div>
            </div>


            <hr class="my-4">


            <h3 class="my-2 p-2">Seleccione su metodo de pago</h3>
            <div class="col-12 mt-2">
                <ul class="nav nav-pills mb-3" id="methods-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cardMethod-tab" data-toggle="pill" data-target="#cardMethod" type="button" role="tab" aria-controls="cardMethod" aria-selected="true" type="button">Tarjeta</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="achMethod-tab" data-toggle="pill" data-target="#achMethod" type="button" role="tab" aria-controls="achMethod" aria-selected="false" type="button">ACH</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="cardMethod" role="tabpanel" aria-labelledby="cardMethod-tab">
                        <form id="cardForm" class="needs-validation" novalidate>
                            <div class="row">

                                <div class="form-group col-12">
                                    <label for="cc-name" class="form-label">Nombre en la tarjeta</label>
                                    <input type="text" class="form-control justText" id="cc-name" required>
                                    <small class="text-muted">Nombre completo como aparece en la tarjeta.</small>
                                    <div class="invalid-feedback">
                                        El nombre en la tarejeta es obligatorio.
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="cc-number" class="form-label">Número de la tarjeta</label>
                                    <input type="text" class="form-control" id="cc-number" required>
                                    <div class="invalid-feedback">
                                        El número de la tarjeta es obligatorio.
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="cc-expiration" class="form-label">Fecha de expiración</label>
                                    <input type="text" class="form-control" id="cc-expiration" required>
                                    <div class="invalid-feedback">
                                        Fecha de experiración es obligatorio.
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="cc-cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cc-cvv" required>
                                    <div class="invalid-feedback">
                                        El codigo de seguridad es obligatorio.
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="cc-zip" class="form-label">Codigo Postal</label>
                                    <input type="text" class="form-control zip" id="cc-zip" required>
                                    <div class="invalid-feedback">
                                        Se requiere el codigo postal.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- end cardMethod -->
                    <div class="tab-pane fade" id="achMethod" role="tabpanel" aria-labelledby="achMethod-tab">
                        <form id="achForm" class="needs-validation" novalidate>
                            <div class="row ">

                                <div class="form-group col-12">
                                    <label for="ach-name" class="form-label">Nombre en la cuenta</label>
                                    <input type="text" class="form-control justText" id="ach-name" required>
                                    <small class="text-muted">Nombre completo como aparece en la cuenta.</small>
                                    <div class="invalid-feedback">
                                        El nombre en la tarejeta es obligatorio.
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="ach-type" class="form-label">Tipo de cuenta</label>
                                    <select id="ach-type" class="form-control" required>
                                        <option value="">Selecciona</option>
                                        <option value="w">Cuenta de cheques</option>
                                        <option value="s">Cuenta de ahorros</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Seleccione un tipo de cuenta.
                                    </div>
                                </div>




                                <div class="form-group col-md-6">
                                    <label for="ach-number" class="form-label">Número de cuenta</label>
                                    <input type="text" class="form-control justNumber" id="ach-number" required>
                                    <div class="invalid-feedback">
                                        Número de cuenta es obligatorio.
                                    </div>
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="ach-route" class="form-label">Número de ruta</label>
                                    <input type="text" class="form-control justNumber" id="ach-route" required>
                                    <div class="invalid-feedback">
                                        Número de ruta es obligatorio.
                                    </div>
                                </div>



                                <div class="form-group col-md-3">
                                    <label for="ach-zip" class="form-label">Codigo Postal</label>
                                    <input type="text" class="form-control zip" id="ach-zip" required>
                                    <div class="invalid-feedback">
                                        Se requiere el codigo postal.
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- endachMethod -->
                </div>
            </div>


            <hr class="my-4">
            <button class="w-100 btn btn-primary btn-lg mb-5 pagar" type="button" id="pagar" <?= $oneStudent ? '' : 'disabled' ?>>Pagar</button>
            <!-- needed for ajax request  -->
            <input type="hidden" id='cuenta' value="<?= Session::id() ?>">
        </div>
        <!-- End Payment -->
    </div>

    <!-- Modal for the payment alert -->
    <div id="alertModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body d-flex justify-content-between align-items-center">

                </div>
            </div>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

</body>

</html>