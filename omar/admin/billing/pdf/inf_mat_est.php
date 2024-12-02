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
    ['TOTALES: ', 'TOTALS: '],
    ['Total', 'Total'],
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

        $pag = 80;
        $this->Cell($pag);
        $this->SetFont('Arial', 'B', 12);
        if ($_POST['estq'] == 1) {
            $this->Cell(30, 3, $lang->translation('LISTA DE ESTUDIANTES MATRICULA NUEVOS') . ' / ' . $year, 0, 1, 'C');
        }
        if ($_POST['estq'] == 2) {
            $this->Cell(30, 3, $lang->translation('LISTA DE ESTUDIANTES MATRICULA ACTIVOS') . ' / ' . $year, 0, 1, 'C');
        }
        if ($_POST['estq'] == 3) {
            $this->Cell(30, 3, $lang->translation('LISTA DE ESTUDIANTES MATRICULA GENERAL') . ' / ' . $year, 0, 1, 'C');
        }
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(12, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(80, 5, $lang->translation('NOMBRE ESTUDIANTES'), 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation('Grado'), 1, 0, 'C', true);
        $this->Cell(65, 5, utf8_decode($lang->translation('Descripción')), 1, 1, 'C', true);
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
$pdf->AddPage();
$pdf->SetFont('Times', '', 11);
$cl = $_POST['cl'];
$db = new DB();
$db->query('Truncate deudores');
if ($_POST['orden'] == 1) {
    if ($_POST['estq'] == 1) {
        $result1 = DB::table('year')->select("DISTINCT id, ss")->where([
            ['nuevo', 'Si'],
            ['year', $year]
        ])->orderBy('id')->get();
    }
    if ($_POST['estq'] == 2) {
        $result1 = DB::table('year')->select("DISTINCT id, ss")->where([
            ['nuevo', 'No'],
            ['year', $year]
        ])->orderBy('id')->get();
    }
    if ($_POST['estq'] == 3) {
        $result1 = DB::table('year')->select("DISTINCT id, ss")->where([
            ['year', $year]
        ])->orderBy('id')->get();
    }
} else {
    $result1 = DB::table('year')->select("DISTINCT id, ss")->where([
        ['nuevo', 'Si'],
        ['year', $year]
    ])->orderBy('id')->get();
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
    $result22 = DB::table('year')->where([
        ['id', $row1->id],
        ['ss', $row1->ss],
        ['year', $year]
    ])->orderBy('id')->get();
    $row2 = DB::table('year')->where([
        ['id', $row1->id],
        ['ss', $row1->ss],
        ['year', $year]
    ])->orderBy('id')->first();
    $code = $_POST['desc'];
    $debe = 0;
    foreach ($result22 as $row3) {
        if ($_POST['desc'] == 'Todos') {
            $result3 = DB::table('pagos')->select("DISTINCT id, codigo, deuda, pago")->where([
                ['id', $row1->id],
                ['deuda', '>', 0],
                ['ss', $row1->ss],
                ['baja', ''],
                ['year', $year]
            ])->orderBy('id')->get();
        } else {
            $result3 = DB::table('pagos')->where([
                ['id', $row1->id],
                ['deuda', '>', 0],
                ['ss', $row1->ss],
                ['baja', ''],
                ['codigo', $code],
                ['fecha_d', '<', $_POST['ft1']],
                ['year', $year]
            ])->orderBy('id')->get();
        }
        foreach ($result3 as $row33) {
            $debe = $debe + $row33->deuda;
        }
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
            $q = DB::table('presupuesto')->where([
                ['year', $year]
            ])->orderBy('codigo')->get();
        } else {
            $q = DB::table('presupuesto')->where([
                ['codigo', $code],
                ['year', $year]
            ])->orderBy('codigo')->get();
        }
    }
    $result31 = $q ?? [];
    $result32 = $q ?? [];
    $result33 = $q ?? [];
    $result34 = $q ?? [];
    $debe = 0;
    $mes1 = 0;
    $mes2 = 0;
    $mes3 = 0;
    $mes4 = 0;
    $mes5 = 0;
    list($yy1, $mm1, $dd1) = explode("-", date('Y-m-d'));
    list($yy2, $mm2, $dd2) = explode("-", date('Y-m-d'));
    list($yy3, $mm3, $dd3) = explode("-", date('Y-m-d'));
    list($yy4, $mm4, $dd4) = explode("-", date('Y-m-d'));
    $m1 = '-';
    $m2 = '-';
    $m3 = '-';
    $m4 = '-';
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
        $result10 = DB::table('pagos')->select("DISTINCT id, codigo, deuda")->where([
            ['id', $row1->id],
            ['deuda', '>', 0],
            ['ss', $row1->ss],
            ['baja', ''],
            ['codigo', $row3->codigo],
            ['year', $year]
        ])->orderBy('id')->get();
        foreach ($result10 as $row10) {
            $debe = $debe + $row10->deuda;
            $mes1 = $mes1 + $row10->deuda;
            $db3 = $db3 + $row10->deuda;
            $dbm = $dbm + $row10->deuda;
        }
        if ($mes1 > 0) {
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
                'grado' => $row2->grado,
            ]);
        }
    }
    $li = 0;
    foreach ($result32 as $row3) {
        $mm2 = 0;
        $mes1 = 0;
        $result10 = DB::table('pagos')->select("DISTINCT id, codigo, deuda")->where([
            ['id', $row1->id],
            ['deuda', '>', 0],
            ['ss', $row1->ss],
            ['baja', ''],
            ['codigo', $row3->codigo],
            ['year', $year],
            ['fecha_d', date($fec2)]
        ])->orderBy('id')->get();
        foreach ($result10 as $row10) {
            $debe = $debe + $row10->deuda;
            $mes1 = $mes1 + $row10->deuda;
            $db6 = $db6 + $row10->deuda;
            $dbm = $dbm + $row10->deuda;
        }
        if ($mes1 > 0) {
            $li = $li + 1;
            $vlb = $vlb + 5;
            $ns2 = $ns2 + 1;
            $re = DB::table('deudores')->where([
                ['cta', $row2->id],
                ['ss', $row2->ss]
            ])->orderBy('cta')->first();
            $ba = $re->ss ?? 0;
            if ($ba == 0) {
                DB::table('deudores')->insert([
                    'cta' => $row2->id,
                    'ss' => $row2->ss,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'da60' => $row3->descripcion,
                    'db60' => $mes1,
                    'linia' => $li,
                    'grado' => $row2->grado,
                ]);
            } else {
                $t = DB::table('deudores')->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'da60' => $row3->descripcion,
                    'db60' => $mes1,
                ]);
            }
        }
    }
    $li = 0;
    foreach ($result33 as $row3) {
        $mm2 = 0;
        $mes1 = 0;
        $result10 = DB::table('pagos')->select("DISTINCT id, codigo, deuda")->where([
            ['id', $row1->id],
            ['deuda', '>', 0],
            ['ss', $row1->ss],
            ['baja', ''],
            ['codigo', $row3->codigo],
            ['year', $year],
            ['fecha_d', date($fec3)]
        ])->orderBy('id')->get();
        foreach ($result10 as $row10) {
            $debe = $debe + $row10->deuda;
            $mes1 = $mes1 + $row10->deuda;
            $db3 = $db3 + $row10->deuda;
            $dbm = $dbm + $row10->deuda;
        }
        if ($mes1 > 0) {
            $li = $li + 1;
            $vlc = $vlc + 5;
            $re = DB::table('deudores')->where([
                ['cta', $row2->id],
                ['ss', $row2->ss]
            ])->orderBy('cta')->first();
            $ba = $re->ss ?? 0;
            if ($ba == 0) {
                DB::table('deudores')->insert([
                    'cta' => $row2->id,
                    'ss' => $row2->ss,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'da90' => $row3->descripcion,
                    'db90' => $mes1,
                    'linia' => $li,
                    'grado' => $row2->grado,
                ]);
            } else {
                $t = DB::table('deudores')->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'da90' => $row3->descripcion,
                    'db90' => $mes1,
                ]);
            }
        }
    }
    $li = 0;
    foreach ($result34 as $row3) {
        $mm2 = 0;
        $mes1 = 0;
        $result10 = DB::table('pagos')->select("DISTINCT id, codigo, deuda, pago")->where([
            ['id', $row1->id],
            ['deuda', '>', 0],
            ['ss', $row1->ss],
            ['baja', ''],
            ['codigo', $row3->codigo],
            ['year', $year],
            ['fecha_d', date($fec4)]
        ])->orderBy('id')->get();
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
            $re = DB::table('deudores')->where([
                ['cta', $row2->id],
                ['ss', $row2->ss]
            ])->orderBy('cta')->first();
            $ba = $re->ss ?? 0;
            if ($ba == 0) {
                DB::table('deudores')->insert([
                    'cta' => $row2->id,
                    'ss' => $row2->ss,
                    'nombre' => $row2->apellidos . ' ' . $row2->nombre,
                    'dam' => $row3->descripcion,
                    'dbm' => $mes1,
                    'linia' => $li,
                    'grado' => $row2->grado,
                ]);
            } else {
                $t = DB::table('deudores')->where([
                    ['cta', $row2->id],
                    ['ss', $row2->ss],
                    ['linia', $li]
                ])->update([
                    'dam' => $row3->descripcion,
                    'dbm' => $mes1,
                ]);
            }
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

$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;

$result1 = DB::table('deudores')->select("DISTINCT cta, ss, nombre, grado")->orderBy('cta, ss')->get();
$l = 0;
foreach ($result1 as $row0) {
    $pdf->SetFont('Arial', '', 12);
    $l = $l + 1;
    $result2 = DB::table('deudores')->where([
        ['ss', $row0->ss]
    ])->orderBy('cta, ss')->get();
    $pdf->Cell(10, 5, $l, $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(12, 5, $row0->cta, $cl, 0, 'R');
    $pdf->Cell(80, 5, $row0->nombre, $cl, 0, 'L');
    $pdf->Cell(15, 5, $row0->grado, $cl, 0, 'C');
    $l2 = 0;
    $de1 = 0;
    $de2 = 0;
    $de3 = 0;
    $de4 = 0;
    $de5 = 0;
    foreach ($result2 as $row1) {
        $l2 = $l2 + 1;
        if ($l2 > 1) {
            $pdf->Cell(117, 5, '', 0, 0, 'R');
        }
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5, $row1->da30, $cl, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(15, 5, $row1->db30, $cl, 1, 'R');
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
    $pdf->Cell(117, 5, '', 0, 0, 'R');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 5, $lang->translation('Total'), $cl, 0, 'R');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(15, 5, number_format($de1, 2), $cl, 1, 'R');
    $pdf->Cell(15, 5, '', 0, 1, 'R');
}
$pdf->Cell(10, 5, '', 0, 0, 'R');
$pdf->Cell(12, 5, '', 0, 0, 'R');
$pdf->Cell(95, 5, $lang->translation('TOTALES: '), 1, 0, 'R');
$pdf->Cell(64, 5, number_format($tot1, 2), 1, 1, 'R');
$pdf->Output();
