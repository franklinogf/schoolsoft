<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();
Server::is_post();
if (isset($_POST['addCode'])) {
    $value = $_POST['value'] !== '' ? $_POST['value'] : null;
    $code = $_POST['code'];
    $description = $_POST['description'];

    $id = DB::table('memos_codes')->insert([
        'valor' => $value,
        'codigo' => $code,
        'nombre' => $description
    ],true);

   echo Util::toJson(['id' => $id]);
    
}else if(isset($_POST['editCode'])) {
    $value = $_POST['value'] !== '' ? $_POST['value'] : null;
    $code = $_POST['code'];
    $description = $_POST['description'];

    DB::table('memos_codes')->where(['id', $_POST['editCode']])->update([
        'valor' => $value,
        'codigo' => $code,
        'nombre' => $description
    ]);
}
else if(isset($_POST['deleteCode'])) {
    $id = $_POST['deleteCode'];
    DB::table('memos_codes')->where(['id', $id])->delete();
}
?>