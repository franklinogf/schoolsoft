<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF as BasePDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$school = new School(Session::id());
$year = $school->info('year2');

class PDF extends BasePDF
{
    function Header()
    {
        $nombre = $_POST['nombre'] ?? '';
        list($ape, $nom) = explode(', ', $nombre . ', ');

        $colegioRow = DB::table('colegio')->where('usuario', 'administrador')->first();
        $row = $colegioRow ? array_values((array) $colegioRow) : [];

        $school = new School(Session::id());
        $year = $school->info('year2');

        $presRow = DB::table('presupuesto')->where('codigo', $_POST['codi'] ?? '')->where('year', $year)->first();
        $row2 = $presRow ? array_values((array) $presRow) : [];

        $this->Image('../../../logo/logo.gif', 10, 10, 25);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 5, $row[0] ?? '', 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Ln(2);
        $this->Cell(80);
        $this->Cell(30, 2, $row[1] ?? '', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 2, $row[2] ?? '', 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 2, ($row[3] ?? '') . ', ' . ($row[4] ?? '') . ' ' . ($row[5] ?? ''), 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->SetFont('Arial', '', 8);
        $this->Cell(30, 3, 'Tel. ' . ($row[12] ?? '') . ' Fax ' . ($row[13] ?? ''), 0, 0, 'C');
        $this->Ln(3);
        $this->Cell(80);
        $this->Cell(30, 3, $row[20] ?? '', 0, 0, 'C');
        $this->Ln(7);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 10, 'LISTADO DE DESCRIPCION POR MES ' . $year, 0, 1, 'C');
        $this->Ln(1);
        $this->Cell(80);
        if (($_POST['mes'] ?? '') == '01') {
            $mes = 'enero';
        } elseif (($_POST['mes'] ?? '') == '02') {
            $mes = 'febrero';
        } elseif (($_POST['mes'] ?? '') == '03') {
            $mes = 'narzo';
        } elseif (($_POST['mes'] ?? '') == '04') {
            $mes = 'abril';
        } elseif (($_POST['mes'] ?? '') == '05') {
            $mes = 'mayo';
        } elseif (($_POST['mes'] ?? '') == '06') {
            $mes = 'junio';
        } elseif (($_POST['mes'] ?? '') == '07') {
            $mes = 'julio';
        } elseif (($_POST['mes'] ?? '') == '08') {
            $mes = 'agosto';
        } elseif (($_POST['mes'] ?? '') == '09') {
            $mes = 'septiembre';
        } elseif (($_POST['mes'] ?? '') == '10') {
            $mes = 'octubre';
        } elseif (($_POST['mes'] ?? '') == '11') {
            $mes = 'noviembre';
        } elseif (($_POST['mes'] ?? '') == '12') {
            $mes = 'diciembre';
        } else {
            $mes = '';
        }
        $cod = $row2[1] ?? '';
        if (empty($row2[1] ?? '')) {
            $cod = 'Todos';
        }
        $this->Cell(30, 5, $cod . ' Mes de ' . $mes, 0, 1, 'C');
        $this->Ln(5);
        //    $this->SetFillColor(230);
        $this->Cell(8, 5, '', 1, 0, 'C', true);
        $this->Cell(18, 5, 'CTA', 1, 0, 'C', true);
        $this->Cell(85, 5, 'NOMBRE', 1, 0, 'C', true);
        $this->Cell(18, 5, 'GRADO', 1, 0, 'C', true);
        $this->Cell(24, 5, 'CANTIDAD', 1, 0, 'C', true);
        $this->Cell(20, 5, 'PAGADO', 1, 0, 'C', true);
        $this->Cell(20, 5, 'DEUDAS', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();

$pdf->SetFont('Times', '', 11);

$colegioRow = DB::table('colegio')->where('usuario', Session::id())->first();
$row = $colegioRow ? array_values((array) $colegioRow) : [];

$presRow = DB::table('presupuesto')->where('codigo', $_POST['codi'] ?? '')->where('year', $year)->first();
$row2 = $presRow ? array_values((array) $presRow) : [];
$cod = '';
if (($_POST['codi'] ?? '') != 'T') {
    $cod = " and codigo = '" . ($_POST['codi'] ?? '') . "'";
}

$orden = $_POST['orden'] ?? 'apellidos';
if ($orden !== 'apellidos' && $orden !== 'id') {
    $orden = 'apellidos';
}
$estudiantes = DB::table('year')->where('year', $year)->orderBy($orden)->get();
$f = 0;
$m = 0;
$deut = 0;
$pagt = 0;
$x = 0;

$pdf->SetFont('Times', '', 10);
foreach ($estudiantes as $est) {
    $row7 = array_values((array) $est);
    $pagos = DB::table('pagos')
        ->whereRaw("ss='$row7[0]' and month(fecha_d) = '" . $_POST['mes'] . "' and id = '$row7[5]' and year = '$row[116]' $cod")->get();


    $deu = 0;
    $pag = 0;
    foreach ($pagos as $p) {
        $row8 = array_values((array) $p);
        $deu = $deu + ($row8[7] ?? 0);
        $pag = $pag + ($row8[4] ?? 0);
        $deut = $deut + ($row8[7] ?? 0);
        $pagt = $pagt + ($row8[4] ?? 0);
    }

    if ($deu > 0) {
        if (($row7[6] ?? '') == 'F' || ($row7[6] ?? '') == '1') {
            $f = $f + 1;
        }
        if (($row7[6] ?? '') == 'M' || ($row7[6] ?? '') == '2') {
            $m = $m + 1;
        }
        $x = $x + 1;
        $pdf->Cell(8, 5, $x, 0, 0, 'R');
        $pdf->Cell(18, 5, $row7[5] ?? '', 0, 0, 'R'); // id
        $pdf->Cell(85, 5, ($row7[4] ?? '') . ' ' . ($row7[3] ?? ''), 0, 0); // nombre
        $pdf->Cell(18, 5, $row7[2] ?? '', 0, 0, 'C'); // grado
        $pdf->Cell(24, 5, number_format($deu, 2), 0, 0, 'R'); // descripcion
        $pa = 'Si';
        if (($deu - $pag) > 0) {
            $pa = 'No';
        }
        $pdf->Cell(20, 5, $pa, 0, 0, 'C'); // pagado
        $pa = '';
        if (($deu - $pag) != 0) {
            $pa = $deu - $pag;
        }
        $pdf->Cell(20, 5, $pa, 0, 1, 'C');
    }
}
$pdf->Cell(136, 5, ' Gran Total: ', 1, 0, 'R');
$pdf->Cell(25, 5, number_format($deut, 2), 1, 0, 'R');
$pdf->Cell(25, 5, '', 1, 1, 'R');
$pdf->Cell(136, 5, ' Total Pagados: ', 1, 0, 'R');
$pdf->Cell(25, 5, number_format($pagt, 2), 1, 1, 'R');
$pdf->Cell(136, 5, ' Total Deudas: ', 1, 0, 'R');
$pdf->Cell(25, 5, number_format($deut - $pagt, 2), 1, 1, 'R');
$pdf->Output();
