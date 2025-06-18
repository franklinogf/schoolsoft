<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\Lang;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Util;

Server::is_post();

$lang = new Lang([
    ['Fechas actualizadas', 'Dates updated']
]);

if (isset($_POST['save'])) {
    $updates = [
        'rema_msg' => htmlspecialchars($_POST['message']),
    ];

    DB::table('colegio')->where('usuario', 'administrador')->update($updates);
} else if (isset($_POST['search'])) {

    $school = new School();
    echo Util::toJson([
        'message' => $school->info('rema_msg'),
    ]);
} else if (isset($_POST['check'])) {
    DB::table('year')->where('mt', $_POST['check'])->update(['re_ma' => $_POST['value']]);
}
