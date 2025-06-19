<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Lista de Estudios Supervisados ", "List of Supervised Studies "],
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
        $this->Cell(0, 5, $lang->translation('Lista de Estudios Supervisados ') . $year, 0, 1, 'C');
        $this->Ln(5);
    }
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->SetTitle($lang->translation('Lista de Estudios Supervisados ') . $year);
$students2 = DB::table('year')->select("DISTINCT grado")->where([
    ['year', $year],
    ['pago_e_s', 'OK']
])->orderBy('grado')->get();

$gt = 0;
$m = 0;
foreach ($students2 as $student2) {
    $m = $m + 1;
    $pdf->AddPage();
    $students = DB::table('year')->where([
        ['year', $year],
        ['grado', $student2->grado],
        ['pago_e_s', 'OK'],
    ])->orderBy('apellidos, horam DESC')->get();
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(80, 5, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'Grado', 1, 0, 'C', true);
    $pdf->Cell(60, 5, 'Comentarios', 1, 1, 'C', true);
    $pdf->SetFont('Times', '', 10);
    $count = 1;
    $t = 0;
    foreach ($students as $student) {
        $pdf->Cell(10, 5, $count, 'B', 0, 'R');
        $pdf->Cell(15, 5, $student->id, 'B', 0, 'C');
        $pdf->Cell(80, 5, "$student->apellidos $student->nombre", 'B');
        $pdf->Cell(15, 5, $student->grado, 'B', 0, 'C');
        $pdf->Cell(60, 5, '', 'B', 1, 'R');
        $count++;
    }
}
if ($m == 0) {
    $pdf->AddPage();
}

$pdf->Output();
