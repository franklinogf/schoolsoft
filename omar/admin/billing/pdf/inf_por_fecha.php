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

        //Salto de lnea
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(120);
        $this->Cell(30, 10, $_POST['ft1'] . ' / ' . $_POST['ft2'], 0, 0, 'C');
        $this->Ln(5);
        $this->Cell(120);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, $lang->translation('LISTA DE TOTALES POR MES ') . $year, 0, 0, 'C');
        $this->Ln(7);

        list($code, $desc) = explode(", ", $_POST['desc']);
        $this->Cell(40, 5, $desc, 0, 1, 'L');
        $this->Cell(15, 5, '', 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(75, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
        $this->Cell(65, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
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
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetTitle($lang->translation('LISTA DE TOTALES POR MES ') . $year);

$pdf->SetFont('Times', '', 11);

list($code, $desc) = explode(", ", $_POST['desc']);
$result7 = DB::table('year')->select("DISTINCT id, ss, nombre, apellidos")
    ->whereRaw("year='$year' and activo !='B'")->orderBy('ID')->get();

$a = 0;
$id = '';
$deut = 0;
$pagt = 0;
foreach ($result7 as $row7) {
    $deu = 0;
    $pag = 0;
    $resultad2 = DB::table('pagos')
        ->whereRaw(" year='$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' and fecha_d >= '" . $_POST['ft1'] . "' and fecha_d <= '" . $_POST['ft2'] . "'")->get();
    foreach ($resultad2 as $row8) {
        $pag = $pag + $row8->pago;
        $deu = $deu + $row8->deuda;
        $pagt = $pagt + $row8->pago;
        $deut = $deut + $row8->deuda;
    }
    //      $resultad2 = DB::table('pagos')
    //          ->whereRaw("year='$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' and fecha_p >= '".$_POST['ft1']."' and fecha_p <= '".$_POST['ft2']."'")->get();
    //    foreach ($resultad2 as $row8)
    //            {
    //            $deu=$deu+$row8->deuda;
    //            $deut=$deut+$row8->deuda;
    //            }
    if ($deu > 0) {
        if ($id != $row7->id) {
            $a = $a + 1;
            $id = $row7->id;
            $pdf->Cell(15, 5, $a, 0, 0, 'R');
            $pdf->Cell(20, 5, $row7->id, 0, 0, 'R');
        } else {
            $pdf->Cell(15, 5, '', 0, 0, 'R');
            $pdf->Cell(20, 5, '', 0, 0, 'R');
        }
        $pdf->Cell(75, 5, $row7->apellidos, 0, 0, 'L');
        $pdf->Cell(65, 5, $row7->nombre, 0, 0, 'L');
        $pdf->Cell(30, 5, number_format($deu, 2), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($pag, 2), 0, 0, 'R');
        $pdf->Cell(30, 5, number_format($deu - $pag, 2), 0, 1, 'R');
    }
}
$pdf->Cell(110, 5, '', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(65, 5, $lang->translation('GRAN TOTAL:'), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($deut, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($pagt, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($deut - $pagt, 2), 1, 1, 'R', true);

$pdf->Output();
