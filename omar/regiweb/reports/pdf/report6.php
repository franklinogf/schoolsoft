<?php
require_once '../../app.php';

use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Teacher;
use Classes\Util;

global $_class;
global $_report;

$teacher = new Teacher(Session::id());
Session::is_logged();
$data = DB::table('padres')->where([
    ['curso', $_class],
    ['year', $teacher->info('year')],
    ['id', Session::id()],
    ['verano', '']
])->orderBy('apellidos')->first();
$students = new Student();
$students = $students->findByClass($_class);

list($grade, $section) = explode("-", $data->grado);

$titles = [
    ["NTA-1", "40%", "C-1", "NTA-2", "40%", "C-2", "EXF", "20%", "NOTA"],
    ["NTA-1", "45%", "C-1", "NTA-2", "45%", "C-2", "EXF", "10%", "NOTA"]
];
$percentNotes = [
    [0.40, 0.20],
    [0.45, 0.10]
];
$number = ($grade > 8) ? 0 : 1;
$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle('Semestre Porciento');
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 5, 'Profesor', 1, 0, 'C', true);
$pdf->Cell(18, 5, 'Curso', 1, 0, 'C', true);
$pdf->Cell(40, 5, utf8_decode('Descripción'), 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Creditos', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Total Est.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Fecha', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, utf8_decode($teacher->fullName()), 1, 0, 'C');
$pdf->Cell(18, 5, $data->curso, 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode($data->descripcion), 1, 0, 'C');
$pdf->Cell(20, 5, $teacher->credits($_class), 1, 0, 'C');
$pdf->Cell(20, 5, count($students), 1, 0, 'C');
$pdf->Cell(25, 5, Util::formatDate(Util::date()), 1, 1, 'C');

$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 5, 'Apellidos', 1, 0, 'C', true);
$pdf->Cell(35, 5, 'Nombre', 1, 0, 'C', true);
foreach ($titles[$number] as $index => $title) {
    if (sizeof($titles[$number]) === $index + 1) {

        $pdf->Cell(12, 5, $title, 1, 1, 'C', true);
    } else {
        $pdf->Cell(12, 5, $title, 1, 0, 'C', true);
    }
}

foreach ($students as $student) {
    $firstNote = ($student->nota1 <> 0) ? round($student->nota1 * $percentNotes[$number][0]) : 0;
    $secondNote = ($student->nota2 <> 0) ? round($student->nota2 * $percentNotes[$number][0]) : 0;
    $examNote = ($student->ex1 <> 0) ? round($student->ex1 * $percentNotes[$number][1]) : 0;
    $finalNote = round($firstNote + $secondNote + $examNote);
    
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(45, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(35, 5, utf8_decode($student->nombre), 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(12, 5, $student->nota1, 1, 0, 'C');
    $pdf->Cell(12, 5, ($firstNote <> 0) ? $firstNote : '', 1, 0, 'C');
    $pdf->Cell(12, 5, $student->con1, 1, 0, 'C');
    $pdf->Cell(12, 5, $student->nota2, 1, 0, 'C');
    $pdf->Cell(12, 5, ($secondNote <> 0) ? $secondNote : '', 1, 0, 'C');
    $pdf->Cell(12, 5, $student->con2, 1, 0, 'C');
    $pdf->Cell(12, 5, $student->ex1, 1, 0, 'C');
    $pdf->Cell(12, 5, ($examNote <> 0) ? $examNote : '', 1, 0, 'C');
    $pdf->Cell(12, 5, ($finalNote <> 0) ? $finalNote : '', 1, 1, 'C');
}


$pdf->Output();
