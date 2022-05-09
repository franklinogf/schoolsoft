<?php
require_once '../../../app.php';

use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;

Server::is_post();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$_info = [
    "Notas" => [
        'Trimestre-1' => [
            'totalGrade' => 'nota1',
            'values' => [
                'tdia' => 'td1',
                'tlib' => 'tl1',
                'pcor' => 'pc1',
                'tpa' => 'tpa1',
                'tdp' => 'por1'
            ]
        ],
        'Trimestre-2' => [
            'totalGrade' => 'nota2',
            'values' => [
                'tdia' => 'td2',
                'tlib' => 'tl2',
                'pcor' => 'pc2',
                'tpa' => 'tpa2',
                'tdp' => 'por2'
            ]
        ],
        'Trimestre-3' => [
            'totalGrade' => 'nota3',
            'values' => [
                'tdia' => 'td3',
                'tlib' => 'tl3',
                'pcor' => 'pc3',
                'tpa' => 'tpa3',
                'tdp' => 'por3'
            ]
        ],
        'Trimestre-4' => [
            'totalGrade' => 'nota4',
            'values' => [
                'tdia' => 'td4',
                'pcor' => 'pc4',
                'tlib' => 'tl4',
                'tpa' => 'tpa4',
                'tdp' => 'por4'
            ]
        ]
    ],
    "Cond-Asis" => [
        'Trimestre-1' => ['con1', 'aus1', 'tar1', 'de1'],
        'Trimestre-2' => ['con2', 'aus2', 'tar2', 'de2'],
        'Trimestre-3' => ['con3', 'aus3', 'tar3', 'de3'],
        'Trimestre-4' => ['con4', 'aus4', 'tar4', 'de4']
    ],
    "Ex-Final" => [
        'Trimestre-2' => 'ex1',
        'Trimestre-4' => 'ex2'
    ]

];

$_reports = [
    'Trab-Diarios' => 'tdia',
    'Trab-Libreta' => 'tlib',
    'Pruebas-Cortas' => 'pcor',
];

if (isset($_POST['changeValue'])) {
    $type = $_POST['changeValue'];
    $value = $_POST['value'];
    $id = $_POST['id'];
    DB::table('valores')
        ->where('id', $id)
        ->update([$type => $value]);
} else if (isset($_POST['changeOption'])) {
    $type = $_POST['changeOption'];
    $value = $_POST['value'];
    $subjectCode = $_POST['subjectCode'];
    DB::table('padres')->where([
        ['curso', $subjectCode],
        ['year', $year],
    ])->update([$type => $value === 'true' ? 'ON' : '']);
} else if (isset($_POST['submitForm'])) {
    $data = Util::toObject($_POST['data']);
    $_subjectCode = $data->subject[0];
    $_table = $data->table[0];
    $_report = $data->report[0];
    $_sumTrimester = $data->sumTrimester[0];
    if ($_report !== 'Cond-Asis' && $_report !== 'Cond-Asis') {
        $_values = $_info['Notas'][$data->trimester[0]]['values'];
        // foreach student ss
        foreach ($data->ss as $ss) {
            $_gradeStart = (int) $data->gradeStart[0];
            $inputsGrades = [];
            $studentData = DB::table($_table)
                ->where([
                    ['ss', $ss],
                    ['curso', $_subjectCode],
                    ['year', $teacher->info('year')]
                ])->first();
            // each grade
            foreach ($data->{"grade-$ss"} as $index => $grade) {
                $inputsGrades["not$_gradeStart"] = $grade;
                // Para el Informe cambio de notas
                $newNote = $grade;
                $noteIndex = $index + 1;
                $lastNote = $studentData->{"not$_gradeStart"};
                if ($newNote !== $lastNote && $lastNote !== '') {
                    echo "$ss New Note = $newNote lastNote = $lastNote \n";

                    DB::table('tarjeta_cambios')->insert([
                        'id' => $teacher->id,
                        'fecha' => Util::date(),
                        'hora' => Util::time(),
                        'ip' => Util::getIp(),
                        'curso' => $_subjectCode,
                        'nt1' => $newNote,
                        'nt2' => $lastNote,
                        'cual' => $noteIndex,
                        'ss' => $ss,
                        'year' => $teacher->info('year'),
                        'tri' => $data->trimester[0],
                        'pag' => $_report
                    ]);
                }
                $_gradeStart++;
            }

            $allInputs = array_merge(
                $inputsGrades,
                [$data->tpa[0] => $data->{"tpa-$ss"}[0]],
                [$data->tdp[0] => $data->{"tdp-$ss"}[0]],
                [$data->totalGrade[0] => $data->{"totalGrade-$ss"}[0]]
            );

            if ($_report === 'Notas') {
                $allInputs = array_merge(
                    $allInputs,
                    [$data->tdia[0] => $data->{"tdia-$ss"}[0]],
                    [$data->tlib[0] => $data->{"tlib-$ss"}[0]],
                    [$data->pcor[0] => $data->{"pcor-$ss"}[0]]
                );
            }

            if (
                DB::table($_table)
                ->where([
                    ['ss', $ss],
                    ['curso', $_subjectCode],
                    ['year', $teacher->info('year')]
                ])
                ->update($allInputs)
                &&
                $_report !== 'Notas'
            ) {
                DB::table('padres')
                    ->where([
                        ['ss', $ss],
                        ['curso', $_subjectCode],
                        ['year', $teacher->info('year')]
                    ])
                    ->update([
                        $_values[$_reports[$_report]] => $data->{"tpa-$ss"}[0]
                    ]);
            } else {
                $studentData = DB::table($_table)
                    ->where([
                        ['ss', $ss],
                        ['curso', $_subjectCode],
                        ['year', $teacher->info('year')]
                    ])->first();
                // Suma de trimestre
                if (!$_sumTrimester) {
                    if ($data->trimester[0] === 'Trimestre-1' || $data->trimester[0] === 'Trimestre-2') {
                        $note1 = 'nota1';
                        $note2 = 'nota2';
                        $sem = 'sem1';
                    } else {
                        $note1 = 'nota3';
                        $note2 = 'nota4';
                        $sem = 'sem2';
                    }
                    // Semester 1 or Semester 2 Notes
                    $div = 0;
                    $div += $studentData->{$note1} !== '' ? 1 : 0;
                    $div += $studentData->{$note2} !== '' ? 1 : 0;
                    $semNote = $div !== 0 ? round((+$studentData->{$note1} + +$studentData->{$note2}) / $div) : '';


                    // // Final Note
                    // $sem1 = $sem === 'sem1' ? $semNote : $studentData->sem1;
                    // $sem2 = $sem === 'sem2' ? $semNote : $studentData->sem2;
                    // $div = 0;
                    // $div += $sem1 !== '' ? 1 : 0;
                    // $div += $sem2 !== '' ? 1 : 0;
                    // $semFinalNote = $div !== 0 ? round((+$sem1 + +$sem2) / $div) : '';

                    // $updateSem = [
                    //     $sem => $semNote,
                    //     'final' => $semFinalNote
                    // ];
                    // DB::table($_table)
                    //     ->where([
                    //         ['ss', $ss],
                    //         ['curso', $_subjectCode],
                    //         ['year', $teacher->info('year')]
                    //     ])->update($updateSem);
                    // var_dump($updateSem);
                } else {
                    if ($data->trimester[0] === 'Trimestre-2') {
                        $note = 'nota2';
                        $sem = 'sem1';
                    } else if ($data->trimester[0] === 'Trimestre-4') {
                        $note = 'nota4';
                        $sem = 'sem2';
                    }
                    $semNote = round(+$studentData->{$note});
                    // // Final Note
                    // $sem1 = $sem === 'sem1' ? $semNote : $studentData->sem1;
                    // $sem2 = $sem === 'sem2' ? $semNote : $studentData->sem2;
                    // $div = 0;
                    // $div += $sem1 !== '' ? 1 : 0;
                    // $div += $sem2 !== '' ? 1 : 0;
                    // $semFinalNote = $div !== 0 ? round((+$sem1 + +$sem2) / $div) : '';
                    // $updateSem = [
                    //     $sem => $semNote,
                    //     'final' => $semFinalNote
                    // ];
                }
                // Final Note
                $sem1 = $sem === 'sem1' ? $semNote : $studentData->sem1;
                $sem2 = $sem === 'sem2' ? $semNote : $studentData->sem2;
                $div = 0;
                $div += $sem1 !== '' ? 1 : 0;
                $div += $sem2 !== '' ? 1 : 0;
                $semFinalNote = $div !== 0 ? round((+$sem1 + +$sem2) / $div) : '';
                $updateSem = [
                    $sem => $semNote,
                    'final' => $semFinalNote
                ];
                DB::table($_table)
                    ->where([
                        ['ss', $ss],
                        ['curso', $_subjectCode],
                        ['year', $teacher->info('year')]
                    ])->update($updateSem);
                var_dump($updateSem);
            }
        }
    } else {
        $_values = $_info[$_report][$data->trimester[0]];
        foreach ($data->ss as $ss) {
            if ($_report === 'Cond-Asis') {
                $updateArray = [
                    $_values[0] => $data->{"con-$ss"}[0],
                    $_values[1] => $data->{"aus-$ss"}[0],
                    $_values[2] => $data->{"tar-$ss"}[0],
                    $_values[3] => $data->{"de-$ss"}[0]
                ];
            } else {
                $updateArray = [
                    $_values => $data->{"ex-$ss"}[0]
                ];
            }
            DB::table('padres')
                ->where([
                    ['ss', $ss],
                    ['curso', $_subjectCode],
                    ['year', $year]
                ])
                ->update($updateArray);
        }
    }
}
