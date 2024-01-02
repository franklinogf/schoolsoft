<?php
require_once '../../../app.php';

use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Server::is_post();

$school = new School(Session::id());

if (isset($_POST['searchGrade'])) {
    $grades = DB::table('materias')->where([
        ['grado', $_POST['searchGrade']],
        ['year', $school->info('year2')]
    ])->orderBy('grado')->first();

    echo json_encode($grades);
} else if (isset($_POST['changeCourse'])) {

    $index = $_POST['changeCourse'];
    $value = $_POST['value'];
    $desc = $_POST['desc'];
    $grade = $_POST['grade'];
    $new = false;
    if (!DB::table('materias')->where([
        ['grado', $grade],
        ['year', $school->info('year2')]
    ])->first()) {
        $new = true;
        DB::table('materias')->insert([
            'grado' => $grade,
            'year' => $school->info('year2')
        ]);
    }

    DB::table('materias')->where([
        ['grado', $grade],
        ['year', $school->info('year2')]
    ])->update([
        "curso$index" => $value,
        "des$index" => $desc
    ]);
    echo json_encode(['new' => $new]);
} else if (isset($_POST['changeGrade'])) {
    $newGrade = $_POST['changeGrade'];
    $exist = true;

    if (!DB::table('materias')->where([
        ['grado', $newGrade],
        ['year', $school->info('year2')]
    ])->first()) {
        $exist = false;
        DB::table('materias')->insert([
            'grado' => $newGrade,
            'year' => $school->info('year2')
        ]);
    }
    echo json_encode(['exist' => $exist]);
} else if (isset($_POST['deleteGrade'])) {
    DB::table('materias')->where([
        ['grado', $_POST['deleteGrade']],
        ['year', $school->info('year2')]
    ])->delete();
}
