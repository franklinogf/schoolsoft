<?php
if ($_POST['desc'] == 'D') {
    //   require('inf_pagos2.php');
    //   exit;
}

require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['INFORME DE PAGOS POR GRADO', 'PAYMENT REPORT BY GRADE'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['CODIGO', 'CODE'],
    ['PAGOS', 'PAYS'],
    ['DEUDAS', 'DEBTS'],
    ['BALANCES', 'BALANCES'],
    ['Pagina ', 'Page '],
    ['FECHA: ', 'DATE: '],
    ['NOMBRE ESTUDIANTES', 'STUDENT NAMES'],
    ['HASTA', 'TO'],
    ['GRAN TOTAL:', 'GRAND TOTAL:'],
    ['Ago', 'Aug'],
    ['Sep', 'Sep'],
    ['Oct', 'Oct'],
    ['Nov', 'Nov'],
    ['Dic', 'Dec'],
    ['Ene', 'Jan'],
    ['Feb', 'Feb'],
    ['Mar', 'Mar'],
    ['Abr', 'Abr'],
    ['May', 'May'],
    ['Jun', 'Jun'],
    ['Jul', 'Jul'],
    ['Grados', 'Grades'],
    ['Matri/Junio', 'Regis/June'],
    ['Totales: ', 'Totals: '],
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
$grades = $school->allGrades();

class nPDF extends PDF
{
    function Header()
    {

        global $year;
        parent::header();
        global $lang;

        if ($_POST['pag'] == 'P') {
            $pag = 80;
        }
        if ($_POST['pag'] == 'L') {
            $pag = 120;
        }
        //	$this->Cell($pag);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 3, $lang->translation('INFORME DE PAGOS POR GRADO') . ' ' . $year, 0, 0, 'C');
        $this->Ln(5);
    }

    function foo($bb)
    {
        $this->Cell(90, 5, $lang->translation('NOMBRE ESTUDIANTES'), 1, 0, 'C', true);
        if ($_POST['ago'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('AGO'), 1, 0, 'C', true);
        }
        if ($_POST['sep'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('SEP'), 1, 0, 'C', true);
        }
        if ($_POST['oct'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('OCT'), 1, 0, 'C', true);
        }
        if ($_POST['nov'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('NOV'), 1, 0, 'C', true);
        }
        if ($_POST['dic'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('DIC'), 1, 0, 'C', true);
        }
        if ($_POST['ene'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('ENE'), 1, 0, 'C', true);
        }
        if ($_POST['feb'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('FEB'), 1, 0, 'C', true);
        }
        if ($_POST['mar'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('MAR'), 1, 0, 'C', true);
        }
        if ($_POST['abr'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('ABR'), 1, 0, 'C', true);
        }
        if ($_POST['may'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('MAY'), 1, 0, 'C', true);
        }
        if ($_POST['jun'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('JUN'), 1, 0, 'C', true);
        }
        if ($_POST['jul'] ?? '' == 1) {
            $this->Cell(15, 5, $lang->translation('JUL'), 1, 0, 'C', true);
        }
        $this->Cell(1, 5, '', 0, 1, 'C');
    }

    function gra($gra)
    {
        $this->AddPage($_POST['pag'], $_POST['pag1']);
        $this->SetFont('Times', '', 11);
        $this->Cell(30, 5, 'GRADO: ' . $gra, 0, 0, 'L');
        if ($_POST['pag'] == 'P') {
            $pag = 50;
        }
        if ($_POST['pag'] == 'L') {
            $pag = 90;
        }

        $this->Cell($pag, 5, '', 0, 0, 'L');
        $this->Cell(30, 5, $lang->translation('FECHA: ') . date('m-d-Y'), 0, 1, 'C');
    }

    //Pie de pgina
    function Footer()
    {
        global $lang;

        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //N&uacute;mero de p&aacute;gina
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

//Creacin del objeto de la clase heredada
$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetTitle($lang->translation('INFORME DE PAGOS POR GRADO') . ' ' . $year);

$aa = 0;
$gra = '';
foreach ($grades as $grade) {
    $result2 = DB::table('year')->whereRaw("grado='$grade' and year='$year' and activo = ''")->orderBy('apellidos, nombre')->get();

    $debe2 = 0;
    $mes11 = 0;
    $mes22 = 0;
    $mes33 = 0;
    $mes44 = 0;
    $mes55 = 0;
    $mes66 = 0;
    $mes77 = 0;
    $mes88 = 0;
    $mes99 = 0;
    $mes110 = 0;
    $mes111 = 0;
    $mes112 = 0;

    $aa = $aa + 1;
    foreach ($result2 as $row2) {
        if ($_POST['desc'] == 'Todos') {
            $result3 = DB::table('pagos')->whereRaw("ss='$row2->ss' and year='$year' and fecha_d < '" . date('Y-m-d') . "'")->orderBy('nombre')->get();
        } else {
            $code = $_POST['desc'];
            $result3 = DB::table('pagos')->whereRaw("codigo='$code' and ss='$row2->ss' and year='$year' and fecha_d < '" . date('Y-m-d') . "'")->orderBy('nombre')->get();
        }
        $debe = 0;
        $mes1 = 0;
        $mes2 = 0;
        $mes3 = 0;
        $mes4 = 0;
        $mes5 = 0;
        $mes6 = 0;
        $mes7 = 0;
        $mes8 = 0;
        $mes9 = 0;
        $mes10 = 0;
        //            $mes11=0;
        $mes12 = 0;
        $mes113 = 0;

        foreach ($result3 as $row3) {
            list($yy, $mm, $dd) = explode("-", $row3->fecha_d);
            if ($_POST['ago'] ?? '' == 1 and $mm == 8) {
                $debe = $debe + $row3->pago;
                $mes8 = $mes8 + $row3->pago;
                $mes88 = $mes88 + $row3->pago;
            }
            if ($_POST['sep'] ?? '' == 1 and $mm == 9) {
                $debe = $debe + $row3->pago;
                $mes9 = $mes9 + $row3->pago;
                $mes99 = $mes99 + $row3->pago;
            }
            if ($_POST['oct'] ?? '' == 1 and $mm == 10) {
                $debe = $debe + $row3->pago;
                $mes10 = $mes10 + $row3->pago;
                $mes110 = $mes110 + $row3->pago;
            }
            if ($_POST['nov'] ?? '' == 1 and $mm == 11) {
                $debe = $debe + $row3->pago;
                $mes113 = $mes113 + $row3->pago;
                $mes111 = $mes111 + $row3->pago;
            }
            if ($_POST['dic'] ?? '' == 1 and $mm == 12) {
                $debe = $debe + $row3->pago;
                $mes12 = $mes12 + $row3->pago;
                $mes112 = $mes112 + $row3->pago;
            }
            if ($_POST['ene'] ?? '' == 1 and $mm == 1) {
                $debe = $debe + $row3->pago;
                $mes1 = $mes1 + $row3->pago;
                $mes11 = $mes11 + $row3->pago;
            }
            if ($_POST['feb'] ?? '' == 1 and $mm == 2) {
                $debe = $debe + $row3->pago;
                $mes2 = $mes2 + $row3->pago;
                $mes22 = $mes22 + $row3->pago;
            }
            if ($_POST['mar'] ?? '' == 1 and $mm == 3) {
                $debe = $debe + $row3->pago;
                $mes3 = $mes3 + $row3->pago;
                $mes33 = $mes33 + $row3->pago;
            }
            if ($_POST['abr'] ?? '' == 1 and $mm == 4) {
                $debe = $debe + $row3->pago;
                $mes4 = $mes4 + $row3->pago;
                $mes44 = $mes44 + $row3->pago;
            }
            if ($_POST['may'] ?? '' == 1 and $mm == 5) {
                $debe = $debe + $row3->pago;
                $mes5 = $mes5 + $row3->pago;
                $mes55 = $mes55 + $row3->pago;
            }
            if ($_POST['jun'] ?? '' == 1 and $mm == 6) {
                $debe = $debe + $row3->pago;
                $mes6 = $mes6 + $row3->pago;
                $mes66 = $mes66 + $row3->pago;
            }
            if ($_POST['jul'] ?? '' == 1 and $mm == 7) {
                $debe = $debe + $row3->pago;
                $mes7 = $mes7 + $row3->pago;
                $mes77 = $mes77 + $row3->pago;
            }
        }
        if ($gra != $grade and $debe > 0) {
            $gra = $grade;
            $pdf->gra($grade);
            $pdf->foo($aa);
        }
        if ($debe > 0) {
            $debe2 = $debe;
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(10, 5, $row2->id, 1, 0, 'R');
            $pdf->Cell(80, 5, $row2->apellidos . ' ' . $row2->nombre, 1, 0, 'L');
            if ($_POST['ago'] ?? '' == 1) {
                if ($mes8 > 0) {
                    $pdf->Cell(15, 5, number_format($mes8, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['sep'] ?? '' == 1) {
                if ($mes9 > 0) {
                    $pdf->Cell(15, 5, number_format($mes9, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['oct'] ?? '' == 1) {
                if ($mes10 > 0) {
                    $pdf->Cell(15, 5, number_format($mes10, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['nov'] ?? '' == 1) {
                if ($mes113 > 0) {
                    $pdf->Cell(15, 5, number_format($mes113, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['dic'] ?? '' == 1) {
                if ($mes12 > 0) {
                    $pdf->Cell(15, 5, number_format($mes12, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['ene'] ?? '' == 1) {
                if ($mes1 > 0) {
                    $pdf->Cell(15, 5, number_format($mes1, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['feb'] ?? '' == 1) {
                if ($mes2 > 0) {
                    $pdf->Cell(15, 5, number_format($mes2, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['mar'] ?? '' == 1) {
                if ($mes3 > 0) {
                    $pdf->Cell(15, 5, number_format($mes3, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['abr'] ?? '' == 1) {
                if ($mes4 > 0) {
                    $pdf->Cell(15, 5, number_format($mes4, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['may'] ?? '' == 1) {
                if ($mes5 > 0) {
                    $pdf->Cell(15, 5, number_format($mes5, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['jun'] ?? '' == 1) {
                if ($mes6 > 0) {
                    $pdf->Cell(15, 5, number_format($mes6, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            if ($_POST['jul'] ?? '' == 1) {
                if ($mes7 > 0) {
                    $pdf->Cell(15, 5, number_format($mes7, 2), 1, 0, 'R');
                } else {
                    $pdf->Cell(15, 5, '', 1, 0, 'R');
                }
            }
            $pdf->Cell(1, 5, '', 0, 1, 'L');
        }
    }
    if ($debe2 > 0) {
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(80, 5, $lang->translation('Totales: '), 1, 0, 'R');
        if ($_POST['ago'] ?? '' == 1) {
            if ($mes88 > 0) {
                $pdf->Cell(15, 5, number_format($mes88, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['sep'] ?? '' == 1) {
            if ($mes99 > 0) {
                $pdf->Cell(15, 5, number_format($mes99, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['oct'] ?? '' == 1) {
            if ($mes110 > 0) {
                $pdf->Cell(15, 5, number_format($mes110, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['nov'] ?? '' == 1) {
            if ($mes111 > 0) {
                $pdf->Cell(15, 5, number_format($mes111, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['dic'] ?? '' == 1) {
            if ($mes112 > 0) {
                $pdf->Cell(15, 5, number_format($mes112, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['ene'] ?? '' == 1) {
            if ($mes11 > 0) {
                $pdf->Cell(15, 5, number_format($mes11, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['feb'] ?? '' == 1) {
            if ($mes22 > 0) {
                $pdf->Cell(15, 5, number_format($mes22, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['mar'] ?? '' == 1) {
            if ($mes33 > 0) {
                $pdf->Cell(15, 5, number_format($mes33, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['abr'] ?? '' == 1) {
            if ($mes44 > 0) {
                $pdf->Cell(15, 5, number_format($mes44, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['may'] ?? '' == 1) {
            if ($mes55 > 0) {
                $pdf->Cell(15, 5, number_format($mes55, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['jun'] ?? '' == 1) {
            if ($mes66 > 0) {
                $pdf->Cell(15, 5, number_format($mes66, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        if ($_POST['jul'] ?? '' == 1) {
            if ($mes77 > 0) {
                $pdf->Cell(15, 5, number_format($mes77, 2), 1, 0, 'R');
            } else {
                $pdf->Cell(15, 5, '', 1, 0, 'R');
            }
        }
        $pdf->Cell(1, 5, '', 0, 1, 'L');
    }
}

$pdf->Output();
