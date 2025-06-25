<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

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
$school = new School(Session::id());
$courses = DB::table('colegio')->orderBy('usuario')->get();

$teachers = new Teacher;

if (isset($_REQUEST['search']) or isset($_REQUEST['search2'])) {
    $thisUser = DB::table('colegio')->where('id', $_POST['course'])->first();
    $emails = DB::table('email_queue')->select("DISTINCT subject ")->where([
        ['user', $thisUser->usuario],
        ['year', $school->info('year2')]
    ])->get();
}
$courses = DB::table('colegio')->orderBy('usuario')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />

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
                        <select class="form-control selectpicker w-100" name="course" data-live-search="true" required>
                            <option><?= $lang->translation('Selección') ?></option>
                            <?php foreach ($courses as $course) : ?>
                                <option <?= isset($_REQUEST['course']) && $_REQUEST['course'] == $course->id ? 'selected=""' : '' ?> value="<?= $course->id ?>"><?= "$course->usuario ($course->id)" ?></option>
                            <?php endforeach ?>
                        </select>
                        <input name="search" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                    </div>

                    <?php if (isset($_REQUEST['search']) or isset($_REQUEST['search2'])) { ?>

                        <div class="row">
                            <select class="form-control selectpicker w-100" name="course2" data-live-search="true" required>
                                <option><?= $lang->translation('Selección') ?></option>
                                <?php foreach ($emails as $email) : ?>
                                    <option <?= isset($_REQUEST['course2']) && $_REQUEST['course2'] == $email->subject ? 'selected=""' : '' ?> value="<?= $email->subject ?>"><?= "$email->subject ()" ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="row">
                            <label for="ava"><?= $lang->translation("Opciones") ?></label>
                            <select class="form-control" name="env" id="env" required>
                                <option <?= isset($_REQUEST['course2']) && $_REQUEST['env'] == '0' ? 'selected=""' : '' ?> value="0"><?= $lang->translation("Todos") ?></option>
                                <option <?= isset($_REQUEST['course2']) && $_REQUEST['env'] == '1' ? 'selected=""' : '' ?> value="1"><?= $lang->translation("Los recividos") ?></option>
                                <option <?= isset($_REQUEST['course2']) && $_REQUEST['env'] == '2' ? 'selected=""' : '' ?> value="2"><?= $lang->translation("No recividos") ?></option>
                            </select>
                        </div>
                        <div class="row">
                            <input name="search2" class="btn btn-primary mx-auto d-block mt-1" type="submit" value="<?= $lang->translation("Buscar") ?>">
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($_POST['search2'])) : ?>

        <?php
        if ($_POST['env'] == '0') {
            $emails = DB::table('email_queue')->where([
                ['subject', $_POST['course2']],
                ['user', $thisUser->usuario],
                ['year', $school->info('year2')]
            ])->get();
        } else {
            $emails = DB::table('email_queue')->where([
                ['subject', $_POST['course2']],
                ['user', $thisUser->usuario],
                ['status', $_POST['env']],
                ['year', $school->info('year2')]
            ])->get();
        }
        ?>
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
                        <?php foreach ($emails as $email) : ?>
                            <?php $em = json_decode($email->to);
                            $em1 =  $em[0] ?? '';
                            $em2 =  $em[1] ?? '';
                            if (!empty($email->social_securities)) {
                                $est = DB::table('year')->where([
                                    ['ss', $email->social_securities],
                                    ['year', $school->info('year2')]
                                ])->first();
                            } else {
                                $est = DB::table('year')->where([
                                    ['id', $email->id2],
                                    ['year', $school->info('year2')]
                                ])->first();
                            }
                            $pad = DB::table('madre')->where([
                                ['id', $email->id2]
                            ])->first();
                            $pa1 = $pad->madre ?? '';
                            $pa2 = $pad->padre ?? '';
                            $es1 = $est->apellidos ?? '';
                            $es2 = $est->nombre ?? '';
                            ?>
                            <tr>
                                <td class="style1"><?= $email->id2 ?></td>
                                <td style="width: 350px"><?= $pa1 . '<br>' . $pa2 ?></td>
                                <td style="width: 280px"><?= $em1 . '<br>' . $em2 ?></td>
                                <td style="width: 370px"><?= $es1 . ' ' . $es2 ?></td>
                                <td class="style1"><?= $email->status == '1' ? 'Si' : 'No' ?></td>
                                <td style="width: 124px"><?= $email->created_at ?></td>
                                <td><?= $email->failed_reason ?></td>
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