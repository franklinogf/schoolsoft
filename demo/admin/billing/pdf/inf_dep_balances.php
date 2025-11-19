<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Informe balances depositados a los estudiantes", "Report balances deposited to students"],
    ['Hasta', 'Until'],
    ['CTA', 'ACC'],
    ['FECHA', 'DATE'],
    ['COSTO', 'COST'],
    ['CAMBIO', 'CHANGE'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['MENSUALIDAD', 'MONTHLY PAYMENT'],
    ['OPCIONES', 'OPTIONS'],
    ['Fechas de Pagos', 'Payment Dates'],
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
    ['COMENTARIOS', 'COMMENTS'],
    ['NOMBRE', 'NAME'],
    ['BALANCES', 'BALANCES'],
    ['Gran Total: ', 'Grand Total: '],
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
        $this->Ln(-5);
        $this->Cell(80);
        $this->Cell(30, 5, 'Informe balances depositados a los estudiantes' . ' ' . $year, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(8, 5, '#', 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation('CTA.'), 1, 0, 'C', true);
        $this->Cell(75, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(22, 5, $lang->translation('BALANCES'), 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation('COMENTARIOS'), 1, 1, 'C', true);
        $this->SetFont('Arial', '', 11);
    }
    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetTitle($lang->translation('Informe balances depositados a los estudiantes') . ' ' . $year);
$pdf->SetFont('Times', '', 11);
$result = DB::table('year')->where([
    ['year', $year],
    ['cantidad', '<>', 0]
])->orderBy('apellidos')->get();
$tot11 = 0;
$est = 0;
foreach ($result as $row2) {
    $est = $est + 1;
    $pdf->Cell(8, 5, $est, 0, 0, 'R');
    $pdf->Cell(15, 5, $row2->id, 0, 0, 'R');
    $pdf->Cell(75, 5, $row2->apellidos . ' ' . $row2->nombre, 0, 0, 'L');
    $tot11 = $tot11 + $row2->cantidad;
    $pdf->Cell(22, 5, number_format($row2->cantidad, 2), 0, 0, 'R');
    $pdf->Cell(70, 5, '', 'B', 1, 'R');
}
$pdf->Cell(25, 5, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(27, 5, $lang->translation('Gran Total: '), 0, 0, 'L');
$pdf->Cell(18, 5, number_format($tot11, 2), 0, 1, 'R');
$pdf->Output();
