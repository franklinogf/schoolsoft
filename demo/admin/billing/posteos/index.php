<?php
require_once '../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use Classes\DataBase\DB;
use Classes\Route;
use Classes\Session;

Session::is_logged();
$school = Admin::primaryAdmin();
$year = $school->year();
$budgets = DB::table('presupuesto')->where('year', $year)->get();
$posts = DB::table('posteos')->where('year', $year)->get();

$parents = Student::All();

$createdPosts = [];
foreach ($posts as $post) {
    $name = $post->ccNombre ? $post->ccNombre : $post->achNombre;
    $stuId = DB::table('posteos_detalles')->select('estudianteId as mt')->where('posteoId', $post->id)->first();

    if ($stuId) {
        $stu = new Student($stuId->mt);
    }

    $createdPosts[] = [
        'id' => $post->id,
        'account' => $post->cuenta,
        'parentName' => $name,
        'student' => isset($stu) ? "$stu->nombre $stu->apellidos" : '',
    ];
}
$createdPosts = json_decode(json_encode($createdPosts));
$studentName = array_map(function ($element) {
    return $element->student;
}, $createdPosts);

array_multisort($studentName, SORT_ASC, $createdPosts);
// $studentName  = array_column($createdPosts, 'student');
// array_multisort($studentName, SORT_DESC,  $createdPosts);

?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __('Posteos');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();

    ?>

</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container mt-3">
        <h1 class="text-center"><?= __("Posteos") ?></h1>

        <div class="form-group">
            <?php if (!isset($_POST['new'])): ?>
                <form method="POST">
                    <input class="btn btn-primary my-2" type="submit" name="new" value="<?= __("Crear nuevo posteo") ?>">
                </form>
                <form method="POST">
                    <label for="posts"><?= __("Posteos ya creados") ?></label>
                    <select class="form-control selectpicker show-tick" id="posts" name="posts" data-live-search="true" required>
                        <?php foreach ($createdPosts as $post): ?>
                            <option <?= isset($_POST['posts']) && $_POST['posts'] === $post->id ? 'selected' : '' ?> value="<?= $post->id ?>"><?= "($post->account) $post->student - $post->parentName" ?></option>
                        <?php endforeach ?>
                    </select>

                    <input class="btn btn-primary my-2" type="submit" value="<?= __("Buscar") ?>">
                </form>
            <?php else: ?>
                <form method="get">
                    <input class="btn btn-primary my-2" type="submit" value="<?= __("Reiniciar") ?>">
                </form>

            <?php endif ?>

            <?php if (isset($_POST['posts']) || isset($_POST['new'])): ?>
                <form method="POST">
                    <?php if (isset($_POST['posts'])):
                        $post = DB::table('posteos')->where('id', $_POST['posts'])->first();
                        $_POST['parents'] = $post->cuenta;

                    ?>
                        <input type="hidden" id="postId" name="postId" value="<?= $_POST['posts'] ?>">
                        <p><?= __("Número de cuenta familiar") ?>: <span class="badge badge-primary"><?= $post->cuenta ?></span></p>
                    <?php else: ?>
                        <input type="hidden" id="postId">
                        <input type="hidden" name="new">
                        <label for="parents">Cuenta</label>
                        <select class="form-control selectpicker show-tick" id="parents" name="parents" data-live-search="true" required>
                            <?php foreach ($parents as $parent): ?>
                                <option <?= isset($_POST['parents']) && $_POST['parents'] == $parent->id ? 'selected=""' : '' ?> value="<?= $parent->id ?>"><?= "$parent->apellidos $parent->nombre ($parent->id)" ?></option>
                            <?php endforeach ?>
                        </select>
                        <input class="btn btn-primary my-2" type="submit" value="<?= __("Buscar") ?>">
                    <?php endif ?>


                </form>

                <?php
                if (isset($_POST['parents'])):
                    $students = Student::byId($_POST['parents'])->get();
                    $post = DB::table('posteos')->where('cuenta', $_POST['parents'])->first();
                    $_POST['posts'] = $post->id ?? null;
                ?>
                    <input type="hidden" id='account' value="<?= $_POST['parents'] ?>">
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="email" class="form-label"><?= __("Email") ?></label>
                                    <input type="email" class="form-control" id="email" placeholder="you@example.com" required value="<?= $post->email ?? '' ?>">
                                    <div class="invalid-feedback">
                                        <?= __("Email es obligatorio") ?>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <h3 class="my-2"><?= __("Seleccione su metodo de pago") ?></h3>
                                </div>

                                <div class="col-12 mt-2">
                                    <ul class="nav nav-pills mb-3" id="methods-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?= $post && $post->tipoDePago === 'tarjeta' ? 'active' : '' ?>" id="cardMethod-tab" data-toggle="pill" data-target="#cardMethod" type="button" role="tab" aria-controls="cardMethod" aria-selected="true"><?= __("Tarjeta") ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link <?= $post && $post->tipoDePago === 'ach' ? 'active' : '' ?>" id="achMethod-tab" data-toggle="pill" data-target="#achMethod" type="button" role="tab" aria-controls="achMethod" aria-selected="false"><?= __("ACH") ?></button>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <h4 class="mb-4"><?= __("Informacion del pago") ?></h4>
                                        <div class="tab-pane fade <?= isset($post) && $post->tipoDePago === 'tarjeta' ? 'show active' : '' ?>" id="cardMethod" role="tabpanel" aria-labelledby="cardMethod-tab">
                                            <form id="cardForm" class="needs-validation form" novalidate>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="cc-name" class="form-label"><?= __("Nombre en la tarjeta") ?></label>
                                                        <input type="text" class="form-control justText" id="cc-name" required value="<?= isset($post) ? $post->ccNombre : '' ?>">
                                                        <small class="text-muted"><?= __("Nombre completo como aparece en la tarjeta.") ?></small>
                                                        <div class="invalid-feedback">
                                                            <?= __("El nombre en la tarjeta es obligatorio") ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="cc-number" class="form-label"><?= __("Número de la tarjeta") ?></label>
                                                        <input type="text" class="form-control" id="cc-number" required value="<?= isset($post) ? $post->ccNumero : '' ?>">
                                                        <div class="invalid-feedback">
                                                            <?= __("Número de tarjeta es obligatorio") ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="cc-expiration" class="form-label"><?= __("Fecha de expiración") ?></label>
                                                        <input type="text" class="form-control" id="cc-expiration" required value="<?= isset($post) ? $post->fechaExpiracion : '' ?>">
                                                        <div class="invalid-feedback">
                                                            <?= __("Fecha de expiración es obligatorio") ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="cc-cvv" class="form-label"><?= __("CVV") ?></label>
                                                        <input type="text" class="form-control" id="cc-cvv" required value="<?= isset($post) ? $post->cvv : '' ?>">
                                                        <div class="invalid-feedback">
                                                            <?= __("El codigo de seguridad es obligatorio") ?>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-5">
                                                        <label for="cc-zip" class="form-label"><?= __("Codigo Postal") ?></label>
                                                        <input type="text" class="form-control zip" id="cc-zip" required value="<?= isset($post) ? $post->ccZip : '' ?>">
                                                        <div class="invalid-feedback">
                                                            <?= __("Se requiere el codigo postal") ?>
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade <?= isset($post) && $post->tipoDePago === 'ach' ? 'show active' : '' ?>" id="achMethod" role="tabpanel" aria-labelledby="achMethod-tab">
                                            <form id="achForm" class="needs-validation form" novalidate>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="ach-name" class="form-label"><?= __("Nombre en la cuenta") ?></label>
                                                        <input type="text" class="form-control justText" id="ach-name" required value="<?= isset($post) ? $post->achNombre : '' ?>">
                                                        <small class="text-muted"><?= __("Nombre completo como aparece en la cuenta") ?></small>
                                                        <div class="invalid-feedback">
                                                            <?= __("El nombre en la cuenta es obligatorio") ?>
                                                        </div>
                                                    </div>

                                                    <div class="col">
                                                        <label for="ach-type" class="form-label"><?= __("Tipo de cuenta") ?></label>
                                                        <select id="ach-type" class="form-control" required>
                                                            <option value="">Selecciona</option>
                                                            <option <?= isset($post) && $post->tipoCuenta === 'w' ? 'selected' : '' ?> value="w"><?= __("Cuenta de cheques") ?></option>
                                                            <option <?= isset($post) && $post->tipoCuenta === 's' ? 'selected' : '' ?> value="s"><?= __("Cuenta de ahorros") ?></option>
                                                        </select>
                                                        <div class="invalid-feedback">
                                                            Seleccione un tipo de cuenta.
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <label for="ach-number" class="form-label">Número de cuenta</label>
                                                        <input type="text" class="form-control justNumber" id="ach-number" required value="<?= isset($post) ? $post->achNumero : '' ?>">
                                                        <div class="invalid-feedback">
                                                            Número de cuenta es obligatorio.
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <label for="ach-route" class="form-label">Número de ruta</label>
                                                        <input type="text" class="form-control justNumber" id="ach-route" required value="<?= isset($post) ? $post->numeroRuta : '' ?>">
                                                        <div class="invalid-feedback">
                                                            Número de ruta es obligatorio.
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <label for="ach-zip" class="form-label">Codigo Postal</label>
                                                        <input type="text" class="form-control zip" id="ach-zip" required value="<?= isset($post) ? $post->achZip : '' ?>">
                                                        <div class="invalid-feedback">
                                                            Se requiere el codigo postal.
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="paymentType" id="paymentType1" value="manual" <?= $post && $post->formaDePago === 'manual' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="paymentType1">Pago manual</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="paymentType" id="paymentType2" value="automatico" <?= $post && $post->formaDePago === 'automatico' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="paymentType2">Pago automatico</label>
                                            </div>
                                            <div id="dayOfPaymentDiv" class="form-group row mt-2 <?= $post && $post->formaDePago === 'automatico' ? '' : 'invisible' ?>">
                                                <label for="dayOfPayment" class="col-8 col-md-4 col-form-label">Dia de pago automatico</label>
                                                <input class="form-control col-3 col-md-1" type="number" name="dayOfPayment" id="dayOfPayment" min='1' max='30' value='<?= isset($post) ? $post->diaDePago : '' ?>'>
                                            </div>
                                        </div>


                                        <div class="text-center my-3">
                                            <button id="savePaymentMethod" class="btn btn-primary btn-block" data-type="<?= $post ? 'update' : 'save' ?>" type="button"><?= $post ? 'Actualizar' : 'Guardar' ?> metodo de pago</button>

                                            <div id="paymentMethodText" class="alert alert-success d-none mt-2" role="alert">
                                                Metodo de pago guardado
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-4">
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mt-3">
                                <label for="student">Seleccionar el estudiante al que se le quiere configurar el pago automatico</label>
                                <select class="form-control" id="student" name="student" title="Selecciona un estudiante" required>
                                    <option value=""></option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student->mt ?>"><?= "$student->apellidos $student->nombre" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="budgets">Seleccionar el cargo que se quiere configurar</label>
                                <select class="form-control" id="budgets" name="budgets" title="Selecciona un cargo" required>
                                    <?php foreach ($budgets as $budget): ?>
                                        <option <?= isset($_POST['budgets']) && $_POST['budgets'] === $budget->codigo ? 'selected' : '' ?> value="<?= $budget->codigo ?>"><?= $budget->descripcion ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label for="amount">Cantidad que desea pagar</label>
                                <input class="form-control" type="text" name="amount" id="amount" style="width:12rem">
                            </div>

                            <div class="col-12">
                                <button class="w-100 btn btn-primary btn-lg mb-5 disabled" disabled type="button" id="add">Guardar</button>
                            </div>

                        </div>

                        <div class="col-12 col-md-6">
                            <h4 class="text-center d-inline">Posteos creados </h4>
                            <p class="badge badge-primary">Total de todos los estudiantes: $<span id="totalAmount"><?= $post ? $post->total : '' ?></span></p>
                            <div id="postsList" class="list-group mt-2">

                            </div>
                        </div>

                    </div>





                <?php endif ?>


                <!-- End Payment -->

            <?php endif ?>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
</body>

</html>