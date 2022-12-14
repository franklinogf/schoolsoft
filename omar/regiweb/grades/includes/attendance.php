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

    $attendanceArray = [];
    if ($attendanceOption === '3') {
        $class = $_POST['class'];
        $students = DB::table('padres')
            ->where([
                ['id', $teacher->id],
                ['curso', $class],
                ['year', $year],
            ])
            ->orderBy('apellidos, nombre')
            ->get();

        foreach ($students as $student) {
            if ($attendance = DB::table('asispp')
                ->where([
                    ['ss', $student->ss],
                    ['curso', $class],
                    ['year', $year],
                    ['fecha', $date],
                ])
                ->orderBy('apellidos, nombre')
                ->first()
            ) {
                $attendanceArray[$student->ss] = $attendance->codigo;
            } else {
                $attendanceArray[$student->ss] = '';
            }
        }
    } else {
        $students = DB::table('asisdia')
            ->where([
                ['baja', ''],
                ['grado', $attendanceOption === '1' && __SCHOOL_ACRONYM !== 'cbtm' ? $teacher->grado : $_POST['grade']],
                ['year', $year],
                ['mes', $month],
            ])
            ->orderBy('apellidos, nombre')
            ->get();
    }
    // create array with the students
    if ($students) {

        $array = [
            'response' => true,
            'data' => $students,

        ];
        if (count($attendanceArray) > 0) {
            $array = array_merge(
                $array,
                ['attendance' => $attendanceArray]
            );
        }
    } else {
        $array = ['response' => false];
    }
    echo Util::toJson($array);
} else if (isset($_POST['changeAttendance'])) {
    $attendanceOption = $_POST['changeAttendance'];
    $value = $_POST['value'];
    $ss = $_POST['ss'];
    // update the attendance value
    if ($attendanceOption === '3') {
        $class = $_POST['class'];
        if (DB::table('asispp')
                ->where([
                    ['ss', $ss],
                    ['curso', $class],
                    ['year', $year],
                    ['fecha', $date],
                ])
                ->orderBy('apellidos, nombre')
                ->first()
            ) {
                DB::table('asispp')
                ->where([
                    ['ss', $ss],
                    ['curso', $class],
                    ['fecha', $date],
                ])->update(['codigo'=>$value]);
                echo "existe";
                
            } else {
                echo "no existe";
                $student = new Student($ss);
                DB::table('asispp')
                ->insert([
                    'ss'=> $ss,
                    'curso'=> $class,
                    'fecha'=> $date,
                    'year' => $year,
                    'codigo'=>$value,
                    'nombre'=>$student->nombre,
                    'apellidos'=>$student->apellidos,
                    'grado'=>$student->grado,
                ]);
            }
        
    }else{
        DB::table('asisdia')->where([
            ['ss', $ss],
            ['year', $year],
            ['grado', $attendanceOption === '1' ? $teacher->grado : $_POST['grade']],
            ['mes', $month],
        ])->update(["d$day" => $value]);
    }
}