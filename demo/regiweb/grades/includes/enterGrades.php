<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\Teacher;
use App\Services\SchoolService;
use Classes\Server;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;
use App\Enums\TrimesterEnum;
use App\Enums\GradePageEnum;
use App\Models\Value;
use Classes\Util;

Server::is_post();
$teacher = Teacher::find(Session::id());
$year = SchoolService::getCurrentYear();

if (isset($_POST['changeValue'])) {
    $name = $_POST['changeValue'];
    $value = $_POST['value'];
    $id = $_POST['id'];
    Value::query()
        ->find($id)
        ->update([$name => $value]);
    echo json_encode(['status' => 'success', 'message' => "$name updated successfully."]);
} elseif (isset($_POST['changeNoteType'])) {
    $type = $_POST['changeNoteType'];
    $value = $_POST['value'];
    $subjectCode = $_POST['subjectCode'];
    DB::table('padres')->where([
        ['curso', $subjectCode],
        ['year', $year],
    ])->update([$type => $value]);
    echo json_encode(['status' => 'success', 'message' => "$type updated successfully."]);
} elseif (isset($_POST['changeOption'])) {
    $type = $_POST['changeOption'];
    $value = $_POST['value'];
    $subjectCode = $_POST['subjectCode'];
    DB::table('padres')->where([
        ['curso', $subjectCode],
        ['year', $year],
    ])->update([$type => $value === 'true' ? 'ON' : '']);
    echo json_encode(['status' => 'success', 'message' => "$type updated successfully."]);
} elseif (isset($_POST['submitForm'])) {
    // dd($_POST);

    $report = GradePageEnum::from($_POST['report']);
    $trimester = TrimesterEnum::from($_POST['trimester']);
    $table = $report->getTableName();
    $sumTrimester = empty($_POST['sumTrimester']) ? false : true;
    $subjectCode = $_POST['subject'];
    $students = $_POST['students'];


    $oldStudentData = DB::table($table)
        ->where([
            ['curso', $subjectCode],
            ['year', $year],
        ])
        ->whereIn('ss', array_keys($students))
        ->get();

    DB::connection()->transaction(function () use ($students, $oldStudentData, $table, $subjectCode, $year, $teacher, $trimester, $report) {
        foreach ($students as $ss => $data) {
            $studentData = $oldStudentData->firstWhere('ss', $ss);
            $newGrades = collect($data)
                ->mapWithKeys(function ($grade, $key) use ($studentData, $trimester) {

                    if (
                        preg_match('/^not\d+$/', $key)
                        && $studentData->{$key} !== $grade
                        && ($studentData->{$key} !== '' && $studentData->{$key} !== null)
                    ) {
                        $gradeNumber = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                        return [
                            $gradeNumber => [
                                'new' => $grade,
                                'old' => $studentData->{$key},
                            ]
                        ];
                    }
                    return [];
                })->toArray();

            DB::table($table)
                ->where([
                    ['ss', $ss],
                    ['curso', $subjectCode],
                    ['year', $year],
                ])->update($data);

            foreach ($newGrades as $gradeNumber => $grades) {
                DB::table('tarjeta_cambios')->insert([
                    'id' => $teacher->id,
                    'fecha' => date('Y-m-d'),
                    'hora' => date('H:i:s'),
                    'ip' => Util::getIp(),
                    'curso' => $subjectCode,
                    'nt1' => $grades['new'],
                    'nt2' => $grades['old'],
                    'cual' => $gradeNumber,
                    'ss' => $ss,
                    'year' => $year,
                    'tri' => $trimester->value,
                    'pag' => $report->value
                ]);
            }


            if ($report === GradePageEnum::SHORT_TESTS || $report === GradePageEnum::DAILY_WORKS || $report === GradePageEnum::NOTEBOOK_WORKS) {

                DB::table('padres')
                    ->where([
                        ['ss', $ss],
                        ['curso', $subjectCode],
                        ['year', $year],
                    ])->update([
                        $report->getShortColumn($trimester) => $data['nota' . $trimester->getNumber()]
                    ]);
            }
        }
    });

    echo json_encode(['status' => 'success', 'message' => 'Grades updated successfully.']);

    // $data = Util::toObject($_POST);
    // $_subjectCode = $data->subject[0];
    // $_table = $data->table[0];
    // $_report = $data->report[0];
    // $_sumTrimester = $data->sumTrimester[0];
    // if ($_report !== 'Cond-Asis' && $_report !== 'Ex-Final') {
    //     // $_values = $_info[$_report][$data->trimester[0]]['values'];
    //     // foreach student ss
    //     foreach ($data->ss as $ss) {

    //         $_gradeStart = (int) $data->gradeStart[0];
    //         $inputsGrades = [];
    //         $studentData = Manager::table($_table)
    //             ->where([
    //                 ['ss', $ss],
    //                 ['curso', $_subjectCode],
    //                 ['year', $year]
    //             ])->first();
    //         // each grade
    //         foreach ($data->{"grade-$ss"} as $index => $grade) {
    //             $inputsGrades["not$_gradeStart"] = $grade;
    //             // Para el Informe cambio de notas
    //             $newNote = $grade;
    //             $noteIndex = $index + 1;
    //             $lastNote = $studentData->{"not$_gradeStart"};
    //             if ($newNote !== $lastNote && ($lastNote !== '' && $lastNote !== null)) {
    //                 Manager::table('tarjeta_cambios')->insert([
    //                     'id' => $teacher->id,
    //                     'fecha' => Util::date(),
    //                     'hora' => Util::time(),
    //                     'ip' => Util::getIp(),
    //                     'curso' => $_subjectCode,
    //                     'nt1' => $newNote,
    //                     'nt2' => $lastNote,
    //                     'cual' => $noteIndex,
    //                     'ss' => $ss,
    //                     'year' => $year,
    //                     'tri' => $data->trimester[0],
    //                     'pag' => $_report
    //                 ]);
    //             }
    //             $_gradeStart++;
    //         }
    //         $allInputs = $inputsGrades;

    //         if ($_report !== 'Notas2') {
    //             $allInputs = array_merge(
    //                 $inputsGrades,
    //                 [$data->tpa[0] => $data->{"tpa-$ss"}[0]],
    //                 [$data->tdp[0] => $data->{"tdp-$ss"}[0]],
    //                 [$data->totalGrade[0] => $data->{"totalGrade-$ss"}[0]]
    //             );
    //         }
    //         if ($_report === 'Notas') {
    //             $allInputs = array_merge(
    //                 $allInputs,
    //                 [$data->tdia[0] => $data->{"tdia-$ss"}[0]],
    //                 [$data->tlib[0] => $data->{"tlib-$ss"}[0]],
    //                 [$data->pcor[0] => $data->{"pcor-$ss"}[0]]
    //             );
    //         }

    //         if (__ONLY_CBTM__ && $_report === 'Notas') {
    //             $allInputs = array_merge(
    //                 $allInputs,
    //                 [$data->totalAverage[0] => $data->{"totalAverage-$ss"}[0]]
    //             );
    //         }
    //         if (
    //             Manager::table($_table)
    //             ->where([
    //                 ['ss', $ss],
    //                 ['curso', $_subjectCode],
    //                 ['year', $year]
    //             ])
    //             ->update($allInputs)
    //             &&
    //             $_report !== 'Notas' && $_report !== 'Notas2'
    //         ) {
    //             // if($_report === 'Pruebas-Cortas'){
    //             //     $reportUpdate =[$_info['Notas'][$data->trimester[0]]['values'][$_reports[$_report]] => $data->{"tpa-$ss"}[0]] ;
    //             // }else{
    //             //     $reportUpdate = [$_values[$_reports[$_report]] => $data->{"tpa-$ss"}[0]];
    //             // }
    //             Manager::table('padres')
    //                 ->where([
    //                     ['ss', $ss],
    //                     ['curso', $_subjectCode],
    //                     ['year', $year]
    //                 ])
    //                 ->update([$_info['Notas'][$data->trimester[0]]['values'][$_reports[$_report]] => $data->{"tpa-$ss"}[0]]);
    //         } else {
    //             if ($_report !== 'Notas2') {
    //                 $studentData = Manager::table($_table)
    //                     ->where([
    //                         ['ss', $ss],
    //                         ['curso', $_subjectCode],
    //                         ['year', $year]
    //                     ])->first();
    //                 // Suma de trimestre
    //                 if (!$_sumTrimester) {
    //                     if ($data->trimester[0] === 'Trimestre-1' || $data->trimester[0] === 'Trimestre-2') {
    //                         $note1 = 'nota1';
    //                         $note2 = 'nota2';
    //                         $sem = 'sem1';
    //                     } else {
    //                         $note1 = 'nota3';
    //                         $note2 = 'nota4';
    //                         $sem = 'sem2';
    //                     }
    //                     // Semester 1 or Semester 2 Notes
    //                     $div = 0;
    //                     $div += $studentData->{$note1} !== '' ? 1 : 0;
    //                     $div += $studentData->{$note2} !== '' ? 1 : 0;
    //                     $semNote = $div !== 0 ? round(((int) $studentData->{$note1} + (int) $studentData->{$note2}) / $div) : '';
    //                 } else {
    //                     if ($data->trimester[0] === 'Trimestre-2') {
    //                         $note = 'nota2';
    //                         $sem = 'sem1';
    //                     } elseif ($data->trimester[0] === 'Trimestre-4') {
    //                         $note = 'nota4';
    //                         $sem = 'sem2';
    //                     }
    //                     $semNote = round((int) $studentData->{$note});
    //                 }
    //                 // Final Note
    //                 $sem1 = $sem === 'sem1' ? $semNote : $studentData->sem1;
    //                 $sem2 = $sem === 'sem2' ? $semNote : $studentData->sem2;
    //                 $div = 0;
    //                 $div += $sem1 !== '' ? 1 : 0;
    //                 $div += $sem2 !== '' ? 1 : 0;
    //                 $semFinalNote = $div !== 0 ? round(((int)$sem1 + (int) $sem2) / $div) : '';
    //                 /* -------------------------------------------------------------------------- */
    //                 /*       // PARA QUE EL CBTM NO APAREZCA EL PROMEDIO CUANDO SEA PESO = 1      */
    //                 /* -------------------------------------------------------------------------- */
    //                 if (__ONLY_CBTM__ && $data->{"peso-$ss"}[0] == 1) {
    //                     $semNote = '';
    //                 }
    //                 $updateSem = [
    //                     $sem => $semNote,
    //                     'final' => $semFinalNote
    //                 ];
    //                 Manager::table($_table)
    //                     ->where([
    //                         ['ss', $ss],
    //                         ['curso', $_subjectCode],
    //                         ['year', $year]
    //                     ])->update($updateSem);
    //             }
    //         }
    //     }
    // } else {
    //     $_values = $_info[$_report][$data->trimester[0]];
    //     foreach ($data->ss as $ss) {
    //         if ($_report === 'Cond-Asis') {
    //             $updateArray = [
    //                 $_values[0] => $data->{"con-$ss"}[0],
    //                 $_values[1] => $data->{"aus-$ss"}[0],
    //                 $_values[2] => $data->{"tar-$ss"}[0],
    //                 $_values[3] => $data->{"de-$ss"}[0]
    //             ];
    //         } else {
    //             $updateArray = [
    //                 $_values => $data->{"ex-$ss"}[0]
    //             ];
    //             /* ---------- Sacarle el porciento a las notas para el examen final --------- */
    //             if (school_is('cbtm')) {
    //                 if ($data->{"ex-$ss"}[0] !== '') {
    //                     $exGrade = $data->exGrade[0];
    //                     if ($exGrade === '06') {
    //                         $quater = 42.5 / 100;
    //                         $ex = 15 / 100;
    //                     } elseif ($exGrade === '07') {
    //                         $quater = 40 / 100;
    //                         $ex = 20 / 100;
    //                     } elseif ($exGrade === '08') {
    //                         $quater = 37.5 / 100;
    //                         $ex = 25 / 100;
    //                     } else {
    //                         $quater = 35 / 100;
    //                         $ex = 30 / 100;
    //                     }
    //                     $exStu =  Manager::table('padres')
    //                         ->where([
    //                             ['ss', $ss],
    //                             ['curso', $_subjectCode],
    //                             ['year', $year]
    //                         ])->first();

    //                     if ($data->trimester[0] === 'Trimestre-2') {
    //                         $q1 =  round($exStu->nota1 * $quater);
    //                         $q2 =  round($exStu->nota2 * $quater);
    //                         $qex = round($data->{"ex-$ss"}[0] * $ex);
    //                         $exTotal = $q1 + $q2 + $qex;
    //                         $updateArray["q1"] = $q1 != 0 ? $q1 : null;
    //                         $updateArray["q2"] = $q2 != 0 ? $q2 : null;
    //                         $updateArray["qex1"] = $qex;
    //                         $updateArray["sem1"] = $exTotal;
    //                     } else {
    //                         $q1 =  round($exStu->nota3 * $quater);
    //                         $q2 =  round($exStu->nota4 * $quater);
    //                         $qex = round($data->{"ex-$ss"}[0] * $ex);
    //                         $exTotal = $q1 + $q2 + $qex;
    //                         $updateArray["q3"] = $q1 != 0 ? $q1 : null;
    //                         $updateArray["q4"] = $q2 != 0 ? $q2 : null;
    //                         $updateArray["qex2"] = $qex;
    //                         $updateArray["sem2"] = $exTotal;
    //                     }
    //                 } else {
    //                     $updateArray["qex1"] = '';
    //                     $updateArray["qex2"] = '';
    //                     $studentData = Manager::table($_table)
    //                         ->where([
    //                             ['ss', $ss],
    //                             ['curso', $_subjectCode],
    //                             ['year', $year]
    //                         ])->first();
    //                     if ($data->trimester[0] === 'Trimestre-1' || $data->trimester[0] === 'Trimestre-2') {
    //                         $note1 = 'nota1';
    //                         $note2 = 'nota2';
    //                         $sem = 'sem1';
    //                     } else {
    //                         $note1 = 'nota3';
    //                         $note2 = 'nota4';
    //                         $sem = 'sem2';
    //                     }
    //                     // Semester 1 or Semester 2 Notes
    //                     $div = 0;
    //                     $div += $studentData->{$note1} !== '' ? 1 : 0;
    //                     $div += $studentData->{$note2} !== '' ? 1 : 0;
    //                     $semNote = $div !== 0 ? round((+$studentData->{$note1} + +$studentData->{$note2}) / $div) : '';
    //                     $updateArray[$sem] = round($semNote);
    //                     // var_dump($updateArray);

    //                 }
    //             }
    //         }
    //         Manager::table('padres')
    //             ->where([
    //                 ['ss', $ss],
    //                 ['curso', $_subjectCode],
    //                 ['year', $year]
    //             ])
    //             ->update($updateArray);
    //     }
    // }
}
