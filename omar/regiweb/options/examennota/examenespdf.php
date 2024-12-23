<?php
require_once '../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

Session::is_logged();

$lang = new Lang([
    ['Titulo', 'Title'],
    ['Fecha', 'Date'],
    ['Otros', 'Others'],
    ["Trimestre 1", "Quarter 1"],
    ["Trimestre 2", "Quarter 2"],
    ["Trimestre 3", "Quarter 3"],
    ["Trimestre 4", "Quarter 4"],
    ["Notas", "Grades"],
    ["Pruebas cortas", "Short tests"],
    ["Trabajos Diarios", "Daily Work"],
    ["Trabajos Libreta", "Notebook Work"],
    ['Informe de notas por examen', 'Exam grade report'],
    ['', ''],
    ['', ''],
    ['', ''],

]);


$teacher = new Teacher(Session::id());
$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();
$year = $colegio->year;

$curso = $_POST['curso'];
$cuatri = $_POST['cuatrimestre'];
if ($cuatri == 'Trimestre-1') {
    $num = 0;
} elseif ($cuatri == 'Trimestre-2') {
    $num = 10;
}
if ($cuatri == 'Trimestre-3') {
    $num = 20;
}
if ($cuatri == 'Trimestre-4') {
    $num = 30;
}
$pagina = $_POST['pagina'];
if ($pagina == 'Notas') {
    $tri = 'padres';
    $cant = 9;
} else {
    if ($pagina == 'Pruebas-Cortas') {
        $tri = 'padres4';
    } elseif ($pagina == 'Trab-Diarios') {
        $tri = 'padres2';
    } elseif ($pagina == 'Trab-Libreta') {
        $tri = 'padres3';
    }
    $cant = 10;
}

$row = DB::table('valores')->where([
    ['year', $year],
    ['curso', $curso],
    ['trimestre', $cuatri],
    ['nivel', $pagina]
])->orderBy('id')->first();

$notas = array();
for ($i = 1; $i <= $cant; $i++) {
    if ($row->{"val$i"} != '') {
        array_push($notas, $row->{"val$i"});
    }
}
$profe = "$teacher->nombre $teacher->apellidos";
class nPDF extends PDF
{

    private $curso = '';
    function setCurso($curso)
    {
        $this->curso = $curso;
    }
    private $info = '';
    function setInfo($info)
    {
        $this->info = $info;
    }

    //Cabecera de pagina

    function Header()
    {
        global $lang;
        global $year;
        global $cuatri;
        global $pagina;
        parent::header();
        $info = $lang->translation("$cuatri") . ' - ' . $lang->translation("$pagina");
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $lang->translation('Informe de notas por examen') . ' ' . $year, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 10);
        $y = $this->GetY();
        $this->Cell(0, 5, $this->curso, 0, 1);
        $this->SetY($y);
        //		$this->Cell(0,5,$this->info,0,1,'R');		
        $this->Cell(0, 5, $info, 0, 1, 'R');
        //columnas
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(15, 7, "", 1, 0, "C", true);
        $this->Cell(90, 7, $lang->translation("Titulo"), 1, 0, "C", true);
        $this->Cell(25, 7, $lang->translation("Fecha"), 1, 0, "C", true);
        $this->Cell(20, 7, "A", 1, 0, "C", true);
        $this->Cell(20, 7, "B", 1, 0, "C", true);
        $this->Cell(20, 7, "C", 1, 0, "C", true);
        $this->Cell(20, 7, "D", 1, 0, "C", true);
        $this->Cell(20, 7, "F", 1, 0, "C", true);
        $this->Cell(20, 7, $lang->translation("Otros"), 1, 0, "C", true);
        $this->Cell(25, 7, "Total", 1, 0, "C", true);
        $this->Ln();
    }
    function Footer()
    {
        $footer = 'Pagina ' . $this->PageNo() . ' de {nb} ' . ' | ' . date("m-d-Y");
        $this->SetY(-15);
        //Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        //Numero de pagina
        $this->Cell(0, 10, $footer, 0, 0, 'C');
    }
}

$cur = DB::table('cursos')->select("desc1")->where([
    ['year', $year],
    ['curso', $curso],
])->orderBy('curso')->first();

$pdf = new nPDF();
$pdf->SetTitle($lang->translation('Informe de notas por examen'));
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->setCurso("$profe ($curso - $cur->desc1 $year)");
//$pdf->setCurso(utf8_encode("$profe ($curso - $cur->desc1 $year)"));
$cuatri = str_replace('-', ' ', $cuatri);
$pagina = str_replace('-', ' ', $pagina);
$pdf->setInfo("$cuatri - $pagina");
$pdf->AddPage('L');
$pdf->SetFont('Arial', '', 12);
$count = 1;
$n = $num;
$AA = 0;
$BB = 0;
$CC = 0;
$DD = 0;
$FF = 0;
$OO = 0;
$ABC = 0;
$num = $n;
foreach ($notas as $not) {
    $A = 0;
    $B = 0;
    $C = 0;
    $D = 0;
    $F = 0;
    $O = 0;
    $num++;
    $rs = DB::table("$tri")->select("not$num AS puntos")->where([
        ['year', $year],
        ['curso', $curso],
        ['baja', ''],
    ])->orderBy('curso')->get();

    foreach ($rs as $e) {
        $puntos = $e->puntos;
        if (intval($not) < 100) {
            $puntos = (intval($e->puntos) / intval($not)) * 100;
        }

        if (is_numeric($puntos) && $puntos != '') {
            if ($puntos >= 90) {
                $A++;
            } elseif ($puntos >= 80 && $puntos <= 89.99) {
                $B++;
            } elseif ($puntos >= 70 && $puntos <= 79.99) {
                $C++;
            } elseif ($puntos >= 60 && $puntos <= 69.99) {
                $D++;
            } elseif ($puntos <= 59.99) {
                $F++;
            }
        } else {
            $O++;
        }
    }

    $pdf->Cell(15, 7, $count, 1, 0, "C");
    $pdf->Cell(90, 7, $row->{"tema$num"}, 1);
    $pdf->Cell(25, 7, $row->{"fec$num"}, 1);
    $pdf->Cell(20, 7, $A, 1, 0, "C");
    $pdf->Cell(20, 7, $B, 1, 0, "C");
    $pdf->Cell(20, 7, $C, 1, 0, "C");
    $pdf->Cell(20, 7, $D, 1, 0, "C");
    $pdf->Cell(20, 7, $F, 1, 0, "C");
    $pdf->Cell(20, 7, $O, 1, 0, "C");
    $pdf->Cell(25, 7, $A + $B + $C + $D + $F, 1, 1, "C", true);
    $ABC += $A + $B + $C + $D + $F;
    $AA += $A;
    $BB += $B;
    $CC += $C;
    $DD += $D;
    $FF += $F;
    $OO += $O;
    $count++;
}
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(15, 7, '', 0, 0, "C");
$pdf->Cell(90, 7, '');
$pdf->Cell(25, 7, 'Total', 1, 0, 'R', true);
$pdf->Cell(20, 7, $AA, 1, 0, "C", true);
$pdf->Cell(20, 7, $BB, 1, 0, "C", true);
$pdf->Cell(20, 7, $CC, 1, 0, "C", true);
$pdf->Cell(20, 7, $DD, 1, 0, "C", true);
$pdf->Cell(20, 7, $FF, 1, 0, "C", true);
$pdf->Cell(20, 7, $OO, 1, 0, "C", true);
$pdf->Cell(25, 7, $ABC, 1, 1, "C", true);

$pdf->Output();
