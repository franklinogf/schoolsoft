<?php
require_once '../../app.php';

use Classes\Controllers\School;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
DB::table('colegio')->alter("ADD COLUMN constants JSON NULL;");
$lang = new Lang([
    ["Información del colegio", "School information"],
]);
$school = new School(Session::id());
$environmtsKeys = ["whatsapp", "resend", 'evertec'];
$constantsKeys = ['cafeteria_deposit'];
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Información del colegio");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= $lang->translation("Información del colegio") ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto">
            <form method="POST" action="<?= Route::url('/admin/information/includes/index.php') ?>" class="p-5">
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="colegio">Nombre del colegio</label>
                        <input type="text" class="form-control" name="colegio" id="colegio" value="<?= $school->info('colegio') ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="director">Director(a)</label>
                        <input type="text" class="form-control" name="director" id="director" value="<?= $school->info('director') ?>">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="idioma">Idioma</label>
                        <select class="form-control" name="idioma" id="idioma">
                            <option value="es" <?= $school->info('idioma') === 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="en" <?= $school->info('idioma') === 'en' ? 'selected' : '' ?>>Inglés</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" class="form-control" name="telefono" id="telefono" value="<?= $school->info('telefono') ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fax">Fax</label>
                        <input type="tel" autocomplete="fax" class="form-control" name="fax" id="fax" value="<?= $school->info('fax') ?>">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="principal">Principal</label>
                        <input type="text" class="form-control" name="principal" id="principal" value="<?= $school->info('principal') ?>">
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="correo">Correo eléctronico</label>
                                <input type="email" class="form-control" name="correo" id="correo" value="<?= $school->info('correo') ?>">
                            </div>
                            <div class="form-group col-12">
                                <label for="pagina">Pagina de internet</label>
                                <input type="text" class="form-control" name="pagina" id="pagina" value="<?= $school->info('pagina') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <h6 class="my-2"><?= $lang->translation("Dirección fisica") ?></h6>
                        <div class="form-row">
                            <div class="col-12">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir1" id="dir1" value="<?= $school->info('dir1') ?>" />
                            </div>
                            <div class="col-12 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir2" id="dir2" value="<?= $school->info('dir2') ?>" />
                            </div>
                            <div class="col-5 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="pueblo1" id="pueblo1" value="<?= $school->info('pueblo1') ?>" />
                            </div>
                            <div class="col-3 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="esta1" id="esta1" value="<?= $school->info('esta1') ?>" />
                            </div>
                            <div class="col-4 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= $school->info('zip1') ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="my-2"><?= $lang->translation("Dirección postal") ?></h6>
                        <div class="form-row">
                            <div class="col-12">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 1" name="dir3" id="dir3" value="<?= $school->info('dir3') ?>" />
                            </div>
                            <div class="col-12 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Dirección") ?> 2" name="dir4" id="dir4" value="<?= $school->info('dir4') ?>" />
                            </div>
                            <div class="col-5 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Ciudad") ?>" name="pueblo2" id="pueblo2" value="<?= $school->info('pueblo2') ?>" />
                            </div>
                            <div class="col-3 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Estado") ?>" name="esta2" id="esta2" value="<?= $school->info('esta2') ?>" />
                            </div>
                            <div class="col-4 mt-1">
                                <input class="form-control" type="text" placeholder="<?= $lang->translation("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= $school->info('zip2') ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <h6 class="my-2"><?= $lang->translation("Correos para solicitud de cita de padres") ?></h6>
                        <div class="row">
                            <div class="col-12">
                                <input type="email" placeholder="Primer correo" class="form-control" name="email3" id="email3" value="<?= $school->info('email3') ?>">
                            </div>
                            <div class="col-12">
                                <input type="email" placeholder="Segundo correo" class="form-control" name="email5" id="email5" value="<?= $school->info('email5') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="my-2"><?= $lang->translation("Correo para solicitud de pre-admición") ?></h6>
                        <div class="col-12">
                            <input type="email" placeholder="Correo" class="form-control" name="email4" id="email4" value="<?= $school->info('email4') ?>">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <h6 class="my-2"><?= $lang->translation("Constantes") ?></h6>
                    </div>
                    <?php
                    $constants = json_decode($school->info('constants'));
                    foreach ($constantsKeys as $key): ?>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend col-3 pr-0">
                                <span class="input-group-text w-100" id="<?= $key ?>"><?= $key ?></span>
                            </div>
                            <input type="text" class="form-control" value="<?= isset($constants->{$key}) ? $constants->{$key} : '' ?>" name="constants[<?= $key ?>]" placeholder="Valor de la constante" aria-describedby="<?= $key ?>">
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="row my-2">
                    <div class="col-12">
                        <button id="advancedOptionsBtn" class="btn btn-outline-primary" type="button" data-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                            Opciones avanzadas
                        </button>
                    </div>
                </div>

                <div class="collapse" id="advancedOptions">
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6 class="my-2"><?= $lang->translation("Variables de entorno") ?></h6>
                        </div>
                        <?php
                        $environments = json_decode($school->info('environments'));
                        foreach ($environmtsKeys as $key): ?>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend col-3 pr-0">
                                    <span class="input-group-text w-100" id="<?= $key ?>"><?= $key ?></span>
                                </div>
                                <input type="text" class="form-control" value="<?= isset($environments->{$key}) ? $environments->{$key}->value : '' ?>" name="environments[<?= $key ?>][value]" placeholder="Valor de la variable de entorno" aria-describedby="<?= $key ?>">
                                <input type="text" class="form-control" value="<?= isset($environments->{$key}) ? $environments->{$key}->other : '' ?>" name="environments[<?= $key ?>][other]" placeholder="Otro valor si es necesario" aria-describedby="<?= $key ?>">
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary" name="save" type="submit">Guardar</button>
                    </div>
                </div>
            </form>
            <form method="POST" action="<?= Route::url('/admin/information/includes/index.php') ?>">
                <div class="row">
                    <div class="offset-md-6 col-md-6">
                        <label for="clave">Cambiar contraseña</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="clave" id="clave" required placeholder="Contraseña nueva">
                            <?php if (Session::get('passwordSaved', true)): ?>
                                <div class="alert alert-success mt-1" role="alert">
                                    <strong>Contraseña actualizada</strong>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="offset-md-6 col-12">
                        <button class="btn btn-primary" name="savePassword" type="submit">Actualizar contraseña</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>
    <script>
        $(document).ready(function () {
            $("#advancedOptionsBtn").click(function (e) {
                const advancedOptions = $("#advancedOptions")
                if (!advancedOptions.hasClass('show')) {
                    if (prompt("Password") === '123456') {
                        advancedOptions.collapse('show')
                    }
                } else {
                    advancedOptions.collapse('hide')

                }
            })
        });
    </script>
</body>

</html>