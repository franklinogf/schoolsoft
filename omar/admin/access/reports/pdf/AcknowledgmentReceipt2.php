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
    ['Fecha', 'Date'],
    ['Si', 'Yes'],
    ['Es la Dirección unica que asigna el internet a una PC, y queda registrada en su proveedor del Internet.', 'It is the unique address that the Internet assigns to a PC, and it is registered with your Internet provider.'],
    ['Apellidos', 'Surnames'],
    ['Las vio', 'He saw them'],
    ['Tarjeta de notas', 'Note card'],
    ['Hoja de deficiencia', 'Deficiency sheet'],
    ['Hoja de progreso', 'Progress sheet'],

]);
$hoja = $_POST['hoja'];
$grade = $_POST['grade'];
$trimestre = $_POST['trimestre'];

$school = new School();
$year = $school->info('year');
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
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 5, $grade,0,1,'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, $lang->translation("Trimestres"), 1, 0, 'C', true);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(70, 5, $trimestre,0,1,'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(5);
$pdf->Cell(15, 5, '', 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Las vio"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
list($tri1,$tri4) = explode(" ",$trimestre);
$tri3 = 'Trimestre-'.$tri4;
$students = DB::table('year')->where([
    ['activo', ''],
    ['year', $year],
    ['grado', $grade],
])->orderBy('apellidos')->get();

$count = 1;
$tcount = 0;
foreach ($students as $student) {
        $pdf->SetFont('Arial', '', 10);
$acuse1 = DB::table('acuse')->where([
    ['tri2', $tri3],
    ['hoja', $hoja],
    ['year', $year],
    ['ss', $student->ss],
])->orderBy('fecha')->get();
        $pdf->Cell(15, 5, $count, 0, 0, 'R');
        $pdf->Cell(60, 5, $student->apellidos, 0, 0, 'L');
        $pdf->Cell(50, 5, $student->nombre, 0, 0, 'L');
        $lv = 'No';
        $fe = '';
        foreach ($acuse1 as $acuse2) {
                $lv=$title=$lang->translation("Si");
                $fe = $acuse2->fecha;
                }
        $pdf->Cell(30, 5, $lv, 0, 0, 'C');
        $pdf->Cell(30, 5, $fe, 0, 1, 'C');
        $count++;
}

$pdf->Output();
