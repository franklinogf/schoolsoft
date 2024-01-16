<?php
require_once '../../../app.php';

use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Server::is_post();
Session::is_logged(false);

$school = new School();
$year = $school->year();

if (isset($_POST['addCourse'])) {
    $studentSS = $_POST['studentSS'];
    $courseID = $_POST['courseID'];

    $student = new Student($studentSS);

    $course = DB::table('cursos')->where([
        ['year', $year],
        ['id', $courseID]
    ])->first();
    $teacher = new Teacher($course->id);


    if (
        !DB::table('padres')->where([
            ['ss', $student->ss],
            ['curso', $course->curso],
            ['year', $year]
        ])->first()
    ) {

        DB::table("padres")->insert([
            'id' => $teacher->id,
            'nombre' => $student->nombre,
            'apellidos' => $student->apellidos,
            'descripcion' => $course->desc1,
            'grado' => $student->grado,
            'curso' => $course->curso,
            'credito' => $course->credito,
            'ss' => $student->ss,
            'year' => $year,
            'id2' => $student->id,
            'profesor' => "$teacher->nombre $teacher->apellidos",
            'email' => $course->peso,
            'desc2' => $course->desc2,
            'ava' => $course->ava,
            'valor' => $course->valor,
        ]);
    }
    for ($a = 2; $a <= 6; $a++) {
        if (
            !DB::table("padres$a")->where([
                ['ss', $student->ss],
                ['curso', $course->curso],
                ['year', $year]
            ])->first()
        ) {
            DB::table("padres$a")->insert([
                'id' => $teacher->id,
                'nombre' => $student->nombre,
                'apellidos' => $student->apellidos,
                'descripcion' => $course->desc1,
                'grado' => $student->grado,
                'curso' => $course->curso,
                'profesor' => "$teacher->nombre $teacher->apellidos",
                'ss' => $student->ss,
                'year' => $year,
            ]);
        }
    }

} else if ($_POST['removeCourse']) {
    $studentSS = $_POST['studentSS'];
    $courseID = $_POST['courseID'];
    $course = DB::table('cursos')->where([
        ['year', $year],
        ['id', $courseID]
    ])->first();

    DB::table("padres")->where(
        [
            ['ss', $studentSS],
            ['curso', $course->curso],
            ['year', $year]
        ]
    )->delete();
    for ($a = 2; $a <= 6; $a++) {

        DB::table("padres$a")->where(
            [
                ['ss', $studentSS],
                ['curso', $course->curso],
                ['year', $year]
            ]
        )->delete();

    }

} else if ($_POST['removeAllCourses']) {
    $studentSS = $_POST['studentSS'];

    DB::table("padres")->where(
        [
            ['ss', $studentSS],
            ['year', $year]
        ]
    )->delete();
    for ($a = 2; $a <= 6; $a++) {

        DB::table("padres$a")->where(
            [
                ['ss', $studentSS],
                ['year', $year]
            ]
        )->delete();

    }
}
