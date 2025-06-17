<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
//use Classes\Controllers\School;
//use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Mensajes y Opciones", "Messages and Options"],
    ["Ver mensajes", "View messages"],
    ["Hacer cita", "Make an appointment"],
    ["Re-Matrícula", "Re-Enrollment"],
    ["Tareas", "Homeworks"],
    ["Tarjeta de notas", "Grades card"],
    ["Documentos", "Documents"],
    ["Hoja de progreso", "Progress sheet"],
    ["Informe de deficiencia", "Deficiency report"],
    ["Reporte de compras", "Purchase report"],
    ["Deposito Cafetería", "Cafeteria Deposit"],
]);



//session_start();
//$id = $_SESSION['id1'];
//$usua = $_SESSION['usua1'];

//require_once "../../control.php";

//if (!isset($_SESSION['id1']) || $_SESSION['id1'] == "") {
//    session_destroy();
//    exit;
// header("Location: ../ss_maestros.htm");
//}
//include('../../control.php');
//$school = new School(Session::id());

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();


$year = $colegio->year;
$minAmount = $colegio->deposito_minimo;


//mysql_set_charset('utf8', $con);

//$qry = mysql_query("SELECT * from colegio where usuario = 'administrador'", $con) or die("problema con query 1 " . mysql_error());
//$cole = mysql_fetch_object($qry);
//$year = $cole->year;
//$qry = "SELECT * from year where id='$id' AND year='$year'";
//$estudiantes = mysql_query($qry, $con) or die("problema con query 2 ({$qry})" . mysql_error());

$estudiantes = DB::table('year')->where([
    ['id', Session::id()],
    ['year', $year]
])->orderBy('apellidos')->get();


$oneStudent = false;
//mysql_query("ALTER TABLE `depositos` ADD `id2` INT NOT NULL AUTO_INCREMENT AFTER `grado`,ADD PRIMARY KEY (`id2`);");
//mysql_query("ALTER TABLE `depositos` ADD `studentId` INT NULL AFTER `id2`");
//mysql_query("ALTER TABLE `depositos` ADD `nombreEnLaTarjeta` VARCHAR(150) NULL AFTER `studentId`");
//mysql_query("ALTER TABLE `depositos` ADD `email` VARCHAR(100) NULL AFTER `studentId`");
//mysql_query("ALTER TABLE `depositos` ADD `descripcion` VARCHAR(150) NULL AFTER `email`");
//mysql_query("ALTER TABLE `depositos` ADD `autorizacion` VARCHAR(150) NULL AFTER `descripcion`");
//mysql_query("ALTER TABLE `depositos` ADD `referencia` VARCHAR(150) NULL AFTER `autorizacion`");
//mysql_query("ALTER TABLE `depositos` ADD `tarjetaUltimosDigitos` VARCHAR(4) NULL AFTER `referencia`");
//mysql_query("ALTER TABLE `depositos` ADD `zip` VARCHAR(5) NULL AFTER `tarjetaUltimosDigitos`");
//mysql_query("ALTER TABLE `depositos` ADD `hora` TIME NULL AFTER `fecha`");
//mysql_query("ALTER TABLE `depositos` ADD `tipoDePago` varchar(10) NULL AFTER `zip`");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <?php
    $title = $lang->translation("Deposito Cafetería");
    Route::includeFile('/parents/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/parents/includes/layouts/menu.php');
    ?>

    <div class="container">
        <h2 class="text-center mt-5">Seleccionar el estudiante al que se le quiere hacer el desposito</h2>

        <?php if (count($estudiantes) > 1) : ?>

            <div id="students" class="list-group my-5 bg-gray mx-auto" style="width:30rem;">
                <? // while ($estu = mysql_fetch_object($estudiantes)) : 
                ?>
                <?php foreach ($estudiantes as $estu) { ?>
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-primary" aria-current="true" data-student-id="<?= $estu->mt ?>">
                        <span class="text-dark name"><?= "$estu->nombre $estu->apellidos" ?></span>
                        <span class="badge bg-success rounded-pill ">$<?= $estu->cantidad ?></span>
                    </button>
                <?php } ?>
            </div>
        <?php endif ?>



        <?php if (count($estudiantes) === 1) :
            $estu = DB::table('year')->where([
                ['id', Session::id()],
                ['year', $year]
            ])->orderBy('apellidos')->first();

            //            $estu = $estudiantes;
            //            $estu = mysql_fetch_object($estudiantes);
            $mt = $estu->mt;
            $oneStudent = true;
        ?>
            <div id="students" class="list-group my-5 bg-gray mx-auto" style="width:30rem;">
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-primary active" aria-current="true" data-student-id="<?= $estu->mt ?>">
                    <span class="text-dark name text-white"><?= "$estu->nombre $estu->apellidos" ?></span>
                    <span class="badge bg-success rounded-pill">$<?= $estu->cantidad ?></span>
                </button>
            </div>

        <?php endif ?>


        <div class="row">
            <div class="row">
                <div class="col-md-3">
                    <label for="money" class="form-label">Cantidad a depositar <?= $oneStudent ? "a $estu->nombre $estu->apellidos" : '' ?></label>
                    <input type="text" class="form-control" id="money" required dir="rtl">
                    <small class="text-muted">La cantidad minima es de $<?= $minAmount ?></small>
                    <!-- Obligatorio para el deposito minimo en el JS -->
                    <input type="hidden" id="minAmount" value="<?= $minAmount ?>">
                    <div class="invalid-feedback">
                        Por favor introduzca una cantidad igual o mayor a $<?= $minAmount ?>
                    </div>
                </div>
            </div>
            <div class="row g-3">

                <div class="col-md-8">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                    <div class="invalid-feedback">
                        Por favor introduzca un correo electronico valido.
                    </div>
                </div>
            </div>

            <hr class="my-4">


            <h3 class="my-2">Seleccione su metodo de pago</h3>
            <div class="col-12 mt-2">
                <ul class="nav nav-pills mb-3" id="methods-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cardMethod-tab" data-bs-toggle="pill" data-bs-target="#cardMethod" type="button" role="tab" aria-controls="cardMethod" aria-selected="true">Tarjeta</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="achMethod-tab" data-bs-toggle="pill" data-bs-target="#achMethod" type="button" role="tab" aria-controls="achMethod" aria-selected="false">ACH</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <h4 class="mb-4">Informacion del pago</h4>
                    <div class="tab-pane fade show active" id="cardMethod" role="tabpanel" aria-labelledby="cardMethod-tab">
                        <form id="cardForm" class="needs-validation" novalidate>
                            <div class="row ">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="cc-name" class="form-label">Nombre en la tarjeta</label>
                                        <input type="text" class="form-control justText" id="cc-name" required>
                                        <small class="text-muted">Nombre completo como aparece en la tarjeta.</small>
                                        <div class="invalid-feedback">
                                            El nombre en la tarejeta es obligatorio.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="cc-number" class="form-label">Número de la tarjeta</label>
                                        <input type="text" class="form-control" id="cc-number" required>
                                        <div class="invalid-feedback">
                                            El número de la tarjeta es obligatorio.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="cc-expiration" class="form-label">Fecha de expiración</label>
                                    <input type="text" class="form-control" id="cc-expiration" required>
                                    <div class="invalid-feedback">
                                        Fecha de experiración es obligatorio.
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label for="cc-cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cc-cvv" required>
                                    <div class="invalid-feedback">
                                        El codigo de seguridad es obligatorio.
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="cc-zip" class="form-label">Codigo Postal</label>
                                        <input type="text" class="form-control zip" id="cc-zip" required>
                                        <div class="invalid-feedback">
                                            Se requiere el codigo postal.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- end cardMethod -->
                    <div class="tab-pane fade" id="achMethod" role="tabpanel" aria-labelledby="achMethod-tab">
                        <form id="achForm" class="needs-validation" novalidate>
                            <div class="row ">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ach-name" class="form-label">Nombre en la cuenta</label>
                                        <input type="text" class="form-control justText" id="ach-name" required>
                                        <small class="text-muted">Nombre completo como aparece en la cuenta.</small>
                                        <div class="invalid-feedback">
                                            El nombre en la tarejeta es obligatorio.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
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

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ach-number" class="form-label">Número de cuenta</label>
                                        <input type="text" class="form-control justNumber" id="ach-number" required>
                                        <div class="invalid-feedback">
                                            Número de cuenta es obligatorio.
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="ach-route" class="form-label">Número de ruta</label>
                                        <input type="text" class="form-control justNumber" id="ach-route" required>
                                        <div class="invalid-feedback">
                                            Número de ruta es obligatorio.
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="ach-zip" class="form-label">Codigo Postal</label>
                                        <input type="text" class="form-control zip" id="ach-zip" required>
                                        <div class="invalid-feedback">
                                            Se requiere el codigo postal.
                                        </div>
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
            <input type="hidden" id='cuenta' value="<?= $id ?>">
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script type="text/javascript" src="deposito.js"></script>

</body>

</html>