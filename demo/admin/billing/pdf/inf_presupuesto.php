<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['ESTADO DE PRESUPUESTO', 'BUDGET STATEMENT'],
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
    ['', ''],
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
$grades = $school->allGrades();

class nPDF extends PDF
{

    //Cabecera de p&#65533;gina
    function Header()
    {
        global $year;
        parent::header();
        global $lang;

        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, $lang->translation('ESTADO DE PRESUPUESTO') . ' ' . $year, 0, 1, 'C');
        $this->Ln(2);
        $this->Cell(80);
        $this->Cell(30, 5, $_POST['fec1'] . ' / ' . $_POST['fec2'], 0, 1, 'C');
        $this->Ln(5);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(22, 5, $lang->translation('CODIGO'), 1, 0, 'C', true);
        $this->Cell(65, 5, $lang->translation('DESCRIPCION'), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation('DEUDAS'), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation('PAGOS'), 1, 0, 'C', true);
        $this->Cell(30, 5, $lang->translation('BALANCES'), 1, 1, 'C', true);
    }

    //Pie de p&#65533;gina
    function Footer()
    {
        global $lang;
        //Posici&oacute;n: a 1,5 cm del final
        $this->SetY(-15);

        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //N&uacute;mero de p&aacute;gina
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }
}

//Creaci&#65533;n del objeto de la clase heredada
$pdf = new nPDF();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetTitle($lang->translation('ESTADO DE PRESUPUESTO') . ' ' . $year);
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);
if ($_POST['desc'] == 'Todos') {
    $result7 = DB::table('presupuesto')->where('year', $year)->orderBy('codigo')->get();
} else {
    $r1 = $_POST['desc'];
    $result7 = DB::table('presupuesto')->whereRaw("year='$year' and codigo='$r1'")->orderBy('codigo')->get();
}
$deut = 0;
$pagt = 0;

foreach ($result7 as $row7) {
    $deu = 0;
    $deu2 = 0;
    $pag = 0;

    $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and codigo='$row7->codigo' and fecha_p >= '" . $_POST['fec1'] . "' and fecha_p <= '" . $_POST['fec2'] . "'")->get();
    foreach ($resultad2 as $row8) {
        //        $deu=$deu+$row8->deuda;
        $pag = $pag + $row8->pago;
    }
    list($y, $m, $d) = explode("-", $_POST['fec1']);
    $fec = $y . '-' . $m . '-01';
    $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and codigo='$row7->codigo' and fecha_d <= '" . $fec . "' and fecha_p <= '" . $_POST['fec2'] . "'")->get();
    foreach ($resultad2 as $row8) {
        $deu2 = $deu2 + $row8->deuda;
    }
    if ($pag > 0 or $deu2 > 0) {
        $deut = $deut + $deu2;
        $pagt = $pagt + $pag;
        $pdf->Cell(10, 5, '', 0, 0, 'C');
        $pdf->Cell(22, 5, $row7->codigo, 0, 0, 'R');
        $pdf->Cell(65, 5, $row7->descripcion, 0, 0, 'L');
        $pdf->Cell(30, 5, number_format($deu2, 2), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($pag, 2), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($deu2 - $pag, 2), 0, 1, 'R');
    }
}
$pdf->Cell(97, 5, $lang->translation('GRAN TOTAL:'), 0, 0, 'R');
$pdf->Cell(30, 5, number_format($deut, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($pagt, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($deut - $pagt, 2), 1, 1, 'R', true);
$pdf->Output();
