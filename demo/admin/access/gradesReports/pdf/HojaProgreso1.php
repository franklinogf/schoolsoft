<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Server;
use Classes\Session;
use Classes\Util;

Session::is_logged();
Server::is_post();

$school = new School(Session::id());
$student = new Student();
$teacher = new Teacher();
$year = $school->info('year2');

if ($_POST['idioma'] == '1') {
    $idi = 'Espanol';
} else {
    $idi = 'Ingles';
}

class nPDF extends PDF
{
    function Header()
    {
        global $lang;
        global $year;
        global $idi;
        parent::header();

        $this->Cell(80);
        $this->SetFont('Arial', 'B', 12);
        if ($idi == 'Ingles') {
            $this->Cell(30, 3, 'PROGRESS REPORT', 0, 1, 'C');
        } else {
            $this->Cell(30, 3, 'PROGRESO DE NOTAS', 0, 1, 'C');
        }
        $this->SetFont('Arial', '', 12);
        $this->Cell(30, 3, '', 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(30, 3, $_POST['hoja'], 0, 0, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        global $idi;

        if ($idi == 'Ingles') {
            $this->SetY(-50);
            $this->Cell(50, 6, 'GRADING SCALE', 1, 0, 'C', true);
            $this->Cell(140, 6, 'LETTER DESCRIPTION', 1, 1, 'C', true);
            $this->Cell(50, 22, '', 1, 0, 'C');
            $this->Cell(140, 22, '', 1, 1, 'C');
            $this->Cell(50, -38, ' 100 - 90   A   =   4.00 - 3.50', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' E = EXCELLENT', 0, 0, 'E');
            $this->Cell(70, -38, ' INC = INCOMPLETE', 0, 1, 'E');
            $this->Cell(50, 46, '  89 - 80   B   =   3.49 - 2.50', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' B,G = GOOD', 0, 0, 'E');
            $this->Cell(70, 46, ' P = PARTICIPATION', 0, 1, 'E');
            $this->Cell(50, -38, '  79 - 70   C   =   2.49 - 1.50', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' S = SATISFACTORY', 0, 0, 'E');
            $this->Cell(70, -38, ' TP = TOTAL POINTS', 0, 1, 'E');
            $this->Cell(50, 46, '  69 - 60   D   =   1.49 - 0.80', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' NM,NI = NEEDS IMPROVEMENT', 0, 0, 'E');
            $this->Cell(70, 46, ' TPA = POINTS ACCUMULATED', 0, 1, 'E');
            $this->Cell(50, -38, '  59 -   0    F   =   0.79 - 0.00', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' NS,U = UNSATISFACTORY', 0, 0, 'E');
            $this->Cell(70, -38, ' AVG = AVERAGE', 0, 1, 'E');
            $this->Cell(5, 55, '  ', 0, 0, 'R');
            $this->Cell(50, 55, ' ______________________________ ', 0, 0, 'C');
            $this->Cell(65, 55, '  ', 0, 0, 'C');
            $this->Cell(65, 55, ' ______________________________ ', 0, 1, 'C');
            $this->Cell(5, -47, '  ', 0, 0, 'R');
            $this->Cell(50, -47, 'Teacher / Authorized Signature', 0, 0, 'C');
            $this->Cell(65, -47, '', 0, 0, 'C');
            $this->Cell(65, -47, "Parent's Signature", 0, 0, 'C');
        } else {
            $this->SetY(-50);
            $this->Cell(1, 6, '', 0, 0, 'C');
            $this->Cell(52, 6, 'ESCALA', 1, 0, 'C', true);
            $this->Cell(140, 6, 'LEYENDA', 1, 1, 'C', true);
            $this->Cell(1, 6, '', 0, 0, 'C');
            $this->Cell(52, 22, '', 1, 0, 'C');
            $this->Cell(140, 22, '', 1, 1, 'C');
            $this->Cell(52, -38, ' 100 - 90   A   =   4.00 - 3.50', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' E = EXELENTE', 0, 0, 'E');
            $this->Cell(70, -38, ' INC = INCOMPLETO', 0, 1, 'E');
            $this->Cell(52, 46, '  89 - 80   B   =   3.49 - 2.50', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' B,G = BUENO', 0, 0, 'E');
            $this->Cell(70, 46, ' P = PARTICIPACION', 0, 1, 'E');
            $this->Cell(52, -38, '  79 - 70   C   =   2.49 - 1.50', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' S = SATISFACTORIO', 0, 0, 'E');
            $this->Cell(70, -38, ' TP = TOTAL DE PUNTOS', 0, 1, 'E');
            $this->Cell(52, 46, '  69 - 60   D   =   1.49 - 0.80', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' NM,NI = NECESITA MEJORAR', 0, 0, 'E');
            $this->Cell(70, 46, ' TPA = PUNTOS ACUMULADOS', 0, 1, 'E');
            $this->Cell(52, -38, '  59 -   0    F   =   0.79 - 0.00', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' NS,U = NO SATISFACTORIO', 0, 0, 'E');
            $this->Cell(70, -38, ' PRO = PROMEDIO', 0, 1, 'E');
            $this->Cell(5, 55, '  ', 0, 0, 'R');
            $this->Cell(52, 55, ' ______________________________ ', 0, 0, 'C');
            $this->Cell(65, 55, '  ', 0, 0, 'C');
            $this->Cell(65, 55, ' ______________________________ ', 0, 1, 'C');
            $this->Cell(7, -47, '  ', 0, 0, 'R');
            $this->Cell(52, -47, 'Maestro(a) / Firma Autorizada', 0, 0, 'C');
            $this->Cell(65, -47, '', 0, 0, 'C');
            $this->Cell(65, -47, 'Firma padre/madre', 0, 0, 'C');
        }
    }
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->SetFont('Times', '', 11);
$fra = $_POST['tri'];
$grado = $_POST['grade'];
$men = $_POST['mensaj'];
$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

if ($idi == 'Ingles') {
    if ($fra == '1') {
        $qui = 'PARTIAL GRADES FOR THE FIRST QUARTER';
    }
    if ($fra == '2') {
        $qui = 'PARTIAL GRADES FOR THE SECOND QUARTER';
    }
    if ($fra == '3') {
        $qui = 'PARTIAL GRADES FOR THE THIRD QUARTER';
    }
    if ($fra == '4') {
        $qui = 'PARTIAL GRADES FOR THE FOURTH QUARTER';
    }
    $ye = 'SCHOOL YEAR';
    $no = 'Name: ';
    $gr = 'Grade: ';
    $de = 'DESCRIPTION';
    $pr = 'AVG';
    $va = 'Assigned Value';
    $fe = 'Dates';
    $rr = 20;
    $fi = 'AVE';
} else {
    if ($fra == '1') {
        $qui = 'NOTAS PARCIALES PARA EL PRIMER CUATRIMESTRE';
    }
    if ($fra == '2') {
        $qui = 'NOTAS PARCIALES PARA EL SEGUNDO CUATRIMESTRE';
    }
    if ($fra == '3') {
        $qui = 'NOTAS PARCIALES PARA EL TERCER CUATRIMESTRE';
    }
    if ($fra == '4') {
        $qui = 'NOTAS PARCIALES PARA EL CUARTO CUATRIMESTRE';
    }
    $ye = utf8_encode('AÑO ESCOLAR');
    $no = 'Nombre: ';
    $gr = 'Grado: ';
    $de = 'DESCRIPCION';
    $pr = 'PRO';
    $va = 'Valor Asignado';
    $fe = 'Fechas';
    $rr = 0;
    $fi = 'PRO';
}
$result = DB::table('year')->where([
    ['grado', $grado],
    ['activo', ''],
    ['year', $year]
])->orderBy('apellidos')->get();

foreach ($result as $row1) {
    $a = 0;
    $gra = '';
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(1, 5, '', 0, 0, 'R');
    $pdf->Cell(7, 5, 'ID ', 0, 0, 'L');

    $pdf->Cell(16, 5, $row1->cta, 0, 0, 'L');
    $pdf->Cell(128, 5, '', 0, 0, 'R');
    $pdf->Cell(30, 5, $ye, 0, 0, 'L');
    $pdf->Cell(10, 5, $year, 0, 1, 'L');
    $pdf->Cell(93, 5, $no . ' ' . $row1->apellidos . ' ' . $row1->nombre, 0, 0, 'L');
    $pdf->Cell(33, 5, $gr, 0, 0, 'R');
    $pdf->Cell(10, 5, $row1->grado, 0, 0, 'R');
    $pdf->Cell(57, 5, date("m-d-Y"), 0, 1, 'R');
    $pdf->Cell(1, 5, ' ', 0, 0, 'C');
    $pdf->Cell(30, 5, $de, 1, 0, 'C', true);
    $pdf->Cell(135, 5, $qui, 1, 0, 'C', true);
    $pdf->Cell(9, 5, 'TPA', 1, 0, 'C', true);
    $pdf->Cell(9, 5, 'TP', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $pr, 1, 1, 'C', true);
    $pdf->Cell(1, 5, '  ', 0, 0, 'R');
    $pdf->Cell(192, 5, '                                     1             2              3             4              5             6              7             8             9', 1, 1, 'L', true);
    $cu = 0;
    if ($_POST['hoja'] == 'Notas') {
        $cu = 7;
        if ($fra == '1') {
            $tri = 'Trimestre-1';
            $result2 = DB::table('padres')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();

            $nn = 0;
            $nn2 = 1;
        }
        if ($fra == '2') {
            $tri = 'Trimestre-2';
            $result2 = DB::table('padres')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();

            $nn = 10;
            $nn2 = 2;
        }
        if ($fra == '3') {
            $tri = 'Trimestre-3';
            $result2 = DB::table('padres')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 29;
            $nn2 = 3;
        }
        if ($fra == '4') {
            $tri = 'Trimestre-4';
            $result2 = DB::table('padres')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 39;
            $nn2 = 4;
        }
    }

    if ($_POST['hoja'] == 'Pruebas-Cortas') {
        $cu = 8;
        if ($fra == '1') {
            $tri = 'Trimestre-1';
            $result2 = DB::table('padres4')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 0;
            $nn2 = 1;
        }
        if ($fra == '2') {
            $tri = 'Trimestre-2';
            $result2 = DB::table('padres4')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 10;
            $nn2 = 2;
        }
        if ($fra == '3') {
            $tri = 'Trimestre-3';
            $result2 = DB::table('padres4')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 20;
            $nn2 = 3;
        }
        if ($fra == '4') {
            $tri = 'Trimestre-4';
            $result2 = DB::table('padres4')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 30;
            $nn2 = 4;
        }
    }

    if ($_POST['hoja'] == 'Trab-Diarios') {
        $cu = 8;
        if ($fra == '1') {
            $tri = 'Trimestre-1';
            $result2 = DB::table('padres2')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 0;
            $nn2 = 1;
        }
        if ($fra == '2') {
            $tri = 'Trimestre-2';
            $result2 = DB::table('padres2')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 10;
            $nn2 = 2;
        }
        if ($fra == '3') {
            $tri = 'Trimestre-3';
            $result2 = DB::table('padres2')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 20;
            $nn2 = 3;
        }
        if ($fra == '4') {
            $tri = 'Trimestre-4';
            $result2 = DB::table('padres2')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 30;
            $nn2 = 4;
        }
    }

    if ($_POST['hoja'] == 'Trab-Libreta') {
        $cu = 8;
        if ($fra == '1') {
            $tri = 'Trimestre-1';
            $result2 = DB::table('padres3')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 0;
            $nn2 = 1;
        }
        if ($fra == '2') {
            $tri = 'Trimestre-2';
            $result2 = DB::table('padres3')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 10;
            $nn2 = 2;
        }
        if ($fra == '3') {
            $tri = 'Trimestre-3';
            $result2 = DB::table('padres3')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 20;
            $nn2 = 3;
        }
        if ($fra == '4') {
            $tri = 'Trimestre-4';
            $result2 = DB::table('padres3')->where([
                ['ss', $row1->ss],
                ['year', $year]
            ])->orderBy('curso')->get();
            $nn = 30;
            $nn2 = 4;
        }
    }

    foreach ($result2 as $row) {
        $a = $a + 1;
        $pdf->SetFont('Times', '', 9);
        $pdf->Cell(1, 5, '  ', 0, 0, 'R');
        $lon = 18;
        if ($_POST['hoja'] == 'Notas') {
            if ($idi == 'Ingles') {
                if (strlen($row->desc2) > 25) {
                    $lon = strlen($row->desc2) - 18;
                }
                $r = $row->desc2;
            } else {
                if (strlen($row->descripcion) > 25) {
                    $lon = strlen($row->descripcion) - 18;
                }
                $r = $row->descripcion;
            }

            $pdf->Cell(30, 5, substr($r, 0, $lon), 1, 0);
            for ($x = 1; $x <= 9; $x++) {
                $n = $nn + $x;
                $pdf->Cell(15, 5, $row->{"not$n"}, 1, 0, 'C');
            }
            $pdf->Cell(9, 5, $row->{"tpa$nn2"}, 1, 0, 'R');
            $pdf->Cell(9, 5, $row->{"por$nn2"}, 1, 0, 'R');
            $pdf->Cell(9, 5, $row->{"nota$nn2"}, 1, 1, 'R');
        } else {
            if ($idi == 'Ingles') {
                if (strlen($row->descripcion) > 25) {
                    $lon = strlen($row->descripcion) - 18;
                }
                $r = $row->descripcion;
            } else {
                if (strlen($row->descripcion) > 25) {
                    $lon = strlen($row->descripcion) - 18;
                }
                $r = $row->descripcion;
            }
            $pdf->Cell(30, 5, substr($r, 0, $lon), 1, 0);
            for ($x = 1; $x <= 9; $x++) {
                $n = $nn + $x;
                $pdf->Cell(15, 5, $row->{"not$n"}, 1, 0, 'C');
            }
            $pdf->Cell(9, 5, $row->{"tpa$nn2"}, 1, 0, 'R');
            $pdf->Cell(9, 5, $row->{"por$nn2"}, 1, 0, 'R');
            $pdf->Cell(9, 5, $row->{"nota$nn2"}, 1, 1, 'R');
        }
        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['trimestre', $tri],
            ['nivel', $_POST['hoja']],
            ['year', $year]
        ])->orderBy('curso')->first();

        $pdf->Cell(1, 5, '  ', 0, 0, 'R');
        $pdf->Cell(30, 5, $va, 1, 0);

        for ($x = 1; $x <= 9; $x++) {
            $pdf->Cell(15, 5, $row3->{"val$x"} ?? '', 1, 0, 'C');
        }
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 1, 'R');

        $pdf->Cell(1, 5, '  ', 0, 0, 'R');
        $pdf->Cell(30, 5, $fe, 1, 0);

        for ($x = 1; $x <= 9; $x++) {
            $fec = $row3->{"fec$x"} ?? '';
            if ($fec == '0000-00-00') {
                $fec = '';
            }
            $pdf->Cell(15, 5, $fec, 1, 0, 'C');
        }

        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 1, 'R');
    }
    $pdf->Cell(1, 10, '', 0, 0, 'R');
    $pdf->Cell(192, 10, '', 1, 1, 'L');
    $pdf->Cell(1, -15, '', 0, 0, 'R');
    $pdf->Cell(192, -15, $mensaj->t1e ?? '', 0, 1, 'L');
    $pdf->Cell(1, 23, '', 0, 0, 'R');
    $pdf->Cell(192, 23, $mensaj->t2e ?? '', 0, 1, 'L');
}

$pdf->Output();
