<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Informe de Depositos Diarios", "Daily Deposit Report"],
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
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 5, $lang->translation('Informe de Depositos Diarios') . ' ' . $year, 0, 1, 'C');
        $this->Cell(30, 5, '', 0, 1, 'C');
        $this->Cell(80);
        $t1 = $_POST['t1'] ?? '';
        $t2 = $_POST['t2'] ?? '';
        $this->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(8, 5, '#', 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('FECHA'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('HORA'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('DEPOSITOS'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('TRANS.'), 1, 1, 'C', true);
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
$pdf->SetTitle($lang->translation('Informe de Depositos Diarios') . ' ' . $year);
$pdf->SetFont('Times', '', 11);
$result = DB::table('depositos')->whereRaw("year ='$year' and fecha >= '" . $_POST['ft1'] . "' and fecha <= '" . $_POST['ft2'] . "'")
    ->orderBy('fecha, hora')->get();
$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    if ($row2->fecha == $_POST['ft1'] and $row2->hora >= $_POST['t1'] or $row2->fecha != $_POST['ft1'] and $row2->fecha != $_POST['ft2'] or $row2->fecha == $_POST['ft2'] and $row2->hora <= $_POST['t2']) {
        $aa = '';
        $row22 = DB::table('year')->where([
            ['year', $year],
            ['ss', $row2->ss]
        ])->orderBy('desc1')->first();
        $est = $est + 1;
        $tdp = $row2->tipoDePago;
        $pdf->Cell(8, 5, $est, 0, 0, 'R');
        $pdf->Cell(20, 5, $row2->id, 0, 0, 'R');
        $pdf->Cell(70, 5, $row22->apellidos . ' ' . $row22->nombre, 0, 0, 'L');
        if ($row2->tipoDePago == 'Tarjeta') {
            $tot1 = $tot1 + $row2->cantidad;
        }
        if ($row2->tipoDePago == 'ACH') {
            $tot2 = $tot2 + $row2->cantidad;
        }
        if ($row2->tipoDePago == 'Cash') {
            $tot3 = $tot3 + $row2->cantidad;
        }
        if ($tdp == 'Otros' and $row2->cantidad > 0) {
            $tot4 = $tot4 + $row2->cantidad;
        }
        if ($tdp == 'Correccion' and $row2->cantidad <> 0) {
            $tot9 = $tot9 + $row2->cantidad;
        }
        if ($tdp == 'Otros' and $row2->cantidad < 0) {
            $tot5 = $tot5 + $row2->cantidad;
        }
        if (utf8_decode($tdp) == 'Pago a través de oficina' and $row2->cantidad > 0) {
            $tot6 = $tot6 + $row2->cantidad;
        }
        if ($tdp == 'Transferencia en familia' and $row2->cantidad > 0) {
            $tot7 = $tot7 + $row2->cantidad;
            $tdp = 'Trans. fam.';
        }
        if ($tdp == 'Transferencia en familia' and $row2->cantidad < 0) {
            $tot8 = $tot8 + $row2->cantidad;
            $tdp = 'Trans. fam.';
        }
        $tot11 = $tot11 + $row2->cantidad;
        $tot0 = $tot0 + $row2->cantidad;
        $pdf->Cell(20, 5, $row2->fecha, 0, 0, 'R');
        $pdf->Cell(20, 5, $row2->hora, 0, 0, 'R');
        $pdf->Cell(25, 5, $row2->cantidad, 0, 0, 'R');
        if ($row2->tipoDePago == 'Tarjeta') {
            $tdp = utf8_encode('Tarj. Crédito');
        }
        if ($row2->tipoDePago == 'ACH') {
            $tdp = 'Cheque';
        }
        if ($row2->tipoDePago == 'Cash') {
            $tdp = 'Efectivo';
        }
        if ($row2->tipoDePago == utf8_encode('Pago a través de oficina')) {
            $tdp = 'Pago Oficina';
        }
        $pdf->Cell(25, 5, $lang->translation($tdp) . ' ' . $aa, 0, 1, 'R');
    }
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, $lang->translation('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Cheque: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
}
if ($tot3 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Efectivo: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot3, 2), 0, 1, 'R');
}
if ($tot4 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Otros Positivos: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot4, 2), 0, 1, 'R');
}
if ($tot5 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Otros Negativos: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot5, 2), 0, 1, 'R');
}
if ($tot6 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Pago Oficina: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot6, 2), 0, 1, 'R');
}
if ($tot7 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Trans. fam.: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot7 + $tot8, 2), 0, 1, 'R');
}
if ($tot9 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, $lang->translation('Correción: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot9, 2), 0, 1, 'R');
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, $lang->translation('Gran Total: '), 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');

$pdf->Output();
