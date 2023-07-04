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
    ['Lista de estudiantes nuevos', 'New students List'],
    ["Maestro(a):", "Teacher:"],
    ["Grado", "Grade"],
    ["Nombre del estudiante", "Student name"],
    ['Cuenta', 'Account'],
    ['Genero', 'Gender'],
    ['Apellidos', 'Surnames'],
    ['Nombre', 'Name'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha', 'Date'],
    ['Correo', 'E-Mail'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],

]);

$school = new School();
$year = $school->info('year');

$students = DB::table('year')->where([
    ['activo', ''],
    ['nuevo', 'Si'],
    ['year', $year]
])->orderBy('grado, apellidos')->get();

$lista = $_POST['list'];
$correo=$_POST['email'];
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de estudiantes nuevos"). " $year", true);
$pdf->Fill();
if ($lista=='L')
   {
   $pdf->AddPage();
   $pdf->SetFont('Arial', 'B', 15);
   $pdf->Cell(0, 5, $lang->translation("Lista de estudiantes nuevos") . " $year", 0, 1, 'C');
   $pdf->Ln(5);
   $pdf->SetFont('Arial', 'B', 12);
   $pdf->SetFont('Arial', 'B', 10);
   $pdf->Cell(10, 5, '', 1, 0, 'C', true);
   $pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
   $pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
   $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
   $pdf->Cell(80, 5, $lang->translation("Correo"), 1, 1, 'C', true);
   }
$grado='';
$g=0;
foreach ($students as $student) {
    $parent = DB::table('madre')->where([
        ['id', $student->id],
    ])->orderBy('id')->first();
    if ($grado != $student->grado and $lista == 'G')
       {
       $pdf->AddPage();
       $pdf->SetFont('Arial', 'B', 15);
       $pdf->Cell(0, 5, $lang->translation("Lista de estudiantes nuevos") . " $year", 0, 1, 'C');
       $pdf->Ln(5);
       $pdf->SetFont('Arial', 'B', 10);
       $pdf->Cell(10, 5, '', 1, 0, 'C', true);
       $pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
       $pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
       $pdf->Cell(15, 5, $lang->translation("Grado"), 1, 0, 'C', true);
       $pdf->Cell(80, 5, $lang->translation("Correo"), 1, 1, 'C', true);
       $grado=$student->grado;
       }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 5, $count + 1, 1, 0, 'C');
    $pdf->Cell(50, 5, $student->apellidos, 1);
    $pdf->Cell(40, 5, $student->nombre, 1, 0);
    $pdf->Cell(15, 5, $student->grado, 1, 0, 'C');
    if ($correo=='S')
       {
       $pdf->Cell(80, 5, $parent->email_m, 1, 1, 'C');
       }
    else
       {
       $pdf->Cell(80, 5, '', 1, 1, 'C');
       }
    $count++;
}


$pdf->Output();
