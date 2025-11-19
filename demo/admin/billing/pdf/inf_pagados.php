<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['LISTA DE NO DEUDORES', 'LIST OF NON-DEBTORS'],
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
    ['Código ', 'Code '],
    ['APELLIDOS', 'LAST NAME'],
    ['NOMBRE', 'NAME'],
    ['GRADO', 'GRADE'],
    ['CTA', 'ACC'],
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

        $code = $_POST['desc'];
        $result7 = DB::table('presupuesto')->whereRaw("year='$year' and codigo='$code'")->orderBy('codigo')->first();
        $desc = $result7->descripcion ?? '';
        if ($code == 'Todos') {
            $desc = 'Todos';
        }
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_encode('Código ') . $desc, 0, 0, 'C');
        $this->Ln(10);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 5, 'LISTA DE NO DEUDORES' . ' ' . $year, 0, 0, 'C');
        $this->Ln(8);
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
$pdf->SetTitle($lang->translation('LISTA DE NO DEUDORES') . ' ' . $year);
$pdf->AliasNbPages();
if ($_POST['lg'] == '1') {
    $pdf->AddPage('');
}

$pdf->SetFont('Times', '', 11);
$code = $_POST['desc'];
$desc = '';
if ($code == 'Todos') {
    $desc = 'A';
}

$result7 = DB::table('year')->select("DISTINCT id, ss, nombre, apellidos, grado")->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('grado, apellidos, nombre')->get();
$a = 0;
$id = '';
$deut = 0;
$pagt = 0;
$gr1 = '';
$saldo = '';
list($c1, $c2) = explode("-", $year);
if ($_POST['saldo'] == 1) {
    $saldo = "and fecha_d < '20" . $c1 . "-12-20'";
}
if ($_POST['saldo'] == 2) {
    $saldo = "and fecha_d < '20" . $c2 . "-05-20'";
}
foreach ($result7 as $row7) {
    $deu = 0;
    $pag = 0;
    if ($desc == 'A') {
        $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and id='$row7->id' and ss='$row7->ss' $saldo")->get();
    } else {
        $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' $saldo")->get();
    }
    foreach ($resultad2 as $row8) {
        $pag = $pag + $row8->pago;
        $pagt = $pagt + $row8->pago;
    }
    if ($desc == 'A') {
        $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and id='$row7->id' and ss='$row7->ss' $saldo")->get();
    } else {
        $resultad2 = DB::table('pagos')->whereRaw("baja='' and year = '$year' and id='$row7->id' and ss='$row7->ss' and codigo='$code' $saldo")->get();
    }
    foreach ($resultad2 as $row8) {
        $deu = $deu + $row8->deuda;
        $deut = $deut + $row8->deuda;
    }
    if ($deu == $pag and $deu > 0) {
        if ($_POST['lg'] == '1' and ($a == 0 or $a == 40 or $a == 80)) {
            $gr1 = $row7->grado;
            $pdf->Cell(15, 5, '', 1, 0, 'C', true);
            $pdf->Cell(20, 5, $lang->translation('CTA'), 1, 0, 'C', true);
            $pdf->Cell(70, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
            $pdf->Cell(60, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
            $pdf->Cell(17, 5, $lang->translation('GRADO'), 1, 1, 'C', true);
        }

        if ($gr1 != $row7->grado and $_POST['lg'] == '2') {
            $gr1 = $row7->grado;
            $pdf->AddPage('');
            $pdf->Cell(15, 5, '', 1, 0, 'C', true);
            $pdf->Cell(20, 5, $lang->translation('CTA'), 1, 0, 'C', true);
            $pdf->Cell(70, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
            $pdf->Cell(60, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
            $pdf->Cell(17, 5, $lang->translation('GRADO'), 1, 1, 'C', true);
        }
        if ($id != $row7->id) {
            $a = $a + 1;
            $id = $row7->id;
            $pdf->Cell(15, 5, $a, 0, 0, 'R');
            $pdf->Cell(20, 5, $row7->id, 0, 0, 'R');
        } else {
            $pdf->Cell(15, 5, '', 0, 0, 'R');
            $pdf->Cell(20, 5, '', 0, 0, 'R');
        }
        $pdf->Cell(70, 5, $row7->apellidos, 0, 0, 'L');
        $pdf->Cell(60, 5, $row7->nombre, 0, 0, 'L');
        $pdf->Cell(17, 5, $row7->grado, 0, 1, 'L');
    }
}
$pdf->Output();
