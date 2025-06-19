<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['LISTA DE TOTALES POR MES ', 'LIST OF TOTALS PER MONTH '],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['APELLIDOS', 'LAST NAME'],
    ['DEUDAS', 'DEBTS'],
    ['BALANCES', 'BALANCES'],
    ['Pagina ', 'Page '],
    ['PAGOS', 'PAYMENTS'],
    ['DESDE', 'FROM'],
    ['NOMBRE', 'NAME'],
    ['GRAN TOTAL:', 'GRAND TOTAL:'],
    ['CTA', 'ACC'],
    ['LISTA DE PAGOS', 'PAYMENT LIST'],
    ['FECHA', 'DATE'],
    ['COMENTARIO', 'COMMENT'],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
]);

$school = new School(Session::id());
$year = $school->info('year2');
$grades = $school->allGrades();

class nPDF extends PDF
{

    function Header()
    {
        global $year;
        parent::header();
        global $lang;

        $sp = 80;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 5, $lang->translation('LISTA DE PAGOS') . ' ' . $year, 0, 0, 'C');
        $this->Ln(10);
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 5, $_POST['ft1'] . ' / ' . $_POST['ft2'], 0, 1, 'C');
        $this->Ln(4);
        list($code, $desc) = explode(", ", $_POST['desc']);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(230);
        $this->Cell(30, 5, $desc, 0, 1, 'L');
        $this->Cell(6, 5, '', 1, 0, 'C', true);
        $this->Cell(12, 5, 'ID', 1, 0, 'C', true);
        $this->Cell(53, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
        $this->Cell(40, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(21, 5, $lang->translation('PAGOS'), 1, 0, 'C', true);
        $this->Cell(16, 5, $lang->translation('FECHA'), 1, 0, 'C', true);
        $this->Cell(43, 5, $lang->translation('COMENTARIO'), 1, 1, 'C', true);
    }

    function Footer()
    {

        global $lang;
        //Posici&oacute;n: a 1,5 cm del final
        $this->SetY(-15);

        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }

    function foo($bb)
    {
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(80);
        $this->Cell(30, 5, $bb, 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(2, 5, '', 0, 0, 'C');
    }
}
$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', '', 10);

list($code, $desc) = explode(", ", $_POST['desc']);

$a = 0;
if ($_POST['gra'] == 'Todos') {
    $result7 = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('grado, apellidos, nombre')->get();
} else {
    $result7 = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("grado='" . $_POST['gra'] . "' and year='$year' and activo !='B'")->orderBy('grado, apellidos, nombre')->get();
}

foreach ($result7 as $row7) {
    $row8 = DB::table('year')
        ->whereRaw("year = '$year' and id='$row7->id' and ss='$row7->ss'")->orderBy('id')->first();

    $resultad2 = DB::table('pagos')
        ->whereRaw(" year='$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' and fecha_d >= '" . $_POST['ft1'] . "' and fecha_d <= '" . $_POST['ft2'] . "'")->get();
    $pago = 0;
    $deuda = 0;
    $fp = '';
    foreach ($resultad2 as $row9) {
        $pago = $pago + $row9->pago;
        $deuda = $deuda + $row9->deuda;
        //            $resultad21 = DB::table('pagos')
        //                 ->whereRaw(" year='$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' and fecha_d = '$row9->fecha_d'")->get();
        //            foreach ($resultad21 as $row91)
        //                    {
        //                    $deuda=$deuda+$row91->deuda;
        //                    }

        if ($row9->pago > 0) {
            $fp = $row9->fecha_d;
        }
    }
    if ($deuda > 0) {
        $pag = 'No';
        $a = $a + 1;
        if ($deuda == $pago) {
            $pag = 'Si';
        }
        $pdf->Cell(6, 5, $a, 0, 0, 'R');
        $pdf->Cell(12, 5, $row8->id, 0, 0, 'R');
        $pdf->Cell(53, 5, $row8->apellidos, 0, 0, 'L');
        $pdf->Cell(40, 5, $row8->nombre, 0, 0, 'L');
        $pdf->Cell(21, 5, $pag, 0, 0, 'C');
        $pdf->Cell(16, 5, $fp, 0, 0, 'C');
        $pdf->Cell(43, 5, '________________________', 0, 1, 'L');
    }
}

$pdf->Output();
