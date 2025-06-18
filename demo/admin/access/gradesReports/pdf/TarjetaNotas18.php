<?php
require_once '../../../../app.php';
// ACADEMIA SALLY OLSEN

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
        $this->SetY(-90);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, 'LEYENDA', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Ln(5);
        $this->Cell(50, 5, 'T = Trimestre/Notas', 1, 0, 'C');
        $this->Cell(10, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, 'C = Conducta / Agricultura', 'TLR', 1, 'C');
        $this->Rect($this->GetX(), $this->GetY() - 0, 50, 30);
        $this->Cell(50, 5, '100   -   90   =   A', 0, 0, 'C');
        $this->Cell(10, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, utf8_encode('Arte / Biblia / Educación Física'), 'BLR', 1, 'C');
        $this->Cell(50, 5, '89   -   80   =   B', 0, 1, 'C');
        $this->Cell(50, 5, '79   -   70   =   C', 0, 1, 'C');
        $this->Cell(50, 5, '69   -   60   =   D', 0, 1, 'C');
        $this->Cell(50, 5, '59   -     0   =   F', 0, 1, 'C');
        $this->SetXY(70, -70);
        $this->Rect($this->GetX(), $this->GetY() - 0, 60, 25);
        $this->Cell(50, 2, '', 0, 2, 'C');
        $this->Cell(60, 5, 'E = Excelente', 0, 2, 'C');
        $this->Cell(60, 5, 'S = Satisfactorio', 0, 2, 'C');
        $this->Cell(60, 5, 'NM = Necesita Mejorar', 0, 2, 'C');
        $this->Cell(60, 5, 'I = Insatisfactorio', 0, 2, 'C');

        $this->SetXY(140, -75);
        $this->Rect($this->GetX(), $this->GetY() - 3, 50, 30);
        $this->Cell(50, 25, 'SELLO', 0, 2, 'C');
        $this->SetY(-38);
        $this->Cell(5, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, '_______________________________________', 0, 0, 'C');
        $this->Cell(50, 5, '', 0, 0, 'C');
        $this->Cell(60, 5, '_______________________________________', 0, 1, 'C');
        $this->Cell(5, 5, '', 0, 0, 'C');
        $this->Cell(60, 3, 'Firma Maestro(a)', 0, 0, 'C');
        $this->Cell(50, 3, '', 0, 0, 'C');
        $this->Cell(60, 3, utf8_encode('Firma Directora Académica'), 0, 0, 'C');
    }
}

$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();
$year = $school->info('year2');
$pdf = new nPDF();
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$men = $_POST['mensaje'];
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$ccr = $_POST['cr'] ?? '';
$tri = $_POST['tri'] ?? '';
$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

$pdf->SetFont('Times', '', 11);
$idi = '';
if ($idi == 'Ingles') {
    $ye = 'SCHOOL YEAR:';
    $no = 'Name: ';
    $gr = 'Grade: ';
    $de = 'DESCRIPTION';
    $pr = 'AVG';
    $va = 'Assigned Value';
    $fe = 'Dates';
    $rr = 20;
    $text1 = $mensaj->t1i ?? '';
    $text2 = $mensaj->t2i ?? '';
    $fi = 'YR';
    $f2 = 'AVG';
    $se1 = 'FIRST SEMESTER';
    $se2 = 'SECOND SEMESTER';
    $qq1 = '   AVERAGE     COND  ';
    $qq2 = '   AVERAGE     COND  ';
    $pq = 'AVERAGE.';
    $asi = 'ABSENCE AND LATE';
} else {
    $ye = 'AÑO ESCOLAR:';
    $no = 'Nombre: ';
    $gr = 'Grado: ';
    $de = 'DESCRIPCION';
    $pr = 'PRO';
    $va = 'Valor Asignado';
    $fe = 'Fechas';
    $rr = 0;
    $text1 = $mensaj->t1e ?? '';
    $text2 = $mensaj->t2e ?? '';
    $fi = 'PRO';
    $f2 = 'AñO';
    $se1 = 'PRIMER SEMESTRE';
    $se2 = 'SEGUNDO SEMESTRE';
    $qq1 = '  S1  Cond  Aus  Tar';
    $qq2 = '  S2  Cond  Aus  Tar';
    $pq = 'PROMEDIO';
    $asi = 'Ausencias/Tardanzas';
}

$a = 0;
foreach ($students as $estu) {
    $pdf->AddPage();
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Reporte de Notas") . " $year", 0, 1, 'C');
    $pdf->Ln(10);
    $a = $a + 1;

    $gra = '';
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(1, 5, '', 0, 0, 'R');
    $pdf->Cell(30, 5, utf8_encode($ye), 0, 0, 'L');
    $pdf->Cell(55, 5, $year, 0, 0, '');
    $pdf->Cell(7, 5, ' ', 0, 0, 'L');
    $pdf->Cell(1, 5, '', 0, 1, 'R');
    $pdf->Cell(8, 5, '', 0, 0, 'R');
    $pdf->Cell(10, 5, 'FECHA: ', 0, 0, 'R');
    $pdf->Cell(24, 5, date("m-d-Y"), 0, 0, 'R');
    $pdf->Cell(10, 5, '', 0, 0, 'L');
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');

    $pdf->Cell(22, 5, '', 0, 0, '');
    $pdf->Cell(52, 5, 'MAESTRO SALON HOGAR:', 0, 0, '');
    $pdf->Cell(67, 5, $nom . ' ' . $ape, 0, 1, 'L');

    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(129, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', '', 11);
    list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
    $pdf->Cell(38, 5, 'S.S.: XXX-XX-' . $ss3, 1, 0, 'C', true);
    $pdf->Cell(26, 5, $gr . ' ' . $grade, 1, 1, 'C', true);

    $pdf->Cell(45, 5, $de, 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'T-1', 1, 0, 'C', true);
    $pdf->Cell(6, 5, 'C1', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'T-2', 1, 0, 'C', true);
    $pdf->Cell(6, 5, 'C2', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'S-1', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'T-3', 1, 0, 'C', true);
    $pdf->Cell(6, 5, 'C3', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'T-4', 1, 0, 'C', true);
    $pdf->Cell(6, 5, 'C4', 1, 0, 'C', true);
    $pdf->Cell(10, 5, 'S-2', 1, 0, 'C', true);
    $pdf->Cell(12, 5, 'NFIN', 1, 0, 'C', true);
    $pdf->Cell(8, 5, 'CR', 1, 0, 'C', true);
    $pdf->Cell(44, 5, 'COMENTARIOS', 1, 1, 'C', true);

    $cursos = DB::table('padres')->where([
        ['year', $year],
        ['ss', $estu->ss],
        ['grado', $grade],
        ['curso', '!=', ''],
        ['curso', 'NOT LIKE', '%AA-%']
    ])->orderBy('orden')->get();


    $notas = 0;
    $cr = 0;
    $au = 0;
    $ta = 0;

    $notas2 = 0;
    $cr2 = 0;
    $au2 = 0;
    $ta2 = 0;

    $notas3 = 0;
    $cr3 = 0;
    $au3 = 0;
    $ta3 = 0;

    $notas4 = 0;
    $cr4 = 0;
    $au4 = 0;
    $ta4 = 0;
    $notas5 = 0;
    $cr5 = 0;
    $notas6 = 0;
    $cr6 = 0;
    $notas7 = 0;
    $cr7 = 0;
    $cr8 = 0;

    foreach ($cursos as $curso) {
        $V5 = 0;
        $V6 = 0;
        $tot1t = "";
        $v7 = 0;
        $v8 = 0;
        $tot1t1 = "";
        if ($curso->credito > 0 and $curso->nota1 > 0 or $curso->credito > 0 and $curso->nota1 == '0') {
            $c = '1';
            $cr = $cr + 1;
            $notas = $notas + $curso->nota1;
        }

        if ($curso->credito > 0 and $curso->nota2 > 0 or $curso->credito > 0 and $curso->nota2 == '0') {
            $c = '2';
            $cr2 = $cr2 + 1;
            $notas2 = $notas2 + $curso->nota2;
        }

        if ($curso->credito > 0 and $curso->nota3 > 0 or $curso->credito > 0 and $curso->nota3 == '0') {
            $c = '3';
            $cr3 = $cr3 + 1;
            $notas3 = $notas3 + $curso->nota3;
        }

        if ($curso->credito > 0 and $curso->nota4 > 0 or $curso->credito > 0 and $curso->nota4 == '0') {
            $c = '4';
            $cr4 = $cr4 + 1;
            $notas4 = $notas4 + $curso->nota4;
        }
        if ($curso->credito > 0 and $curso->sem1 > 0) {
            $cr5 = $cr5 + 1;
            $notas5 = $notas5 + $curso->sem1;
        }
        if ($curso->credito > 0 and $curso->sem2 > 0) {
            $cr6 = $cr6 + 1;
            $notas6 = $notas6 + $curso->sem2;
        }
        if ($curso->credito > 0 and $curso->final > 0) {
            $cr7 = $cr7 + 1;
            $notas7 = $notas7 + $curso->final;
        }
        if ($curso->credito > 0) {
            $cr8 = $cr8 + $curso->credito;
        }

        if ($curso->aus1 > 0) {
            $au = $au + number_format($curso->aus1, 0);
        }
        if ($curso->tar1 > 0) {
            $ta = $ta + $curso->tar1;
        }
        if ($curso->aus2 > 0) {
            $au2 = $au2 + number_format($curso->aus2, 0);
        }
        if ($curso->tar2 > 0) {
            $ta2 = $ta2 + $curso->tar2;
        }
        if ($curso->aus3 > 0) {
            $au3 = $au3 + number_format($curso->aus3, 0);
        }
        if ($curso->tar3 > 0) {
            $ta3 = $ta3 + $curso->tar3;
        }
        if ($curso->aus4 > 0) {
            $au4 = $au4 + number_format($curso->aus4, 0);
        }
        if ($curso->tar4 > 0) {
            $ta4 = $ta4 + $curso->tar4;
        }

        $pdf->SetFont('Times', '', 9);
        if ($idi == 'Ingles') {
            $pdf->Cell(45, 5, $curso->descripcion, 1, 0);
        } else {
            $pdf->Cell(45, 5, $curso->descripcion, 1, 0);
        }
        $nn1 = '';
        $pdf->Cell(10, 5, $curso->nota1, 1, 0, 'C');
        $pdf->Cell(6, 5, $curso->con1, 1, 0, 'C');

        if ($tri2 == 'Si') {
            $pdf->Cell(10, 5, $curso->nota2, 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con2, 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        if ($sem1 == 'Si') {
            $pdf->Cell(10, 5, $curso->sem1, 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
        }


        if ($tri3 == 'Si') {
            $pdf->Cell(10, 5, $curso->nota3, 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con3, 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        if ($tri4 == 'Si') {
            $pdf->Cell(10, 5, $curso->nota4, 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con4, 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        if ($sem2 == 'Si') {
            $pdf->Cell(10, 5, $curso->sem2, 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
        }


        if ($prof == 'Si') {
            $pdf->Cell(12, 5, $curso->final, 1, 0, 'C');
        } else {
            $pdf->Cell(12, 5, '', 1, 0, 'C');
        }

        $cr1 = '';
        if ($ccr == 'Si') {
            $cr1 = $curso->credito;
        }
        $pdf->Cell(8, 5, $cr1, 1, 0, 'R');

        $comen = DB::table('comentarios')->where([
            ['code', $curso->{"com$c"}]
        ])->orderBy('code')->first();


        $pdf->Cell(44, 5, $comen->comentario ?? '', 1, 1, 'L');

        //  $consult4 = "select * from profesor where id='$row[0]'";
        //  $resultad4 = mysql_query($consult4);
        //  $row4=mysql_fetch_array($resultad4);

        $pdf->Cell(45, 5, '     ' . $curso->profesor, 1, 0);
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');


        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(12, 5, '', 1, 0, 'L');
        $pdf->Cell(8, 5, '', 1, 0, 'C');
        $pdf->Cell(44, 5, '', 1, 1, 'L');
    }

    $pdf->Cell(45, 5, $pq, 1, 0, 'R');
    if ($cr > 0) {
        $pdf->Cell(10, 5, round($notas / $cr, 0), 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    }

    if ($cr2 > 0 and $tri2 == 'Si') {
        $pdf->Cell(10, 5, round($notas2 / $cr2, 0), 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    }
    if ($cr5 > 0 and $sem1 == 'Si') {
        $pdf->Cell(10, 5, round($notas5 / $cr5, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }

    if ($cr3 > 0 and $tri3 == 'Si') {
        $pdf->Cell(10, 5, round($notas3 / $cr3, 0), 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    }
    if ($cr4 > 0 and $tri4 == 'Si') {
        $pdf->Cell(10, 5, round($notas4 / $cr4, 0), 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
        $pdf->Cell(6, 5, '', 1, 0, 'C');
    }

    if ($cr6 > 0 and $sem2 == 'Si') {
        $pdf->Cell(10, 5, round($notas6 / $cr6, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }

    if ($cr7 > 0 and $prof == 'Si') {
        $pdf->Cell(12, 5, round($notas7 / $cr7, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(12, 5, '', 1, 0, 'C');
    }


    if ($ccr == 'Si') {
        $pdf->Cell(8, 5, number_format($cr8, 2, '.', ''), 1, 0, 'R');
    } else {
        $pdf->Cell(8, 5, '', 1, 0, 'R');
    }
    $pdf->Cell(44, 5, '', 1, 1, 'C');

    $pdf->Cell(45, 5, $asi, 1, 0, 'R');
    $pdf->Cell(16, 5, $au . ' / ' . $ta, 1, 0, 'C');
    $pdf->Cell(16, 5, $au2 . ' / ' . $ta2, 1, 0, 'C');
    $pdf->Cell(10, 5, '  ', 1, 0, 'C');

    $pdf->Cell(16, 5, $au3 . ' / ' . $ta3, 1, 0, 'C');
    $pdf->Cell(16, 5, $au4 . ' / ' . $ta4, 1, 0, 'C');
    $pdf->Cell(10, 5, '  ', 1, 0, 'C');
    $au0 = $au + $au2 + $au3 + $au4;
    $ta0 = $ta + $ta2 + $ta3 + $ta4;
    $pdf->Cell(20, 5, $au0 . ' / ' . $ta0, 1, 0, 'C');
    $pdf->Cell(44, 5, '', 1, 1, 'R');

    $pdf->Cell(1, 5, '', 0, 1, 'R');
    $pdf->Cell(193, 10, '', 1, 1, 'L');
    $pdf->Cell(1, -15, '', 0, 0, 'R');
    $pdf->Cell(193, -15, $text1, 0, 1, 'C');
    $pdf->Cell(1, 23, '', 0, 0, 'R');
    $pdf->Cell(193, 23, $text2, 0, 1, 'C');
}

$pdf->Output();
