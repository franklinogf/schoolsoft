<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Server::is_post();

$lang = new Lang([
    ['Fechas actualizadas', 'Dates updated']
]);

if (isset($_POST['save'])) {
    $updates = [
        'asis1' => $_POST['quater1_start'],
        'asis2' => $_POST['quater1_end'],
        'asis3' => $_POST['quater2_start'],
        'asis4' => $_POST['quater2_end'],
        'asis5' => $_POST['quater3_start'],
        'asis6' => $_POST['quater3_end'],
        'asis7' => $_POST['quater4_start'],
        'asis8' => $_POST['quater4_end'],
        'asist' => $_POST['asist'],
        'asis' => $_POST['asis'],
    ];

    DB::table('colegio')->where('usuario', 'administrador')->update($updates);
    Session::set('changeDates', $lang->translation("Fechas actualizadas"));

    Route::redirect('/access/Attendance.php');
} else if (isset($_POST['check'])) {

    DB::table('profesor')->where('id', $_POST['check'])->update(['fechas' => (bool)$_POST['value']]);
} else if (isset($_POST['changeTrimester'])) {

    DB::table('profesor')->where('id', $_POST['changeTrimester'])->update(['tri' => $_POST['value']]);
}
