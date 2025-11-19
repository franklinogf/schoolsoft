<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\DataBase\DB;
use Classes\Server;
use Classes\Session;

Session::is_logged();
Server::is_post();
if (isset($_POST['addCode'])) {
    $code = $_POST['code'];
    $description = $_POST['description'];
    $description2 = $_POST['description2'];

    DB::table('codigos')->insert([
        'codigo' => $code,
        't1e' => $description,
        't2e' => $description2,
    ]);

} else if (isset($_POST['editCode'])) {
    $code = $_POST['code'];
    $description2 = $_POST['description2'];
    $description = $_POST['description'];

    DB::table('codigos')->where(['codigo', $_POST['editCode']])->update([
        'codigo' => $code,
        't1e' => $description,
        't2e' => $description2,
    ]);
} else if (isset($_POST['deleteCode'])) {
    $code = $_POST['code'];
    DB::table('codigos')->where(['codigo', $code])->delete();
}
