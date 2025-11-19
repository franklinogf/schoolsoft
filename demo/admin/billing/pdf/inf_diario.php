<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['INFORME DE PAGOS DIARIOS RESUMEN', 'DAILY PAYMENTS REPORT SUMMARY'],
    ['NOMBRE', 'NAME'],
    ['CTA', 'ACCT'],
    ['PAGOS', 'PAYS'],
    ['FECHA P.', 'PAY DAY'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
    ['', ''],
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

$date = date('Y-m-d');
class nPDF extends PDF
{
    public function Header()
    {
        global $colegio;
        global $year;
        parent::header();
        $this->Ln(1);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, "INFORME DE DEUDORES POR GRADO $year", 0, 0, 'C');
        $this->Ln(10);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

$pdf = new nPDF();
$pdf->SetTitle($lang->translation('INFORME DE DEUDORES POR GRADO') . ' ' . $year);
$pdf->Fill();
$pdf->AliasNbPages();

//$usua=$_SESSION['usua1'];
//$f1=$_POST['ft1'].' '.$_POST['t1'];
//$f2=$_POST['ft2'].' '.$_POST['t2'];
$t1 = $_POST['t1'] ?? '';
$t2 = $_POST['t2'] ?? '';

$tcr1 = 0;
$tch1 = 0;
$tcr2 = 0;
$tch2 = 0;
$tcr3 = 0;
$tch3 = 0;
$tcr4 = 0;
$tch4 = 0;
$tcr5 = 0;
$tch5 = 0;
$tcr6 = 0;
$tch6 = 0;
$tcr7 = 0;
$tch7 = 0;


$tef1 = 0;
$tpo1 = 0;
$tef2 = 0;
$tpo2 = 0;
$tef3 = 0;
$tpo3 = 0;
$tef4 = 0;
$tpo4 = 0;
$tef5 = 0;
$tpo5 = 0;
$tef6 = 0;
$tpo6 = 0;
$tef7 = 0;
$tpo7 = 0;

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, '-1 INFORME COMPRAS DE CAMISAS DIARIOS ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
//$pdf->SetFillColor(230);
$pdf->Cell(8, 5, '#', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CTA.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'PAGOS', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T.P.', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);

if ($_POST['ho'] == '1') {
    $result = DB::table('compras')
        ->whereRaw("shopping=1 AND year='$year' and DATE(`date`) >= '{$_POST['ft1']}' AND DATE(`date`) <= '{$_POST['ft2']}'")->orderBy('date')->get();
} else {
    $result = DB::table('compras')
        ->whereRaw("shopping=1 and year='$year' and date >= '" . $_POST['ft1'] . " $t1' and date <= '" . $_POST['ft2'] . " $t2'")->orderBy('date')->get();
}

$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    list($fecha, $hora) = explode(" ", $row2->date);
    $estu = DB::table('year')
        ->whereRaw(" ss = '" . $row2->deliveryTo . "' limit 1")->orderBy('ss')->get();

    $aa = '';
    $est = $est + 1;
    $tdp = '';
    $pdf->Cell(8, 5, $est, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2[1], 0, 0, 'R');
    if ($row2->payment_type == 'credit') {
        $tdp = 'Tarj. Credito';
        $tot1 = $tot1 + $row2->total;
    }
    if ($row2->payment_type == 'ach') {
        $tdp = 'Cheque';
        $tot2 = $tot2 + $row2->total;
    }
    if ($row2->payment_type == 'Cash') {
        $tot3 = $tot3 + $row2->total;
    }
    $tot11 = $tot11 + $row2->total;
    $tot0 = $tot0 + $row2->total;
    $pdf->Cell(25, 5, $fecha, 0, 0, 'R');
    $pdf->Cell(20, 5, $hora, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2->total, 0, 0, 'R');
    $pdf->Cell(70, 5, $estu->apellidos . ' ' . $estu->nombre, 0, 0, 'l');
    $pdf->Cell(25, 5, $tdp . ' ' . $aa, 0, 1, 'R');
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, utf8_encode('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
    $tcr1 = $tcr1 + $tot1;
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Cheque: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
    $tch1 = $tch1 + $tot2;
}
if ($tot3 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Efectivo: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot3, 2), 0, 1, 'R');
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');



//***********************************************************

$pdf->AddPage();

$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, '-2 INFORME ABONO MENSUALIDAD ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 5, '#', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CTA.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'PAGOS', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T.P.', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);

if ($_POST['ho'] == '1') {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=2 AND year='$year' and DATE(`date`) >= '{$_POST['ft1']}' AND DATE(`date`) <= '{$_POST['ft2']}'")->orderBy('date')->get();
} else {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=2 and year='$year' and date >= '" . $_POST['ft1'] . " $t1' and date <= '" . $_POST['ft2'] . " $t2'")->orderBy('date')->get();
}

$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    list($fecha, $hora) = explode(" ", $row2->date);
    $detail = DB::table('compras_detalle')
        ->whereRaw("id_compra = '{$row2->id}'")->orderBy('id_compra')->get();
    foreach ($detail as $estu) {
        $estu2 = DB::table('year')
            ->whereRaw(" ss = '" . $estu->item_name . "' limit 1")->orderBy('ss')->get();

        $aa = '';
        $est = $est + 1;
        $tdp = '';
        $pdf->Cell(8, 5, $est, 0, 0, 'R');
        $pdf->Cell(20, 5, $row2->accountID, 0, 0, 'R');
        if ($row2->payment_type == 'credit') {
            $tdp = 'Cheque';
            $tot2 = $tot2 + $estu->price;
        }
        if ($row2->payment_type == 'ach') {
            $tdp = 'Tarj. Credito';
            $tot1 = $tot1 + $estu->price;
        }
        if ($row2->tipoDePago == 'Cash') {
            $tot3 = $tot3 + $estu->price;
        }
        $tot11 = $tot11 + $estu->price;
        $tot0 = $tot0 + $estu->price;
        $pdf->Cell(25, 5, $fecha, 0, 0, 'R');
        $pdf->Cell(20, 5, $hora, 0, 0, 'R');
        $pdf->Cell(20, 5, $estu->price, 0, 0, 'R');
        $pdf->Cell(70, 5, $estu2->apellidos . ' ' . $estu2->nombre, 0, 0, 'l');
        $pdf->Cell(25, 5, $tdp . ' ' . $aa, 0, 1, 'R');
    }
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, utf8_encode('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
    $tcr2 = $tcr2 + $tot1;
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Cheque: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
    $tch2 = $tch2 + $tot2;
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');

//***********************************************************

$pdf->AddPage();
$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, '-3 INFORME DEPOSITOS DE ABONO DE MATRICULA DIARIAS ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 5, '#', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CTA.', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'PAGOS', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'TRANS', 1, 1, 'C', true);
$pdf->SetFont('Times', '', 11);

if ($_POST['ho'] == '1') {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=3 AND year='$year' and DATE(`date`) >= '{$_POST['ft1']}' AND DATE(`date`) <= '{$_POST['ft2']}'")->orderBy('date')->get();
} else {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=3 and year='$year' and date >= '" . $_POST['ft1'] . " $t1' and date <= '" . $_POST['ft2'] . " $t2'")->orderBy('date')->get();
}

$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    list($fecha, $hora) = explode(" ", $row2->date);

    $aa = '';
    $result33 = DB::table('compras_detalle')
        ->whereRaw("id_compra = '$row2->id'")->orderBy('id_compra')->get();
    foreach ($result33 as $row3) {
        $row22 = DB::table('year')
            ->whereRaw("ss = '" . $row2->deliveryTo . "' limit 1")->orderBy('ss')->get();
        $est = $est + 1;
        $tdp = '';
        $pdf->Cell(8, 5, $est, 0, 0, 'R');
        $pdf->Cell(20, 5, $row2[1], 0, 0, 'R');
        $pdf->Cell(70, 5, $row22->apellidos . ' ' . $row22->nombre, 0, 0, 'L');

        if ($row2->payment_type == 'credit') {
            $tdp = 'Tarj. Credito';
            $tot1 = $tot1 + $row2->total;
        }
        if ($row2->payment_type == 'ach') {
            $tdp = 'Cheque';
            $tot2 = $tot2 + $row2->total;
        }
        $tot11 = $tot11 + $row2->total;
        $tot0 = $tot0 + $row2->total;
        $pdf->Cell(25, 5, $fecha, 0, 0, 'R');
        $pdf->Cell(20, 5, $hora, 0, 0, 'R');
        $pdf->Cell(25, 5, $row2->total, 0, 0, 'R');
        $pdf->Cell(25, 5, $row2->payment_type . ' ' . $aa, 0, 1, 'R');
    }
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, utf8_encode('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
    $tcr3 = $tcr3 + $tot1;
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Cheque: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
    $tch3 = $tch3 + $tot2;
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');

//***********************************************************

$pdf->AddPage();
$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, '-4 INFORME COMPRAS DE INFLABLES DIARIOS ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 5, '#', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CTA.', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'PAGOS', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'T.P.', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);

if ($_POST['ho'] == '1') {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=4 AND year='$year' and DATE(`date`) >= '{$_POST['ft1']}' AND DATE(`date`) <= '{$_POST['ft2']}'")->orderBy('date')->get();
} else {
    $infoData = DB::table('compras')
        ->whereRaw("shopping=4 and year='$year' and date >= '" . $_POST['ft1'] . " $t1' and date <= '" . $_POST['ft2'] . " $t2'")->orderBy('date')->get();
}

$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    list($fecha, $hora) = explode(" ", $row2->date);
    $estu = DB::table('year')
        ->whereRaw("ss = '" . $estu->deliveryTo . "' limit 1")->orderBy('ss')->get();
    $aa = '';
    $est = $est + 1;
    $tdp = '';
    $pdf->Cell(8, 5, $est, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2[1], 0, 0, 'R');
    if ($row2->payment_type == 'credit') {
        $tdp = 'Tarj. Credito';
        $tot1 = $tot1 + $row2->total;
    }
    if ($row2->payment_type == 'ach') {
        $tdp = 'Cheque';
        $tot2 = $tot2 + $row2->total;
    }
    if ($row2->tipoDePago == 'Cash') {
        $tot3 = $tot3 + $row2->total;
    }
    $tot11 = $tot11 + $row2->total;
    $tot0 = $tot0 + $row2->total;
    $pdf->Cell(25, 5, $fecha, 0, 0, 'R');
    $pdf->Cell(20, 5, $hora, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2->total, 0, 0, 'R');
    $pdf->Cell(70, 5, $estu->apellidos . ' ' . $estu->nombre, 0, 0, 'l');
    $pdf->Cell(25, 5, $tdp . ' ' . $aa, 0, 1, 'R');
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, utf8_encode('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
    $tcr4 = $tcr4 + $tot1;
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Cheque: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
    $tch4 = $tch4 + $tot2;
}
if ($tot3 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Efectivo: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot3, 2), 0, 1, 'R');
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');
//*********************************************************
$pdf->AddPage();
$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, 'INFORME DEPOSITOS DIARIOS RESUMEN ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(8, 5, '#', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'CTA.', 1, 0, 'C', true);
$pdf->Cell(70, 5, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'FECHA', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'HORA', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'DEPOSITOS', 1, 0, 'C', true);
$pdf->Cell(25, 5, 'TRANS', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 11);

$result = DB::table('depositos')
    ->whereRaw("year='$year' and fecha >= '" . $_POST['ft1'] . "' and fecha <= '" . $_POST['ft2'] . "'")->orderBy('fecha, hora')->get();
foreach ($result as $row2) {
    $thisCourse2 = DB::table('depositos')->where([
        ['id2', $row2->id2]
    ])->update([
        'date' => "$row2[2].' '.$row2[3]",
    ]);
}

if ($_POST['ho'] == '1') {
    $result = DB::table('depositos')
        ->whereRaw("year='$year' and fecha >= '" . $_POST['ft1'] . "' AND fecha <= '" . $_POST['ft2'] . "'")->orderBy('fecha, hora')->get();
} else {
    $result = DB::table('depositos')
        ->whereRaw("year='$year' and date >= '" . $_POST['ft1'] . " $t1' and date <= '" . $_POST['ft2'] . " $t2'")->orderBy('date')->get();
}

$est = 0;
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$tot6 = 0;
$tot7 = 0;
$tot8 = 0;
$tot9 = 0;
$tot10 = 0;
$tot11 = 0;
$tot19 = 0;
$aa = '';
$est = 0;
foreach ($result as $row2) {
    $aa = '';
    $row22 = DB::table('year')
        ->whereRaw("ss = '" . $row2->ss . "' limit 1")->orderBy('ss')->get();
    $est = $est + 1;
    $tdp = $row2->tipoDePago;
    $pdf->Cell(8, 5, $est, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2[0], 0, 0, 'R');
    $pdf->Cell(70, 5, $row22->apellidos . ' ' . $row22->nombre, 0, 0, 'L');
    if ($row2->tipoDePago == 'Tarjeta') {
        $tot1 = $tot1 + $row2->cantidad;
    }
    if ($row2->tipoDePago == 'ACH') {
        $tot2 = $tot2 + $row2->cantidad;
    }
    if ($row2->tipoDePago == 'Cash') {
        $tot3 = $tot3 + $row2->cantidad;
    }
    if ($tdp == 'Otros' and $row2->cantidad > 0) {
        $tot4 = $tot4 + $row2->cantidad;
    }
    if ($tdp == 'Correccion' and $row2->cantidad <> 0) {
        $tot9 = $tot9 + $row2->cantidad;
    }
    if ($tdp == 'Otros' and $row2->cantidad < 0) {
        $tot5 = $tot5 + $row2->cantidad;
    }
    if ($tdp == 'Transferencia en familia' and $row2->cantidad > 0) {
        $tot7 = $tot7 + $row2->cantidad;
        $tdp = 'Trans. fam.';
    }
    if ($tdp == 'Transferencia en familia' and $row2->cantidad < 0) {
        $tot8 = $tot8 + $row2->cantidad;
        $tdp = 'Trans. fam.';
    }
    if (utf8_decode($tdp) == 'Pago a través de oficina' and $row2->cantidad > 0) {
        $tot6 = $tot6 + $row2->cantidad;
    } else {
        $tot11 = $tot11 + $row2->cantidad;
    }
    $tot0 = $tot0 + $row2->cantidad;
    $pdf->Cell(20, 5, $row2->fecha, 0, 0, 'R');
    $pdf->Cell(20, 5, $row2->hora, 0, 0, 'R');
    $pdf->Cell(25, 5, $row2->cantidad, 0, 0, 'R');
    if ($row2->tipoDePago == 'Tarjeta') {
        $tdp = utf8_encode('Tarj. Crédito');
    }
    if ($row2->tipoDePago == 'ACH') {
        $tdp = 'Cheque';
    }
    if ($row2->tipoDePago == 'Cash') {
        $tdp = 'Efectivo';
    }
    if ($row2->tipoDePago == utf8_encode('Pago a través de oficina')) {
        $tdp = 'Pago Oficina';
    }
    $pdf->Cell(25, 5, $tdp . ' ' . $aa, 0, 1, 'R');
}

$pdf->Cell(25, 3, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
    $pdf->Cell(25, 5, '', 0, 0, 'R');
    $pdf->Cell(27, 5, utf8_encode('Tarjas Crédito: '), 0, 0, 'L');
    $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
    $tcr5 = $tcr5 + $tot1;
}
if ($tot2 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Cheque: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot2, 2), 0, 1, 'R');
    $tch5 = $tch5 + $tot2;
}
if ($tot3 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Efectivo: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot3, 2), 0, 1, 'R');
    $tef5 = $tef5 + $tot3;
}
if ($tot4 > 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Otros Positivos: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot4, 2), 0, 1, 'R');
}
if ($tot5 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Otros Negativos: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot5, 2), 0, 1, 'R');
}
if ($tot7 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Trans. fam.: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot7 + $tot8, 2), 0, 1, 'R');
}
if ($tot9 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, utf8_encode('Correción: '), 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot9, 2), 0, 1, 'R');
}

$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(27, 4, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 4, number_format($tot11, 2), 0, 1, 'R');
$pdf->Cell(25, 4, '', 0, 0, 'R');
$pdf->Cell(28, 4, '=====================', 0, 1, 'L');
if ($tot6 <> 0) {
    $pdf->Cell(25, 4, '', 0, 0, 'R');
    $pdf->Cell(27, 4, 'Pago Oficina: ', 0, 0, 'L');
    $pdf->Cell(18, 4, number_format($tot6, 2), 0, 1, 'R');
    $tpo5 = $tpo5 + $tot6;
}
//**********************************
$pdf->AddPage();

$pdf->Cell(80);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, 'INFORME CUADRE DIARIOS ' . $year, 0, 1, 'C');
$pdf->Cell(30, 5, '', 0, 1, 'C');
$pdf->Cell(80);
$pdf->Cell(30, 5, 'DESDE: ' . $_POST['ft1'] . ' - H: ' . $t1 . '  /  HASTA: ' . $_POST['ft2'] . ' - H: ' . $t2, 0, 1, 'C');
$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 5, '', 1, 0, 'C', true);
$pdf->Cell(30, 5, utf8_encode('Tar. Crédito'), 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Cheque', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Total', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Efectivo', 1, 0, 'C', true);
$pdf->Cell(30, 5, 'Pago Oficina', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(20, 5, '-1', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr1, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tch1, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tcr1 + $tch1, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tef1, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tpo1, 2), 1, 1, 'R');
$pdf->Cell(20, 5, '-2', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr2, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tch2, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tcr2 + $tch2, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tef2, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tpo2, 2), 1, 1, 'R');
$pdf->Cell(20, 5, '-3', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr3, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tch3, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tcr3 + $tch3, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tef3, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tpo3, 2), 1, 1, 'R');
$pdf->Cell(20, 5, '-4', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr4, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tch4, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tcr4 + $tch4, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tef4, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tpo4, 2), 1, 1, 'R');

$pdf->Cell(20, 5, '-0', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr5, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tch5, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tcr5 + $tch5, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tef5, 2), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($tpo5, 2), 1, 1, 'R');

$pdf->Cell(20, 5, 'G. Total', 1, 0, 'C', true);
$pdf->Cell(30, 5, number_format($tcr1 + $tcr2 + $tcr3 + $tcr4 + $tcr5, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($tch1 + $tch2 + $tch3 + $tch4 + $tch5, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($tch1 + $tch2 + $tch3 + $tch4 + $tch5 + $tcr1 + $tcr2 + $tcr3 + $tcr4 + $tcr5, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($tef5, 2), 1, 0, 'R', true);
$pdf->Cell(30, 5, number_format($tpo5, 2), 1, 1, 'R', true);
$pdf->Output();
