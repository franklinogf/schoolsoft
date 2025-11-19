<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Informe por deudas por familias", "Debt report for families"],
    ['Hasta', 'Until'],
    ['CTA', 'ACC'],
    ['Todos', 'All'],
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
    ['Femeninas,', 'Female'],
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
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 10, 'Informe por deudas por familias' . ' ' . $year, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(8, 5, '', 1, 0, 'C', true);
        $this->Cell(12, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(35, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(50, 5, $lang->translation('APELLIDOS'), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation('GRADO'), 1, 0, 'C', true);
        $this->Cell(40, 5, $lang->translation('Mensualidad'), 1, 0, 'C', true);
        $this->Cell(14, 5, $lang->translation('COSTO'), 1, 0, 'C', true);
        $this->Cell(16, 5, $lang->translation('CAMBIO'), 1, 1, 'C', true);
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
$pdf->SetTitle($lang->translation('Informe por deudas por familias') . ' ' . $year);
$pdf->SetFont('Times', '', 11);
$result7 = DB::table('year')->select("DISTINCT id")->where([
    ['activo', ''],
    ['year', $year]
])->orderBy('CAST(id as SIGNED INTEGER) ASC')->get();
$a = 0;
$b = 0;
foreach ($result7 as $row7) {
    $a = $a + 1;
    $x = 1;
    $pdf->Cell(8, 5, $a, 0, 0, 'C');

    $resultad2 = DB::table('year')->where([
        ['year', $year],
        ['activo', ''],
        ['id', $row7->id]
    ])->orderBy('id')->get();

    foreach ($resultad2 as $row8) {
        $b = $b + 1;
        if ($b > 40) {
            $b = 0;
            $pdf->AddPage();
        }
        if ($x == 0) {
            $pdf->Cell(8, 5, '', 0, 0, 'C');
        }
        $x = 0;
        $pdf->Cell(12, 5, $row8->id, 0, 0, 'R');
        $pdf->Cell(35, 5, $row8->nombre, 0, 0);
        $pdf->Cell(50, 5, $row8->apellidos, 0, 0);
        $pdf->Cell(15, 5, $row8->grado, 0, 0, 'C');
        $row9 = DB::table('pagos')->whereRaw(" ss = '$row8->ss' AND year ='$year' and baja='' and (codigo='101' or codigo='201' or codigo='301')")
            ->first();
        $pdf->Cell(20, 5, $row9->desc1 ?? '', 0, 0, 'C');
        $pdf->Cell(32, 5, $row9->pago ?? '', 0, 1, 'C');
    }
}
$pdf->Output();
