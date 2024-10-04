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
    ['CTA', 'ACC'],
    ['TOTALES', 'TOTALS'],
    ['MAS 90', 'MORE 90'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['Pagina ', 'Page '],
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
    ['GRADO', 'GRADE'],
    ['Matri/Junio', 'Regis/June'],
    ['ESTUDIANTES', 'STUDENTS'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['DEUDA', 'DEBT'],
    ['PAGO', 'PAY'],
    ['BALANCE', 'BALANCE SHEET'],
    ['Estado de cuenta', 'Statement'],
    ['BALANCE DEL ESTADO DE CUENTA:', 'TOTAL BALANCE SHEET:'],
    ['PAGO REQUERIDO:', 'PAYMENT REQUIRED:'],
    ['Mensaje', 'Message'],
    ['No has seleccionado el mes del estado. Por favor vuelve e int&#65533;ntalo de nuevo.', 'You have not selected the state month. Please come back and try again.'],
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

        //Movernos a la derecha
        $pag = 80;
        //Ttulo
        //Salto de lnea
        $this->Ln(1);
        $this->Cell($pag);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 3, $lang->translation('Lista de deudores 30, 60, 90 ') . '/ ' . $year, 0, 0, 'C');
        $this->Ln(12);
        $this->Cell($pag);
        $this->Cell(30, 3, $_POST['ft1'], 0, 0, 'C');
        $this->Ln(12);

        $this->SetFont('Arial', 'B', 11);
        $this->Cell(10, 5, '', 1, 0, 'C', true);
        $this->Cell(15, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(80, 5, $lang->translation('NOMBRE ESTUDIANTES'), 1, 0, 'C', true);
        $this->Cell(15, 5, '30', 1, 0, 'C', true);
        $this->Cell(15, 5, '60', 1, 0, 'C', true);
        $this->Cell(15, 5, '90', 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('MAS 90'), 1, 0, 'C', true);
        $this->Cell(20, 5, 'TOTAL', 1, 1, 'C', true);
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
$pdf = new nPDF("P");
$pdf->SetTitle($lang->translation('Lista de deudores 30, 60, 90 ') . '/ ' . $year);
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetFont('Times', '', 11);
$cl = $_POST['cl'];
if ($_POST['orden'] == 1) {
    $result1 = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and activo=''")->orderBy('id')->get();
} else {
    $result1 = DB::table('year')->select("DISTINCT id")
        ->whereRaw("year='$year' and activo=''")->orderBy('apellidos, nombre')->get();
}
$aa = 0;
$gra = '';
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$tot4 = 0;
$tot5 = 0;
foreach ($result1 as $row1) {
    $row2 = DB::table('year')->whereRaw("id='$row1->id' and year='$year' and activo=''")->orderBy('apellidos, nombre')->first();
    $row4 = DB::table('madre')->whereRaw("id='$row1->id'")->first();

    $code = $_POST['desc'];
    if ($code == 'Todos') {
        $result3 = DB::table('pagos')->whereRaw("id='$row1->id' and year='$year' and baja='' and fecha_d < '" . $_POST['ft1'] . "'")->get();
    } else {
        $result3 = DB::table('pagos')->whereRaw("id='$row1->id' and codigo='$code' and year='$year' and baja='' and fecha_d < '" . $_POST['ft1'] . "'")->get();
    }
    $debe = 0;
    $mes1 = 0;
    $mes2 = 0;
    $mes3 = 0;
    $mes4 = 0;
    $mes5 = 0;
    foreach ($result3 as $row3) {
        $mm2 = 0;
        list($yy1, $mm1, $dd1) = explode("-", $_POST['ft1']);
        list($yy2, $mm2, $dd2) = explode("-", $_POST['ft1']);
        list($yy3, $mm3, $dd3) = explode("-", $_POST['ft1']);
        list($yy4, $mm4, $dd4) = explode("-", $_POST['ft1']);
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
        if (date($row3->fecha_d) == date($fec1)) {
            $debe = $debe + ($row3->deuda - $row3->pago);
            $mes1 = $mes1 + ($row3->deuda - $row3->pago);
        }
        if (date($row3->fecha_d) == date($fec2)) {
            $debe = $debe + ($row3->deuda - $row3->pago);
            $mes2 = $mes2 + ($row3->deuda - $row3->pago);
        }
        if (date($row3->fecha_d) == date($fec3)) {
            $debe = $debe + ($row3->deuda - $row3->pago);
            $mes3 = $mes3 + ($row3->deuda - $row3->pago);
        }
        if (date($row3->fecha_d) < date($fec3)) {
            $debe = $debe + ($row3->deuda - $row3->pago);
            $mes4 = $mes4 + ($row3->deuda - $row3->pago);
        }
    }
    if ($debe > 0) {
        $pdf->SetFont('Times', '', 10);
        $aa = $aa + 1;
        $pdf->Cell(10, 5, $aa, $cl, 0, 'R');
        $pdf->Cell(15, 5, $row2->id, $cl, 0, 'R');
        $pdf->Cell(80, 5, $row2->apellidos . ' ' . $row2->nombre, $cl, 0, 'L');
        if ($mes1 > 0) {
            $pdf->Cell(15, 5, number_format($mes1, 2), $cl, 0, 'R');
        } else {
            $pdf->Cell(15, 5, '', $cl, 0, 'R');
        }
        if ($mes2 > 0) {
            $pdf->Cell(15, 5, number_format($mes2, 2), $cl, 0, 'R');
        } else {
            $pdf->Cell(15, 5, '', $cl, 0, 'R');
        }
        if ($mes3 > 0) {
            $pdf->Cell(15, 5, number_format($mes3, 2), $cl, 0, 'R');
        } else {
            $pdf->Cell(15, 5, '', $cl, 0, 'R');
        }
        if ($mes4 > 0) {
            $pdf->Cell(20, 5, number_format($mes4, 2), $cl, 0, 'R');
        } else {
            $pdf->Cell(20, 5, '', $cl, 0, 'R');
        }
        if ($debe > 0) {
            $pdf->Cell(20, 5, number_format($mes1 + $mes2 + $mes3 + $mes4, 2), $cl, 0, 'R');
        } else {
            $pdf->Cell(20, 5, '', $cl, 0, 'R');
        }
        $pdf->Cell(1, 5, '', 0, 1, 'L');
        if ($_POST['ct'] == 'Si') {
            $pdf->Cell(25, 5, '', 0, 0, 'R');
            if ($row4->qpaga == 'P') {
                $pdf->Cell(80, 5, $row4->padre, $cl, 0, 'L');
                if ($row4->tel_p == '(___)___-____') {
                    $pdf->Cell(30, 5, 'R. ', $cl, 0, 'L');
                } else {
                    $pdf->Cell(30, 5, 'R. ' . $row4->tel_p, $cl, 0, 'L');
                }
                if ($row4->tel_t_p == '(___)___-____') {
                    $pdf->Cell(28, 5, 'T. ', $cl, 0, 'L');
                } else {
                    $pdf->Cell(28, 5, 'T. ' . $row4->tel_t_p, $cl, 0, 'L');
                }
                if ($row4->cel_p == '(___)___-____') {
                    $pdf->Cell(27, 5, 'C. ', $cl, 1, 'L');
                } else {
                    $pdf->Cell(27, 5, 'C. ' . $row4->cel_p, $cl, 1, 'L');
                }
            } else {
                $pdf->Cell(80, 5, $row4->madre, $cl, 0, 'L');
                if ($row4->tel_m == '(___)___-____') {
                    $pdf->Cell(30, 5, 'R. ', $cl, 0, 'L');
                } else {
                    $pdf->Cell(30, 5, 'R. ' . $row4->tel_m, $cl, 0, 'L');
                }
                if ($row4->tel_t_m == '(___)___-____') {
                    $pdf->Cell(28, 5, 'T. ', $cl, 0, 'L');
                } else {
                    $pdf->Cell(28, 5, 'T. ' . $row4->tel_t_m, $cl, 0, 'L');
                }
                if ($row4->cel_m == '(___)___-____') {
                    $pdf->Cell(27, 5, 'C. ', $cl, 1, 'L');
                } else {
                    $pdf->Cell(27, 5, 'C. ' . $row4->cel_m, $cl, 1, 'L');
                }
            }
        }
        $tot1 = $tot1 + $mes1;
        $tot2 = $tot2 + $mes2;
        $tot3 = $tot3 + $mes3;
        $tot4 = $tot4 + $mes4;
        $tot5 = $tot5 + $mes1 + $mes2 + $mes3 + $mes4;
    }
}

$pdf->Cell(10, 5, '', 1, 0, 'R');
$pdf->Cell(15, 5, '', 1, 0, 'R');
$pdf->Cell(80, 5, 'TOTALES: ', 1, 0, 'R');
$pdf->Cell(15, 5, number_format($tot1, 2), 1, 0, 'R');
$pdf->Cell(15, 5, number_format($tot2, 2), 1, 0, 'R');
$pdf->Cell(15, 5, number_format($tot3, 2), 1, 0, 'R');
$pdf->Cell(20, 5, number_format($tot4, 2), 1, 0, 'R');
$pdf->Cell(20, 5, number_format($tot5, 2), 1, 1, 'R');

$pdf->Output();
