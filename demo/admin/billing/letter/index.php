<?php
require_once '../../../app.php';


use Classes\Route;
use Classes\Session;

Session::is_logged();

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    $title = __('Carta de cobro');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?> <h1 class="text-center mb-4 mt-5"><?= __('Carta de cobro') ?></h1>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-center"><?= __('Opciones') ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="carta.php" target="_blank">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo" class="form-label font-weight-bold"><?= __('Tipo de salida') ?></label>
                                        <select name="tipo" id="tipo" class="form-control">
                                            <option value="pdf"><?= __('Informe pdf') ?></option>
                                            <option value="email"><?= __('Enviar por E-mail') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="opcion" class="form-label font-weight-bold"><?= __('Opción') ?></label>
                                        <select name="opcion" id="opcion" class="form-control">
                                            <option value="todos"><?= __('Todos') ?></option>
                                            <option value="deudores"><?= __('Deudores') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mes" class="form-label font-weight-bold"><?= __('Mes') ?></label>
                                        <select name="mes" id="mes" class="form-control">
                                            <option value="1"><?= __('Enero') ?></option>
                                            <option value="2"><?= __('Febrero') ?></option>
                                            <option value="3"><?= __('Marzo') ?></option>
                                            <option value="4"><?= __('Abril') ?></option>
                                            <option value="5"><?= __('Mayo') ?></option>
                                            <option value="6"><?= __('Junio') ?></option>
                                            <option value="7"><?= __('Julio') ?></option>
                                            <option value="8"><?= __('Agosto') ?></option>
                                            <option value="9"><?= __('Septiembre') ?></option>
                                            <option value="10"><?= __('Octubre') ?></option>
                                            <option value="11"><?= __('Noviembre') ?></option>
                                            <option value="12"><?= __('Diciembre') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="carta" class="form-label font-weight-bold"><?= __('Carta') ?></label>
                                        <select id="carta" name="carta" class="form-control">
                                            <option value="1"><?= __('Primer aviso de cobro') ?></option>
                                            <option value="2"><?= __('Segundo aviso de cobro') ?></option>
                                            <option value="3"><?= __('Carta de suspensión') ?></option>
                                            <option value="4"><?= __('Carta general') ?></option>
                                            <option value="5"><?= __('Carta de cobro general A') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="buscar" id="Aceptar" class="btn btn-primary btn-lg px-5">
                                    <?= __('Procesar') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>