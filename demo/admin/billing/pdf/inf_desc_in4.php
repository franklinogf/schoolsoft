<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Listado por descripción mensual", "List by monthly description"],
    ['Hasta', 'Until'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selecci&#65533;n', 'Select'],
    ['Desde', 'From'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['Descripción', 'Description'],
    ['OPCIONES', 'OPTIONS'],
    ['Fechas de Pagos', 'Payment Dates'],
    ['Sin Matrícula', 'No registration'],
    ['CTA', 'ACC'],
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
    ['PAGADO', 'PAID'],
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

        $row2 = DB::table('presupuesto')->where([
            ['year', $year],
            ['codigo', $_POST['codi']]
        ])->orderBy('codigo')->first();

        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, $lang->translation('LISTADO DE DESCRIPCION POR MES ') . $year, 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(80);
        if ($_POST['mes'] == '01') {
            $mes = 'enero';
        } else
	if ($_POST['mes'] == '02') {
            $mes = 'febrero';
        } else
	if ($_POST['mes'] == '03') {
            $mes = 'narzo';
        } else
	if ($_POST['mes'] == '04') {
            $mes = 'abril';
        } else
	if ($_POST['mes'] == '05') {
            $mes = 'mayo';
        } else
	if ($_POST['mes'] == '06') {
            $mes = 'junio';
        } else
	if ($_POST['mes'] == '07') {
            $mes = 'julio';
        } else
	if ($_POST['mes'] == '08') {
            $mes = 'agosto';
        } else
	if ($_POST['mes'] == '09') {
            $mes = 'septiembre';
        } else
	if ($_POST['mes'] == '10') {
            $mes = 'octubre';
        } else
	if ($_POST['mes'] == '11') {
            $mes = 'noviembre';
        } else
	if ($_POST['mes'] == '12') {
            $mes = 'diciembre';
        }
        $cod = $row2->descripcion ?? '';
        if (empty($row2->descripcion)) {
            $cod = $lang->translation('Todos');
        }
        $this->Cell(30, 5, $lang->translation('Código') . ' ' . $cod . ' / ' . $lang->translation(' Mes de ') . $lang->translation($mes), 0, 1, 'C');
        $this->Ln(5);

        $this->Cell(8, 5, '', 1, 0, 'C', true);
        $this->Cell(18, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(90, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('GRADO'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('CANTIDAD'), 1, 0, 'C', true);
        $this->Cell(25, 5, $lang->translation('PAGADO'), 1, 1, 'C', true);
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
$pdf->SetTitle($lang->translation('INFORME DE PAGOS POR GRADO') . ' ' . $year);
$pdf->SetFont('Times', '', 11);

$cod = '';
if ($_POST['codi'] != 'T') {
    $cod = " and codigo = '" . $_POST['codi'] . "'";
}

$result7 = DB::table('year')->where([
    ['year', $year],
    ['activo', '']
])->orderBy('grado, apellidos')->get();

$f = 0;
$m = 0;
$deut = 0;
$pagt = 0;
$grado = '';
$p = 0;
$pdf->SetFont('Times', '', 10);
foreach ($result7 as $row7) {
    $result8 = DB::table('pagos')->whereRaw("ss='$row7->ss' and month(fecha_d) = '" . $_POST['mes'] . "' and id = '$row7->id' and year = '$year' and baja = '' $cod")->get();
    $deu = 0;
    $pag = 0;
    foreach ($result8 as $row8) {
        $deu = $deu + $row8->deuda;
        $pag = $pag + $row8->pago;
        $pagt = $pagt + $row8->pago;
    }
    if ($row7->grado != $grado and $p == 1) {
        $pdf->Cell(136, 5, '', 1, 0, 'R');
        $pdf->Cell(25, 5, number_format($pagt, 2), 1, 1, 'R');
        $p = 0;
    }
    if ($deu <> 0) {

        if ($row7->grado != $grado) {
            $grado = $row7->grado;
            $pdf->AddPage();
            $x = 0;
            $f = 0;
            $m = 0;
            $deut = 0;
            $pagt = 0;
            $p = 1;
        }
        if ($row7->genero == 'F' or $row7->genero == '1') {
            $f = $f + 1;
        }
        if ($row7->genero == 'M' or $row7->genero == '2') {
            $m = $m + 1;
        }
        $x = $x + 1;
        $pdf->Cell(8, 5, $x, 0, 0, 'R');
        $pdf->Cell(18, 5, $row7->id, 0, 0, 'R'); //id
        $pdf->Cell(90, 5, $row7->apellidos . ' ' . $row7->nombre, 0, 0); //nombre
        $pdf->Cell(20, 5, $row7->grado, 0, 0, 'C'); //grado
        $pdf->Cell(25, 5, number_format($deu, 2), 0, 0, 'R'); //descripcion
        $pa = 'Si';

        if (($deu - $pag) > 0) {
            $pa = 'No';
        }
        $pdf->Cell(25, 5, $pa, 0, 1, 'C'); //pagado
        if ($pa === 'Si') {
            $pagt += floatval($pag);
        }
    }
}
$pdf->Output();
