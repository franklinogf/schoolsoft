<?php
require_once '../../../../app.php';

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

$fra = $_POST['tri'];
$grado = $_POST['grade'];
$men = $_POST['mensaj'];
$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$lang = new Lang([
    ["Hoja de progreso", "Progress sheet"],
    ["Este documento no es oficial.", "This document is not official."],
    ['Informe de notas', 'Grades report'],
    ['Espanol', 'Ingles']
]);

$pdf = new PDF();
$pdf->SetAutoPageBreak(true, 10);
$pdf->footer = false;

if ($_POST['idioma'] == '1') {
    $idi = 'Espanol';
} else {
    $idi = 'Ingles';
}

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
        $qui = 'PARTIAL GRADES FOR THE FOURHT QUARTER';
    }
    $ye = 'SCHOOL YEAR';
    $no = 'Name: ';
    $gr = 'Grade: ';
    $de = 'DESCRIPTION';
    $pr = 'CR.';
    $va = 'Assigned Value';
    $fe = 'Dates';
    $rr = 20;
    $text1 = '';
    $text2 = '';
    $fi = 'AVE';
    $CURSOS = 'COURSES';
    $QUIZZES = 'TEST';
    $maes = 'Teacher ';
    $ex = 'EXAMS';
    $tit = "Progress sheet";
} else {
    if ($fra == '1') {
        $qui = 'PRIMER TRIMESTRE';
    }
    if ($fra == '2') {
        $qui = 'SEGUNDO TRIMESTRE';
    }
    if ($fra == '3') {
        $qui = 'TERCER TRIMESTRE';
    }
    if ($fra == '4') {
        $qui = 'CUARTO TRIMESTRE';
    }

    $ye = utf8_encode('Año escolar: ');
    $no = 'NOMBRE ESTUDIANTE: ';
    $gr = 'GRADO: ';
    $de = 'DESCRIPCION';
    $pr = 'CR.';
    $va = 'Valor Asignado';
    $fe = 'Fechas';
    $rr = 0;
    $text1 = '';
    $text2 = '';
    $fi = 'PRO';
    $CURSOS = 'CURSOS';
    $QUIZZES = 'QUIZZES';
    $maes = 'Maestro(a): ';
    $ex = 'EXAMENES';
    $tit = "Hoja de progreso";
}

$result = DB::table('year')->where([
    ['grado', $grado],
    ['activo', ''],
    ['year', $year]
])->orderBy('apellidos')->get();
$pdf->SetTitle($tit);
$pdf->Fill();

foreach ($result as $row1) {

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 40);
    $pdf->Ln(-5);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 5, $tit, 0, 1, 'C');
    $pdf->Ln(7);

    $pdf->SetFont('Times', '', 11);

    $a = 0;
    $gra = '';
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(160, 5, $no . ' ' . $row1->apellidos . ' ' . $row1->nombre, 1, 0, 'L', true);
    $pdf->Cell(35, 5, $gr . ' ' . $row1->grado, 1, 1, 'L', true);

    $pdf->Cell(110, 5, $qui, 1, 0, 'C', true);
    $pdf->Cell(50, 5, $ye . ' ' . $year, 1, 0, 'C', true);
    $pdf->Cell(35, 5, '', 1, 1, 'C', true);

    $pdf->Cell(39, 5, $CURSOS, 1, 0, 'C', true);
    $pdf->Cell(26, 5, $ex, 1, 0, 'C', true);
    $pdf->Cell(26, 5, $QUIZZES, 1, 0, 'C', true);
    $pdf->Cell(26, 5, 'T.DIARIO', 1, 0, 'C', true);
    $pdf->Cell(26, 5, 'ASSESS', 1, 0, 'C', true);
    $pdf->Cell(26, 5, 'T.ESPECIALES', 1, 0, 'C', true);
    $pdf->Cell(26, 5, 'T.LIBRETAS', 1, 1, 'C', true);

    $result2 = DB::table('padres')->where([
        ['ss', $row1->ss],
        ['year', $year]
    ])->orderBy('curso')->get();

    $col1 = 0;
    $col2 = 0;

    $au = 0;
    $ta = 0;
    $au2 = 0;
    $ta2 = 0;
    $au3 = 0;
    $ta3 = 0;
    $au4 = 0;
    $ta4 = 0;
    if ($fra == 1) {
        $tri = 'Trimestre-1';
        $nt = 0;
        $nt2 = 0;
    }
    if ($fra == 2) {
        $tri = 'Trimestre-2';
        $nt = 10;
        $nt2 = 0;
    }
    if ($fra == 3) {
        $tri = 'Trimestre-3';
        $nt = 20;
        $nt2 = 29;
    }

    if ($fra == 4) {
        $tri = 'Trimestre-4';
        $nt = 30;
        $nt2 = 0;
    }

    foreach ($result2 as $row) {
        $a = $a + 1;
        $pdf->SetXY(10, 67 + $col2);

        $pdf->SetFont('Times', '', 8);
        if ($_POST['idioma'] == '1') {
            $pdf->Cell(39, 20, $row->descripcion, 1, 0);
        } else {
            $pdf->Cell(39, 20, $row->desc2, 1, 0);
        }
        $pdf->Cell(13, 20, '', 1, 0, 'L');
        $pdf->Cell(13, 20, '', 1, 0, 'L');
        $pdf->Cell(13, 20, '', 1, 0, 'L');
        $pdf->Cell(13, 20, '', 1, 0, 'L');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 0, 'C');
        $pdf->Cell(13, 20, '', 1, 1, 'C');

        $row21 = DB::table('profesor')->where([
            ['id', $row->id]
        ])->orderBy('id')->first();

        $pdf->Cell(195, 4, $maes . $row21->nombre . ' ' . $row21->apellidos, 1, 1, 'L');

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Notas']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);

        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$a1"} ?? '';
            $not1 = $row->{"not$b"} ?? '';
            $not2 = $row->{"not$c"} ?? '';
            $pdf->Cell(39, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $row4 = DB::table('padres4')->where([
            ['ss', $row1->ss],
            ['year', $year],
            ['curso', $row->curso]
        ])->orderBy('curso')->first();

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Pruebas-Cortas']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);
        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$a1"} ?? '';
            $not1 = $row4->{"not$b"} ?? '';
            $not2 = $row4->{"not$c"} ?? '';
            $pdf->Cell(65, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $row4 = DB::table('padres2')->where([
            ['ss', $row1->ss],
            ['year', $year],
            ['curso', $row->curso]
        ])->orderBy('curso')->first();

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Trab-Diarios']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);
        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$a1"} ?? '';
            $not1 = $row4->{"not$b"} ?? '';
            $not2 = $row4->{"not$c"} ?? '';
            $pdf->Cell(91, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $row4 = DB::table('padres5')->where([
            ['ss', $row1->ss],
            ['year', $year],
            ['curso', $row->curso]
        ])->orderBy('curso')->first();

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Asesment']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);
        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$1a"} ?? '';
            $not1 = $row4->{"not$b"} ?? '';
            $not2 = $row4->{"not$c"} ?? '';
            $pdf->Cell(117, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $row4 = DB::table('padres6')->where([
            ['ss', $row1->ss],
            ['year', $year],
            ['curso', $row->curso]
        ])->orderBy('curso')->first();

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Listados']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);
        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$a1"} ?? '';
            $not1 = $row4->{"not$b"} ?? '';
            $not2 = $row4->{"not$c"} ?? '';
            $pdf->Cell(143, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $row4 = DB::table('padres3')->where([
            ['ss', $row1->ss],
            ['year', $year],
            ['curso', $row->curso]
        ])->orderBy('curso')->first();

        $row3 = DB::table('valores')->where([
            ['curso', $row->curso],
            ['year', $year],
            ['trimestre', $tri],
            ['nivel', 'Trab-Libreta']
        ])->orderBy('id')->first();

        $pdf->SetXY(10, 67 + $col2);
        for ($x = 1; $x <= 5; $x++) {
            $a1 = $x + 5;
            $b = $x + $nt;
            $c = $x + $nt + 5;
            $val1 = $row3->{"val$x"} ?? '';
            $val2 = $row3->{"val$a1"} ?? '';
            $not1 = $row4->{"not$b"} ?? '';
            $not2 = $row4->{"not$c"} ?? '';
            $pdf->Cell(169, 4, '', 0, 0, 'C');
            $pdf->Cell(13, 4, $not1 . '/' . $val1, 0, 0, 'C');
            $pdf->Cell(13, 4, $not2 . '/' . $val2, 0, 1, 'C');
        }

        $col1 = $col1 + 27;
        $col2 = $col2 + 24;
    }
    if ($men != 0) {
        $pdf->Ln(4);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(0, 3, $mensaj->t1e . ' ' . $mensaj->t2e, 0, 1, 'L');
        //     $pdf->Cell(0,3,$mensaj->t2e,0,1,'L');
    }
}
$pdf->Output();
