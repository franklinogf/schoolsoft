<?php
require_once __DIR__ . '/../../../app.php';

use App\Models\Admin;
use App\Models\Student;
use App\Models\Teacher;


use Classes\Util;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;



Server::is_post();
$teacher = Teacher::findOrFail(Session::id());
$schoolInfo = Admin::primaryAdmin();
$year = $schoolInfo->year;

$date = $_POST['date'];
$newDate = strtotime($date);
$month = date('m', $newDate);
$day = date('j', $newDate); //day without "0"

if (isset($_POST['getStudents'])) {
    $attendanceOption = $_POST['getStudents'];
    $isCourse = $attendanceOption === '3';
    $attendanceArray = [];
    
    $students = !$isCourse ? Student::query()
        ->byGrade($_POST['grade'])
        ->get()
        : Student::query()
            ->byClass($_POST['class'])
            ->get();

    foreach ($students as $student) {

        $attendance = DB::table('asispp')
            ->where([
                ['ss', $student->ss],
                [$isCourse ? 'curso' : 'grado', $isCourse ? $_POST['class'] : $_POST['grade']],
                ['year', $year],
                ['fecha', $date],
            ])
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->first();

        $attendanceArray[$student->ss]['code'] = $attendance?->codigo ?? '';
        $attendanceArray[$student->ss]['p'] = [
            'p1' => $attendance?->p1 ?? '',
            'p2' => $attendance?->p2 ?? '',
            'p3' => $attendance?->p3 ?? '',
            'p4' => $attendance?->p4 ?? '',
            'p5' => $attendance?->p5 ?? '',
            'p6' => $attendance?->p6 ?? '',
        ];
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
} else if (isset($_POST['saveAttendance'])) {
    $attendanceOption = $_POST['saveAttendance'];
    $isCourse = $attendanceOption === '3';
    $students = $_POST['students'] ?? [];
    $hasP = school_is('csaa');
    $class = $_POST['class'] ?? null;

    DB::connection()->transaction(function () use ($students, $isCourse, $year, $date, $hasP, $class) {
        foreach ($students as $ss => $data) {
            $student = Student::query()->bySS($ss)->first();

            $where = [
                ['ss', $ss],
                [$isCourse ? 'curso' : 'grado', $isCourse ? $class : $student->grado],
                ['year', $year],
                ['fecha', $date],
            ];

            $update = ['codigo' => $data['codigo'] ?? ''];
            if ($hasP) {
                foreach (['p1', 'p2', 'p3', 'p4', 'p5', 'p6'] as $p) {
                    $update[$p] = $data[$p] ?? '';
                }
            }

            if (DB::table('asispp')->where($where)->first()) {
                DB::table('asispp')->where($where)->update($update);
            } else {
                $insert = array_merge($update, [
                    'ss' => $ss,
                    'fecha' => $date,
                    'year' => $year,
                    'nombre' => $student->nombre,
                    'apellidos' => $student->apellidos,
                    'grado' => $student->grado,
                ]);
                if ($isCourse) {
                    $insert['curso'] = $class;
                }
                DB::table('asispp')->insert($insert);
            }
        }
    });

    echo Util::toJson(['status' => 'success']);
}
