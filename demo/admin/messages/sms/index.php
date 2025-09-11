<?php
require_once '../../../app.php';

use App\Models\Student;
use App\Models\Teacher;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

$lang = new Lang([
    ['Enviar mensaje de texto a', 'Send sms to'],
    ['estudiantes', 'students'],
    ['profesores', 'teachers'],
    ['administradores', 'admins'],
    ['Redactar mensaje de texto', 'Write sms']
]);
$key = array_keys($_GET)[0];
$dataList = $_GET[$key];

$titleHeader = $lang->translation("Enviar mensaje de texto a");
if (count($dataList) > 1) {

    $titleHeader .= ' ' . count($dataList) . ' ' . $lang->translation($key === 'teachers' ? 'profesores' : ($key === 'students' ? 'estudiantes' : 'administradores'));
} else {
    if ($key === 'teachers') {
        $teacher =  Teacher::find($dataList[0]);
        $titleHeader .= " $teacher->nombre $teacher->apellidos";
    } else if ($key === 'students') {
        $student =  Student::find($dataList[0]);
        $titleHeader .= " $student->nombre $student->apellidos";
    } else {

        $titleHeader .= " $dataList[0]";
    }
}
$savedMessages = Manager::table('T_sms_guardados')->where('enviado_por', Session::id())->orderBy('id', 'desc')->get();
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php
    $title = $lang->translation("Redactar mensaje de texto");
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
                    <textarea class="form-control" name="message" id="message" rows="3" placeholder="Mensaje"></textarea>
                </div>
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input bg-success checkAll" type="checkbox" id="saveMessage">
                    <label class="custom-control-label" for="saveMessage">Guardar este mensaje</label>
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
                        <th colspan="2">Titulo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($savedMessages as $message): ?>
                        <tr class="cursor-pointer" data-id="<?= $message->id ?>">
                            <td class="selectMessage"><?= $message->titulo ?></td>
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