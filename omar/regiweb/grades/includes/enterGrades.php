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
    if ($data->report[0] !== 'Cond-Asis' && $data->report[0] !== 'Cond-Asis') {
        $_values = $_info['Notas'][$data->trimester[0]]['values'];
        // foreach student ss
        foreach ($data->ss as $ss) {
            $_gradeStart = (int) $data->gradeStart[0];
            $inputsGrades = [];
            // each grade
            foreach ($data->{"grade-$ss"} as $key => $grade) {
                $inputsGrades["not$_gradeStart"] = $grade;
                $_gradeStart++;
            }

            $allInputs = array_merge(
                $inputsGrades,
                [$data->tpa[0] => $data->{"tpa-$ss"}[0]],
                [$data->tdp[0] => $data->{"tdp-$ss"}[0]],
                [$data->totalGrade[0] => $data->{"totalGrade-$ss"}[0]]
            );

            if ($data->report[0] === 'Notas') {
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
                $data->report[0] !== 'Notas'
            ) {
                DB::table('padres')
                    ->where([
                        ['ss', $ss],
                        ['curso', $_subjectCode],
                        ['year', $teacher->info('year')]
                    ])
                    ->update([
                        $_values[$_reports[$data->report[0]]] => $data->{"tpa-$ss"}[0]
                    ]);
            } else {
                var_dump($allInputs);
            }
        }
    } else {
        $_values = $_info[$data->report[0]][$data->trimester[0]];
        foreach ($data->ss as $ss) {
            if ($data->report[0] === 'Cond-Asis') {
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
