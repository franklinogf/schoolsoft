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
    ["Condiciones / Alergias", "Conditions / Allergy"],
    ["Maestro(a):", "Teacher:"],
    ["Grado", "Grade"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Condiciones', 'Conditions'],
    ['Alergias', 'Allergy'],

]);

$school = new School(Session::id());
$year = $school->info('year2');
$count = 0;

$pdf = new PDF();
$pdf->SetTitle($lang->translation("Condiciones / Alergias"). " $year", true);
$pdf->Fill();
$pdf->AddPage('L');
$students = DB::table('year')->whereRaw("year='$year' and activo='' and (enf1 != '' or enf1 != '')")->orderBy('apellidos')->get();

    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Condiciones / Alergias") . " $year", 0, 1, 'C');
    $pdf->Ln(7);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Cuenta"), 1, 0, 'C', true);
    $pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(13, 5, $lang->translation("Grado"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Condiciones"), 1, 0, 'C', true);
    $pdf->Cell(65, 5, $lang->translation("Alergias"), 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 9);

    foreach ($students as $student) {
    $count = $count + 1;
    $pdf->Cell(10, 5, $count, 1, 0, 'C');
        $pdf->Cell(17, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(60, 5, $student->apellidos, 1);
        $pdf->Cell(50, 5, $student->nombre, 1, 0);
        $pdf->Cell(13, 5, $student->grado, 1, 0);

        $pdf->Cell(65, 5, $student->imp1, 1, 0);
        $pdf->Cell(65, 5, $student->enf1, 1, 1);
        if ($student->imp2 !='' or $student->enf2 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->imp2, 1, 0);
           $pdf->Cell(65, 5, $student->enf2, 1, 1);
           }
        if ($student->imp3 !='' or $student->enf3 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->imp3, 1, 0);
           $pdf->Cell(65, 5, $student->enf3, 1, 1);
           }
        if ($student->imp4 !='' or $student->enf4 !='')
           {
           $pdf->Cell(150, 5, '', 0, 0, 'C');
           $pdf->Cell(65, 5, $student->imp4, 1, 0);
           $pdf->Cell(65, 5, $student->enf4, 1, 1);
           }
        

    }



$pdf->Output();
