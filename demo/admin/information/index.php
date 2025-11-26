<?php
require_once __DIR__ . '/../../app.php';

use App\Enums\LanguageCode;
use App\Models\Admin;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


Session::is_logged();
$columns = Capsule::schema()->getColumnListing('colegio');

Capsule::schema()->table('colegio', function (Blueprint $table) use ($columns): void {
    if (!in_array('constants', $columns)) {
        $table->json('constants')->nullable();
    }

    if (!in_array('environments', $columns)) {
        $table->json('environments')->nullable();
    }

    if (!in_array('theme', $columns)) {
        $table->json('theme')->nullable();
    }
    if (!in_array('pdf', $columns)) {
        $table->json('pdf')->nullable();
    }
});

$school = Admin::primaryAdmin();

$environmtsKeys = ["whatsapp", "resend", 'evertec'];
$constantsKeys = ['cafeteria_deposit'];

$defaultTheme = require __ROOT . '/config/theme.php';
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = __("Información del colegio");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-md mt-md-3 mb-md-5 px-0">
        <h1 class="text-center my-3"><?= __("Información del colegio") ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto">
            <form method="POST" action="<?= Route::url('/admin/information/includes/index.php') ?>" class="p-5">
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="colegio"><?= __("Nombre del colegio") ?></label>
                        <input type="text" class="form-control" name="colegio" id="colegio" value="<?= $school->colegio ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="director"><?= __("Director(a)") ?></label>
                        <input type="text" class="form-control" name="director" id="director" value="<?= $school->director ?>">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="idioma"><?= __("Idioma") ?></label>
                        <select class="form-control" name="idioma" id="idioma">
                            <?php foreach (LanguageCode::cases() as $language): ?>
                                <option value="<?= $language->value ?>" <?= $school->idioma === $language->value ? 'selected' : '' ?>><?= $language->label() ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="telefono"><?= __("Teléfono") ?></label>
                        <input type="tel" class="form-control" name="telefono" id="telefono" value="<?= $school->telefono ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fax"><?= __("Fax") ?></label>
                        <input type="tel" autocomplete="fax" class="form-control" name="fax" id="fax" value="<?= $school->fax ?>">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-6">
                        <label for="principal"><?= __("Principal") ?></label>
                        <input type="text" class="form-control" name="principal" id="principal" value="<?= $school->principal ?>">
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="correo"><?= __("Correo eléctronico") ?></label>
                                <input type="email" class="form-control" name="correo" id="correo" value="<?= $school->correo ?>">
                            </div>
                            <div class="form-group col-12">
                                <label for="pagina"><?= __("Pagina de internet") ?></label>
                                <input type="text" class="form-control" name="pagina" id="pagina" value="<?= $school->pagina ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <h6 class="my-2"><?= __("Dirección fisica") ?></h6>
                        <div class="form-row">
                            <div class="col-12">
                                <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir1" id="dir1" value="<?= $school->dir1 ?>" />
                            </div>
                            <div class="col-12 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir2" id="dir2" value="<?= $school->dir2 ?>" />
                            </div>
                            <div class="col-5 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="pueblo1" id="pueblo1" value="<?= $school->pueblo1 ?>" />
                            </div>
                            <div class="col-3 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="esta1" id="esta1" value="<?= $school->esta1 ?>" />
                            </div>
                            <div class="col-4 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip1" id="zip1" value="<?= $school->zip1 ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="my-2"><?= __("Dirección postal") ?></h6>
                        <div class="form-row">
                            <div class="col-12">
                                <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 1" name="dir3" id="dir3" value="<?= $school->dir3 ?>" />
                            </div>
                            <div class="col-12 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Dirección") ?> 2" name="dir4" id="dir4" value="<?= $school->dir4 ?>" />
                            </div>
                            <div class="col-5 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Ciudad") ?>" name="pueblo2" id="pueblo2" value="<?= $school->pueblo2 ?>" />
                            </div>
                            <div class="col-3 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Estado") ?>" name="esta2" id="esta2" value="<?= $school->esta2 ?>" />
                            </div>
                            <div class="col-4 mt-1">
                                <input class="form-control" type="text" placeholder="<?= __("Codigo Postal") ?>" name="zip2" id="zip2" value="<?= $school->zip2 ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <h6 class="my-2"><?= __("Correos para solicitud de cita de padres") ?></h6>
                        <div class="row">
                            <div class="col-12">
                                <input type="email" placeholder="<?= __("Primer correo") ?>" class="form-control" name="email3" id="email3" value="<?= $school->email3 ?>">
                            </div>
                            <div class="col-12">
                                <input type="email" placeholder="<?= __("Segundo correo") ?>" class="form-control" name="email5" id="email5" value="<?= $school->email5 ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="my-2"><?= __("Correo para solicitud de pre-admición") ?></h6>
                        <div class="col-12">
                            <input type="email" placeholder="Correo" class="form-control" name="email4" id="email4" value="<?= $school->email4 ?>">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <h6 class="my-2"><?= __("Constantes") ?></h6>
                    </div>
                    <?php
                    $constants = $school->constants;
                    foreach ($constantsKeys as $key): ?>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend col-3 pr-0">
                                <span class="input-group-text w-100" id="<?= $key ?>"><?= $key ?></span>
                            </div>
                            <input type="text" class="form-control" value="<?= isset($constants->{$key}) ? $constants->{$key} : '' ?>" name="constants[<?= $key ?>]" placeholder="<?= __("Valor de la constante") ?>" aria-describedby="<?= $key ?>">
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="row my-2">
                    <div class="col-12">
                        <button id="advancedOptionsBtn" class="btn btn-outline-primary" type="button" data-target="#advancedOptions" aria-expanded="false" aria-controls="advancedOptions">
                            <?= __("Opciones avanzadas") ?>
                        </button>
                    </div>
                </div>

                <div class="collapse" id="advancedOptions">
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6 class="my-2"><?= __("Variables de entorno") ?></h6>
                        </div>
                        <?php
                        $environments = $school->environments;
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
                        <button class="btn btn-primary" name="save" type="submit"><?= __("Guardar") ?></button>
                    </div>
                </div>
            </form>
            <form method="POST" action="<?= Route::url('/admin/information/includes/index.php') ?>">
                <div class="row">
                    <div class="offset-md-6 col-md-6">
                        <label for="clave"><?= __("Cambiar contraseña") ?></label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="clave" id="clave" required placeholder="Contraseña nueva">
                            <?php if (Session::get('passwordSaved', true)): ?>
                                <div class="alert alert-success mt-1" role="alert">
                                    <strong><?= __("Contraseña actualizada") ?></strong>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="offset-md-6 col-12">
                        <button class="btn btn-primary" name="savePassword" type="submit"><?= __("Actualizar contraseña") ?></button>
                    </div>

                </div>
            </form>
        </div>
        <div class="container bg-white shadow-lg py-3 mt-4 rounded mx-auto">
            <?php
            $pdf = $school->pdf;
            $pdfColor =  $pdf !== null
                ? "#" . dechex($pdf['red']) . dechex($pdf['green']) . dechex($pdf['blue'])
                : "#" . dechex(config('pdf.fill_color.red')) . dechex(config('pdf.fill_color.green')) . dechex(config('pdf.fill_color.blue'));

            ?>
            <div>
                <small class="text-info"><?= Session::get('pdf', true) ?></small>
            </div>

            <form id="pdfForm" method="POST" action="<?= Route::url('/admin/information/includes/pdf.php') ?>">

                <div class="row my-2">
                    <div class="col-2">
                        <label for="pdf"><?= __('PDF RGB color') ?></label>
                        <input name="pdf" id="<?= $key ?>" class="form-control" type="color" value="<?= $pdfColor ?>">
                    </div>
                </div>


                <button class="btn btn-primary mt-2" type="submit"><?= __("Actualizar") ?></button>
            </form>

        </div>
        <div class="container bg-white shadow-lg py-3 mt-4 rounded mx-auto">
            <?php
            $theme = $school->theme;

            ?>
            <h6><?= __("Tema de la página") ?></h6>
            <div>
                <small class="text-info"><?= Session::get('theme', true) ?></small>
            </div>

            <form id="themeForm" method="POST" action="<?= Route::url('/admin/information/includes/theme.php') ?>">
                <?php foreach ($defaultTheme as $themeKey => $themeValue): ?>
                    <div class="row my-2">
                        <div class="col-12">
                            <h7><?= ucfirst($themeKey) ?></h>
                        </div>
                        <?php foreach ($themeValue as $key => $value):
                            $label = ucfirst(str_replace('-', ' ', $key));
                        ?>
                            <?php if ($themeKey === 'colors'): ?>
                                <div class="col-2">
                                    <label for="<?= $key ?>"><?= $label ?></label>
                                    <input name="theme[<?= $themeKey ?>][<?= $key ?>]" id="<?= $key ?>" class="form-control" type="color" value="<?= $theme?->{$themeKey}->{$key} ?? $value ?>">
                                </div>
                            <?php elseif ($themeKey === 'booleans') : ?>

                                <div class="col-12">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="checkbox" name="theme[<?= $themeKey ?>][<?= $key ?>]" id="<?= $key ?>" <?= $theme?->{$themeKey}->{$key} ?? $value ? 'checked' : '' ?>> <?= $label ?>
                                        </label>
                                    </div>
                                </div>

                            <?php endif ?>
                        <?php endforeach ?>
                    </div>

                <?php endforeach ?>
                <button class="btn btn-primary mt-2" type="submit"><?= __("Actualizar tema") ?></button>
            </form>
            <form method="POST" action="<?= Route::url('/admin/information/includes/resetTheme.php') ?>">
                <button class="btn btn-warning mt-2" type="submit"><?= __("Restaurar a tema por defecto") ?></button>
            </form>


        </div>

        <?php
        Route::includeFile('/includes/layouts/scripts.php', true);
        ?>
        <script>
            $(document).ready(function() {
                $("#advancedOptionsBtn").click(function(e) {
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