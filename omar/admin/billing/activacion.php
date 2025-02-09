<?php
require_once '../../app.php';

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

$school = new School(Session::id());
$year = $school->info('year2');

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



$r2 = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

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
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Untitled 1</title>
    <script language="JavaScript">
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
        document.oncontextmenu = function() {
            return false
        }
    </script>
    <style type="text/css">
        .style1 {
            font-size: large;
            text-align: center;
        }

        .style3 {
            background-color: #FFFFCC;
        }

        .style4 {
            text-align: center;
        }

        .style5 {
            text-align: center;
            background-color: #CCCCCC;
        }

        .style6 {
            background-color: #FFFFCC;
            text-align: center;
        }

        .style7 {
            background-color: #FFFFCC;
            text-align: right;
        }

        .photoType {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px;
            display: flex;
            gap: 10px;
        }

        .photoType div {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .photoType div:last-child {
            align-self: center;
        }

        .photoTypeHeader {
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
        }
    </style>
    <?php
    $title = $lang->translation('Pantalla para activación y desactivación de pantallas');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>
<?php
$title = $lang->translation('Pantalla para activación y desactivación de pantallas');
Route::includeFile('/admin/includes/layouts/header.php');
?>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Pantalla para activación y desactivación de pantallas') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">

            <p>&nbsp;</p>
            <form method="post">

                <table align="center" cellpadding="2" cellspacing="0" style="width: 70%">
                    <tr>
                        <td><strong><?= $lang->translation('Grado') ?></strong></td>
                        <td><strong><?= $lang->translation('Mat.') ?></strong></td>
                        <td><strong><?= $lang->translation('Cantidad') ?></strong></td>
                        <td><strong><?= $lang->translation('Grado') ?></strong></td>
                        <td><strong><?= $lang->translation('Mat.') ?></strong></td>
                        <td><strong><?= $lang->translation('Cantidad') ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>KG</strong></td>
                        <td><?= $tes1; ?></td>
                        <td>
                            <input name="es1" type="text" maxlength="2" size="2" value="<?= $r2->es1; ?>" />
                        </td>
                        <td><strong>05</strong></td>
                        <td><?= $tes6; ?></td>
                        <td>
                            <input name="es6" type="text" maxlength="2" size="2" value="<?= $r2->es6; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><strong>01</strong></td>
                        <td><?= $tes2; ?></td>
                        <td>
                            <input name="es2" type="text" maxlength="2" size="2" value="<?= $r2->es2; ?>" />
                        </td>
                        <td><strong>06</strong></td>
                        <td><?= $tes7; ?></td>
                        <td>
                            <input name="es7" type="text" maxlength="2" size="2" value="<?= $r2->es7; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 31px"><strong>02</strong></td>
                        <td style="height: 31px"><?= $tes3; ?></td>
                        <td style="height: 31px">
                            <input name="es3" type="text" maxlength="2" size="2" value="<?= $r2->es3; ?>" />
                        </td>
                        <td style="height: 31px"><strong>07</strong></td>
                        <td style="height: 31px"><?= $tes8; ?></td>
                        <td style="height: 31px">
                            <input name="es8" type="text" maxlength="2" size="2" value="<?= $r2->es8; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><strong>03</strong></td>
                        <td><?= $tes4; ?></td>
                        <td>
                            <input name="es4" type="text" maxlength="2" size="2" value="<?= $r2->es4; ?>" />
                        </td>
                        <td><strong>08</strong></td>
                        <td><?= $tes9; ?></td>
                        <td>
                            <input name="es9" type="text" maxlength="2" size="2" value="<?= $r2->es9; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td><strong>04</strong></td>
                        <td><?= $tes5; ?></td>
                        <td>
                            <input name="es5" type="text" maxlength="2" size="2" value="<?= $r2->es5; ?>" />
                        </td>
                        <td colspan="2"><strong><?= $lang->translation('Activar Estudios Supervisados:') ?></strong></td>
                        <td>
                            <select name="esac">
                                <option value="No" <?= $r2->esac === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->esac === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <th colspan="2"><?= $lang->translation('Total Matrículados:') ?></th>
                        <td><b><? echo $test; ?></b></td>
                    </tr>
                    <tr>
                        <td><strong><?= $lang->translation('Cuenta:') ?></strong></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activar ordenes de camisetas:') ?></strong></td>
                        <td>
                            <select name="camisa">
                                <option value="No" <?= $r2->camisas === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->camisas === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>
                                <input name="ctacamisa" type="text" maxlength="6" size="5" value="<?= $r2->cta_camisa; ?>" /></strong></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activar cena de graduación 12:') ?></strong></td>
                        <td>
                            <select name="cena">
                                <option value="No" <?= $r2->cena === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->cena === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activación Inflable') ?></strong></td>
                        <td>
                            <select name="actinf">
                                <option value="No" <?= $r2->actinf === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->actinf === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activación abono matrícula') ?></strong></td>
                        <td>
                            <select name="actmat">
                                <option value="No" <?= $r2->actmat === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->actmat === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activación abono mensualidad') ?></strong></td>
                        <td>
                            <select name="actmen">
                                <option value="No" <?= $r2->actmen === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->actmen === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2"><strong><?= $lang->translation('Activación compras de fotos') ?></strong></td>
                        <td>
                            <select name="actfotos">
                                <option value="No" <?= $r2->actfotos === 'No' ? 'selected' : '' ?>>No</option>
                                <option value="Si" <?= $r2->actfotos === 'Si' ? 'selected' : '' ?>><?= $lang->translation('Si') ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p>&nbsp;</p>
                <div class="style4">
                    <input class="btn btn-primary" name="grabar" style="width: 90px" type="submit" value="<?= $lang->translation('Grabar') ?>" />
                </div>
            </form>
        </div>
    </div>

    <?php if ($r2->actfotos === 'Si') : ?>
        <div>
            <div class="photoTypeHeader">
                <h2>Tipos de fotos</h2>
                <button onclick="addNewPhotoType()" class="btn btn-primary"><?= $lang->translation('Agregar') ?></button>
            </div>
            <div id="photosList">

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
				<div class="photoType" data-id="${id}">
					<div>
						<label for="name${id}"><?= $lang->translation('Nombre') ?></label>
						<input type="text" id="name${id}" value="${name}">
					</div>
					<div>
						<label for="description${id}"><?= $lang->translation('Descripción') ?></label>
						<input type="text" id="description${id}" value="${description ?? ''}">
					</div>
					<div>
						<label for="price${id}"><?= $lang->translation('Precio') ?></label>
						<input type="text" id="price${id}" value="${price}">
					</div>
					<div>
						<button onclick="updatePhotoType(${id})" class="btn btn-primary"><?= $lang->translation('Guardar') ?></button>
						<button onclick="deletePhotoType(${id})" class="btn btn-danger"><?= $lang->translation('Borrar') ?></button>
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
<br />
<br />
<br />
<br />
<br />

</html>