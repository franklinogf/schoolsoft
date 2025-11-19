<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([['Comparación de notas', 'Comparison of notes'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Trimestre', 'Quarter'],
   ['Descripción', 'Description'],
    ['Nota B', 'Note B'],
    ['Nota C', 'Note C'],
    ['Nota D', 'Note D'],
    ['Nota F', 'Note F'],
    ['Otros', 'Other'],
    ['Total', 'Total'],
    ['P-C', 'QZ'],
    ['TPA', 'TAP'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Trabajos Diarios', 'Daily Homework'],
    ['Trabajos Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Pruebas Cortas', 'Quiz'],
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['S-1', 'S-1'],
    ['S-2', 'S-2'],
   ['Final', 'Final'],
]);

$pdf = new PDF();
$school = new School(Session::id());
$grado = $_POST['grade'];
$nota = $_POST['nota'];
$divicion = $_POST['divicion'] ?? '';

$cl = $_POST['cl'];
$notar = $_POST['notar'];

$year = $school->info('year2');
$pdf = new PDF();
$allGrades = DB::table('padres')->select("distinct grado, curso, descripcion")->where([
          ['year', $year],
          ['grado', $grado],
])->orderBy('curso')->get();

foreach ($allGrades as $grade) 
        {
        $pdf->AddPage('');
   $pdf->Cell(0, 5, utf8_encode($lang->translation("Comparación de notas")) . ' / ' . $grade->grado . " / $year", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Fill();
        $pdf->Cell(17, 5, $lang->translation("Curso"), 1, 0, 'L', true);
        $pdf->Cell(20, 5, $grade->curso, 0, 0, 'L');
        $pdf->Cell(70, 5, $grade->descripcion, 0, 1, 'L');
        $pdf->Cell(10, 5, '', 1, 0, 'C', true);
        $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        if ($nota=='1')
           {
           $pdf->Cell(15, 5, $lang->translation("T-1"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("T-2"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("S-1"), 1, 1, 'C', true);
           }
        if ($nota=='2')
           {
           $pdf->Cell(15, 5, $lang->translation("T-3"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("T-4"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("S-2"), 1, 1, 'C', true);
           }
        if ($nota=='3')
           {
           $pdf->Cell(15, 5, $lang->translation("S-1"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("S-2"), 1, 0, 'C', true);
           $pdf->Cell(15, 5, $lang->translation("Final"), 1, 1, 'C', true);
           }
        $n = 0;
        $materias = [];
        $curs = [];
        $estudiantes = [];
   $c = 0;
   $students = DB::table('padres')->where([
                  ['year', $year],
                  ['grado', $grade->grado],
                  ['curso', $grade->curso]
                  ])->orderBy('apellidos')->get();
        $t=0;
        foreach ($students as $student) 
                {
                $t=$t+1;
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 5, $t, $cl, 0, 'R');
                $pdf->Cell(55, 5, $student->apellidos, $cl, 0, 'L');
                $pdf->Cell(45, 5, $student->nombre, $cl, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $a=0;$b=0;
                if ($nota=='1')
                   {
                   $pdf->Cell(15, 5, $student->nota1, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->nota2, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->sem1, $cl, 1, 'R');
                   }
                if ($nota=='2')
                   {
                   $pdf->Cell(15, 5, $student->nota3, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->nota4, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->sem2, $cl, 1, 'R');
                   }
                if ($nota=='3')
                   {
                   $pdf->Cell(15, 5, $student->sem1, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->sem2, $cl, 0, 'R');
                   $pdf->Cell(15, 5, $student->final, $cl, 1, 'R');
                   }
   }
         }


$pdf->Output();