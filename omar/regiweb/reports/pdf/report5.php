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


$columns = [
    ["nota1", "con1", "aus1", "tar1", "nota2", "con2", "aus2", "tar2", "sem1", "nota3", "con3", "aus3", "tar3", "nota4", "con4", "aus4", "tar4", "sem2", "final"],
    ["nota1", "con1", "aus1", "tar1", "nota2", "con2", "aus2", "tar2", "nota3", "con3", "aus3", "tar3", "nota4", "con4", "aus4", "tar4", "final"]
];

$titles = [
    ["T1", "C1", "A", "T", "T2", "C2", "A", "T", "S1", "T3", "C3", "A", "T", "T4", "C4", "A", "T", "S2"],
    ["T1", "C1", "A", "T", "S1", "C2", "A", "T", "T3", "C3", "A", "T", "S2", "C4", "A", "T"]
];
$number = $teacher->info('sutri') === "" ? 0 : 1;
$pdf = new PDF();
$pdf->footer = false;
$pdf->SetLeftMargin(5);
$pdf->AddPage();
$pdf->SetTitle('Finales');
$pdf->Fill();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0,5,"Finales",0,1,'C');
$pdf->Ln(3);
$pdf->Cell(50, 5, 'Profesor', 1, 0, 'C', true);
$pdf->Cell(18, 5, 'Curso', 1, 0, 'C', true);
$pdf->Cell(40, 5, utf8_decode('Descripción'), 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Creditos', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'Total Est.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'Fecha', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
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
foreach ($titles[$number] as $title) {
    $pdf->Cell(6, 5, $title, 1, 0, 'C', true);
}
$pdf->Cell(10, 5, 'Fin', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);
foreach ($students as $student) {
    $pdf->Cell(45, 5, utf8_decode($student->apellidos), 1);
    $pdf->Cell(35, 5, utf8_decode($student->nombre), 1);

    foreach ($columns[$number] as $index => $column) {
        if (sizeof($columns[$number]) === $index + 1) {
            $pdf->Cell(10, 5, $student->{$column}, 1, 1, 'C');
        } else {
            $pdf->Cell(6, 5, $student->{$column}, 1, 0, 'C');
        }
    }
}


$pdf->Output();
