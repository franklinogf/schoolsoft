<?php
require_once __DIR__ . '/../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Generar Listado PDF', 'Generate PDF List'],
    ['Código filtrado', 'Filtered code'],
    ['Orden', 'Order'],
    ['Apellidos A-Z', 'Last names A-Z'],
    ['Selecciona uno o varios grupos. Se generara en orden alfabetico por apellidos.', 'Select one or more groups. It will be generated in alphabetical order by last name.'],
    ['Mes', 'Month'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'April'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Tipo de listado', 'List type'],
    ['Apellidos', 'Last names'],
    ['Cuentas', 'Accounts'],
    ['Grados Separados', 'Separate Grades'],
    ['Generar PDF Ahora', 'Generate PDF Now'],
]);
$school = new School(Session::id());
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

<head>
    <?php
    $title = $lang->translation('Generar Listado PDF');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
    <style>
        .grupo-label {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: .2s;
            font-weight: 600;
            margin-bottom: 0;
        }

        .grupo-label:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .grupo-label input {
            display: none;
        }

        .grupo-label.active {
            border-color: #3b82f6;
            background: #3b82f6;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Generar Listado PDF') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 700px;">
                <p class="text-center text-muted"><?= $lang->translation('Código filtrado') ?>: <b>2473</b> | <?= $lang->translation('Orden') ?>: <?= $lang->translation('Apellidos A-Z') ?></p>
                <div class="alert alert-warning"><?= $lang->translation('Selecciona uno o varios grupos. Se generara en orden alfabetico por apellidos.') ?></div>

                <form action="pdf/generar_pdf.php" method="POST" target="_blank">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="mes"><?= $lang->translation('Mes') ?>:</label>
                                <select class="form-control" name="mes" id="mes" required>
                                    <option value="08"><?= $lang->translation('Agosto') ?></option>
                                    <option value="09"><?= $lang->translation('Septiembre') ?></option>
                                    <option value="10"><?= $lang->translation('Octubre') ?></option>
                                    <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                    <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                    <option value="01"><?= $lang->translation('Enero') ?></option>
                                    <option value="02"><?= $lang->translation('Febrero') ?></option>
                                    <option value="03"><?= $lang->translation('Marzo') ?></option>
                                    <option value="04"><?= $lang->translation('Abril') ?></option>
                                    <option value="05"><?= $lang->translation('Mayo') ?></option>
                                    <option value="06"><?= $lang->translation('Junio') ?></option>
                                    <option value="07"><?= $lang->translation('Julio') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="tipo_listado"><?= $lang->translation('Tipo de listado') ?>:</label>
                                <select class="form-control" name="tipo_listado" id="tipo_listado" required>
                                    <option value="1">1 - <?= $lang->translation('Apellidos') ?></option>
                                    <option value="2">2 - <?= $lang->translation('Cuentas') ?></option>
                                    <option value="3">3 - <?= $lang->translation('Grados Separados') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <div class="row" id="gridGrupos">
                                    <?php
                                    $grupos_extra = ['K-EO', 'PK-', 'M -'];
                                    foreach ($grupos_extra as $g) {
                                        echo '<div class="col-4 mb-2"><label class="grupo-label d-block"><input type="checkbox" name="grupos[]" value="' . $g . '"> ' . $g . '</label></div>';
                                    }
                                    for ($i = 1; $i <= 12; $i++) {
                                        $num = str_pad($i, 2, '0', STR_PAD_LEFT) . '-';
                                        echo '<div class="col-4 mb-2"><label class="grupo-label d-block"><input type="checkbox" name="grupos[]" value="' . $num . '"> ' . $num . '</label></div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary"><?= $lang->translation('Generar PDF Ahora') ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
    <script>
        document.querySelectorAll('.grupo-label').forEach(function(el) {
            el.addEventListener('click', function() {
                var chk = this.querySelector('input');
                // Timeout para dejar que el click cambie el checked
                setTimeout(function() {
                    if (chk.checked) {
                        el.classList.add('active');
                    } else {
                        el.classList.remove('active');
                    }
                }, 10);
            });
        });
    </script>
</body>

</html>