<?php
require_once __DIR__ . '/../../../app.php';


use App\Models\Teacher;
use App\Models\Student;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Route;
use Classes\Session;



Session::is_logged();

$lang = new Lang([
    ['Enviar correo a', 'Send email to'],
    ['estudiantes', 'students'],
    ['profesores', 'teachers'],
    ['administradores', 'admins'],
    ['Redactar correo', 'Write email']
]);
$key = array_keys($_GET)[0];
$dataList = $_GET[$key];


$titleHeader = $lang->translation("Enviar correo a");
if (count($dataList) > 1) {

    $titleHeader .= ' ' . count($dataList) . ' ' . $lang->translation($key === 'teachers' ? 'profesores' : ($key === 'students' ? 'estudiantes' : 'administradores'));
} else {
    if ($key === 'teachers') {
        $teacher = Teacher::find($dataList[0]);
        $titleHeader .= " $teacher->nombre $teacher->apellidos";
    } else if ($key === 'students') {
        $student = Student::query()->byMT($dataList[0])->first();
        $titleHeader .= " $student->nombre $student->apellidos";
    } else {

        $titleHeader .= " $dataList[0]";
    }
}
$savedMessages = DB::table('T_correos_guardados')->where('colegio', Session::id())->whereRaw("AND id_profesor IS NULL")->orderBy('id', 'desc')->get();


?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Redactar correo");
    Route::includeFile('/admin/includes/layouts/header.php');
    ?>
    <style>
        .selectMessage {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php
    Route::includeFile('/admin/includes/layouts/menu.php');
    ?>
    <div class="container-lg mt-lg-3">
        <h1 class="text-center mb-3 mt-5"><?= $titleHeader ?></h1>

        <div class="container bg-white shadow-lg py-3 my-3 rounded">
            <form id="form" method="POST" action="<?= Route::url('/admin/messages/email/includes/send.php') ?>" enctype="multipart/form-data">
                <input type="hidden" name="key" value="<?= $key ?>">
                <?php foreach ($dataList as $data): ?>
                    <input type="hidden" name="values[]" value="<?= $data ?>">
                <?php endforeach ?>
                <div class="form-group">
                    <input type="text" class="form-control" name="title" id="title" placeholder="Titulo" required>
                </div>
                <div class="form-group">

                    <input type="text" class="form-control" name="subject" id="subject" placeholder="Asunto" required>
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="message" id="message" rows="3" placeholder="Mensaje"></textarea>
                </div>
                <div class="container my-4">
                    <button type="button" class="btn btn-primary mx-auto d-block addFile"><?= $lang->translation("Agregar archivo") ?></button>
                </div>
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input bg-success checkAll" type="checkbox" id="saveMessage">
                    <label class="custom-control-label" for="saveMessage">Guardar este mensaje</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input bg-success checkAll" type="checkbox" id="smsNotification">
                    <label class="custom-control-label" for="smsNotification">Notificar por SMS</label>
                </div>
                <div class="container my-4 text-center">
                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                    <a href="./<?= $key ?>.php" class="btn btn-secondary">Ir atrás</a>
                </div>
            </form>
        </div>


        <div>
            <h2 class="text-center">Mensajes Guardados</h2>
            <table id="savedMessages" class="table table-striped table-hover bg-white">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th colspan="2">Asunto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($savedMessages as $message): ?>
                        <tr class="cursor-pointer" data-id="<?= $message->id ?>">
                            <td class="selectMessage"><?= $message->titulo ?></td>
                            <td class="selectMessage"><?= $message->asunto ?></td>
                            <td class="text-center"><button class="btn btn-sm btn-outline-danger delete">Borrar</button></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

    </div>




    <?php
    Route::includeFile('/includes/layouts/scripts.php', true);
    Route::sweetAlert();
    ?>

</body>

</html>