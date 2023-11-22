<?php
require_once '../../../app.php';

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
        'ft1' => $_POST['quater1_start'],
        'ft2' => $_POST['quater1_end'],
        'ft3' => $_POST['quater2_start'],
        'ft4' => $_POST['quater2_end'],
        'ft5' => $_POST['quater3_start'],
        'ft6' => $_POST['quater3_end'],
        'ft7' => $_POST['quater4_start'],
        'ft8' => $_POST['quater4_end'],
        'fechav1' => $_POST['summer_start'],
        'fechav2' => $_POST['summer_end'],
    ];

    DB::table('colegio')->where('usuario', 'administrador')->update($updates);
    Session::set('changeDates', $lang->translation("Fechas actualizadas"));

    Route::redirect('/access/changeDates.php');
} else if (isset($_POST['check'])) {

    DB::table('profesor')->where('id', $_POST['check'])->update(['fechas' => (bool)$_POST['value']]);
} else if (isset($_POST['changeTrimester'])) {

    DB::table('profesor')->where('id', $_POST['changeTrimester'])->update(['tri' => $_POST['value']]);
}
