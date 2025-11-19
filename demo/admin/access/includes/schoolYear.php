<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Server::is_post();

$lang = new Lang([
    ['Año cambiado', 'Year edited']
]);


$adminYear = $_POST['adminYear'];
$teacherYear = $_POST['teacherYear'];

if (isset($_POST['save'])) {
    $updates = [
        'year' => $teacherYear
    ];
    if (isset($adminYear)) {
        $updates['year2'] = $adminYear;
    }
    DB::table('colegio')->where('usuario', Session::id())->update($updates);
    Session::set('schoolYear', $lang->translation("Año cambiado"));
    Route::redirect('/access/schoolYear.php');
}
