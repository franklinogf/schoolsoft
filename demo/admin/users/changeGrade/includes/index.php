<?php

require_once '../../../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;

Session::is_logged();
Server::is_post();
$school = new School();
$year = $school->year();
if (isset($_POST['grade'])) {
    $grade = $_POST['grade'];
    $students = $_POST['students'];
    foreach ($students as $mt => $newGrade) {
        $studentInfo = new Student($mt);
        if ($studentInfo->grado  !== $newGrade) {
            $studentSS = $studentInfo->ss;
            $studentInfo->grado = $newGrade;
            $studentInfo->save();
            DB::table('pagos')->where([
                ['year', $year],
                ['ss', $studentSS],
            ])->update([
                'grado' => $newGrade
            ]);
            $tables = ['padres', 'padres2', 'padres3', 'padres4', 'padres5', 'padres6', 'asisdia', 'asispp'];
            foreach ($tables as $table) {
                DB::table($table)->where([
                    ['year', $year],
                    ['ss', $studentSS]
                ])->update([
                    'grado' => $newGrade
                ]);
            }
        }
    }
    Route::redirect("/users/changeGrade/index.php?grade=$grade");
}
