<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase0\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['LISTA DE DEUDORES DETALLADAS', 'DETAILED LIST OF DEBTORS'],
    ['NOMBRE ESTUDIANTES', 'STUDENT NAMES'],
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
    ['MAS 90', 'MORE 90'],
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

$db = new DB();
$db->query('Truncate deudores');

class nPDF extends PDF
{
    //Cabecera de pgina
    function Header()
    {
        global $year;
        parent::header();
        global $lang;

        $pag = 160;
        $this->Ln(5);
        $this->Cell($pag);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, $lang->translation('LISTA DE DEUDORES DETALLADAS') . ' ' . $year, 0, 1, 'C');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(12, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(75, 5, $lang->translation('NOMBRE ESTUDIANTES'), 1, 0, 'C', true);
        $this->Cell(55, 5, '30', 1, 0, 'C', true);
        $this->Cell(55, 5, '60', 1, 0, 'C', true);
        $this->Cell(55, 5, '90', 1, 0, 'C', true);
        $this->Cell(55, 5, $lang->translation('MAS 90'), 1, 0, 'C', true);
        $this->Cell(18, 5, 'TOTAL', 1, 1, 'C', true);
    }

    //Pie de pgina
    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb} ' . date('m-d-Y'), 0, 0, 'C');
    }

}
//Creacin del objeto de la clase heredada
$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->AddPage('L', 'Legal');
$pdf->SetFont('Times', '', 11);
$cl = $_POST['cl'] ?? '0';

if ($_POST['orden'] == 1) {
    $result1 = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('id')->get();
} else {
    $result1 = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('id, apellidos, nombre')->get();
}
$aa = 0;
$gra = '';
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$vl3 = 0;
$vl2 = $pdf->GetY();
$vl2 = 0;
$db3 = 0;
$db6 = 0;
$db9 = 0;
$db0 = 0;
$dbm = 0;
$p1 = 0;
$ldc1 = 0;
$vld = 0;
foreach ($result1 as $row1) {
    $nsl = 0;
    $ldc1 = 0;
    $nuevo = 0;
    $row2 = DB::table('year')
        ->whereRaw("id='$row1->id' and ss='$row1->ss' and year='$year' and activo !='B'")->orderBy('id, apellidos, nombre')->first();
    $debe = 0;
    if ($_POST['desc'] == 'Todos') {
        $result3 = DB::table('pagos')
            ->whereRaw("id='$row1->id' and ss='$row1->ss' and year='$year' and baja='' and fecha_d < '" . $_POST['ft1'] . "'")->orderBy('id, nombre')->get();
    } else {
        $code = $_POST['desc'];
        $result3 = DB::table('pagos')
            ->whereRaw("id='$row1->id' and ss='$row1->ss' and codigo='$code' and year='$year' and baja='' and fecha_d < '" . $_POST['ft1'] . "'")->orderBy('id, nombre')->get();
    }
    foreach ($result3 as $row33) {
        $debe = $debe + $row33->deuda - $row33->pago;
    }
    if ($debe > 0) {
        $nsl = 0;
        $ns2 = 0;
        $ns3 = 0;
        $ns4 = 0;
        if ($dbm > 0) {
            if ($vl3 == 0) {
                $vl3 = 10;
            }
        }
        $aa = $aa + 1;
        $db3 = 0;
        $db6 = 0;
        $db9 = 0;
        $db0 = 0;
        $dbm = 0;
    }

    if ($debe > 0) {
        if ($_POST['desc'] == 'Todos') {
            $result31 = DB::table('presupuesto')
            ->whereRaw("year='$year'")->orderBy('codigo')->get();
        } else {
            $result31 = DB::table('presupuesto')
            ->whereRaw("codigo='$code' and year='$year'")->orderBy('codigo')->get();
        }
        $debe = 0;
        $mes1 = 0;
        $mes2 = 0;
        $mes3 = 0;
        $mes4 = 0;
        $mes5 = 0;
        //     list($y1,$y2) = explode("-",$year);
        list($yy1, $mm1, $dd1) = explode("-", $_POST['ft1']);
        list($yy2, $mm2, $dd2) = explode("-", $_POST['ft1']);
        list($yy3, $mm3, $dd3) = explode("-", $_POST['ft1']);
        list($yy4, $mm4, $dd4) = explode("-", $_POST['ft1']);
        $m1 = '-';
        $m2 = '-';
        $m3 = '-';
        $m4 = '-';
        //     $yy1 = '20'.$y1;
        //     $yy2 = '20'.$y1;
        //     $yy3 = '20'.$y1;
        //     $yy4 = '20'.$y1;
        if ($mm1 == 1) {
            $mm1 = 12;
            $yy1 = $yy1 - 1;
        } else {
            $mm1 = $mm1 - 1;
        }
        if ($mm1 < 10) {
            $m1 = '-0';
        }
        $fec1 = $yy1 . $m1 . $mm1 . '-01';
        if ($mm2 == 1) {
            $mm2 = 11;
            $yy2 = $yy2 - 1;
        } else {
            if ($mm2 == 2) {
                $mm2 = 12;
                $yy2 = $yy2 - 1;
            } else {
                $mm2 = $mm2 - 2;
            }
        }
        if ($mm2 < 10) {
            $m2 = '-0';
        }
        $fec2 = $yy2 . $m2 . $mm2 . '-01';
        if ($mm3 == 1) {
            $mm3 = 10;
            $yy3 = $yy3 - 1;
        } else {
            if ($mm3 == 2) {
                $mm3 = 11;
                $yy3 = $yy3 - 1;
            } else {
                if ($mm3 == 3) {
                    $mm3 = 12;
                    $yy3 = $yy3 - 1;
                } else {
                    $mm3 = $mm3 - 3;
                }
            }
        }
        if ($mm3 < 10) {
            $m3 = '-0';
        }
        $fec3 = $yy3 . $m3 . $mm3 . '-01';
        if ($mm4 == 1) {
            $mm4 = 9;
            $yy4 = $yy4 - 1;
        } else {
            if ($mm4 == 2) {
                $mm4 = 10;
                $yy4 = $yy4 - 1;
            } else {
                if ($mm4 == 3) {
                    $mm4 = 11;
                    $yy4 = $yy4 - 1;
                } else {
                    if ($mm4 == 4) {
                        $mm4 = 12;
                        $yy4 = $yy4 - 1;
                    } else {
                        $mm4 = $mm4 - 4;
                    }
                }
            }
        }
        if ($mm4 < 10) {
            $m4 = '-0';
        }
        $fec4 = $yy4 . $m4 . $mm4 . '-01';
        $vla = 0;
        $vlb = 0;
        $vlc = 0;
        $vld = 0;
        $li = 0;
        foreach ($result31 as $row3) {
            $mm2 = 0;
            $mes1 = 0;
            $result10 = DB::table('pagos')
                ->whereRaw("id='$row1->id' and ss='$row1->ss' and codigo='$row3->codigo' and year='$year' and baja='' and fecha_d = '" . date($fec1) . "'")->orderBy('codigo')->get();
            foreach ($result10 as $row10) {
                $debe = $debe + ($row10->deuda - $row10->pago);
                $mes1 = $mes1 + ($row10->deuda - $row10->pago);
                $db3 = $db3 + ($row10->deuda - $row10->pago);
                $dbm = $dbm + ($row10->deuda - $row10->pago);
            }
            if ($mes1 > 0) {
                $nuevo = 1;
                $li = $li + 1;
                $vla = $vla + 5;
                $nsl = $nsl + 1;

                DB::table('deudores')->insert([
                    'cta' => $row2->id,
                    'ss' => $row2->ss,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'da30' => $row3->descripcion,
                    'db30' => $mes1,
                    'linia' => $li,
                ]);
            }
        }
        $li = 0;
        foreach ($result31 as $row3) {
            $mm2 = 0;
            $mes1 = 0;
            $result10 = DB::table('pagos')
                ->whereRaw(" id='$row1->id' and ss='$row1->ss' and year='$year' and codigo='$row3->codigo' and baja='' and fecha_d = '" . date($fec2) . "'")->orderBy('codigo')->get();
            //echo "id='$row1->id' and ss='$row1->ss' and codigo='$row3->codigo' and year='$year' and baja='' and fecha_d = '".date($fec2)."'".'<br>';
            foreach ($result10 as $row10) {
                $debe = $debe + ($row10->deuda - $row10->pago);
                $mes1 = $mes1 + ($row10->deuda - $row10->pago);
                $db6 = $db6 + ($row10->deuda - $row10->pago);
                $dbm = $dbm + ($row10->deuda - $row10->pago);
            }
            if ($mes1 > 0) {
                $li = $li + 1;
                $vlb = $vlb + 5;
                $ns2 = $ns2 + 1;
                if ($nuevo == 0) {
                    $nuevo = 1;
                    DB::table('deudores')->insert([
                        'cta' => $row2->id,
                        'ss' => $row2->ss,
                        'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                        'da60' => $row3->descripcion,
                        'db60' => $mes1,
                        'linia' => $li,
                    ]);
                }

                $thisCourse2 = DB::table("deudores")->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'da60' => $row3->descripcion,
                    'db60' => $mes1,
                ]);
            }
        }
        $li = 0;
        foreach ($result31 as $row3) {
            $mm2 = 0;
            $mes1 = 0;
            $result10 = DB::table('pagos')
                ->whereRaw(" id='$row1->id' and ss='$row1->ss' and year='$year' and codigo='$row3->codigo' and baja='' and fecha_d = '" . date($fec3) . "'")->orderBy('codigo')->get();

            foreach ($result10 as $row10) {
                $debe = $debe + ($row10->deuda - $row10->pago);
                $mes1 = $mes1 + ($row10->deuda - $row10->pago);
                $db9 = $db9 + ($row10->deuda - $row10->pago);
                $dbm = $dbm + ($row10->deuda - $row10->pago);
            }
            if ($mes1 > 0) {
                $li = $li + 1;
                $vlc = $vlc + 5;

                if ($nuevo == 0) {
                    $nuevo = 1;
                    DB::table('deudores')->insert([
                        'cta' => $row2->id,
                        'ss' => $row2->ss,
                        'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                        'da90' => $row3->descripcion,
                        'db90' => $mes1,
                        'linia' => $li,
                    ]);
                }

                $thisCourse2 = DB::table("deudores")->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'da90' => $row3->descripcion,
                    'db90' => $mes1,
                ]);
            }
        }
        $li = 0;
        foreach ($result31 as $row3) {
            $mm2 = 0;
            $mes1 = 0;
            $result10 = DB::table('pagos')
                ->whereRaw(" id='$row1->id' and ss='$row1->ss' and year='$year' and codigo='$row3->codigo' and baja='' and fecha_d = '" . date($fec4) . "'")->orderBy('codigo')->get();
            foreach ($result10 as $row10) {
                $debe = $debe + ($row10->deuda - $row10->pago);
                $mes1 = $mes1 + ($row10->deuda - $row10->pago);
                $db0 = $db0 + ($row10->deuda - $row10->pago);
                $dbm = $dbm + ($row10->deuda - $row10->pago);
            }
            if ($mes1 > 0) {
                $li = $li + 1;
                $vld = $vld + 5;
                $ns4 = $ns4 + 1;
                if ($nuevo == 0) {
                    $nuevo = 1;
                    DB::table('deudores')->insert([
                        'cta' => $row2->id,
                        'ss' => $row2->ss,
                        'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                        'dam' => $year,
                        'dbm' => $mes1,
                        'linia' => $li,
                    ]);
                }
                $thisCourse2 = DB::table("deudores")->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'dam' => $row3->descripcion,
                    'dbm' => $mes1,
                ]);
            }
        }
        $tda1 = 0;
        $tda2 = 0;
        $tda3 = 0;

        if ($vla > $vlb) {
            $tda1 = $vla;
        } else {
            $tda1 = $vlb;
        }

        if ($vld > $vlc) {
            $tda2 = $vld;
        } else {
            $tda2 = $vlc;
        }

        if ($tda1 > $tda2) {
            $tda3 = $tda1;
        } else {
            $tda3 = $tda2;
        }
        $vl3 = $tda3;
    }
}

$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
$l2 = 0;

$result1 = DB::table('deudores')->select("DISTINCT cta, ss, nombre")
->orderBy('cta, ss')->get();
$l = 0;
$m = 0;
foreach ($result1 as $row0) {
    $pdf->SetFont('Arial', '', 12);
    $l = $l + 1;
    $m = $m + 1;
    $result2 = DB::table('deudores')
        ->whereRaw(" cta='$row0->cta' and ss='$row0->ss'")->orderBy('cta, linia')->get();
    $pdf->Cell(10, 5, $l, $cl, 0, 'R');
    $pdf->Cell(12, 5, $row0->cta, $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(75, 5, $row0->nombre, $cl, 0, 'L');
    $l2 = 0;
    $de1 = 0;
    $de2 = 0;
    $de3 = 0;
    $de4 = 0;
    $de5 = 0;
    foreach ($result2 as $row1) {
        $l2 = $l2 + 1;
        $m = $m + 1;
        if ($l2 > 1) {
            $pdf->Cell(97, 5, '', 0, 0, 'R');
        }
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(40, 5, $row1->da30, $cl, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $row1->db30, $cl, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(40, 5, $row1->da60, $cl, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $row1->db60, $cl, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(40, 5, $row1->da90, $cl, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $row1->db90, $cl, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(40, 5, $row1->dam, $cl, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $row1->dbm, $cl, 1, 'R');
        if ($m >= 25) {
            $pdf->AddPage('L', 'Legal');
            $m = 1;
        }
        $tot1 = $tot1 + $row1->db30;
        $tot2 = $tot2 + $row1->db60;
        $tot3 = $tot3 + $row1->db90;
        $tot4 = $tot4 + $row1->dbm;
        $tot5 = $tot5 + $row1->db30 + $row1->db60 + $row1->db90 + $row1->dbm;

        $de1 = $de1 + $row1->db30;
        $de2 = $de2 + $row1->db60;
        $de3 = $de3 + $row1->db90;
        $de4 = $de4 + $row1->dbm;
        $de5 = $de5 + $row1->db30 + $row1->db60 + $row1->db90 + $row1->dbm;
    }
    $m = $m + 1;
    $pdf->Cell(97, 5, '', 0, 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(40, 5, '', $cl, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, number_format($de1, 2), $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(40, 5, '', $cl, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, number_format($de2, 2), $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(40, 5, '', $cl, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, number_format($de3, 2), $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(40, 5, '', $cl, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, number_format($de4, 2), $cl, 0, 'R');
    $pdf->Cell(18, 5, number_format($de5, 2), $cl, 1, 'R');
    $pdf->Cell(15, 5, '', 0, 1, 'R');
    if ($m >= 25) {
        $pdf->AddPage('L', 'Legal');
        $m = 1;
    }
}
$pdf->Cell(10, 5, '', 0, 0, 'R');
$pdf->Cell(12, 5, '', 0, 0, 'R');
$pdf->Cell(75, 5, 'TOTALES: ', 1, 0, 'R');
$pdf->Cell(54, 5, number_format($tot1, 2), 1, 0, 'R');
$pdf->Cell(54, 5, number_format($tot2, 2), 1, 0, 'R');
$pdf->Cell(54, 5, number_format($tot3, 2), 1, 0, 'R');
$pdf->Cell(54, 5, number_format($tot4, 2), 1, 0, 'R');
$pdf->Cell(22, 5, number_format($tot5, 2), 1, 1, 'R');

$pdf->Output();
?>