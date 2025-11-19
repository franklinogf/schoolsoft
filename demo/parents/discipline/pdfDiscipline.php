<?php

require_once __DIR__ . '/../../app.php';

use App\Models\Admin;
use Classes\PDF;
use Classes\Lang;
use Classes\Util;
use Classes\Session;
use Classes\DataBase\DB;
use App\Models\Student;
use Classes\Route;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();
$studentSS = $_POST['studentSS'];
$student = Student::bySS($studentSS)->first();


$school = Admin::primaryAdmin();
$year = $school->year;


$lang = new Lang([
    ['Lista de disciplinas aplicadas', 'Applied Discipline List'],
    ['Nombre', 'Name'],
    ['Fecha', 'Date'],
    ['Titulo', 'Title'],
    ['Cantidad', 'Amount'],
    ['Tallies', 'Tallies'],
    ['Méritos', 'Mertis'],
    ['Deméritos', 'Demerits'],
]);

Manager::table('acuse')->insert([
    'id' => $student->id,
    'ss' => $studentSS,
    'grado' => $student->grado,
    'year' => $year,
    'ip' => Util::getIp(),
    'fecha' => Util::date(),
    'hora' => Util::time(),
    'tri' => '',
    'cn' => '',
    'tra' => '',
    'tri2' => '',
    'fra' => '',
    'hoja' => '5'
]);
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de disciplinas aplicadas"));
$pdf->Fill();
$pdf->SetFont('Arial', 'B', 12);
$pdf->addPage();

$pdf->Cell(0, 5, $lang->translation("Lista de disciplinas aplicadas"), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $student->nombre . ' ' . $student->apellidos, 1, 0);
$pdf->Cell(30, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
$pdf->Cell(40, 5, date('m-d-Y'), 1, 1);

$pdf->Ln(5);
$pdf->Cell(100, 5, $lang->translation("Titulo"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
$pdf->Cell(40, 5, $lang->translation("Cantidad"), 1, 1, 'C', true);
$memos = DB::table("memos")->where([
    ['ss', $student->ss],
    ['year', $year]
])->get();
$amounts = [];
foreach ($memos as $memo) {
    $pdf->Cell(100, 5, $memo->titulo, 1);
    $pdf->Cell(40, 5, $memo->fecha, 1, 0, 'C');
    $pdf->Cell(40, 5, $memo->demeritos, 1, 1, 'C');
    if (strpos($memo->titulo, "Merits") > -1) {
        $amounts['merits'] += $memo->demeritos;
    } elseif (strpos($memo->titulo, "Tallies") > -1) {
        $amounts['tallies'] += $memo->demeritos;
    } else {
        $amounts['demertis'] += $memo->demeritos;
    }
}
$pdf->Ln(5);

$pdf->Cell(40, 5, 'Tallies', 1, 0, 'L', true);
$pdf->Cell(20, 5, $amounts['tallies'] ?? null, 1, 1, 'R');
$pdf->Cell(40, 5, 'Merits', 1, 0, 'L', true);
$pdf->Cell(20, 5, $amounts['merits'] ?? null, 1, 1, 'R');
$pdf->Cell(40, 5, 'Demerits', 1, 0, 'L', true);
$pdf->Cell(20, 5, $amounts['demertis'] ?? null, 1, 1, 'R');


$pdf->Output();
