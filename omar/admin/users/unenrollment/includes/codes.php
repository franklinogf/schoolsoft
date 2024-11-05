<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();
if (isset($_POST['addCode'])) {
    $id = $_POST['id'];
    $code = $_POST['code'];
    $description = $_POST['description'];

    DB::table('codigo_bajas')->insert([
        'id' => $id,
        'codigo' => $code,
        'descripcion' => $description
    ]);
    
}else if(isset($_POST['editCode'])) {
    $code = $_POST['code'];
    $id = $_POST['id'];
    $code = $_POST['code'];
    $description = $_POST['description'];

    DB::table('codigo_bajas')->where(['codigo', $_POST['editCode']])->update([
        'id' => $id,
        'codigo' => $code,
        'descripcion' => $description
    ]);
}
else if(isset($_POST['deleteCode'])) {
    $code = $_POST['code'];
    DB::table('codigo_bajas')->where(['codigo', $code])->delete();
}
?>