<?php
require_once __DIR__ . '/../../app.php';

use App\Models\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$lang = new Lang([
    ["Estado de cuenta", "Statement"],
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
    ['El mes para aplicar', 'The month to apply'],
    ['Número de Cuenta', 'Account number'],
    ['Estás seguro que desea eliminar el costo?', 'Are you sure you want to eliminate the cost?'],
    ['Mensaje', 'Message'],
    ['Enviar los estados a las cuentas de los padres?', "Send statements to parent's accounts?"],
    ['Enviar los estados por E-Mail?', 'Send the states by E-Mail?'],
    ['Mes', 'Month'],
    ['Con Estudiantes', 'With Students'],
    ['Sin Estudiantes', 'Without Students'],
    ['Mensajes para el Estado de Cuenta', 'Account Statement Messages'],
    ['Idioma del Estado de Cuenta', 'Account Statement Language'],
    ['Inglés', 'English'],
    ['Mensajes en español', 'Messages in Spanish'],
    ['Mensajes en inglés', 'Messages in English'],
    ['Está seguro que desea eliminar el mensaje?', 'Are you sure you want to delete the message?'],

]);

if (isset($_POST['bor'])) {
    Manager::table('codigos')->where('codigo', $_REQUEST['num1'])->delete();
}

if (isset($_POST['gra'])) {

    if (empty($_POST['num1']) and !empty($_POST['tema']) or empty($_POST['num1']) and !empty($_POST['tema2'])) {
        Manager::table('codigos')->insert([
            'tema' => $_POST['tema'],
            'tema2' => $_POST['tema2'],
            'idc' => '2',
        ]);
    } else {
        Manager::table("codigos")->where([
            ['codigo', $_POST['num1']],
            ['idc', '2']
        ])->update([
            'tema' => $_POST['tema'],
            'tema2' => $_POST['tema2'],
        ]);
    }
}

if (isset($_POST['bus'])) {
    $row1 = Manager::table('codigos')
        ->where([
            ['idc', '2'],
            ['codigo', $_POST['num']]
        ])

        ->orderBy('codigo')->first();
}

$students = Student::all();

$result = Manager::table('codigos')
    ->where('idc', '2')->orderBy('codigo')->get();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $title = $lang->translation('Estado de cuenta');
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>

    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Estado de cuenta') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <form name="algunNombre" action="pdf/estados_inf.php" method="post" target="_blank">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Selection Section -->
                        <div class="mb-4">
                            <h5 class="mb-3"><?= $lang->translation('Selección') ?></h5>

                            <div class="mb-3">
                                <label for="ctas" class="form-label"><?= $lang->translation('Número de Cuenta') ?></label>
                                <input name="ctas" type="text" class="form-control" id="ctas" placeholder="<?= $lang->translation('Número de Cuenta') ?>" />
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label"><?= $lang->translation('Estudiantes') ?></label>
                                <select name="nombre" class="form-control" id="nombre">
                                    <option value="1"><?= $lang->translation('Todos') ?></option>
                                    <?php foreach ($students as $student): ?>
                                        <option value="<?= $student->id ?>"><?= "$student->apellidos, $student->nombre, $student->id" ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="deuda" class="form-label"><?= $lang->translation('Tipo de Deuda') ?></label>
                                <select name="deuda" class="form-control" id="deuda">
                                    <option value="1"><?= $lang->translation('Todos') ?></option>
                                    <option value="2"><?= $lang->translation('Deudores') ?></option>
                                    <option value="3"><?= $lang->translation('Atrasados') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="conest" class="form-label"><?= $lang->translation('Estudiantes') ?></label>
                                <select name="conest" class="form-control" id="conest">
                                    <option value="1"><?= $lang->translation('Con Estudiantes') ?></option>
                                    <option value="2"><?= $lang->translation('Sin Estudiantes') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Message and Settings Section -->
                        <div class="mb-4">
                            <h5 class="mb-3"><?= $lang->translation('Configuración') ?></h5>

                            <div class="mb-3">
                                <label for="num2" class="form-label"><?= $lang->translation('Mensaje') ?></label>
                                <select name="num2" class="form-control" id="num2">
                                    <option><?= $lang->translation('Selección') ?></option>
                                    <?php foreach ($result as $row): ?>
                                        <option><?= $row->codigo ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="mes" class="form-label"><?= $lang->translation('El mes para aplicar') ?></label>
                                <select name="mes" class="form-control" id="mes">
                                    <option value="0"><?= $lang->translation('Mes') ?></option>
                                    <option value="01"><?= $lang->translation('Enero') ?></option>
                                    <option value="02"><?= $lang->translation('Febrero') ?></option>
                                    <option value="03"><?= $lang->translation('Marzo') ?></option>
                                    <option value="04"><?= $lang->translation('Abril') ?></option>
                                    <option value="05"><?= $lang->translation('Mayo') ?></option>
                                    <option value="06"><?= $lang->translation('Junio') ?></option>
                                    <option value="07"><?= $lang->translation('Julio') ?></option>
                                    <option value="08"><?= $lang->translation('Agosto') ?></option>
                                    <option value="09"><?= $lang->translation('Septiembre') ?></option>
                                    <option value="10"><?= $lang->translation('Octubre') ?></option>
                                    <option value="11"><?= $lang->translation('Noviembre') ?></option>
                                    <option value="12"><?= $lang->translation('Diciembre') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="idi" class="form-label"><?= $lang->translation('Idioma del Estado de Cuenta') ?></label>
                                <select name="idi" class="form-control" id="idi">
                                    <option><?= $lang->translation('Español') ?></option>
                                    <option><?= $lang->translation('Inglés') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="envia" class="form-label"><?= $lang->translation('Enviar los estados a las cuentas de los padres?') ?></label>
                                <select name="envia" class="form-control" id="envia">
                                    <option value="No">No</option>
                                    <option value="Si"><?= $lang->translation('Si') ?></option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="enviae" class="form-label"><?= $lang->translation('Enviar los estados por E-Mail?') ?></label>
                                <select name="enviae" class="form-control" id="enviae">
                                    <option value="No">No</option>
                                    <option value="Si"><?= $lang->translation('Si') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button class="btn btn-primary btn-lg px-4" name="Submit1" type="submit">
                            <?= $lang->translation('Procesar') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>



    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensajes para el Estado de Cuenta') ?></h1>
        <div class="container bg-white shadow-lg py-3 rounded mx-auto" style="width: 50rem;">
            <form method="post" action="Statement.php">
                <?php
                $result = Manager::table('codigos')
                    ->where('idc', '2')
                    ->orderBy('codigo')->get();
                ?>
                <input type="hidden" name="num1" value="<?= $row1->codigo ?? '' ?>" />

                <div class="row">
                    <div class="col-12">
                        <div class="mb-4">
                            <label for="tema" class="form-label h5"><?= $lang->translation('Mensajes en español') ?></label>
                            <textarea name="tema" class="form-control" id="tema" rows="4" placeholder="Escriba el mensaje en español..."><?= $row1->tema ?? ''; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="tema2" class="form-label h5"><?= $lang->translation('Mensaje en inglés') ?></label>
                            <textarea name="tema2" class="form-control" id="tema2" rows="4" placeholder="Write the message in English..."><?= $row1->tema2 ?? ''; ?></textarea>
                        </div>

                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <button name="gra" id="gra" type="submit" class="btn btn-primary w-100">
                                    <?= $lang->translation('Grabar') ?>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button name="bor" type="submit" class="btn btn-danger w-100"
                                    onclick="return confirmar('<?= $lang->translation('Está seguro que desea eliminar el mensaje?') ?>')">
                                    <?= $lang->translation('Borrar') ?>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label for="num" class="form-label"><?= $lang->translation('Selección') ?></label>
                                <select name="num" class="form-control" id="num">
                                    <?php foreach ($result as $row): ?>
                                        <option><?= $row->codigo ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button name="bus" type="submit" class="btn btn-primary w-100">
                                    <?= $lang->translation('Buscar') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script>
        function confirmar(mensaje) {
            return confirm(mensaje);
        }
    </script>
</body>

</html>