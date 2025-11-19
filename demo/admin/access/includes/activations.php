<?php
require_once __DIR__ . '/../../../app.php';

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
        'men_inac' => htmlspecialchars($_POST['message']),
        'inactivo' => $_POST['lock'],
        'dia_vence' => $_POST['expireDay'],
        'bloqueoauto' => $_POST['automaticLock'],
        'des1' => $_POST['code1'],
        'des2' => $_POST['code2'],
        'des3' => $_POST['code3'],
    ];

    DB::table('colegio')->where('usuario', 'administrador')->update($updates);
} else if (isset($_POST['search'])) {

    $school = new School();
    echo Util::toJson([
        'message' => $school->info('men_inac'),
        'lock' => $school->info('inactivo'),
        'expireDay' => $school->info('dia_vence'),
        'automaticLock' => $school->info('bloqueoauto'),
        'code1' => $school->info('des1'),
        'code2' => $school->info('des2'),
        'code3' => $school->info('des3'),
    ]);
} else if (isset($_POST['check'])) {

    DB::table('madre')->where('id', $_POST['check'])->update(['activo' => $_POST['value']]);
}
