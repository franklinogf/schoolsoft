<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$grade = $teacher->grado;
$students = new Student();
$students = $students->findByGrade($grade);
$_students = [];
foreach ($students as $student) {
    $_students[$student->ss]['fullName'] = "$student->nombre $student->apellidos";
    $_students[$student->ss]['ss'] = $student->ss;
}
foreach ($_students as $student) {
    $amount = 0;
    for ($i = 1; $i <= 6; $i++) {
        $table = $i == 1 ? 'padres' : "padres$i";
        $fatherTable = DB::table($table)->select("not{$i} as nota")->Where([
            ['ss', $student['ss']],
            ['year',$year],
            ['grado',$grade]
        ])->get();
        foreach ($fatherTable as $father) {
            if($father->nota >= 100) $amount++;
        }
        $_students[$student['ss']]['cantidad'] = $amount;
    }
}

$pdf=new PDF();
$pdf->addPage();
$pdf->Fill();
$pdf->SetFont('Times','B',12);
$pdf->Cell(0, 5, utf8_decode('Listado de 100 o más'), 0, 1, 'C');
$pdf->Ln(5);
$pdf->Cell(0, 5, utf8_decode("GRADO $teacher->grado / AÑO $year"), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',12);
$pdf->Cell(30);
$pdf->Cell(100, 5, 'Estudiante', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Cantidad', 1, 1, 'C', true);

foreach ($_students as $student) {
    $pdf->Cell(30);
    $pdf->Cell(100, 5, $student['fullName'], 1);
    $pdf->Cell(30, 5, $student['cantidad'], 1, 1, 'C');
}
$pdf->Output();
