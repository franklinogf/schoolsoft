<?php
require_once '../../../app.php';

use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

// Server::is_post();
$teacher = new Teacher(Session::id());
// $_trimester = $_POST['trimester'];
// $_report = $_POST['tra'];
// $_info = $_POST['info']; //receive $_info from view
// $_students = $_POST['students'];
// $_subjectCode = $_POST['subjectCode'];

if (isset($_POST['changeValue'])) {
    $type = $_POST['changeValue'];
    $value = $_POST['value'];
    $id = $_POST['id'];
    DB::table('valores')
        ->where('id', $id)
        ->update([$type => $value]);
}
