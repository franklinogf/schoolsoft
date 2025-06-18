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
    ['Lista por grado de Primera Comunión ', 'List by grade of First Communion '],
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
        $this->Cell(0, 5, utf8_encode($lang->translation('Lista por grado de Primera Comunión ')) . $year, 0, 1, 'C');
        $this->Ln(5);
    }
}
$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->SetTitle(utf8_encode($lang->translation('Lista por grado de Primera Comunión ')) . $year);
$students2 = DB::table('year')->select("DISTINCT grado")->where([
    ['year', $year],
    ['pago_p_c', 'OK']
])->orderBy('grado')->get();

$gt = 0;
$m = 0;
foreach ($students2 as $student2) {
    $m = $m + 1;
    $pdf->AddPage();
    $students = DB::table('year')->where([
        ['year', $year],
        ['grado', $student2->grado],
        ['pago_p_c', 'OK'],
    ])->orderBy('apellidos, horam DESC')->get();
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(70, 5, 'Nombre', 1, 0, 'C', true);
    $pdf->Cell(14, 5, 'Grado', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Cantidad', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Comentarios', 1, 1, 'C', true);
    $pdf->SetFont('Times', '', 10);
    $count = 1;
    $t = 0;
    foreach ($students as $student) {
        $pdf->Cell(10, 5, $count, 1, 0, 'R');
        $pdf->Cell(14, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(70, 5, "$student->apellidos $student->nombre", 1, 0);
        $pdf->Cell(14, 5, $student->grado, 1, 0, 'C');
        $pdf->Cell(20, 5, '$40.00', 1, 0, 'C');
        $pdf->Cell(20, 5, $student->p_c_fecha, 1, 0, 'C');
        $pdf->Cell(40, 5, '', 1, 1, 'R');
        $count++;
        $t = $t + 40.00;
        $gt = $gt + 40.00;
    }
    $pdf->Cell(108, 5, 'Total: ', 1, 0, 'R');
    $pdf->Cell(20, 5, '$' . number_format($t, 2), 1, 1, 'R');
}
if ($m == 0) {
    $pdf->AddPage();
}
$pdf->Cell(20, 5, '', 0, 1, 'C');
$pdf->Cell(108, 5, 'Gran Total: ', 1, 0, 'R');
$pdf->Cell(20, 5, '$' . number_format($gt, 2), 1, 1, 'R');
$pdf->Output();
