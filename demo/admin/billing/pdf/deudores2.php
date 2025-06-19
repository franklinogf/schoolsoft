<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Lista de deudores 30, 60, 90 ", "List of debtors 30, 60, 90 "],
    ['NOMBRE ESTUDIANTES', 'STUDENT NAMES'],
    ['Pagina ', 'Page '],
    ['Familia', 'Family'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
    ['Deudas', 'Debts'],
    ['Pagos', 'Payments'],
    ['Balances', 'Balances'],
    ['LISTA DE DEUDORES ', 'LIST OF DEBTORS '],
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
        global $lang;
        parent::header();
        $this->SetFont('Arial', 'B', 15);
        $sp = 120;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 10, $lang->translation('LISTA DE DEUDORES ') . $year, 0, 1, 'C');
        $this->Ln(6);
        $this->Cell(5, 5, '    ', 0, 0, 'C');
        $this->Cell(12, 5, '', 1, 0, 'C', true);
        $this->Cell(22, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(70, 5, $lang->translation('Familia'), 1, 0, 'C', true);
        $this->Cell(20, 5, utf8_encode($lang->translation('Código')), 1, 0, 'C', true);
        $this->Cell(70, 5, utf8_encode($lang->translation('Descripción')), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('Deuda'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('Pagos'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('Balance'), 1, 1, 'C', true);
    }

    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}
$pdf = new nPDF("P");
$pdf->SetTitle($lang->translation('Lista de deudores 30, 60, 90 ') . '/ ' . $year);
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage('L');
$pdf->SetFont('Times', '', 11);
$result = DB::table('year')->select("DISTINCT id")
    ->whereRaw("year='$year' and activo=''")->orderBy('apellidos, nombre')->get();
$nue = 0;
$nuet = 0;
$a = 0;
$m = 0;
$f = 0;
$mt = 0;
$ft = 0;
$te = 0;
$gte = 0;
$aa = 1;
$deug = 0;
$pagg = 0;
$a = 0;
foreach ($result as $row) {
    $deut = 0;
    $pagt = 0;
    $reg = DB::table('year')
        ->whereRaw("year='$year' and id='$row->id'")->orderBy('apellidos, nombre')->first();
    if ($_POST['cods'] == 'A') {
        $result2 = DB::table('presupuesto')->where([
            ['year', $year]
        ])->orderBy('codigo')->get();
    } else {
        $result2 = DB::table('presupuesto')->where([
            ['year', $year],
            ['codigo', $_POST['cods']]
        ])->orderBy('codigo')->get();
    }
    foreach ($result2 as $row2) {
        $deu = 0;
        $pag = 0;
        $result3 = DB::table('pagos')->whereRaw("id='$row->id' and codigo='$row2->codigo' and year='$year' and baja=''")->get();
        foreach ($result3 as $row3) {
            $deu = $deu + $row3->deuda;
            $pag = $pag + $row3->pago;
        }
        if ($deu - $pag > 0) {
            $pdf->Cell(5, 5, '', 0, 0, 'R');
            if ($a == 0) {
                $pdf->Cell(12, 5, $aa, 0, 0, 'R');
            } else {
                $pdf->Cell(12, 5, '', 0, 0, 'R');
            }
            $pdf->Cell(22, 5, $row->id, 0, 0, 'R');
            $pdf->Cell(70, 5, $reg->apellidos, 0, 0);
            $pdf->Cell(20, 5, $row2->codigo, 0, 0, 'R');
            $pdf->Cell(70, 5, $row2->descripcion, 0, 0);
            $pdf->Cell(25, 5, number_format($deu, 2), 0, 0, 'R');
            $pdf->Cell(25, 5, number_format($pag, 2), 0, 0, 'R');
            $pdf->Cell(25, 5, number_format($deu - $pag, 2), 0, 1, 'R');
            $deut = $deut + $deu;
            $pagt = $pagt + $pag;
            $deug = $deug + $deu;
            $pagg = $pagg + $pag;
            $a = 1;
        }
    }
    if ($deut - $pagt > 0) {
        $a = 0;
        $aa = $aa + 1;
        if ($_POST['cods'] == 'A') {
            $pdf->Cell(249, 5, 'Total: ', 0, 0, 'R');
            $pdf->Cell(25, 5, number_format($deut - $pagt, 2), 0, 1, 'R');
            $pdf->Cell(249, 5, '', 0, 1, 'R');
        }
    }
}
$pdf->Cell(249, 5, 'Gran Total: ', 0, 0, 'R');
$pdf->Cell(25, 5, number_format($deug - $pagg, 2), 0, 1, 'R');
$pdf->Output();
