<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Matrículas Estudios Supervisados ", "Enrollments Supervised Studies "],
    ['Cantidad', 'Amount'],
    ['Nombre', 'Name'],
    ['Fecha', 'Date'],
    ['Grado', 'Grade'],
    ['Gran Total: ', 'Grand Total: '],
    ['Tipo de Fechas', 'Type of Dates'],
    ['MENSUALIDAD', 'MONTHLY PAYMENT'],
    ['OPCIONES', 'OPTIONS'],
    [' al ', ' to '],
    ['Mes', 'Month'],
    ['Opciones', 'Options'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['NOMBRE', 'NAME'],
    ['CANTIDAD', 'AMOUNT'],
    ['GRADO', 'GRADE'],
    ['APELLIDOS', 'LAST NAME'],
    [' Mes de ', ' Month of '],
    ['Marculinos', 'Male'],
    ['DEPOSITOS', 'DEPOSITS'],
    ['HORA', 'TIME'],
    ['TRANS.', 'TRANS.'],
]);

$school = new School(Session::id());
$year = $school->info('year2');
class nPDF extends PDF
{
    function Header()
    {
        global $year;
        parent::header();
        global $lang;
        global $colegio;
        list($y1, $y2) = explode("-", $year);
        $y3 = $y1 + 2;
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'Matriculas Estudios Supervisados ' . $year . $lang->translation(' al ') . $y2 . '-' . $y3, 0, 1, 'C');
        $this->Ln(5);
    }
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->SetTitle($lang->translation('Matriculas Estudios Supervisados ') . $year);
$m = 0;

$students2 = DB::table('year')->select("DISTINCT fecha_pago_e_s")->where([
    ['year', $year],
    ['pago_e_s', 'OK']
])->orderBy('fecha_pago_e_s')->get();

$gt = 0;
foreach ($students2 as $student2) {
    $m = $m + 1;
    $pdf->AddPage();
    $students = DB::table('year')->select("DISTINCT fecha_pago_e_s")->where([
        ['fecha_pago_e_s', $student2->fecha_pago_e_s],
        ['pago_e_s', 'OK']
    ])->orderBy('apellidos, horam DESC')->get();

    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(80, 5, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'Grado', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Cantidad', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Hora', 1, 1, 'C', true);
    $pdf->SetFont('Times', '', 10);
    $count = 1;
    $t = 0;
    foreach ($students as $student) {
        $pdf->Cell(10, 5, $count, 1, 0, 'R');
        $pdf->Cell(15, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(80, 5, "$student->apellidos $student->nombre", 1);
        $pdf->Cell(15, 5, $student->grado, 1, 0, 'C');
        $pdf->Cell(20, 5, '$50.00', 1, 0, 'R');
        $pdf->Cell(20, 5, $student->fecha_pago_e_s, 1, 0, 'C');
        $pdf->Cell(20, 5, $student->hora_pago_e_s, 1, 1, 'C');
        $count++;
        //   $t=$t+$student->tmat;
        $t = $t + 50.00;
        //   $gt=$gt+$student->tmat;
        $gt = $gt + 50.00;
    }
    $pdf->Cell(120, 5, 'Total: ', 1, 0, 'R');
    $pdf->Cell(20, 5, '$' . number_format($t, 2), 1, 1, 'R');
}
if ($m == 0) {
    $pdf->AddPage();
}
$pdf->Cell(120, 5, 'Gran Total: ', 1, 0, 'R');
$pdf->Cell(20, 5, '$' . number_format($gt, 2), 1, 1, 'R');

$pdf->Output();
