<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Util;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Server::is_post();

$lang = new Lang([
    ['Año cambiado', 'Year edited']
]);


$adminYear = $_POST['adminYear'];
$teacherYear = $_POST['teacherYear'];

if (isset($_POST['save'])) {
    $updates = [
        'year2' => $teacherYear
    ];
    if (isset($adminYear)) {
        $updates['year'] = $adminYear;
    }
    DB::table('colegio')->where('usuario', Session::id())->update($updates);
    Session::set('schoolYear', $lang->translation("Año cambiado"));
    Route::redirect('/access/schoolYear.php');
}
