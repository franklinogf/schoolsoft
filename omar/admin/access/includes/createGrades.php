<?php
require_once '../../../app.php';

use Classes\Lang;
use Classes\Route;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Teacher;

Server::is_post();

$lang = new Lang([
    ['Grado creado', 'Grade created'],
]);

if (isset($_POST['create'])) {
    $school = new School();

    $grade = $_POST['grade'];
    $option = $_POST['option'];

    $year = $school->info('year');
    $students = DB::table('year')->where([
        ['grado', $grade],
        ['year', $year],
        ['activo', ''],
    ])->get();
    $subject = DB::table('materias')->where([
        ['grado', $grade],
        ['year', $year],
    ])->first();
    foreach ($students as $student) {
        for ($i = 1; $i <= 40; $i++) {
            if (!empty($subject->{"curso$i"})) {
                $course = DB::table('cursos')->where([
                    ['curso', $subject->{"curso$i"}],
                    ['year', $year],
                ])->first();
                $teacher = new Teacher($course->id);
                if ($option === '1' || $option === '2') {
                    if (!DB::table('padres')->where([
                        ['ss', $student->ss],
                        ['curso', $subject->{"curso$i"}],
                        ['year', $year]
                    ])->first()) {

                        DB::table("padres")->insert([
                            'id' => $teacher->id,
                            'nombre' => $student->nombre,
                            'apellidos' => $student->apellidos,
                            'descripcion' => $course->desc1,
                            'grado' => $grade,
                            'curso' => $subject->{"curso$i"},
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
                        if (!DB::table("padres$a")->where([
                            ['ss', $student->ss],
                            ['curso', $subject->{"curso$i"}],
                            ['year', $year]
                        ])->first()) {
                            DB::table("padres$a")->insert([
                                'id' => $teacher->id,
                                'nombre' => $student->nombre,
                                'apellidos' => $student->apellidos,
                                'descripcion' => $course->desc1,
                                'grado' => $grade,
                                'curso' => $subject->{"curso$i"},
                                'profesor' => "$teacher->nombre $teacher->apellidos",
                                'ss' => $student->ss,
                                'year' => $year,
                            ]);
                        }
                    }
                }

                if ($option === '1' || $option == -'3') {
                    $asis = [
                        '08' => '01',
                        '09' => '02',
                        '10' => '03',
                        '11' => '04',
                        '12' => '05',
                        '01' => '06',
                        '02' => '07',
                        '03' => '08',
                        '04' => '09',
                        '05' => '10',
                    ];
                    foreach ($asis as $month => $pos) {
                        DB::table('asisdia')->insert([
                            'ss' => $student->ss,
                            'grado' => $grade,
                            'nombre' => $student->nombre,
                            'apellidos' => $student->apellidos,
                            'year' => $year,
                            'mes' => $month,
                            'pos' => $pos
                        ]);
                    }
                }
            }
        }
    }

    Session::set('createGrades', $lang->translation("Grado creado"));

    Route::redirect('/access/createGrades.php');
}
