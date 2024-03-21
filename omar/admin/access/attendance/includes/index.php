<?php
require_once '../../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\School;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Server::is_post();
$school = new School();
$year = $school->info('year2');

$date = $_POST['date'];
$newDate = strtotime($date);
$month = date('m', $newDate);
$day = date('j', $newDate); //day without "0"

if (isset($_POST['getStudents'])) {

    $students = DB::table('asisdia')
        ->where([
            ['baja', ''],
            ['grado', $_POST['grade']],
            ['year', $year],
            ['mes', $month],
        ])
        ->orderBy('apellidos, nombre')
        ->get();

    // create array with the students
    if ($students) {
        $array = [
            'response' => true,
            'data' => $students,
        ];
    } else {
        $array = ['response' => false];
    }

    echo Util::toJson($array);
} else if (isset($_POST['changeAttendance'])) {
    $value = $_POST['value'];
    $ss = $_POST['ss'];
    // update the attendance value    
    DB::table('asisdia')->where([
        ['ss', $ss],
        ['year', $year],
        ['grado', $_POST['grade']],
        ['mes', $month],
    ])->update(["d$day" => $value]);
}
