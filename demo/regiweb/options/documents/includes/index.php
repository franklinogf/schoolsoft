<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

Server::is_post();
Session::is_logged();
$teacher = new Teacher(Session::id());

if(isset($_POST['saveHistory'])){
    $documentId = $_POST['saveHistory'];
    $documentTitle = $_POST['documentTitle'];
    DB::table('T_historial_descargas')->insert([
        'usuario'=> $teacher->usuario,
        'id' => $teacher->id,
        'fecha' => Util::date(),
        'hora' => Util::time(),
        'titulo' => $documentTitle,
        'ip' => Util::getIp(),
        'year' => $teacher->info('year')
    ]);
}
