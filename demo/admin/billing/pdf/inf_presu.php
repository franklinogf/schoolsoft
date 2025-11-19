<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['PRESUPUESTO AÑO ', 'YEAR BUDGET '],
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

class nPDF extends PDF
{

    function Header()
    {
        global $year;
        parent::header();
        global $lang;

        $sp = 120;
        $this->Cell($sp);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, utf8_encode($lang->translation('PRESUPUESTO AÑO ')) . $year, 0, 1, 'C');
        $this->Ln(6);
        $this->Cell(30, 5, 'Matricula', 1, 0, 'C', true);
        $this->Cell(18, 5, 'Cant.', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Matr. 1er.', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Matr. 2do.', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Men. 1er.', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Men. 2do.', 1, 0, 'C', true);
        $this->Cell(50, 5, 'Beca deporte 50%', 1, 0, 'C', true);
        $this->Cell(50, 5, 'Beca deporte 25%', 1, 1, 'C', true);

        $this->Cell(30, 5, 'Elemental', 1, 0, 'C', true);
        $this->Cell(18, 5, '', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Hijo 615.00', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Hijo 490.00', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Hijo 190.00', 1, 0, 'C', true);
        $this->Cell(30, 5, 'Hijo 185.00', 1, 0, 'C', true);
        $this->Cell(25, 5, 'Matr 307.50 ', 1, 0, 'C', true);
        $this->Cell(25, 5, 'Men 95.00 ', 1, 0, 'C', true);
        $this->Cell(25, 5, 'Matr 461.25 ', 1, 0, 'C', true);
        $this->Cell(25, 5, 'Men 142.50 ', 1, 1, 'C', true);
    }
    function Footer()
    {

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

function Desc1($valor)
{
    global $x2;
    global $x3;
    global $x5;
    global $x6;
    global $x7;
    global $x8;
    if ($valor == 125.00) {
        $x2 = $x2 + 1;
    }
    if ($valor ==  5.00) {
        $x3 = $x3 + 1;
    }
    if ($valor == 307.50) {
        $x5 = $x5 + 1;
    }
    if ($valor == 95.00) {
        $x6 = $x6 + 1;
    }
    if ($valor == 153.75) {
        $x7 = $x7 + 1;
    }
    if ($valor == 47.50) {
        $x8 = $x8 + 1;
    }
}

function Desc2($valor)
{
    global $x2;
    global $x3;
    global $x5;
    global $x6;
    global $x7;
    global $x8;
    if ($valor == 125.00) {
        $x2 = $x2 + 1;
    }
    if ($valor ==  5.00) {
        $x3 = $x3 + 1;
    }
    if ($valor == 337.50) {
        $x5 = $x5 + 1;
    }
    if ($valor == 100.00) {
        $x6 = $x6 + 1;
    }
    if ($valor == 168.75) {
        $x7 = $x7 + 1;
    }
    if ($valor == 50.00) {
        $x8 = $x8 + 1;
    }
}

function Desc3($valor)
{
    global $x2;
    global $x3;
    global $x5;
    global $x6;
    global $x7;
    global $x8;
    if ($valor == 125.00) {
        $x2 = $x2 + 1;
    }
    if ($valor ==  5.00) {
        $x3 = $x3 + 1;
    }
    if ($valor == 357.50) {
        $x5 = $x5 + 1;
    }
    if ($valor == 110.00) {
        $x6 = $x6 + 1;
    }
    if ($valor == 178.75) {
        $x7 = $x7 + 1;
    }
    if ($valor == 55.00) {
        $x8 = $x8 + 1;
    }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage('L');
$pdf->SetFont('Times', '', 11);
$gdo = array("PK", "KG", "01-", "02-", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
$gd2 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

$tabla17 = DB::table('profesor')
    ->whereRaw("grupo = 'Maestro' and activo = 'Activo' and docente= 'Docente'")->get();

$xa1 = 0;
$xa2 = 0;
$xa3 = 0;
$xa4 = 0;
$xa5 = 0;
$xa6 = 0;
$xa7 = 0;
$xa8 = 0;
$xb1 = 0;
$xb2 = 0;
$xb3 = 0;
$xb4 = 0;
$xb5 = 0;
$xb6 = 0;
$xb7 = 0;
$xb8 = 0;

for ($i = 0; $i <= 13; $i++) {
    $x1 = 0;
    $x2 = 0;
    $x3 = 0;
    $x4 = 0;
    $x5 = 0;
    $x6 = 0;
    $x7 = 0;
    $x8 = 0;
    $tabla1 = DB::table('year')
        ->whereRaw(" year='$year' and grado LIKE '%" . $gdo[$i] . "%'")->orderBy('grado')->get();
    $num_res = count($tabla1);

    foreach ($tabla1 as $row7) {
        $tabla2 = DB::table('year')
            ->whereRaw(" year='$year' and grado LIKE '%" . $gdo[$i] . "%'")->orderBy('grado')->get();
        if ($row7->desc_men == 0 and $row7->desc_mat == 0 and $row7->desc_otro1 == 0 and $row7->desc_otro2 == 0) {
            $x1 = $x1 + 1;
        }
        for ($a = 17; $a <= 20; $a++) {
            if ($i < 7) {
                // elemental
                Desc1($row7->desc_men);
                Desc1($row7->desc_mat);
                Desc1($row7->desc_otro1);
                Desc1($row7->desc_otro2);
            }

            if ($i > 6 and $i < 10) {
                // intermedia
                Desc2($row7->desc_men);
                Desc2($row7->desc_mat);
                Desc2($row7->desc_otro1);
                Desc2($row7->desc_otro2);
            }

            if ($i > 9) {
                //superior
                Desc3($row7->desc_men);
                Desc3($row7->desc_mat);
                Desc3($row7->desc_otro1);
                Desc3($row7->desc_otro2);
            }
        }
    }

    $pdf->Cell(30, 5, $gdo[$i], 1, 0, 'C');
    $pdf->Cell(18, 5, $num_res, 1, 0, 'C');
    $pdf->Cell(30, 5, $x1, 1, 0, 'C');
    $pdf->Cell(30, 5, $x2, 1, 0, 'C');
    $pdf->Cell(30, 5, $x1, 1, 0, 'C');
    $pdf->Cell(30, 5, $x3, 1, 0, 'C');
    $pdf->Cell(25, 5, $x5, 1, 0, 'C');
    $pdf->Cell(25, 5, $x6, 1, 0, 'C');
    $pdf->Cell(25, 5, $x7, 1, 0, 'C');
    $pdf->Cell(25, 5, $x8, 1, 1, 'C');
    $xa4 = $xa4 + $num_res;
    $xa1 = $xa1 + $x1;
    $xa2 = $xa2 + $x2;
    $xa3 = $xa3 + $x3;
    $xa5 = $xa5 + $x5;
    $xa6 = $xa6 + $x6;
    $xa7 = $xa7 + $x7;
    $xa8 = $xa8 + $x8;

    $xb4 = $xb4 + $num_res;
    $xb1 = $xb1 + $x1;
    $xb2 = $xb2 + $x2;
    $xb3 = $xb3 + $x3;
    $xb5 = $xb5 + $x5;
    $xb6 = $xb6 + $x6;
    $xb7 = $xb7 + $x7;
    $xb8 = $xb8 + $x8;

    if ($gdo[$i] == '06') {
        $pdf->Cell(30, 5, 'Total elem', 1, 0, 'C');
        $pdf->Cell(18, 5, $xa4, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa2, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa3, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa5, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa6, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa7, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa8, 1, 1, 'C');

        $pdf->Cell(30, 5, 'Intermedia', 1, 0, 'C');
        $pdf->Cell(18, 5, '', 1, 0, 'C');
        $pdf->Cell(30, 5, '675', 1, 0, 'C');
        $pdf->Cell(30, 5, '550', 1, 0, 'C');
        $pdf->Cell(30, 5, '200', 1, 0, 'C');
        $pdf->Cell(30, 5, '195', 1, 0, 'C');
        $pdf->Cell(25, 5, '337.50', 1, 0, 'C');
        $pdf->Cell(25, 5, '100.00', 1, 0, 'C');
        $pdf->Cell(25, 5, '506.25', 1, 0, 'C');
        $pdf->Cell(25, 5, '150.00', 1, 1, 'C');

        $xa1 = 0;
        $xa2 = 0;
        $xa3 = 0;
        $xa4 = 0;
        $xa5 = 0;
        $xa6 = 0;
        $xa7 = 0;
        $xa8 = 0;
    }

    if ($gdo[$i] == '09') {
        $pdf->Cell(30, 5, 'Total inter.', 1, 0, 'C');
        $pdf->Cell(18, 5, $xa4, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa2, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa3, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa5, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa6, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa7, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa8, 1, 1, 'C');

        $pdf->Cell(30, 5, 'Superior', 1, 0, 'C');
        $pdf->Cell(18, 5, '', 1, 0, 'C');
        $pdf->Cell(30, 5, '715', 1, 0, 'C');
        $pdf->Cell(30, 5, '590', 1, 0, 'C');
        $pdf->Cell(30, 5, '220', 1, 0, 'C');
        $pdf->Cell(30, 5, '215', 1, 0, 'C');
        $pdf->Cell(25, 5, '357.50', 1, 0, 'C');
        $pdf->Cell(25, 5, '110.00', 1, 0, 'C');
        $pdf->Cell(25, 5, '536.50', 1, 0, 'C');
        $pdf->Cell(25, 5, '165.00', 1, 1, 'C');

        $xa1 = 0;
        $xa2 = 0;
        $xa3 = 0;
        $xa4 = 0;
        $xa5 = 0;
        $xa6 = 0;
        $xa7 = 0;
        $xa8 = 0;
    }

    if ($gdo[$i] == '12') {
        $pdf->Cell(30, 5, 'Total inter.', 1, 0, 'C');
        $pdf->Cell(18, 5, $xa4, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa2, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xa3, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa5, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa6, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa7, 1, 0, 'C');
        $pdf->Cell(25, 5, $xa8, 1, 1, 'C');
        $pdf->Cell(30, 5, 'Gran Total', 1, 0, 'C');
        $pdf->Cell(18, 5, $xb4, 1, 0, 'C');
        $pdf->Cell(30, 5, $xb1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xb2, 1, 0, 'C');
        $pdf->Cell(30, 5, $xb1, 1, 0, 'C');
        $pdf->Cell(30, 5, $xb3, 1, 0, 'C');
        $pdf->Cell(25, 5, $xb5, 1, 0, 'C');
        $pdf->Cell(25, 5, $xb6, 1, 0, 'C');
        $pdf->Cell(25, 5, $xb7, 1, 0, 'C');
        $pdf->Cell(25, 5, $xb8, 1, 1, 'C');

        $xa1 = 0;
        $xa2 = 0;
        $xa3 = 0;
        $xa4 = 0;
        $xa5 = 0;
        $xa6 = 0;
        $xa7 = 0;
        $xa8 = 0;
    }
}
$pdf->Output();
