<?php
require_once '../../../app.php';

use App\Models\Admin;
use App\Models\EmailQueue;
use App\Models\Student;
use Classes\Lang;
use Classes\Route;
use Classes\Session;


Session::is_logged();
$lang = new Lang([
    ['Mensajes enviados', 'Sent messages'],
    ['Selección', 'Selection'],
    ['Descripción', 'Description'],
    ['español', 'spanish'],
    ['inglés', 'english'],
    ['Crédito', 'Credit'],
    ['Peso', 'Peso'],
    ['Maestro', 'Teacher'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Días', 'Days'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
    ['Estás seguro que quieres borrar el curso?', 'Are you sure you want to delete the course?'],
]);

$users = Admin::all();

if (isset($_REQUEST['user'])) {

    $emails = EmailQueue::where('user', $_REQUEST['user'])->latest('created_at')->get()
        ->groupBy(function ($email) {
            return $email->subject . '/' . $email->created_at->format('Y-m-d');
        });
}


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation('Mensajes enviados');
    Route::includeFile('/admin/includes/layouts/header.php');
    Route::selectPicker();
    ?>
    <style type="text/css">
        .style1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3 mb-5 px-0">
        <h1 class="text-center mb-3 mt-5"><?= $lang->translation('Mensajes enviados') ?></h1>
        <div class="container">
            <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 500px;">
                <form method="POST">
                    <div class="row">
                        <select class="form-control selectpicker w-100" name="user" data-live-search="true" required>
                            <option><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($users as $user) : ?>
                                <option <?= isset($_REQUEST['user']) && $_REQUEST['user'] == $user->usuario ? 'selected=""' : '' ?> value="<?= $user->usuario ?>"><?= "$user->usuario ($user->id)" ?></option>
                            <?php endforeach ?>
                        </select>
                        <input class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                    </div>

                    <?php if (isset($_REQUEST['user'])): ?>

                        <div class="row">
                            <select class="form-control selectpicker w-100" name="email" data-live-search="true" required>
                                <option><?= $lang->translation('Selección') ?></option>
                                <?php foreach ($emails as $label => $email) : ?>
                                    <option <?= isset($_REQUEST['email']) && $_REQUEST['email'] == $label ? 'selected=""' : '' ?> value="<?= $label ?>"><?= "$label" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="row">
                            <label for="ava"><?= $lang->translation("Opciones") ?></label>
                            <select class="form-control" name="env" id="env" required>
                                <option <?= isset($_REQUEST['env']) && $_REQUEST['env'] == '0' ? 'selected=""' : '' ?> value="0"><?= $lang->translation("Todos") ?></option>
                                <option <?= isset($_REQUEST['env']) && $_REQUEST['env'] == '1' ? 'selected=""' : '' ?> value="1"><?= $lang->translation("Enviados") ?></option>
                                <option <?= isset($_REQUEST['env']) && $_REQUEST['env'] == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("No enviados") ?></option>
                            </select>
                        </div>
                        <div class="row">
                            <input name="search2" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                        </div>
                    <?php endif ?>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($_POST['search2'])) : ?>
        <div class="container-lg mt-lg-3 mb-5 px-0">
            <div class="container">
                <div class="mx-auto bg-white shadow-lg py-5 px-3 rounded" style="max-width: 1700px;">
                    <p>&nbsp;</p>
                    <table align="center" style="width: 1695">
                        <tr>
                            <td class="style1" style="width: 80px"><strong>ID</strong></td>
                            <td class="style1" style="width: 350px"><strong>PADRES</strong></td>
                            <td class="style1" style="width: 280px"><strong>E-MAIL</strong></td>
                            <td class="style1" style="width: 370px"><strong>ESTUDIANTES</strong></td>
                            <td class="style1"><strong>ENVIADO</strong></td>
                            <td class="style1" style="width: 130px"><strong>FECHA</strong></td>
                            <td class="style1" style="width: 250px"><strong>RAZON</strong></td>
                        </tr>
                        <?php foreach ($emails[$_REQUEST['email']] as $email) :
                            $students = Student::whereIn('ss', $email->social_securities ?? [])->get();

                        ?>
                            <tr>
                                <td class="style1"><?= $email->id2 ?></td>
                                <td class="style1"><?= $email->family?->madre ?></td>
                                <td class="style1"><?= implode(', ', $email->to) ?></td>
                                <td class="style1">
                                    <?php foreach ($students as $student) : ?>
                                        <?= $student->full_name, '<br>' ?>
                                    <?php endforeach ?>
                                </td>

                                <td><?= $email->status === '1' ? 'Si' : 'No' ?></td>
                                <td><?= $email->sent_at?->translatedFormat('d F Y') ?></td>
                                <td><?= $email->failed_reason ? substr($email->failed_reason, 0, 10) : '' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </table>

                </div>
            </div>
        </div>
    <?php endif ?>

    <?php
    $jqMask = true;
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::selectPicker('js');
    ?>
</body>

</html>