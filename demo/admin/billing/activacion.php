<?php
require_once '../../app.php';

use App\Models\Admin;
use App\Models\Scopes\YearScope;
use App\Models\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Session::is_logged();
$lang = new Lang([
    ["Pantalla para activación y desactivación de pantallas", "Screen for activating and deactivating screens"],
    ['Grabar', 'Save'],
    ['Guardar', 'Save'],
    ['Código', 'Code'],
    ['Editar', 'Edit'],
    ['Borrar', 'Delete'],
    ['Debe de llenar todos los campos', 'You must fill all fields'],
    ['Lista de codigos', 'Codes list'],
    ['Descripción', 'Description'],
    ['Activo', 'Active'],
    ['Costos', 'Costs'],
    ['Opciones', 'Options'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Nombre', 'Name'],
    ['Precio', 'Price'],
    ['Grado', 'Grade'],
    ['Selección', 'Selection'],
    ['Si', 'Yes'],
    ['Cambiar estado', 'Change Status'],
    ['Guardar cambios', 'Save Changes'],
    ['E', 'I'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
    ['Tipos de fotos', 'Types of photos'],
    ['Activar Estudios Supervisados:', 'Activate Studies. Supervised:'],
    ['Total Matrículados:', 'Total Enrolled:'],
    ['Activar ordenes de camisetas:', 'Activate t-shirt orders:'],
    ['Activar cena de graduación 12:', 'Activate graduation dinner 12:'],
    ['Activación Inflable', 'Inflatable Activation'],
    ['Activación abono matrícula', 'Registration payment activation'],
    ['Activación abono mensualidad', 'Activation of monthly payment'],
    ['Activación compras de fotos', 'Activation of photo purchases'],
    ['Cantidad', 'Amount'],
    ['', ''],



]);

$user = Admin::user(Session::id())->first();
$year = $user->year2;

$test = 0;

if (isset($_POST['grabar'])) {
    $thisCourse2 = DB::table('colegio')->where([
        ['usuario', 'administrador']
    ])->update([
        'es1' => $_POST['es1'],
        'es2' => $_POST['es2'],
        'es3' => $_POST['es3'],
        'es4' => $_POST['es4'],
        'es5' => $_POST['es5'],
        'es6' => $_POST['es6'],
        'es7' => $_POST['es7'],
        'es8' => $_POST['es8'],
        'es9' => $_POST['es9'],
        'actfotos' => $_POST['actfotos'],
        'actmen' => $_POST['actmen'],
        'actmat' => $_POST['actmat'],
        'actinf' => $_POST['actinf'],
        'cta_camisa' => $_POST['ctacamisa'],
        'camisas' => $_POST['camisa'],
        'cena' => $_POST['cena'],
        'esac' => $_POST['esac'],
    ]);
}



$r2 = Admin::primaryAdmin();

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', 'KG'],
    ['year', $year]
])->orderBy('id')->get();
$tes1 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '01-'],
    ['year', $year]
])->orderBy('id')->get();
$tes2 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '02-'],
    ['year', $year]
])->orderBy('id')->get();
$tes3 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '03-'],
    ['year', $year]
])->orderBy('id')->get();
$tes4 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '04-'],
    ['year', $year]
])->orderBy('id')->get();
$tes5 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '05-'],
    ['year', $year]
])->orderBy('id')->get();
$tes6 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '06-'],
    ['year', $year]
])->orderBy('id')->get();
$tes7 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '07-'],
    ['year', $year]
])->orderBy('id')->get();
$tes8 = count($est);

$est = DB::table('year')->select("DISTINCT year")->where([
    ['pago_e_s', 'OK'],
    ['grado', 'LIKE', '08-'],
    ['year', $year]
])->orderBy('id')->get();
$tes9 = count($est);

?>
<!DOCTYPE html>
<html>

<head>
    <?php
    $title = $lang->translation('Pantalla para activación y desactivación de pantallas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>

    <script language="JavaScript">
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
        document.oncontextmenu = function() {
            return false
        }
    </script>
    <style type="text/css">
        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .grade-input {
            max-width: 60px;
        }

        .activation-card {
            border-left: 4px solid #007bff;
        }

        .photo-type-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .photo-type-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
        }

        .stats-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
        }
    </style>

</head>

<body class="pb-5">
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-4 mt-5"><?= $lang->translation('Pantalla para activación y desactivación de pantallas') ?></h1>
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs mr-2"></i><?= $lang->translation('Opciones') ?> de Activación
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <!-- Enrollment and Supervised Studies Section -->
                            <div class="row mb-4">
                                <!-- Left Column - Grade Enrollment -->
                                <div class="col-lg-7">
                                    <div class="card mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><?= $lang->translation('Total Matrículados:') ?> por Grado</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th><?= $lang->translation('Grado') ?></th>
                                                            <th class="text-center"><?= $lang->translation('Mat.') ?></th>
                                                            <th class="text-center"><?= $lang->translation('Cantidad') ?></th>
                                                            <th><?= $lang->translation('Grado') ?></th>
                                                            <th class="text-center"><?= $lang->translation('Mat.') ?></th>
                                                            <th class="text-center"><?= $lang->translation('Cantidad') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><strong>KG</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes1; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es1" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es1; ?>" style="width: 70px;">
                                                            </td>
                                                            <td><strong>05</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes6; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es6" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es6; ?>" style="width: 70px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>01</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes2; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es2" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es2; ?>" style="width: 70px;">
                                                            </td>
                                                            <td><strong>06</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes7; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es7" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es7; ?>" style="width: 70px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>02</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes3; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es3" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es3; ?>" style="width: 70px;">
                                                            </td>
                                                            <td><strong>07</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes8; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es8" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es8; ?>" style="width: 70px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>03</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes4; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es4" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es4; ?>" style="width: 70px;">
                                                            </td>
                                                            <td><strong>08</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes9; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es9" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es9; ?>" style="width: 70px;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>04</strong></td>
                                                            <td class="text-center"><span class="badge badge-secondary"><?= $tes5; ?></span></td>
                                                            <td class="text-center">
                                                                <input name="es5" type="number" class="form-control form-control-sm" maxlength="2" value="<?= $r2->es5; ?>" style="width: 70px;">
                                                            </td>
                                                            <td colspan="3" class="text-center">
                                                                <strong class="text-primary"><?= $lang->translation('Total:') ?> <span class="badge badge-primary"><?= $test; ?></span></strong>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- T-Shirt Account Section -->
                                    <div class="card">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0">Configuración de Cuenta</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="ctacamisa" class="form-label font-weight-bold"><?= $lang->translation('Cuenta:') ?></label>
                                                <input name="ctacamisa" id="ctacamisa" type="text" class="form-control" maxlength="6" value="<?= $r2->cta_camisa; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column - Activation Toggles -->
                                <div class="col-lg-5">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">Activaciones del Sistema</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="esac" class="form-label font-weight-bold"><?= $lang->translation('Activar Estudios Supervisados:') ?></label>
                                                <select name="esac" id="esac" class="form-control">
                                                    <option value="No" <?= $r2->esac === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->esac === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="camisa" class="form-label font-weight-bold"><?= $lang->translation('Activar ordenes de camisetas:') ?></label>
                                                <select name="camisa" id="camisa" class="form-control">
                                                    <option value="No" <?= $r2->camisas === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->camisas === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="cena" class="form-label font-weight-bold"><?= $lang->translation('Activar cena de graduación 12:') ?></label>
                                                <select name="cena" id="cena" class="form-control">
                                                    <option value="No" <?= $r2->cena === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->cena === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="actinf" class="form-label font-weight-bold"><?= $lang->translation('Activación Inflable') ?></label>
                                                <select name="actinf" id="actinf" class="form-control">
                                                    <option value="No" <?= $r2->actinf === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->actinf === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="actmat" class="form-label font-weight-bold"><?= $lang->translation('Activación abono matrícula') ?></label>
                                                <select name="actmat" id="actmat" class="form-control">
                                                    <option value="No" <?= $r2->actmat === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->actmat === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="actmen" class="form-label font-weight-bold"><?= $lang->translation('Activación abono mensualidad') ?></label>
                                                <select name="actmen" id="actmen" class="form-control">
                                                    <option value="No" <?= $r2->actmen === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->actmen === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="actfotos" class="form-label font-weight-bold"><?= $lang->translation('Activación compras de fotos') ?></label>
                                                <select name="actfotos" id="actfotos" class="form-control">
                                                    <option value="No" <?= $r2->actfotos === 'No' ? 'selected' : '' ?>>No</option>
                                                    <option value="Si" <?= $r2->actfotos === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" name="grabar" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save mr-2"></i><?= $lang->translation('Grabar') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <?php if ($r2->actfotos === 'Si') : ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-camera mr-2"></i>
                                <?= $lang->translation('Tipos de fotos') ?>
                            </h5>
                            <button onclick="addNewPhotoType()" class="btn btn-light btn-sm">
                                <i class="fas fa-plus mr-1"></i>
                                <?= $lang->translation('Agregar') ?>
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="photosList">
                                <!-- Photo types will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

        <script type="text/javascript">
            getAllPhotosTypes();

            function deletePhotoType(id) {
                $.post('photos.php', {
                    deletePhotoType: true,
                    id
                }, function(data) {
                    getAllPhotosTypes();
                });
            }

            function updatePhotoType(id) {
                const name = $(`#name${id}`).val();
                const description = $(`#description${id}`).val();
                const price = $(`#price${id}`).val();
                $.post('photos.php', {
                    updatePhotoType: true,
                    id,
                    name,
                    description,
                    price
                }, function(data) {
                    getAllPhotosTypes();
                });
            }

            function addNewPhotoType() {
                $.post('photos.php', {
                    addNewPhotoType: true,
                }, function(data) {
                    getAllPhotosTypes();
                });
            }

            function PhotoType({
                id,
                name,
                description,
                price
            }) {
                return `
                <div class="card photo-type-card mb-3" data-id="${id}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name${id}" class="form-label font-weight-bold">
                                        <i class="fas fa-tag mr-1"></i>
                                        <?= $lang->translation('Nombre') ?>
                                    </label>
                                    <input type="text" id="name${id}" class="form-control" value="${name}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description${id}" class="form-label font-weight-bold">
                                        <i class="fas fa-align-left mr-1"></i>
                                        <?= $lang->translation('Descripción') ?>
                                    </label>
                                    <input type="text" id="description${id}" class="form-control" value="${description ?? ''}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price${id}" class="form-label font-weight-bold">
                                        <i class="fas fa-dollar-sign mr-1"></i>
                                        <?= $lang->translation('Precio') ?>
                                    </label>
                                    <input type="text" id="price${id}" class="form-control" value="${price}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">
                                    <i class="fas fa-cogs mr-1"></i>
                                    <?= $lang->translation('Acciones') ?>
                                </label>
                                <div class="btn-group d-flex">
                                    <button onclick="updatePhotoType(${id})" class="btn btn-success btn-sm">
                                        <i class="fas fa-save mr-1"></i>
                                        <?= $lang->translation('Guardar') ?>
                                    </button>
                                    <button onclick="deletePhotoType(${id})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash mr-1"></i>
                                        <?= $lang->translation('Borrar') ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            function getAllPhotosTypes() {

                $.get('photos.php?getAllPhotosTypes', function(data) {
                    const photos = JSON.parse(data);
                    const photosList = $('#photosList');
                    photosList.empty();
                    photos.forEach(function(photo) {
                        photosList.append(PhotoType(photo));
                    });
                });
            }
        </script>
    <?php endif; ?>
</body>

</html>