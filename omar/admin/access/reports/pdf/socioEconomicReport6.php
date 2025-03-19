<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Resumen socioeconómico', 'Socioeconomic summary'],
    ['Tamaño', 'Family'],
    ['familia', 'Size'],
    ['Ingreso maximo', 'Maximun income'],
    ['de familia anual', 'family annual'],
    ['Estudiantes', 'Students'],
    ['sobre nivel', 'over level'],
    ['bajo nivel', 'under level'],
    ['Estudiantes sobre nivel por grado', 'Over level students by grade']
]);

$school = new School(Session::id());
$year = $school->info('year2');


class nPDF extends PDF
{
    function Header()
    {
        global $year;
        parent::header();
        $sp = 80;
        $this->Ln(10);
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 5, 'ESTUDIO SOCIOECONOMICO PARA LAS ESCUELAS PRIVADAS', 0, 1, 'C');
        $this->Cell($sp);
        $this->Cell(30, 5, utf8_encode('PARA EL AÑO ESCOLAR ') . $year, 0, 1, 'C');
        $this->Ln(6);
        $this->Cell(15, 5, '', 'LRT', 0, 'C', true);
        $this->Cell(25, 5, 'CANTIDAD', 'LRT', 0, 'C', true);
        $this->Cell(25, 5, 'CANTIDAD', 'LRT', 0, 'C', true);
        $this->Cell(25, 5, 'CANTIDAD', 'LRT', 0, 'C', true);
        $this->Cell(30, 5, 'BAJO NIVEL', 'LRT', 0, 'C', true);
        $this->Cell(30, 5, 'SOBRE NIVEL', 'LRT', 0, 'C', true);
        $this->Cell(30, 5, '% BAJO NIVEL', 'LRT', 1, 'C', true);

        $this->Cell(15, 5, 'GRADO', 'LRB', 0, 'C', true);
        $this->Cell(25, 5, 'M', 'LRB', 0, 'C', true);
        $this->Cell(25, 5, 'F', 'LRB', 0, 'C', true);
        $this->Cell(25, 5, 'TOTAL', 'LRB', 0, 'C', true);
        $this->Cell(30, 5, 'DE POBREZA', 'LRB', 0, 'C', true);
        $this->Cell(30, 5, 'DE POBREZA', 'LRB', 0, 'C', true);
        $this->Cell(30, 5, 'DE POBREZA', 'LRB', 1, 'C', true);
    }
    function Footer()
    {

        $this->SetY(-15);

        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}


$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte Socioeconómico") . " $year", true);
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', '', 11);

$grados = array('PK', 'K -', '01-', '02-', '03-', '04-', '05-', '06-', '07-', '08-', '09-', '10-', '11-', '12-');
$tma = 0;
$tmax = 0;
$tmin = 0;
$tm = 0;
$tf = 0;

foreach ($grados as $grado) {

    $resultado2 = DB::table('year')->whereRaw("year = '$year' and grado LIKE '%$grado%' and codigobaja = 0 and genero != '0'");
    $ma = 0;
    $max = 0;
    $min = 0;
    $m = 0;
    $f = 0;
    foreach ($resultado2 as $row2) {
        $row1 = DB::table('madre')->where([
            ['id', $row2->id]
        ])->orderBy('id')->first();

        $row4 = DB::table('socio_economico')->where([
            ['dependientes', $row4->nfam]
        ])->orderBy('id')->first();

        $sp = $row1->sueldop; //sueldo padre
        $sm = $row1->sueldom; //sueldo madre
        $result = ($sm + $sp);

        if ($result >= $row4->sobre_nivel) {
            $max = $max + 1;
            $tmax = $tmax + 1;
        } else {
            $min = $min + 1;
            $tmin = $tmin + 1;
        }
        if ($row2->genero == '1') {
            $f = $f + 1;
            $ma = $ma + 1;
            $tf = $tf + 1;
            $tma = $tma + 1;
        }
        if ($row2->genero == '2') {
            $m = $m + 1;
            $ma = $ma + 1;
            $tm = $tm + 1;
            $tma = $tma + 1;
        }
    }

    $pdf->Cell(15, 5, $grado, 1, 0, 'C');
    $pdf->Cell(25, 5, $m, 1, 0, 'C');
    $pdf->Cell(25, 5, $f, 1, 0, 'C');
    $pdf->Cell(25, 5, $ma, 1, 0, 'C');
    $pdf->Cell(30, 5, $min, 1, 0, 'C');
    $pdf->Cell(30, 5, $max, 1, 0, 'C');
    if ($ma > 0) {
        $pdf->Cell(30, 5, round((($min / $ma) * 100), 2) . '%', 1, 1, 'C'); //min
    } else {
        $pdf->Cell(30, 5, '' . '%', 1, 1, 'C');
    }
}
$pdf->Cell(15, 5, 'Totales', 1, 0, 'C');
$pdf->Cell(25, 5, $tm, 1, 0, 'C');
$pdf->Cell(25, 5, $tf, 1, 0, 'C');
$pdf->Cell(25, 5, $tma, 1, 0, 'C');
$pdf->Cell(30, 5, $tmin, 1, 0, 'C');
$pdf->Cell(30, 5, $tmax, 1, 0, 'C');
if ($tma > 0) {
    $pdf->Cell(30, 5, round((($tmin / $tma) * 100), 2) . '%', 1, 1, 'C');
} else {
    $pdf->Cell(30, 5, '' . '%', 1, 1, 'C');
}


$pdf->Output();
