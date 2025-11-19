<?php
require_once __DIR__ . '/../../../../app.php';

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
    ['Femeninas', 'Females'],
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
    } else  if ($valor <= '59') {
        return 'F';
    }
}

class nPDF extends PDF
{
    function Header()
    {
        parent::header();
    }

    function Footer() {}
}

$grade = $_POST['grade'] ?? '';
$men = $_POST['mensaje'] ?? '';
$tri1 = $_POST['tri1'] ?? '';
$tri2 = $_POST['tri2'] ?? '';
$tri3 = $_POST['tri3'] ?? '';
$tri4 = $_POST['tri4'] ?? '';
$sem1 = $_POST['sem1'] ?? '';
$sem2 = $_POST['sem2'] ?? '';
$prof = $_POST['prof'] ?? '';
$ccr = $_POST['cr'] ?? '';

$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$idi = '';

if ($idi == 'Ingles') {
    $ye = 'SCHOOL YEAR:';
    $no = 'Name: ';
    $gr = 'Grade: ';
    $de = 'DESCRIPTION';
    $pr = 'AVG';
    $va = 'Assigned Value';
    $fe = 'DATE: ';
    $rr = 20;
    $text1 = $mensaj->t1i ?? '';
    $text2 = $mensaj->t2i ?? '';
    $fi = 'YR';
    $f2 = 'AVG';
    $se1 = 'FIRST SEMESTER';
    $se2 = 'SECOND SEMESTER';
    $qq1 = ' 1Q    C1   2Q    C2   S-1';
    $qq2 = ' 3Q    C3   4Q    C4   S-2';
    $pq = 'AVERAGE.';
    $asi = 'ABSENCE AND LATE';
    $com1 = 'Comments';
    $msh = 'HOMEROOM TEACHER:';
} else {
    $ye = utf8_encode('AÑO ESCOLAR:');
    $no = 'Nombre: ';
    $gr = 'Grado: ';
    $de = 'DESCRIPCION';
    $pr = 'PRO';
    $va = 'Valor Asignado';
    $fe = 'FECHA: ';
    $rr = 0;
    $text1 = $mensaj->t1e ?? '';
    $text2 = $mensaj->t2e ?? '';
    $fi = 'PRO';
    $f2 = utf8_encode('AÑO');
    $se1 = 'PRIMER SEMESTRE';
    $se2 = 'SEGUNDO SEMESTRE';
    $qq1 = ' 1T    C1   2T    C2   S-1';
    $qq2 = ' 3T    C3   4T    C4   S-2';
    $pq = 'PROMEDIO';
    $asi = 'AUSENCIAS Y TARDANZAS';
    $com1 = 'Comentarios';
    $msh = 'MAESTRO SALON HOGAR:';
}


$pdf = new nPDF();
$pdf->useFooter(false);
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

$year = $school->info('year2');
$pdf->useFooter(false);
$pdf->SetTitle($lang->translation("Reporte de Notas") . " $year", true);
$pdf->Fill();

$mensaj = DB::table('codigos')->where([
    ['codigo', $men],
])->orderBy('codigo')->first();

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);

$a = 0;

foreach ($students as $estu) {

    $pdf->AddPage();
    $a = $a + 1;
    $gra = '';
    $padres = DB::table('madre')->where([
        ['id', $estu->id]
    ])->orderBy('id')->first();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80);
    $pdf->Cell(30, 3, 'TARJETA DE NOTAS', 0, 0, 'C');
    $pdf->Ln(8);
    $pdf->SetFont('Arial', '', 11);

    //     $pdf->SetFillColor(240);
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(1, 5, '', 0, 0, 'R');
    $pdf->Cell(20, 5, $ye, 0, 0, 'L');
    $pdf->Cell(24, 5, $year, 0, 0, 'R');
    $pdf->Cell(7, 5, ' ', 0, 0, 'L');
    $pdf->Cell(1, 5, '', 0, 1, 'R');
    $pdf->Cell(1, 5, '', 0, 0, 'R');
    $pdf->Cell(15, 5, $fe, 0, 0, 'L');
    $pdf->Cell(24, 5, date("m-d-Y"), 0, 0, 'R');
    $pdf->Cell(10, 5, '', 0, 0, 'L');
    $nom = $teacher->nombre ?? '';
    $ape = utf8_encode($teacher->apellidos ?? '');

    $pdf->Cell(22, 5, '', 0, 0, '');
    $pdf->Cell(52, 5, $msh, 0, 0, '');
    $pdf->Cell(67, 5, $nom . ' ' . $ape, 0, 1, 'L');

    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(125, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->SetFont('Times', '', 11);
    list($ss1, $ss2, $ss3) = explode("-", $estu->ss);
    $pdf->Cell(37, 5, 'S.S.: XXX-XX-' . $ss3, 1, 0, 'C', true);
    $pdf->Cell(31, 5, $gr . ' ' . $grade, 1, 1, 'C', true);


    $pdf->Cell(50, 5, $de, 1, 0, 'C', true);
    $pdf->Cell(42, 5, $se1, 1, 0, 'C', true);
    $pdf->Cell(42, 5, $se2, 1, 0, 'C', true);
    $pdf->Cell(9, 5, $fi, 1, 0, 'C', true);
    $pdf->Cell(8, 5, 'CR', 1, 0, 'C', true);
    $pdf->Cell(42, 5, $com1, 1, 1, 'C', true);
    $pdf->Cell(50, 5, '', 1, 0, 'R', true);
    $pdf->Cell(42, 5, $qq1, 1, 0, 'L', true);
    $pdf->Cell(42, 5, $qq2, 1, 0, 'L', true);
    $pdf->Cell(9, 5, $f2, 1, 0, 'C', true);
    $pdf->Cell(8, 5, '', 1, 0, 'C', true);
    $pdf->Cell(42, 5, '', 1, 1, 'C', true);

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

    foreach ($cursos as $curso) {
        $V5 = 0;
        $V6 = 0;
        $tot1t = "";
        $v7 = 0;
        $v8 = 0;
        $tot1t1 = "";
        $c = '1';
        if ($curso->credito > 0 and $curso->nota1 > 0 or $curso->credito > 0 and $curso->nota1 == '0') {
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
            $pdf->Cell(50, 5, $row[25], 1, 0);
        } else {
            $pdf->Cell(50, 5, $curso->descripcion, 1, 0);
        }
        $pdf->Cell(6, 5, $curso->nota1, 1, 0, 'C');
        $nn1 = '';
        $pdf->Cell(4, 5, NLetra($curso->nota1), 1, 0, 'C');
        $pdf->Cell(6, 5, $curso->con1, 1, 0, 'C');
        if ($tri2 == 'Si') {
            $pdf->Cell(6, 5, $curso->nota2, 1, 0, 'C');
            $pdf->Cell(4, 5, NLetra($curso->nota2), 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con2, 1, 0, 'C');
        } else {
            $pdf->Cell(6, 5, '', 1, 0, 'C');
            $pdf->Cell(4, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        $nns1 = '';
        $tot1t = $curso->sem1;

        if ($sem1 == 'Si') {
            $pdf->Cell(6, 5, $tot1t, 1, 0, 'C');
            $pdf->Cell(4, 5, NLetra($curso->sem1), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
        }

        $nn1 = '';
        if ($tri3 == 'Si') {
            $pdf->Cell(6, 5, $curso->nota3, 1, 0, 'C');
            $pdf->Cell(4, 5, NLetra($curso->nota3), 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con3, 1, 0, 'C');
        } else {
            $pdf->Cell(6, 5, '', 1, 0, 'C');
            $pdf->Cell(4, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        $nn2 = '';

        if ($tri4 == 'Si') {
            $pdf->Cell(6, 5, $curso->nota4, 1, 0, 'C');
            $pdf->Cell(4, 5, NLetra($curso->nota4), 1, 0, 'C');
            $pdf->Cell(6, 5, $curso->con4, 1, 0, 'C');
        } else {
            $pdf->Cell(6, 5, '', 1, 0, 'C');
            $pdf->Cell(4, 5, '', 1, 0, 'C');
            $pdf->Cell(6, 5, '', 1, 0, 'C');
        }

        $nns2 = '';
        $tot1t1 = $curso->sem2;

        if ($sem2 == 'Si') {
            $pdf->Cell(6, 5, $tot1t1, 1, 0, 'C');
            $pdf->Cell(4, 5, NLetra($curso->sem2), 1, 0, 'C');
        } else {
            $pdf->Cell(10, 5, '', 1, 0, 'C');
        }
        if ($prof == 'Si') {
            $pdf->Cell(9, 5, $curso->final, 1, 0, 'C');
        } else {
            $pdf->Cell(9, 5, '', 1, 0, 'C');
        }

        $cr1 = '';
        if ($ccr == 'Si') {
            $cr1 = $curso->credito;
        }
        $pdf->Cell(8, 5, $cr1, 1, 0, 'R');

        $comen = DB::table('comentarios')->where([
            ['code', $curso->{"com$c"}]
        ])->orderBy('code')->first();

        $pdf->Cell(42, 5, $comen->comentario ?? '', 1, 1, 'L');

        $pdf->Cell(50, 5, '     ' . $curso->profesor, 1, 0);
        $pdf->Cell(16, 5, '', 1, 0, 'C');
        $pdf->Cell(16, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'C');

        $pdf->Cell(16, 5, '', 1, 0, 'C');
        $pdf->Cell(16, 5, '', 1, 0, 'C');
        $pdf->Cell(10, 5, '', 1, 0, 'R');
        $pdf->Cell(9, 5, '', 1, 0, 'R');
        $pdf->Cell(8, 5, '', 1, 0, 'R');
        $pdf->Cell(42, 5, '', 1, 1, 'L');
    }

    $pdf->Cell(50, 5, $pq, 1, 0, 'R');
    if ($cr > 0) {
        $pdf->Cell(6, 5, round($notas / $cr, 0), 1, 0, 'C');
        $nf = round($notas / $cr, 0);
        $nf1 = '';
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }

    $pdf->Cell(6, 5, '', 1, 0, 'C');
    if ($cr2 > 0 and $tri2 == 'Si') {
        $pdf->Cell(6, 5, round($notas2 / $cr2, 0), 1, 0, 'C');
        $nf = round($notas2 / $cr2, 0);
        $nf1 = '';
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }
    $pdf->Cell(6, 5, '', 1, 0, 'C');


    if ($cr5 > 0) {
        $nf = round($notas5 / $cr5, 0);
    }
    if ($sem1 == 'Si' and $cr5 > 0) {
        $pdf->Cell(6, 5, round($notas5 / $cr5, 0), 1, 0, 'C');
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }

    if ($cr3 > 0 and $tri3 == 'Si') {
        $nf = round($notas3 / $cr3, 0);
        $pdf->Cell(6, 5, round($notas3 / $cr3, 0), 1, 0, 'C');
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }
    $pdf->Cell(6, 5, '', 1, 0, 'C');
    if ($cr4 > 0 and $tri4 == 'Si') {
        $nf = round($notas4 / $cr4, 0);
        $pdf->Cell(6, 5, round($notas4 / $cr4, 0), 1, 0, 'C');
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }
    $pdf->Cell(6, 5, '', 1, 0, 'C');

    if ($cr6 > 0) {
        $nf = round($notas6 / $cr6, 0);
    }
    if ($sem2 == 'Si' and $cr6 > 0) {
        $pdf->Cell(6, 5, round($notas6 / $cr6, 0), 1, 0, 'C');
        $pdf->Cell(4, 5, NLetra($nf), 1, 0, 'C');
    } else {
        $pdf->Cell(10, 5, '', 1, 0, 'C');
    }

    if ($prof == 'Si' and $cr6 > 0) {
        $cr8 = 0;
        if ($cr5 > 0) {
            $notas7 = round($notas5 / $cr5, 0);
            $cr8 = $cr8 + 1;
        }
        if ($cr6 > 0) {
            $cr7 = round($notas6 / $cr6, 0);
            $cr8 = $cr8 + 1;
        }
        $pdf->Cell(9, 5, round(($notas7 + $cr7) / $cr8, 0), 1, 0, 'C');
    } else {
        $pdf->Cell(9, 5, '', 1, 0, 'C');
    }

    if ($ccr == 'Si') {
        $pdf->Cell(8, 5, number_format($cr, 2, '.', ''), 1, 0, 'R');
    } else {
        $pdf->Cell(8, 5, '', 1, 0, 'R');
    }
    $pdf->Cell(42, 5, '', 1, 1, 'R');
    $pdf->Cell(50, 5, $asi, 1, 0, 'R');

    $pdf->Cell(16, 5, '  /  ', 1, 0, 'C');
    $pdf->Cell(16, 5, '  /  ', 1, 0, 'C');

    $pdf->Cell(10, 5, ' ', 1, 0, 'C');

    $pdf->Cell(16, 5, '  /  ', 1, 0, 'C');
    $pdf->Cell(16, 5, '  /  ', 1, 0, 'C');

    $pdf->Cell(10, 5, '', 1, 0, 'C');
    $pdf->Cell(9, 5, '', 1, 0, 'R');
    $pdf->Cell(8, 5, '', 1, 0, 'R');
    $pdf->Cell(42, 5, '', 1, 1, 'R');

    $pdf->Cell(1, 2, '', 0, 1, 'R');
    $pdf->Cell(193, 10, '', 1, 1, 'L');
    $pdf->Cell(1, -15, '', 0, 0, 'R');
    $pdf->Cell(193, -15, $text1, 0, 1, 'C');
    $pdf->Cell(1, 23, '', 0, 0, 'R');
    $pdf->Cell(193, 23, $text2, 0, 1, 'C');


    if ($idi == 'Ingles') {
        $pdf->SetY(-99);
        $pdf->Cell(50, 5, '_____________________________', 0, 0, 'C');
        $pdf->Cell(70, 5, 'LEGEND OF GRADES', 0, 0, 'C');
        $pdf->Cell(70, 5, 'LEGEND', 0, 1, 'C');
        $pdf->Cell(50, 3, 'TEACHER', 0, 0, 'C');
        $pdf->Cell(45, 3, '100 - 89 = A', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '1Q = 1ST TRIMESTER', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 88 - 79 = B', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C1 = CONDUCT 1ST QUARTER', 0, 1, 'L');

        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 78 - 69 = C', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '2Q = 2ND QUARTER', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 68 - 59 = D', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C2 = CONDUCT 2ND QUARTER', 0, 1, 'L');
        $pdf->Cell(50, 3, '_____________________________', 0, 0, 'C');
        $pdf->Cell(45, 3, '58 -   0 =  F', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'S-1 = 1ST SEMESTER', 0, 1, 'L');
        $pdf->Cell(50, 5, 'PRINCIPAL', 0, 0, 'C');
        $pdf->Cell(45, 3, '', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '3Q = 3RD QUARTER', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(62, 3, 'LEGEND RANGES OF CONDUCT', 0, 0, 'R');
        $pdf->Cell(23, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C3 = CONDUCT 3RD QUARTER', 0, 1, 'L');

        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, 'MS = VERY SATISFYING', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '4Q = 4TH QUARTER', 0, 1, 'L');
        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, '    S = SATISFYING', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C4 = CONDUCT 4TH QUARTER', 0, 1, 'L');
        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, 'DM = MUST IMPROVE', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'S-2 = 2ND SEMESTER', 0, 1, 'L');

        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, 'NS = NOT SATISFACTORY', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'CR = CREDITS', 0, 1, 'L');

        $pdf->Cell(135, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'D = DOMINATE', 0, 1, 'L');
        $pdf->Cell(135, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'ND = NO DOMINATE', 0, 1, 'L');
        $pdf->Cell(135, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'EP = IN PROGRESS', 0, 1, 'L');

        $pdf->SetY(-48);
        $pdf->Cell(10, 3, '', 0, 0, 'L');
        if (empty($padres->madre)) {
            $pdf->Cell(50, 3, $padres->padre, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir2, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir4, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->pueblo2 . ' ' . $padres->est2 . ', ' . $padres->zip2, 0, 0, 'L');
        } else {
            $pdf->Cell(50, 3, $padres->madre, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir1, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir3, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->pueblo1 . ' ' . $padres->est1 . ', ' . $padres->zip1, 0, 0, 'L');
        }
        $pdf->SetY(-48);
        $pdf->Cell(10, 5, '', 0, 0, 'L');
        $pdf->Cell(50, 25, '', 0, 0, 'L');
        $pdf->Cell(70, 25, '', 0, 0, 'C');
        $pdf->Cell(40, 25, 'SEAL', 1, 0, 'C');
    } else {

        $pdf->SetY(-95);
        $pdf->Cell(50, 5, '_____________________________', 0, 0, 'C');
        $pdf->Cell(70, 5, 'LEYENDA DE NOTAS', 0, 0, 'C');
        $pdf->Cell(70, 5, 'LEYENDA', 0, 1, 'C');
        $pdf->Cell(50, 3, 'MAESTRO', 0, 0, 'C');
        $pdf->Cell(45, 3, '100 - 89 = A', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '1T = 1ER TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 88 - 79 = B', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C1 = CONDUCTA 1ER TRIMESTRE', 0, 1, 'L');

        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 78 - 69 = C', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '2T = 2DO TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 68 - 59 = D', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C2 = CONDUCTA 2DO TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(50, 3, '_____________________________', 0, 0, 'C');
        $pdf->Cell(45, 3, ' 58 -   0 = F', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'S-1 = 1ER SEMESTRE', 0, 1, 'L');
        $pdf->Cell(50, 3, 'DIRECTORA', 0, 0, 'C');
        $pdf->Cell(45, 3, '', 0, 0, 'R');
        $pdf->Cell(40, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '3T = 3ER TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(50, 3, '', 0, 0, 'C');
        $pdf->Cell(62, 3, 'LEYENDA RANGOS DE CONDUCTA', 0, 0, 'R');
        $pdf->Cell(23, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C3 = CONDUCTA 3ER TRIMESTRE', 0, 1, 'L');

        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, '    E = EXCELENTE', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, '4T = 4TO TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, ' MS = MUY SATISFACTORIO', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'C4 = CONDUCTA 4TO TRIMESTRE', 0, 1, 'L');
        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, '    S = SATISFACTORIO', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'S-2 = 2DO SEMESTRE', 0, 1, 'L');

        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, 'DM = DEBE MEJORAR', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'CR = CREDITOS', 0, 1, 'L');

        $pdf->Cell(60, 3, '', 0, 0, 'C');
        $pdf->Cell(55, 3, 'NS = NO SATISFACTORIO', 0, 0, 'L');
        $pdf->Cell(20, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'D = DOMINA', 0, 1, 'L');
        $pdf->Cell(135, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'ND = NO DOMINA', 0, 1, 'L');
        $pdf->Cell(135, 3, '', 0, 0, 'C');
        $pdf->Cell(40, 3, 'EP = EN PROCESO', 0, 1, 'L');

        $pdf->SetY(-47);
        $pdf->Cell(10, 3, '', 0, 0, 'L');
        if (empty($padres->madre)) {
            $pdf->Cell(50, 3, $padres->padre, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir2, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir4, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->pueblo2 . ' ' . $padres->est2 . ', ' . $padres->zip2, 0, 0, 'L');
        } else {
            $pdf->Cell(50, 3, $padres->madre, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir1, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->dir3, 0, 1, 'L');
            $pdf->Cell(10, 3, '', 0, 0, 'L');
            $pdf->Cell(50, 3, $padres->pueblo1 . ' ' . $padres->est1 . ', ' . $padres->zip1, 0, 0, 'L');
        }
        $pdf->SetY(-47);
        $pdf->Cell(10, 5, '', 0, 0, 'L');
        $pdf->Cell(50, 25, '', 0, 0, 'L');
        $pdf->Cell(70, 25, '', 0, 0, 'C');
        $pdf->Cell(40, 25, 'SELLO', 1, 0, 'C');
    }





    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(80);
    $pdf->Cell(30, 3, 'REPORTE DE ASISTENCIA / ' . $year, 0, 0, 'C');
    $pdf->Ln(8);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(150, 5, $no . ' ' . $estu->apellidos . ' ' . $estu->nombre, 1, 0, 'L', true);
    $pdf->Cell(30, 5, $gr . ' ' . $grade, 1, 1, 'C', true);

    $pdf->Cell(10, 5, '', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'FECHA', 1, 0, 'C', true);
    $pdf->Cell(35, 5, 'CURSO', 1, 0, 'C', true);
    $pdf->Cell(100, 5, 'DESCRIPCION', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 9);
    //    $dat2 = "select * from asispp where ss = '$row1[0]' AND year='$row0[116]'";
    //    $tab2 = mysql_query($dat2, $con) or die ("problema con query 7") ;
    //    $result7=mysql_query($dat2);

    $result7 = DB::table('asispp')->where([
        ['ss', $curso->ss],
        ['year', $year]
    ])->get();


    $a = 0;
    //while ($row8=mysql_fetch_array($result7))
    //      {
    foreach ($result7 as $row8) {
        $a = $a + 1;
        if ($row8->codigo == 1) {
            $cod = 'Ausencia-situación en el hogar';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 2) {
            $cod = 'Ausencia-determinación del hogar(viaje)';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 3) {
            $cod = 'Ausencia-actividad con padres(open house)';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 4) {
            $cod = 'Ausencia-enfermedad';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 5) {
            $cod = 'Ausencia-cita';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 6) {
            $cod = 'Ausencia-actividad educativa del colegio';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 7) {
            $cod = 'Ausencia-sin excusa del hogar';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 15) {
            $cod = 'Ausencia-determinación de la familia';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 16) {
            $cod = 'Ausencia-problema de transportación';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 17) {
            $cod = 'Ausencia-protocolo salud';
            $li2 = $li2 + 1;
        }
        if ($row8->codigo == 8) {
            $cod = 'Tardanza-sin excusa del hogar';
        }
        if ($row8->codigo == 9) {
            $cod = 'Tardanza-situación en el hogar';
        }
        if ($row8->codigo == 10) {
            $cod = 'Tardanza-problema en la transportación';
        }
        if ($row8->codigo == 11) {
            $cod = 'Tardanza-enfermedad';
        }
        if ($row8->codigo == 12) {
            $cod = 'Tardanza-cita';
        }
        if ($row8->codigo == 13) {
            $cod = 'Ausente protocolo COVID-19';
        }
        if ($row8->codigo == 14) {
            $cod = 'Fue recogido antes de la salida';
        }
        if ($row8->codigo == 18) {
            $cod = 'Fue recogido antes de la salida - enfermedad';
        }
        if ($row8->codigo == 19) {
            $cod = 'Fue recogido antes de la salida - personal';
        }
        if ($row8->codigo == 20) {
            $cod = 'Actividad escolar - torneo';
        }
        if ($row8->codigo == 21) {
            $cod = 'Fue recogido antes de la salida - cita';
        }
        if ($row8->codigo == 22) {
            $cod = 'Suspensión';
        }

        $pdf->Cell(10, 4, $a, 1, 0, 'R');
        $pdf->Cell(35, 4, $row8->fecha, 1, 0, 'C');
        $pdf->Cell(35, 4, $row8->curso, 1, 0, 'C');
        $pdf->Cell(100, 4, utf8_encode($cod), 1, 1, 'L');
    }
}

$pdf->Output();
