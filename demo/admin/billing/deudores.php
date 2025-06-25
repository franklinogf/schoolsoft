<?php
require_once '../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ["Lista de deudores", "List of debtors"],
    ['Papel tamaño', 'Paper size'],
    ['Papel orientación', 'Paper orientation'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Con Cantidad', 'With Quantity'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Hoja Legal', 'Legal Sheet'],
    ['Hoja carta', 'Letter Sheet'],
    ['Por Cuenta', 'By Account'],
    ['Por Grado', 'By Grade'],
    ['', ''],
    ['Opciones', 'Options'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Por Familia', 'Per Family'],
    ['Procesar', 'Process'],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['No', 'No'],
    ['Cambiar estado', 'Change Status'],
    ['Selección de Meses', 'Month Selection'],
    ['Borrar todos los Cargos', 'Eliminate all costs'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$grades = $school->allGrades();

$tabla12 = DB::table('presupuesto')->whereRaw("year='$year'")->orderBy('codigo')->get();

?>

<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang->translation('Lista de deudores') ?></title>
    <style>
        .report-card {
            border-left: 4px solid #007bff;
        }

        .month-checkbox {
            transform: scale(1.2);
        }

        .options-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .month-selection-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }

        .month-grid {
            background: #fff;
        }

        .custom-checkbox {
            width: 1.5rem;
            height: 1.5rem;
        }
    </style>
    <script language="JavaScript">
        document.oncontextmenu = function() {
            return false
        }

        function confirmar(mensaje) {
            return confirm(mensaje);
        }

        function cambiaPalabra() {
            // Function for handling selection changes
            return true;
        }
    </script>
    <?php
    $title = $lang->translation('Lista de deudores');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?> <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4 mt-3">
                    <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>
                    <?= $lang->translation('Lista de deudores') ?>
                </h1>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card report-card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cog mr-2"></i>
                            Configuración del Reporte
                        </h5>
                    </div>
                    <div class="card-body">
                        <form name="algunNombre" action="pdf/deudores_inf.php" method="post" target="_blank">
                            <div class="row">
                                <!-- Left Column - Options -->
                                <div class="col-md-6">
                                    <div class="card options-card mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-list mr-1"></i>
                                                <?= $lang->translation('Opciones') ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="desc" class="form-label font-weight-bold">
                                                    <i class="fas fa-filter mr-1"></i>
                                                    Filtro de Cuentas
                                                </label>
                                                <select name="desc" id="desc" class="form-control" onclick="return cambiaPalabra(); return true">
                                                    <option value="Selección"><?= $lang->translation('Selección') ?></option>
                                                    <option value="Todos"><?= $lang->translation('Todos') ?></option>
                                                    <?php foreach ($tabla12 as $row2): ?>
                                                        <option value="<?= $row2->codigo ?>"><?= $row2->codigo . ', ' . $row2->descripcion ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="gru" class="form-label font-weight-bold">
                                                    <i class="fas fa-sort mr-1"></i>
                                                    Agrupación
                                                </label>
                                                <select name="gru" id="gru" class="form-control">
                                                    <option value="A"><?= $lang->translation('Por Grado') ?></option>
                                                    <option value="B"><?= $lang->translation('Por Cuenta') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="cct" class="form-label font-weight-bold">
                                                    <i class="fas fa-hashtag mr-1"></i>
                                                    <?= $lang->translation('Con Cantidad') ?>
                                                </label>
                                                <select name="cct" id="cct" class="form-control">
                                                    <option value="1"><?= $lang->translation('Si') ?></option>
                                                    <option value="2"><?= $lang->translation('No') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Paper Settings -->
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-file-pdf mr-1"></i>
                                                Configuración del Papel
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="pag1" class="form-label font-weight-bold">
                                                    <i class="fas fa-expand-arrows-alt mr-1"></i>
                                                    <?= $lang->translation('Papel tamaño') ?>
                                                </label>
                                                <select name="pag1" id="pag1" class="form-control">
                                                    <option value="Letter"><?= $lang->translation('Hoja Carta') ?></option>
                                                    <option value="Legal"><?= $lang->translation('Hoja Legal') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="pag" class="form-label font-weight-bold">
                                                    <i class="fas fa-rotate-90 mr-1"></i>
                                                    <?= $lang->translation('Papel orientación') ?>
                                                </label>
                                                <select name="pag" id="pag" class="form-control">
                                                    <option value="P">Portrait</option>
                                                    <option value="L">Landscape</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Month Selection Section -->
                            <div class="card month-selection-card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        <?= $lang->translation('Selección de Meses') ?>
                                    </h6>
                                </div>
                                <div class="card-body month-grid p-3">
                                    <!-- First Row of Months -->
                                    <div class="row mb-3">
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="ago" name="ago" value="1">
                                                <label class="custom-control-label font-weight-bold" for="ago">
                                                    <?= $lang->translation('Agosto') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="sep" name="sep" value="1">
                                                <label class="custom-control-label font-weight-bold" for="sep">
                                                    <?= $lang->translation('Septiembre') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="oct" name="oct" value="1">
                                                <label class="custom-control-label font-weight-bold" for="oct">
                                                    <?= $lang->translation('Octubre') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="nov" name="nov" value="1">
                                                <label class="custom-control-label font-weight-bold" for="nov">
                                                    <?= $lang->translation('Noviembre') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="dic" name="dic" value="1">
                                                <label class="custom-control-label font-weight-bold" for="dic">
                                                    <?= $lang->translation('Diciembre') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Row of Months -->
                                    <div class="row mb-3">
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="ene" name="ene" value="1">
                                                <label class="custom-control-label font-weight-bold" for="ene">
                                                    <?= $lang->translation('Enero') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="feb" name="feb" value="1">
                                                <label class="custom-control-label font-weight-bold" for="feb">
                                                    <?= $lang->translation('Febrero') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="mar" name="mar" value="1">
                                                <label class="custom-control-label font-weight-bold" for="mar">
                                                    <?= $lang->translation('Marzo') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="abr" name="abr" value="1">
                                                <label class="custom-control-label font-weight-bold" for="abr">
                                                    <?= $lang->translation('Abril') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="may" name="may" value="1">
                                                <label class="custom-control-label font-weight-bold" for="may">
                                                    <?= $lang->translation('Mayo') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Third Row of Months -->
                                    <div class="row">
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="jun" name="jun" value="1">
                                                <label class="custom-control-label font-weight-bold" for="jun">
                                                    <?= $lang->translation('Matri/Junio') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox text-center">
                                                <input type="checkbox" class="custom-control-input month-checkbox" id="jul" name="jul" value="1">
                                                <label class="custom-control-label font-weight-bold" for="jul">
                                                    <?= $lang->translation('Julio') ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col"></div>
                                        <div class="col"></div>
                                        <div class="col"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" name="pro" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-play mr-2"></i>
                                    <?= $lang->translation('Procesar') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    ?>

</body>

</html>