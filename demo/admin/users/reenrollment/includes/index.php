<?php

require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Student;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Session::is_logged();
Server::is_post();


$students = new Student();
$year = $students->info('year');
$year2 = (($year[0] . $year[1]) + 1) . '-' . (($year[3] . $year[4]) + 1);

if (isset($_POST['search'])) {
    $data = $students->all($_POST['search'] === $year ? null : $_POST['search']);
    if ($_POST['search'] === $year) {
        $data =  array_values(array_filter($data, function ($value) use ($year2) {
            list($g1, $g2) = explode('-', $value->grado);
            $dataStudent = DB::table('year')->where([
                ['year', $year2],
                ['ss', $value->ss]
            ])->first();
            return $g1 !== '12' && !$dataStudent;
        }));
    }
    echo Util::toJson($data);
} else if (isset($_POST['searchGrade'])) {
    $data = $students->findByGrade($_POST['searchGrade'], isset($_POST['new']) ? $_POST['new'] : null);
    if (!isset($_POST['new'])) {
        $data =  array_values(array_filter($data, function ($value) use ($year2) {
            $dataStudent = DB::table('year')->where([
                ['year', $year2],
                ['ss', $value->ss]
            ])->first();
            return !$dataStudent;
        }));
    }
    echo Util::toJson($data);
} else if (isset($_POST['searchBySurname'])) {
    $surnames = $_POST['searchBySurname'];
    $grade = $_POST['grade'] ?? null;
    $whereQuery = "apellidos LIKE '%$surnames%'";
    if ($grade !== null) {
        $whereQuery .= " AND grado = '$grade'";
    }
    $data = DB::table('year')->whereRaw($whereQuery)->get();
    $data =  array_values(array_filter($data, function ($value) use ($year2) {
        $dataStudent = DB::table('year')->where([
            ['year', $year2],
            ['ss', $value->ss]
        ])->first();
        return !$dataStudent;
    }));
    echo Util::toJson($data);
} else if (isset($_POST['passStudents'])) {
    $newYear = $_POST['newYear'];
    $studentsMT = $_POST['passStudents'];

    foreach ($studentsMT as $mt) {
        $student = $students->findPK($mt);
        if (isset($_POST['grade'])) {
            $grade = $_POST['grade'];
        } else {
            $grade = Util::getNextGrade($student->grado);
        }
        $student->grado = $grade;
        $student->gra2 = $grade;
        $student->year = $newYear;
        $student->nuevo = "No";

        unset($student->mt);
        $student->save('new');
    }
} else if (isset($_POST['deleteStudents'])) {
    $studentsMT = $_POST['deleteStudents'];
    foreach ($studentsMT as $mt) {
        $student = $students->findPK($mt);
        $student->delete();
    }
}
