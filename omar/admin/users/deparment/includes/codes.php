<?php
require_once '../../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();
if (isset($_POST['addCode'])) {
    $code = $_POST['code'];
    $description = $_POST['description'];

    DB::table('departamentos')->insert([
        'codigo' => $code,
        'descripcion' => $description
    ]);

} else if (isset($_POST['editCode'])) {
    $code = $_POST['code'];
    $code = $_POST['code'];
    $description = $_POST['description'];

    DB::table('departamentos')->where(['codigo', $_POST['editCode']])->update([
        'codigo' => $code,
        'descripcion' => $description
    ]);
} else if (isset($_POST['deleteCode'])) {
    $code = $_POST['code'];
    DB::table('departamentos')->where(['codigo', $code])->delete();
}