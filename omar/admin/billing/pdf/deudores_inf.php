<?php
if ($_POST['gru'] === 'B') {
    include 'deudores_inf_2.php';
    exit;
}

require_once '../../../app.php';

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

$months = [
    "8" => 'ago',
    "9" => 'sep',
    "10" => 'oct',
    "11" => 'nov',
    "12" => 'dic',
    "01" => 'ene',
    "02" => 'feb',
    "03" => 'mar',
    "04" => 'abr',
    "05" => 'may',
    "06" => 'jun',
    "07" => 'jul',
];
$date = date('Y-m-d');
$code = $_POST['desc'];
class nPDF extends PDF
{
    public function Header()
    {
        global $colegio;
        global $year;
        $this->Ln(12);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, "INFORME DE DEUDORES POR GRADO $year", 0, 0, 'C');
        $this->Ln(12);
    }

    //Pie de pgina
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}

//Creación del objeto de la clase heredada
$pdf = new nPDF();
$pdf->SetTitle($lang->translation('INFORME DE DEUDORES POR GRADO') . ' ' . $year);
$pdf->Fill();

$pdf->AliasNbPages();

if ($_POST['gru'] === 'A') {
    $infoData = DB::table('year')->select("DISTINCT grado")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('grado')->get();
} else {
    $infoData = DB::table('year')->select("DISTINCT id, ss")
        ->whereRaw("year='$year' and activo !='B'")->orderBy('id')->get();
}
$TotalForGrades = $TotalForGradesMonthly = $Grades = [];
foreach ($infoData as $info) {
    // variables
    $debts = [];
    // diseño cabezera
    if ($_POST['gru'] === 'A') {
        /* ----------------- Buscar todos los estudiantes por grado ----------------- */
        $grade = $info->grado;
        $TotalForGrades[$grade] = 0;
        $TotalForGradesMonthly[$grade] = [];
        $Grades[] = $info->grado;
        $students = DB::table('year')
            ->whereRaw("grado='$grade' AND year='$year' and activo !='B'")->orderBy('apellidos, nombre')->get();
    } else {
        /* ------------------------ Buscar estudiante por id ------------------------ */
        $grade = $info->ss;
        $TotalForGrades[$grade] = 0;
        $TotalForGradesMonthly[$grade] = [];
        $Grades[] = $info->ss;

        $students = DB::table('year')
            ->whereRaw("id='$info->id' AND year='$year' and activo !='B'")->orderBy('apellidos, nombre')->get();
    }
    $pdf->addPage($_POST['pag'], $_POST['pag1']);
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(0, 5, $_POST['gru'] === 'A' ? "GRADO: $grade" : "");
    $pdf->Cell(0, 5, "FECHA: " . date('d-m-Y'), 0, 1, 'R');

    $pdf->Cell(95, 5, $_POST['gru'] === 'A' ? "NOMBRE ESTUDIANTE" : "FAMILIA", 1, 0, 'C', true);
    //  echo '<pre>';
    //  var_dump($_POST);
    //  echo '</pre>';
    //  exit;
    foreach ($months as $month) {
        $TotalForGradesMonthly[$grade][$month] = 0;
        if (isset($_POST[$month]) && $_POST[$month] === '1') {
            if ($_POST['cct'] == 1) {
                $pdf->Cell(16, 5, strtoupper($month), 1, 0, 'C', true);
            }
        }
    }
    $pdf->Cell(16, 5, 'TOTAL', 1, 1, 'C', true);
    //end diseño cabezera

    foreach ($students as $student) {
        $TOTAL = [];
        foreach ($months as $month) {
            if (isset($_POST[$month]) && $_POST[$month] === '1') {
                $debts[$student->ss][$month] = 0;
                $TOTAL[$month] = 0;
            }
        }

        if ($_POST['gru'] === 'A') {
            if ($_POST['desc'] === 'Todos') {
                $payments = DB::table('pagos')
                    ->whereRaw("baja='' AND codigo !=711 AND ss='$student->ss' AND year='$year' AND fecha_d <= '$date'")->orderBy('nombre')->get();
            } else {
                $payments = DB::table('pagos')
                    ->whereRaw("baja='' AND codigo='$code' AND ss='$student->ss' AND year='$year' AND fecha_d <= '$date'")->orderBy('nombre')->get();
            }
        } else {
            if ($_POST['desc'] === 'Todos') {
                $payments = DB::table('pagos')
                    ->whereRaw("baja='' AND id = '$student->id' AND year='$year' AND fecha_d <= '$date'")->orderBy('nombre')->get();
            } else {
                $payments = DB::table('pagos')
                    ->whereRaw("baja='' AND codigo='$code' AND id = '$student->id' AND year='$year' AND fecha_d <= '$date'")->orderBy('nombre')->get();
            }
        }
        //   echo '<pre>';
        //   var_dump($debts);
        //   echo '</pre>';
        //   exit;
        foreach ($payments as $payment) {
            list($paymentYear, $paymentMonth, $paymentDay) = explode("-", $payment->fecha_d);
            foreach ($months as $number => $month) {
                if (isset($_POST[$month]) && $_POST[$month] === '1' && $paymentMonth == $number) {
                    $debts[$student->ss][$month] += $payment->deuda - $payment->pago;
                } else if (isset($_POST[$month]) && $_POST[$month] === '1') {
                    $debts[$student->ss][$month] += 0;
                }
            }
        }
    }
    // echo '<pre>';
    // var_dump($debts);
    // echo '</pre>';
    // exit;

    foreach ($students as $student) {
        if (isset($debts[$student->ss])) {
            $total = 0;
            $debtor = false;
            foreach ($debts[$student->ss] as $debt) {
                if ($debt != 0) {
                    $debtor = true;
                    break;
                }
            }
            if ($debtor) {
                $pdf->Cell(15, 5, $student->id, 1, 0, 'C');
                $pdf->Cell(80, 5, $_POST['gru'] === 'A' ? "$student->apellidos $student->nombre" : $student->apellidos, 1);
                foreach ($months as $month) {
                    if (isset($debts[$student->ss][$month])) {
                        $thisDebt = $debts[$student->ss][$month] > 0 ? $debts[$student->ss][$month] : 0;
                        // Total por estudiante
                        $total += $thisDebt;
                        // Total de todos los estudiantes por mes
                        $TOTAL[$month] += $thisDebt;
                        $TotalForGradesMonthly[$grade][$month] += $thisDebt;
                        if ($_POST['cct'] == 1) {
                            $pdf->Cell(16, 5, $thisDebt > 0 ? number_format($thisDebt, 2) : '', 1, 0, 'R');
                        }
                    }
                }
                $pdf->Cell(16, 5, $total > 0 ? number_format($total, 2) : '', 1, 1, 'R');
            }
        }
    }
    // TOTAL
    $pdf->Cell(95, 5, 'TOTAL:', 1, 0, 'R');
    $totalOfAll = 0;
    foreach ($months as $month) {
        if (isset($TOTAL[$month])) {
            // Total de todos los estudiantes de todos los meses
            $totalOfAll += $TOTAL[$month];
            // Total de todos los estudiantes de todos los grados
            $TotalForGrades[$grade] += $TOTAL[$month];
            if ($_POST['cct'] == 1) {
                $pdf->Cell(16, 5, $TOTAL > 0 ? number_format($TOTAL[$month], 2) : '', 1, 0, 'R');
            }
        }
    }
    $pdf->Cell(16, 5, number_format($totalOfAll, 2), 1, 0, 'R');
}
// echo '<pre>';
// var_dump($TotalForGradesMonthly);
// echo '</pre>';

if ($_POST['gru'] === 'A') {
    $pdf->SetFont('Arial', '', 10);

    $pdf->addPage($_POST['pag'], $_POST['pag1']);
    $pdf->Cell(15, 5, 'GRADO', 1, 0, 'C', true);
    $TOTAL = 0;
    $TOTAL2 = [];
    foreach ($months as $month) {
        $TOTAL2[$month] = 0;
        $pdf->Cell(14, 5, strtoupper($month), 1, 0, 'C', true);
    }

    $pdf->Cell(20, 5, 'TOTAL', 1, 1, 'C', true);

    foreach ($Grades as $grade) {
        $TOTAL += $TotalForGrades[$grade];
        $pdf->Cell(15, 5, $grade, 1, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        foreach ($months as $month) {
            $TOTAL2[$month] += $TotalForGradesMonthly[$grade][$month];
            $pdf->Cell(14, 5, number_format($TotalForGradesMonthly[$grade][$month], 2), 1, 0, 'R');
        }
        $pdf->Cell(20, 5, number_format($TotalForGrades[$grade], 2), 1, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(15, 5, "TOTAL:", 1, 0, 'R');
    $pdf->SetFont('Arial', 'B', 8);
    foreach ($months as $month) {
        $pdf->Cell(14, 5, number_format($TOTAL2[$month], 2), 1, 0, 'R');
    }
    $pdf->Cell(20, 5, number_format($TOTAL, 2), 1, 1, 'R');
}

$pdf->Output();
