<?php
require_once '../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Server::is_post();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');

$date = $_POST['date'];
$newDate = strtotime($date);
$month = date('m', $newDate);
$day = date('j', $newDate); //day without "0"

if (isset($_POST['getStudents'])) {
    $attendanceOption = $_POST['getStudents'];
    $isCourse = $attendanceOption === '3';
    $attendanceArray = [];
    $studentsWhere = [

        [$isCourse ? 'curso' : 'grado', $isCourse ? $_POST['class'] : $_POST['grade']],
        ['year', $year],
    ];
    if ($isCourse) {
        $studentsWhere[] = ['id', $teacher->id];
    }

    $students = DB::table($isCourse ? 'padres' : 'year')->select('DISTINCT ss, nombre, apellidos')
        ->where($studentsWhere)
        ->orderBy('apellidos, nombre')
        ->get();

    foreach ($students as $student) {

        $attendance = DB::table('asispp')
            ->where([
                ['ss', $student->ss],
                [$isCourse ? 'curso' : 'grado', $isCourse ? $_POST['class'] : $_POST['grade']],
                ['year', $year],
                ['fecha', $date],
            ])
            ->orderBy('apellidos, nombre')
            ->first();

        $attendanceArray[$student->ss] = $attendance ? $attendance->codigo : '';

    }

    // create array with the students
    if ($students) {
        $array = [
            'data' => $students,
        ];
        if (count($attendanceArray) > 0) {
            $array = array_merge(
                $array,
                ['attendance' => $attendanceArray]
            );
        }
    } else {
        $array = ['error' => true];
    }
    echo Util::toJson($array);
} else if (isset($_POST['changeAttendance'])) {
    $attendanceOption = $_POST['changeAttendance'];
    $isCourse = $attendanceOption === '3';
    $value = $_POST['value'];
    $ss = $_POST['ss'];
    $student = new Student($ss);
    // update the attendance value
    if (
        DB::table('asispp')
            ->where([
                ['ss', $ss],
                [$isCourse ? 'curso' : 'grado', $isCourse ? $_POST['class'] : $student->grado],
                ['year', $year],
                ['fecha', $date],
            ])
            ->first()
    ) {
        DB::table('asispp')
            ->where([
                ['ss', $ss],
                [$isCourse ? 'curso' : 'grado', $isCourse ? $_POST['class'] : $student->grado],
                ['year', $year],
                ['fecha', $date],
            ])->update(['codigo' => $value]);

    } else {
        $insert = [
            'ss' => $ss,
            'fecha' => $date,
            'year' => $year,
            'codigo' => $value,
            'nombre' => $student->nombre,
            'apellidos' => $student->apellidos,
            'grado' => $student->grado,
        ];
        if ($isCourse) {
            $insert['curso'] = $_POST['class'];
        }
        DB::table('asispp')
            ->insert($insert);
    }


}