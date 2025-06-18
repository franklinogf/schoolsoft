<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Lang;
use Classes\Server;

Server::is_post();
Session::is_logged();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$grade = $teacher->grado;
$students = new Student();
$students = $students->findByGrade($grade);

$lang = new Lang([
    ["Listado de 100", "List of 100"],
    ["GRADO", "GRADE"],
    ["AÑO", "YEAR"],
    ["Estudiante", "Student"],
    ["Cantidad", "Amount"],

]);

$_info = [
    "Notas" => [
        'table' => 'padres',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],
        ]
    ],
    "Pruebas-Cortas" => [
        'table' => 'padres4',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],
        ]
    ],
    "Trab-Diarios" => [
        'table' => 'padres2',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],
        ]
    ],
    "Trab-Diarios2" => [
        'table' => 'padres5',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],

        ]
    ],
    "Trab-Libreta" => [
        'table' => 'padres3',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],

        ],
        'Trimestre-4' => [
            'grades' => [31, 40],

        ]
    ],
    "Trab-Libreta2" => [
        'table' => 'padres6',
        'Trimestre-1' => [
            'grades' => [1, 10],
        ],
        'Trimestre-2' => [
            'grades' => [11, 20],
        ],
        'Trimestre-3' => [
            'grades' => [21, 30],
        ],
        'Trimestre-4' => [
            'grades' => [31, 40],
        ]
    ],
];
$pages = $_POST['pages'];
$trimesters = $_POST['trimesters'];

$_students = [];
foreach ($students as $student) {
    $_students[$student->ss]['fullName'] = "$student->nombre $student->apellidos";
    $_students[$student->ss]['ss'] = $student->ss;
}
// echo "<pre>";
foreach ($_students as $student) {
    $amount = 0;
    foreach ($pages as $report) {
        foreach ($trimesters as $trimester) {
            $valIndex = 1;
            $grades = $_info[$report][$trimester]['grades'];
            for ($i = $grades[0]; $i <= $grades[1]; $i++) {

                // echo "{$student['ss']} => {$_info[$report]['table']} - $report = $trimester<br>";
                $fatherTable = DB::table($_info[$report]['table'])->select("not{$i} as note,curso")->Where([
                    ['ss', $student['ss']],
                    ['year', $year],
                    ['grado', $grade]
                ])->get();

                foreach ($fatherTable as $father) {
                    if (is_numeric($father->note)) {
                        $_value = DB::table('valores')->select("val$valIndex as value")
                            ->where([
                                ['curso', $father->curso],
                                ['trimestre', $trimester],
                                ['nivel', $report],
                                ['year', $year]
                            ])->first();

                        if ($father->note >= $_value->value && is_numeric($_value->value)) {
                            //    echo "$father->curso -> $trimester => val$valIndex <br>";
                            //    echo "$father->note >= $_value->value <br>";
                            $amount++;
                        }
                    }
                }
                $_students[$student['ss']]['cantidad'] = $amount;
                $valIndex++;
            }
        }
        // echo "<hr>";
    }
    // echo "</pre>";

    $pdf = new PDF();
    $pdf->SetTitle($lang->translation("Listado de 100"));
    $pdf->addPage();
    $pdf->Fill();
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(0, 5, $lang->translation("Listado de 100"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("GRADO") . " $teacher->grado / " . $lang->translation("AÑO") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(15);
    $pdf->Cell(20, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(100, 5, $lang->translation("Estudiante"), 1, 0, 'C', true);
    $pdf->Cell(30, 5, $lang->translation("Cantidad"), 1, 1, 'C', true);
    $count = 1;
    foreach ($_students as $student) {
        $pdf->Cell(15);
        $pdf->Cell(20, 5, $count, 1, 0, 'C');
        $pdf->Cell(100, 5, $student['fullName'], 1);
        $pdf->Cell(30, 5, $student['cantidad'] ?? '', 1, 1, 'C');
        $count++;
    }
}
$pdf->Output();
