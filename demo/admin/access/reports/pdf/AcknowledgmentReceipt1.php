<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Acuse de recibo', 'Acknowledgment of receipt'],
    ['Trimestres', 'Quarters'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Es la Dirección unica que asigna el internet a una PC, y queda registrada en su proveedor del Internet.', 'It is the unique address that the Internet assigns to a PC, and it is registered with your Internet provider.'],
    ['Fecha', 'Date'],
    ['Hora', 'Hour'],
    ['Tarjeta de notas', 'Note card'],
    ['Hoja de deficiencia', 'Deficiency sheet'],
    ['Hoja de progreso', 'Progress sheet'],

]);
$hoja = $_POST['hoja'];
$grade = $_POST['grade'] ?? '';
$ss = $_POST['student'];

$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Acuse de recibo") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Acuse de recibo") . " $year", 0, 1, 'C');
$pdf->Ln(5);
$title='';
if ($hoja == '0'){$title=$lang->translation("Tarjeta de notas");}
if ($hoja == '2'){$title=$lang->translation("Hoja de deficiencia");}
if ($hoja == '3'){$title=$lang->translation("Hoja de progreso");}

$pdf->Cell(0, 5, $title, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);

$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['ss', $ss],
])->orderBy('apellidos')->get();

$count = 1;
$tcount = 0;
foreach ($students as $student) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 5, $student->apellidos . ' ' . $student->nombre, 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(70, 5, $student->grado,0,1,'L');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Ln(5);
    $pdf->Cell(0, 5, '*IP = ' . utf8_encode($lang->translation("Es la Dirección unica que asigna el internet a una PC, y queda registrada en su proveedor del Internet.")), 0, 1, 'L');
        $pdf->Cell(15, 5, '', 1, 0, 'C', true);
        $pdf->Cell(43, 5, $lang->translation("Trimestres"), 1, 0, 'C', true);
        $pdf->Cell(43, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
        $pdf->Cell(43, 5, $lang->translation("Hora"), 1, 0, 'C', true);
        $pdf->Cell(43, 5, 'IP', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 10);
$acuse1 = DB::table('acuse')->where([
    ['hoja', $hoja],
    ['year', $year],
    ['ss', $student->ss],
])->orderBy('fecha')->get();

        list($yy,$mon,$day) = explode("-",$student->fecha);
        foreach ($acuse1 as $acuse2) {
             $pdf->Cell(15, 5, $count, 0, 0, 'R');
             $pdf->Cell(43, 5, $acuse2->tri2, 0, 0, 'C');
             $pdf->Cell(43, 5, $acuse2->fecha, 0, 0, 'C');
             $pdf->Cell(43, 5, $acuse2->hora, 0, 0, 'C');
             $pdf->Cell(43, 5, $acuse2->ip, 0, 1, 'C');
             $count++;
             }
}

$pdf->Output();
