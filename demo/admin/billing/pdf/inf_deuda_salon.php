<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Lista de Deudores por Salón Hogar", "List of Debtors by Home Room"],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['PAGOS', 'PAYS'],
    ['DEUDAS', 'DEBTS'],
    ['BALANCES', 'BALANCES'],
    ['Pagina ', 'Page '],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['GRAN TOTAL:', 'GRAND TOTAL:'],
    ['COMENTARIO', 'COMMENT'],
    ['APELLIDOS', 'SURNAME'],
    ['NOMBRE', 'NAME'],
    ['Grado: ', 'Grade: '],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
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
        $this->Cell(30, 10, $lang->translation('Lista de Deudores por Salón Hogar') . ' ' . $year, 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->SetTitle($lang->translation('Lista de Deudores por Salón Hogar') . ' ' . $year);
$pdf->AliasNbPages();
$gra = '';
$a = 0;
$mes = $_POST['mes'];
list($y1, $y2) = explode("-", $year);
if ($mes >= '08') {
    $y3 = '20' . $y1 . '-' . $mes . '-01';
} else {
    $y3 = '20' . $y2 . '-' . $mes . '-01';
}
$result = DB::table('year')->where('year', $year)->orderBy('grado, apellidos, nombre DESC')->get();
$cod = $_POST['desc'];
foreach ($result as $row1) {
    if ($cod == 'Todos') {
        $result2 = DB::table('pagos')->whereRaw("fecha_d <= '$y3' and id = $row1->id and year='$year' and baja = ''")->get();
    } else {
        $result2 = DB::table('pagos')->whereRaw("codigo='$cod' and fecha_d <= '$y3' and id = $row1->id and year='$year' and baja = ''")->get();
    }
    $deu = 0;
    $pag = 0;
    $det2 = 0;
    foreach ($result2 as $row2) {
        if ($row2->deuda <> 0) {
            $deu = $deu + $row2->deuda;
        }
        if ($row2->pago <> 0) {
            $pag = $pag + $row2->pago;
        }
        if ($row2->pago <> 0) {
            $det2 = $det2 - $row2->pago;
        }
        if ($row2->deuda <> 0) {
            $det2 = $det2 + $row2->deuda;
        }
    }
    $det = $deu - $pag;
    if ($deu > $pag and $det2 > 10) {
        if ($gra != $row1->grado) {
            $gra = $row1->grado;
            $pdf->AddPage();
            $pdf->Cell(80);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(30, 5, $lang->translation("Grado: ") . $row1->grado, 0, 1, 'C');
            $pdf->Cell(15, 5, '', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(12, 5, '', 1, 0, 'C', true);
            $pdf->Cell(15, 5, $lang->translation('CTA'), 1, 0, 'C', true);
            $pdf->Cell(60, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
            $pdf->Cell(45, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
            $pdf->Cell(50, 5, $lang->translation('COMENTARIO'), 1, 1, 'C', true);
            $a = 0;
        }

        $a = $a + 1;
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(12, 5, $a, 0, 0, 'C');
        $pdf->Cell(15, 5, $row1->id, 0, 0, 'L');
        $pdf->Cell(60, 5, $row1->apellidos, 0, 0, 'L');
        $pdf->Cell(45, 5, $row1->nombre, 0, 0, 'L');
        $pdf->Cell(50, 5, '', 'B', 1, 'L');
    }
}
$pdf->Output();
