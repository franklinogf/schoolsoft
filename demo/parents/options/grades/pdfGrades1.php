<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Reporte de Notas', 'Grade Report'],
    ["Maestro(a):", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Año escolar:", "School year:"],
    ["DESCRIPCION", "DESCRIPTION"],
    ['PRIMER SEMESTRE', 'FIRST SEMESTER'],
    ['SEGUNDO SEMESTRE', 'SECOND SEMESTER'],
    ['PRO', 'AVE'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['AÑO', 'YEAR'],
]);

function NLetra($valor)
{
    if ($valor == '') {
        return '';
    } else if ($valor <= '100' && $valor >= '90') {
        return 'A';
    } else if ($valor <= '89' && $valor >= '80') {
        return 'B';
    } else if ($valor <= '79' && $valor >= '70') {
        return 'C';
    } else if ($valor <= '69' && $valor >= '60') {
        return 'D';
    } else  if ($valor <= '59' && $valor >= '1') {
        return 'F';
    } else  if ($valor == '') {
        return '';
    }
}

class nPDF extends PDF
{
    function Header()
    {
        parent::header();
    }

    function Footer()
    {
        $fir = $_POST['fir'] ?? '';
        if ($fir == 'Si') {
            $this->Image('../../../logo/firma.gif', 22, 260, 45);
        }
        $idi = '';
        if ($idi == 'Ingles') {
            $this->SetY(-70);
            $this->Cell(50, 6, 'GRADING SCALE', 1, 0, 'C', true);
            $this->Cell(140, 6, 'LETTER DESCRIPTION', 1, 1, 'C', true);
            $this->Cell(50, 22, '', 1, 0, 'C');
            $this->Cell(140, 22, '', 1, 1, 'C');
            $this->Cell(50, -38, 'A  =  90  - 100 , {3.50 - 4.00}       ', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' E = EXCELLENT', 0, 0, 'E');
            $this->Cell(70, -38, ' INC = INCOMPLETE', 0, 1, 'E');
            $this->Cell(50, 46, 'B  =  80  -   89 , {2.50 - 3.49}       ', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' B,G = GOOD', 0, 0, 'E');
            $this->Cell(70, 46, ' P = PARTICIPATION', 0, 1, 'E');
            $this->Cell(50, -38, 'C  =  70  -   79 , {1.50 - 2.49}       ', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' S = SATISFACTORY', 0, 0, 'E');
            $this->Cell(70, -38, ' C1 = 1st. QUARTER', 0, 1, 'E');
            $this->Cell(50, 46, 'D  =  60  -   69 , {0.80 - 1.49}       ', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' NM,NI = NEEDS IMPROVEMENT', 0, 0, 'E');
            $this->Cell(70, 46, ' CO = CONDUCT', 0, 1, 'E');
            $this->Cell(50, -38, 'F  =    0  -   59 , {0.00 - 0.79}       ', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' NS,U = UNSATISFACTORY', 0, 0, 'E');
            $this->Cell(70, -38, ' CR = CREDITS', 0, 1, 'E');
            $this->Cell(5, 55, '  ', 0, 0, 'R');
            $this->Cell(50, 55, ' ______________________________ ', 0, 0, 'C');
            $this->Cell(65, 55, '  ', 0, 0, 'C');
            $this->Cell(65, 55, ' ______________________________ ', 0, 1, 'C');
            $this->Cell(5, -47, '  ', 0, 0, 'R');
            $this->Cell(50, -47, 'Authorized Signature', 0, 0, 'C');
            $this->Cell(65, -47, '', 0, 0, 'C');
            $this->Cell(65, -47, "Parent's Signature", 0, 0, 'C');
        } else {
            $this->SetY(-70);
            $this->Cell(50, 6, 'ESCALA', 1, 0, 'C', true);
            $this->Cell(140, 6, 'LEYENDA', 1, 1, 'C', true);
            $this->Cell(50, 22, '', 1, 0, 'C');
            $this->Cell(140, 22, '', 1, 1, 'C');
            $this->Cell(50, -38, 'A  =  90  - 100 , {3.50 - 4.00}', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' E = EXELENTE', 0, 0, 'E');
            $this->Cell(70, -38, ' INC = INCOMPLETO', 0, 1, 'E');
            $this->Cell(50, 46, 'B  =  80  -   89 , {2.50 - 3.49}', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' B,G = BUENO', 0, 0, 'E');
            $this->Cell(70, 46, ' P = PARTICIPACION', 0, 1, 'E');
            $this->Cell(50, -38, 'C  =  70  -   79 , {1.50 - 2.49}', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' S = SATISFACTORIO', 0, 0, 'E');
            $this->Cell(70, -38, ' C1 = 1er. CUATRIMESTRE', 0, 1, 'E');
            $this->Cell(50, 46, 'D  =  65  -   69 , {0.80 - 1.49}', 0, 0, 'R');
            $this->Cell(7, 46, ' ', 0, 0, 'E');
            $this->Cell(65, 46, ' NM,NI = NECESITA MEJORAR', 0, 0, 'E');
            $this->Cell(70, 46, ' CO = CONDUCTA', 0, 1, 'E');
            $this->Cell(50, -38, 'F  =    0  -   64 , {0.00 - 0.79}', 0, 0, 'R');
            $this->Cell(7, -38, ' ', 0, 0, 'E');
            $this->Cell(65, -38, ' NS,U = NO SATISFACTORIO', 0, 0, 'E');
            $this->Cell(70, -38, ' CR = CREDITOS', 0, 1, 'E');
            $this->Cell(5, 75, '  ', 0, 0, 'R');
            $this->Cell(50, 75, ' ______________________________ ', 0, 0, 'C');
            $this->Cell(65, 75, '  ', 0, 0, 'C');
            $this->Cell(65, 75, ' ______________________________ ', 0, 1, 'C');
            $this->Cell(7, -65, '  ', 0, 0, 'R');
            $this->Cell(50, -65, 'Firma del Director', 0, 0, 'C');
            $this->Cell(65, -65, '', 0, 0, 'C');
            $this->Cell(65, -65, 'Firma padre/madre', 0, 0, 'C');
        }
    }
}

$teacher = new Teacher();
$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();

$year = $colegio->year;

$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'] ?? '';
$men = $_POST['mensaje'] ?? '';
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';

$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();


$students = DB::table('year')->where([
    ['ss', $_POST['studentSS']],
    ['year', $year]
])->orderBy('id')->get();

foreach ($students as $estu) {
    $teacher = $teacher->findByGrade($estu->grado);
    $gra = '';
    $id = $estu->id;
    $ss = $estu->ss;
    $gra = $estu->grado;

    $pdf->AddPage('');
    $pdf->Ln(5);
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Reporte de Notas") . " $year", 0, 1, 'C');
    $pdf->Ln(8);
    $pdf->SetFont('Arial', '', 12);
    $pdf->splitCells(utf8_encode($lang->translation("Año escolar:")) . " $year", $lang->translation("Fecha:") . " " . date("m-d-Y"));

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(120, 5, $lang->translation("Nombre:") . " $estu->nombre $estu->apellidos", 1, 0, 'L', true);
    $pdf->Cell(40, 5, "S.S. XXX-XX-XXXX", 1, 0, 'L', true);
    $pdf->Cell(30, 5, $lang->translation("Grado:") . " $estu->grado", 1, 1, 'L', true);
    $pdf->Cell(60, 5, $lang->translation("DESCRIPCION"), 1, 0, 'C', true);
    $pdf->Cell(51, 5, $lang->translation("PRIMER SEMESTRE"), 1, 0, 'C', true);
    $pdf->Cell(51, 5, $lang->translation("SEGUNDO SEMESTRE"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("PRO"), 1, 0, 'C', true);
    $pdf->Cell(13, 5, "CRS", 1, 1, 'C', true);

    $pdf->Cell(60, 5, '', 1, 0, 'L', true);
    $pdf->Cell(51, 5, $lang->translation("TRI-1     TRI-2     SEM-1"), 1, 0, 'C', true);
    $pdf->Cell(51, 5, $lang->translation("TRI-3     TRI-4     SEM-2"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, utf8_encode($lang->translation("AÑO")), 1, 0, 'C', true);
    $pdf->Cell(13, 5, "", 1, 1, 'C', true);
    $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $gra],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();
    $crs = 0;
    $n1 = 0;
    $t1 = 0;
    $n2 = 0;
    $t2 = 0;
    $n3 = 0;
    $t3 = 0;
    $n4 = 0;
    $t4 = 0;
    $n5 = 0;
    $t5 = 0;
    $n6 = 0;
    $t6 = 0;
    $n7 = 0;
    $t7 = 0;
    $au = 0;
    $ta = 0;
    $au2 = 0;
    $ta2 = 0;
    $au3 = 0;
    $ta3 = 0;
    $au4 = 0;
    $ta4 = 0;
    foreach ($cursos as $curso) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(60, 5, $curso->descripcion, 1, 0, 'L');
        if ($colegio->tri >= '1') {
            $pdf->Cell(10, 5, $curso->nota1, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->nota1), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '2') {
            $pdf->Cell(10, 5, $curso->nota2, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->nota2), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '2') {
            $pdf->Cell(10, 5, $curso->sem1, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->sem1), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '3') {
            $pdf->Cell(10, 5, $curso->nota3, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->nota3), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '4') {
            $pdf->Cell(10, 5, $curso->nota4, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->nota4), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '4') {
            $pdf->Cell(10, 5, $curso->sem2, 1, 0, 'R');
            $pdf->Cell(7, 5, NLetra($curso->sem2), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'R');
            $pdf->Cell(7, 5, '', 1, 0, 'C');
        }
        if ($colegio->tri >= '4') {
            $pdf->Cell(9, 5, $curso->final, 1, 0, 'R');
            $pdf->Cell(6, 5, NLetra($curso->final), 1, 0, 'C');
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'R');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }
        $pdf->Cell(13, 5, number_format($curso->credito, 2), 1, 1, 'R');
        $pdf->SetFont('Arial', '', 8);
        $crs = $crs + number_format($curso->credito, 2);
        if ($curso->nota1 > 0 and $curso->credito > 0) {
            $n1 = $n1 + ($curso->nota1 * $curso->credito);
            $t1 = $t1 + $curso->credito;
        }
        if ($curso->nota2 > 0 and $curso->credito > 0) {
            $n2 = $n2 + ($curso->nota2 * $curso->credito);
            $t2 = $t2 + $curso->credito;
        }
        if ($curso->nota3 > 0 and $curso->credito > 0) {
            $n3 = $n3 + ($curso->nota3 * $curso->credito);
            $t3 = $t3 + $curso->credito;
        }
        if ($curso->nota4 > 0 and $curso->credito > 0) {
            $n4 = $n4 + ($curso->nota4 * $curso->credito);
            $t4 = $t4 + $curso->credito;
        }
        if ($curso->sem1 > 0 and $curso->credito > 0) {
            $n5 = $n5 + ($curso->sem1 * $curso->credito);
            $t5 = $t5 + $curso->credito;
        }
        if ($curso->sem2 > 0 and $curso->credito > 0) {
            $n6 = $n6 + ($curso->sem2 * $curso->credito);
            $t6 = $t6 + $curso->credito;
        }
        if ($curso->final > 0 and $curso->credito > 0) {
            $n7 = $n7 + ($curso->final * $curso->credito);
            $t7 = $t7 + $curso->credito;
        }
        if ($curso->aus1 > 0) {
            $au = $au + $curso->aus1;
        }
        if ($curso->tar1 > 0) {
            $ta = $ta + $curso->tar1;
        }
        if ($curso->aus2 > 0) {
            $au2 = $au2 + $curso->aus2;
        }
        if ($curso->tar2 > 0) {
            $ta2 = $ta2 + $curso->tar2;
        }
        if ($curso->aus3 > 0) {
            $au3 = $au3 + $curso->aus3;
        }
        if ($curso->tar3 > 0) {
            $ta3 = $ta3 + $curso->tar3;
        }
        if ($curso->aus4 > 0) {
            $au4 = $au4 + $curso->aus4;
        }
        if ($curso->tar4 > 0) {
            $ta4 = $ta4 + $curso->tar4;
        }
        $pdf->Cell(60, 5, "   " . $curso->profesor, 1, 0, 'L');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(7, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(6, 5, '', 1, 0, 'R');
        $pdf->Cell(13, 5, '', 1, 1, 'R');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 5, $lang->translation("PROMEDIO:"), 1, 0, 'R', true);
    if ($t1 > 0 and $colegio->tri >= '1') {
        $pdf->Cell(10, 5, number_format($n1 / $t1, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n1 / $t1, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
    }
    if ($t2 > 0 and $colegio->tri >= '2') {
        $pdf->Cell(10, 5, number_format($n2 / $t2, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n2 / $t2, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
        $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    }
    if ($t5 > 0 and $colegio->tri >= '2') {
        $pdf->Cell(10, 5, number_format($n5 / $t5, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n5 / $t5, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
        $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    }
    if ($t3 > 0 and $colegio->tri >= '3') {
        $pdf->Cell(10, 5, number_format($n3 / $t3, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n3 / $t3, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
        $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    }
    if ($t4 > 0 and $colegio->tri >= '4') {
        $pdf->Cell(10, 5, number_format($n4 / $t4, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n4 / $t4, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
        $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    }
    if ($t6 > 0 and $colegio->tri >= '4') {
        $pdf->Cell(10, 5, number_format($n6 / $t6, 0), 1, 0, 'R', true);
        $pdf->Cell(7, 5, NLetra(number_format($n6 / $t6, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'R', true);
        $pdf->Cell(7, 5, '', 1, 0, 'R', true);
    }
    if ($t7 > 0 and $colegio->tri >= '4') {
        $pdf->Cell(9, 5, number_format($n7 / $t7, 0), 1, 0, 'R', true);
        $pdf->Cell(6, 5, NLetra(number_format($n7 / $t7, 0)), 1, 0, 'R', true);
    } else {
        $pdf->Cell(9, 5, '', 1, 0, 'R', true);
        $pdf->Cell(6, 5, '', 1, 0, 'R', true);
    }
    $pdf->Cell(13, 5, number_format($crs, 2), 1, 1, 'R', true);
    $pdf->Cell(60, 5, 'AUSENCIAS Y TARDANZAS', 1, 0, 'R', true);
    $pdf->Cell(17, 5, $au . '  /  ' . $ta, 1, 0, 'C', true);
    $pdf->Cell(17, 5, $au2 . '  /  ' . $ta2, 1, 0, 'C', true);
    $pdf->Cell(17, 5, $au + $au2 . '  /  ' . $ta + $ta2, 1, 0, 'C', true);
    $pdf->Cell(17, 5, $au3 . '  /  ' . $ta3, 1, 0, 'C', true);
    $pdf->Cell(17, 5, $au4 . '  /  ' . $ta4, 1, 0, 'C', true);
    $pdf->Cell(17, 5, $au3 + $au4 . '  /  ' . $ta3 + $ta4, 1, 0, 'C', true);
    $pdf->Cell(28, 5, $au + $au2 + $au3 + $au4 . '  /  ' . $ta + $ta2 + $ta3 + $ta4, 1, 1, 'C', true);
    $pdf->Cell(190, 10, '', 1, 1, 'L');
    $pdf->Cell(1, -15, '', 0, 0, 'R');
    $pdf->Cell(190, -15, $mensaj->t1e ?? '', 0, 1, 'C');
    $pdf->Cell(1, 23, '', 0, 0, 'R');
    $pdf->Cell(190, 23, $mensaj->t2e ?? '', 0, 1, 'C');
    $pdf->Ln(1);
}

$pdf->Output();
$ip = $_SERVER['REMOTE_ADDR'];
DB::table('acuse')->insert([
    'id' => $id,
    'ss' => $ss,
    'grado' => $gra,
    'year' => $year,
    'ip' => $ip,
    'hora' => date('h:i:s'),
    'fecha' => date('Y-m-d'),
    'tri' => $colegio->tri,
]);
