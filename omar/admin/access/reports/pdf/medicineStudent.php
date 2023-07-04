<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Medicamentos / Recetas", "Medicine / Prescription"],
    ["Maestro(a):", "Teacher:"],
    ["Grado", "Grade"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Medicina', 'Medicine'],
    ['Recetas', 'Prescription'],

]);

$school = new School();
//$teacherClass = new Teacher();
//$studentClass = new Student();

$year = $school->info('year');
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Medicamentos / Recetas"). " $year", true);
$pdf->Fill();
$pdf->AddPage('L');
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['med1', '!=' , '']
])->orWhere([
    ['rec1','!=','']
])->orderBy('apellidos')->get();

    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Medicamentos / Recetas") . " $year", 0, 1, 'C');
    $pdf->Ln(7);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(13, 5, $lang->translation("Grado"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Medicina"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Recetas"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    foreach ($students as $student) {
        $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
        $pdf->Cell(17, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(60, 5, $student->apellidos, 1);
        $pdf->Cell(50, 5, $student->nombre, 1, 0);
        $pdf->Cell(13, 5, $student->grado, 1, 0);

        $pdf->Cell(65, 5, $student->med1, 1, 0);
        $pdf->Cell(65, 5, $student->rec1, 1, 1);
        if ($student->med2 !='' or $student->rec2 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->med2, 1, 0);
           $pdf->Cell(65, 5, $student->rec2, 1, 1);
           }
        if ($student->med3 !='' or $student->rec3 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->med3, 1, 0);
           $pdf->Cell(65, 5, $student->rec3, 1, 1);
           }
        if ($student->med4 !='' or $student->rec4 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->med4, 1, 0);
           $pdf->Cell(65, 5, $student->rec4, 1, 1);
           }
        

    }



$pdf->Output();
