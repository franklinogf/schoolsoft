<?php
if ($_POST['pagos'] === 'B') {
   require('pagos_diario_inf2.php');
   exit;
}
if ($_POST['pagos'] == 'C') {
   require('pagos_diario_inf3.php');
   exit;
}
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\DataBase\DB;

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


//session_start();
//$grados =  $_COOKIE["variable1"];

class nPDF extends PDF
{
   function Header()
   {
      global $lang;
      global $year;
      parent::header();
      $this->Cell(80);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(30, 5, $lang->translation('INFORME DE PAGOS DIARIOS RESUMEN') . ' ' . $year, 0, 1, 'C');
      $this->Cell(30, 5, '', 0, 1, 'C');
      $this->Cell(80);
      $this->Cell(30, 5, $lang->translation('DESDE') . ': ' . $_POST['ft1'] . '  /  ' . $lang->translation('HASTA') . ': ' . $_POST['ft2'], 0, 1, 'C');
      $this->Ln(8);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(12, 5, $lang->translation('CTA'), 1, 0, 'C', true);
      $this->Cell(70, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
      $this->Cell(20, 5, $lang->translation('FECHA P.'), 1, 0, 'C', true);
      $this->Cell(20, 5, $lang->translation('PAGOS'), 1, 0, 'C', true);
      $this->Cell(20, 5, $lang->translation('T. PAGO'), 1, 0, 'C', true);
      $this->Cell(15, 5, 'CHK.', 1, 0, 'C', true);
      $this->Cell(20, 5, 'TRANS. F.', 1, 0, 'C', true);
      $this->Cell(17, 5, 'RECV.', 1, 1, 'C', true);
      $this->SetFont('Arial', '', 11);
   }

   //Pie de pgina
   function Footer()
   {
      $this->SetY(-15);

      //Arial italic 8
      $this->SetFont('Arial', 'I', 8);
      //N&uacute;mero de p&aacute;gina
      $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
   }
}

//session_start();
$id = $_SESSION['id1'];
$usua = $_SESSION['usua1'];

//Creacin del objeto de la clase heredada
$pdf = new nPDF();
$pdf->SetTitle($lang->translation('INFORME DE PAGOS DIARIOS RESUMEN') . ' ' . $year);
$pdf->Fill();


$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 11);
//include('../../control.php');

$caja = $_POST['caja'];

$codigo = $_POST['codigo'];

$consult1 = "select * from colegio where usuario = '$usua'";

if ($caja == '') {
   $caja = $school->info('caja');
}
if ($caja == '0') {
   if ($codigo == 'Todos') {
      $result = DB::table('pagos')->select("DISTINCT id")
         ->whereRaw("year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id, id2, nombre')->get();
   } else {
      $result = DB::table('pagos')->select("DISTINCT id")
         ->whereRaw("codigo = '$codigo' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id, id2, nombre')->get();
   }
} else {
   if ($codigo == 'Todos') {
      $result = DB::table('pagos')->select("DISTINCT id")
         ->whereRaw("caja='$caja' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id, id2, nombre')->get();
   } else {
      $result = DB::table('pagos')->select("DISTINCT id")
         ->whereRaw("codigo = '$codigo' and caja='$caja' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id, id2, nombre')->get();
   }
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
$tot12 = 0;
$tot13 = 0;
$tot19 = 0;
$tot20 = 0;
foreach ($result as $row) {
   if ($caja == '0') {
      if ($codigo == 'Todos') {
         $result21 = DB::table('pagos')->select("DISTINCT id, ss")
            ->whereRaw("id='$row->id' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id2, nombre')->get();
      } else {
         $result21 = DB::table('pagos')->select("DISTINCT id, ss")
            ->whereRaw("codigo = '$codigo' and id='$row->id' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id2, nombre')->get();
      }
   } else {
      if ($codigo == 'Todos') {
         $result21 = DB::table('pagos')->select("DISTINCT id, ss")
            ->whereRaw("caja='$caja' and id='$row->id' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id2, nombre')->get();
      } else {
         $result21 = DB::table('pagos')->select("DISTINCT id, ss")
            ->whereRaw("codigo = '$codigo' and caja='$caja' and id='$row->id' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('id2, nombre')->get();
      }
   }
   $tot0 = 0;
   foreach ($result21 as $row1) {

      if ($caja == '0') {
         if ($_POST['bash'] == 0) {
            if ($codigo == 'Todos') {
               $result2 = DB::table('pagos')
                  ->whereRaw("id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('nombre')->get();
            } else {
               $result2 = DB::table('pagos')
                  ->whereRaw("codigo = '$codigo' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('nombre')->get();
            }
         } else {
            if ($codigo == 'Todos') {
               $result2 = DB::table('pagos')
                  ->whereRaw("id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "' and bash='" . $_POST['bash'] . "'")->orderBy('nombre')->get();
            } else {
               $result2 = DB::table('pagos')
                  ->whereRaw("codigo = '$codigo' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "' and bash='" . $_POST['bash'] . "'")->orderBy('nombre')->get();
            }
         }
      } else {
         if ($_POST['bash'] == 0) {
            if ($codigo == 'Todos') {
               $result2 = DB::table('pagos')
                  ->whereRaw("caja='$caja' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('nombre')->get();
            } else {
               $result2 = DB::table('pagos')
                  ->whereRaw("codigo = '$codigo' and caja='$caja' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "'")->orderBy('nombre')->get();
            }
         } else {
            if ($codigo == 'Todos') {
               $result2 = DB::table('pagos')
                  ->whereRaw("caja='$caja' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "' and bash='" . $_POST['bash'] . "'")->orderBy('nombre')->get();
            } else {
               $result2 = DB::table('pagos')
                  ->whereRaw("codigo = '$codigo' and caja='$caja' and id='$row1->id' and ss='$row1->ss' and year='$year' and fecha_p >= '" . $_POST['ft1'] . "' and fecha_p <= '" . $_POST['ft2'] . "' and bash='" . $_POST['bash'] . "'")->orderBy('nombre')->get();
            }
         }
      }

      $est = 0;
      foreach ($result2 as $row2) {

         if ($_POST['efe'] ?? '' == $row2->tdp or $_POST['che'] ?? '' == $row2->tdp or $_POST['ath'] ?? '' == $row2->tdp or $_POST['tar'] ?? '' == $row2->tdp or $_POST['gir'] ?? '' == $row2->tdp or $_POST['nom'] ?? '' == $row2->tdp or $_POST['ban'] ?? '' == $row2->tdp or $_POST['pay'] ?? '' == $row2->tdp or $_POST['telp'] ?? '' == $row2->tdp or $_POST['pdir'] ?? '' == $row2->tdp or $_POST['bec'] ?? '' == $row2->tdp or $_POST['athm'] ?? '' == $row2->tdp or $_POST['cac'] ?? '' == $row2->tdp or $_POST['vt1'] ?? '' == $row2->tdp) {
            if ($est == 0) {
               $row22 = DB::table('year')->where([
                  ['ss', $row1->ss]
               ])->first();

               $pdf->Cell(12, 5, $row1->id, 0, 0, 'R');
               $pdf->Cell(70, 5, $row22->apellidos . ' ' . $row22->nombre, 0, 1, 'L');
               $est = 1;
            }
            if (isset($_POST['efe']) && $_POST['efe'] === $row2->tdp) {
               $tot1 = $tot1 + $row2->pago;
            }
            if (isset($_POST['che']) && $_POST['che'] === $row2->tdp) {
               $tot2 = $tot2 + $row2->pago;
            }
            if (isset($_POST['ath']) && $_POST['ath'] === $row2->tdp) {
               $tot3 = $tot3 + $row2->pago;
            }
            if (isset($_POST['tar']) && $_POST['tar'] === $row2->tdp) {
               $tot4 = $tot4 + $row2->pago;
            }
            if (isset($_POST['gir']) && $_POST['gir'] === $row2->tdp) {
               $tot5 = $tot5 + $row2->pago;
            }
            if (isset($_POST['nom']) && $_POST['nom'] === $row2->tdp) {
               $tot6 = $tot6 + $row2->pago;
            }
            if (isset($_POST['ban']) && $_POST['ban'] === $row2->tdp) {
               $tot7 = $tot7 + $row2->pago;
            }
            if (isset($_POST['pdir']) && $_POST['pdir'] === $row2->tdp) {
               $tot8 = $tot8 + $row2->pago;
            }
            if (isset($_POST['telp']) && $_POST['telp'] === $row2->tdp) {
               $tot9 = $tot9 + $row2->pago;
            }
            if (isset($_POST['pay']) && $_POST['pay'] === $row2->tdp) {
               $tot10 = $tot10 + $row2->pago;
            }
            if (isset($_POST['bec']) && $_POST['bec'] === $row2->tdp) {
               $tot12 = $tot12 + $row2->pago;
            }
            if (isset($_POST['athm']) && $_POST['athm'] === $row2->tdp) {
               $tot13 = $tot13 + $row2->pago;
            }
            if (isset($_POST['cac']) && $_POST['cac'] === $row2->tdp) {
               $tot19 = $tot19 + $row2->pago;
            }
            if (isset($_POST['vt1']) && $_POST['vt1'] === $row2->tdp) {
               $tot20 = $tot20 + $row2->pago;
            }
            $tot11 = $tot11 + $row2->pago;
            $tot0 = $tot0 + $row2->pago;
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(12, 5, '', 0, 0, 'R');
            $pdf->Cell(10, 5, $row2->codigo, 0, 0, 'R');
            $pdf->Cell(60, 5, $row2->desc1, 0, 0, 'L');
            $pdf->Cell(20, 5, $row2->fecha_p, 0, 0, 'R');
            $pdf->Cell(20, 5, number_format($row2->pago, 2), 0, 0, 'R');
            if ($row2->tdp == 1) {
               $tdp = 'Efectivo';
            }
            if ($row2->tdp == 2) {
               $tdp = 'Cheque';
            }
            if ($row2->tdp == 3) {
               $tdp = 'ATH';
            }
            if ($row2->tdp == 4) {
               $tdp = 'Tarj. Cred.';
            }
            if ($row2->tdp == 5) {
               $tdp = 'Giro';
            }
            if ($row2->tdp == 6) {
               $tdp = 'Nomina';
            }
            if ($row2->tdp == 7) {
               $tdp = 'Banco';
            }
            if ($row2->tdp == 8) {
               $tdp = 'Pago Dir';
            }
            if ($row2->tdp == 9) {
               $tdp = 'Tele Pago';
            }
            if ($row2->tdp == 10) {
               $tdp = 'Paypal';
            }
            if ($row2->tdp == 11) {
               $tdp = 'Beca';
            }
            if ($row2->tdp == 12) {
               $tdp = 'ATH Movil';
            }
            if ($row2->tdp == 13) {
               $tdp = 'C. a Cuentas';
            }
            if ($row2->tdp == 14) {
               $tdp = 'V. Terminal';
            }
            $pdf->Cell(20, 5, $tdp, 0, 0, 'R');
            $pdf->Cell(15, 5, $row2->nuchk, 0, 0, 'R');
            $pdf->Cell(20, 5, $row2->fecha_d, 0, 0, 'R');
            $pdf->Cell(17, 5, $row2->rec, 0, 1, 'R');
         }
      }
   }

   if ($tot0 > 0) {
      $pdf->Cell(104, 5, 'Gran Total:', 0, 0, 'R');
      $pdf->Cell(18, 5, number_format($tot0, 2), 0, 1, 'R');
      $tot0 = 0;
   }
}


$pdf->Cell(25, 5, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 1, 'R');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
if ($tot1 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Efectivo: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot1, 2), 0, 1, 'R');
}
if ($tot2 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Cheque: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot2, 2), 0, 1, 'R');
}
if ($tot3 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'ATH: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot3, 2), 0, 1, 'R');
}
if ($tot4 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Tarjeta Credito: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot4, 2), 0, 1, 'R');
}
if ($tot5 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Giro: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot5, 2), 0, 1, 'R');
}
if ($tot6 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Nomina: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot6, 2), 0, 1, 'R');
}
if ($tot7 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Banco: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot7, 2), 0, 1, 'R');
}
if ($tot8 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Pago Directo: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot8, 2), 0, 1, 'R');
}
if ($tot9 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Tele Pago: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot9, 2), 0, 1, 'R');
}
if ($tot10 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Paypal: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot10, 2), 0, 1, 'R');
}
if ($tot12 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Beca: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot12, 2), 0, 1, 'R');
}
if ($tot13 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'ATH Movil: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot13, 2), 0, 1, 'R');
}
if ($tot19 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'C. a Cuentas: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot19, 2), 0, 1, 'R');
}
if ($tot20 > 0) {
   $pdf->Cell(25, 5, '', 0, 0, 'R');
   $pdf->Cell(27, 5, 'Virtual Terminal: ', 0, 0, 'L');
   $pdf->Cell(18, 5, number_format($tot20, 2), 0, 1, 'R');
}
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(28, 5, '=====================', 0, 1, 'L');
$pdf->Cell(25, 5, '', 0, 0, 'R');
$pdf->Cell(27, 5, 'Gran Total: ', 0, 0, 'L');
$pdf->Cell(18, 5, number_format($tot11, 2), 0, 1, 'R');

$pdf->Output();
