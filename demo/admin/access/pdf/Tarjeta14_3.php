<?php
require_once __DIR__ . '/../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;
use Classes\DataBase0\DB;

Session::is_logged();

$conducta = [];
$promedioS1 = [];
$promedioS2 = [];
$promedioLetters = [];
$cantS1 = [];
$cantS2 = [];

$lang = new Lang([
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],
    ['', ''],

]);

$school = new School(Session::id());
class nPDF extends PDF
{

    //Cabecera de pagina
    function Header()
    {
        parent::header();
    }
    function Footer() {}
}

function Grado($valor)
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

function Years($grado, $ss)
{
    $row = DB::table('acumulativa')->select("year")
        ->whereRaw("ss = '$ss' and grado like '$grado%'")->first();

    return $row->year ?? '';
}

function Maestro($grado, $ss)
{
    if (Years($grado, $ss)) {

        $row = DB::table('profesor')->select("nombre")
            ->whereRaw("ss = '$ss' and grado like '$grado%'")->first();
        return $row->nombre ?? '';
    } else {
        return '';
    }
}
function Con($valor)
{
    if ($valor == 'A') {
        return 4;
    } elseif ($valor == 'B') {
        return 3;
    } elseif ($valor == 'C') {
        return 2;
    } elseif ($valor == 'D') {
        return 1;
    } elseif ($valor == 'F') {
        return 0;
    } elseif ($valor == '') {
        return '';
    }
}
function Conducta($valor)
{
    if ($valor >= 3.5 && $valor <= 4) {
        return 'A';
    } elseif ($valor >= 2.5 && $valor <= 3.49) {
        return 'B';
    } elseif ($valor >= 1.5 && $valor <= 2.49) {
        return 'C';
    } elseif ($valor >= 0.8 && $valor <= 1.49) {
        return 'D';
    } elseif ($valor >= 0 && $valor <= 0.79) {
        return 'F';
    }
}

function  CursoS1($grado, $curso, $ss)
{
    global $promedioS1;
    global $cantS1;

    $row = DB::table('acumulativa')
        ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();

    $c = 0;
    if ($row->sem1 ?? 0 > 0) {
        if ($row->sem1 != '') {
            $c++;
        }
        if ($c > 0) {
            $promedioS1[$grado] += $row->sem1;
            $cantS1[$grado]++;
        }

        return $row->sem1 ?? '';
    } else {
        return NULL;
    }
}
function  CursoS2($grado, $curso, $ss)
{
    global $promedioS2;
    global $cantS2;

    $row = DB::table('acumulativa')
        ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();

    $c = 0;
    if ($row->sem2 ?? 0 > 0) {
        if ($row->sem2 != '') {
            $c++;
        }
        if ($c > 0) {
            $promedioS2[$grado] += $row->sem2;
            $cantS2[$grado]++;
        }

        return $row->sem2 ?? '';
    } else {
        return NULL;
    }
}

$pdf = new nPDF();
$pdf->Fill();
$pdf->AliasNbPages();

$pdf->SetFont('Arial', '', 11);
if ($opcion == '2') {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("year = '$Year' and grado = '$grados'")->orderBy('apellidos')->get();
} else {
    $students = DB::table('acumulativa')->select("DISTINCT ss, nombre, apellidos")
        ->whereRaw("ss = '$estu'")->orderBy('apellidos')->get();
}

foreach ($students as $estu) {
    $pdf->AddPage();

    //informacion del estudiante
    $info1 = DB::table('year')
        ->whereRaw("ss = '$estu->ss'")->orderBy('apellidos')->first();

    //informacion del encargado
    $info2 = DB::table('madre')->select("encargado")
        ->whereRaw("id = '$info1->id'")->orderBy('id')->first();

    $pdf->Cell(0, 5, utf8_encode('TRANSCRIPCIÓN DE CREDITOS'), 1, 1, 'C', true);
    $pdf->Cell(0, 5, utf8_encode('Nivel Superior'), 0, 1, 'C', true);
    $pdf->Ln(3);

    $pdf->Cell(20, 5, 'Nombre:');
    $pdf->Cell(100, 5, "$estu->apellidos $estu->nombre", 'B');

    $pdf->Cell(13, 5, 'Edad:');
    $pdf->Cell(10, 5, "$info1->edad", 'B', 0, 'C');

    $pdf->Cell(13, 5, 'Sexo:');
    $pdf->Cell(10, 5, "$info1->genero ", 'B', 1, 'C');

    $pdf->Ln(3);
    $pdf->Cell(20, 5, utf8_encode('Dirección:'));
    $pdf->Cell(100, 5, $info1->dir1, 'B');
    $pdf->Cell(33, 5, utf8_encode('Teléfono/Celular:'));
    $pdf->Cell(30, 5, $info1->tel1, 'B', 1);
    $pdf->Ln(3);
    $pdf->Cell(36, 5, 'Padre o Encargado:');
    $pdf->Cell(70, 5, $info2->encargado, 'B');

    $pdf->Cell(25, 5, utf8_encode('Ocupación'));
    $pdf->Cell(40, 5, '', 'B', 1);
    $pdf->Ln(4);
    $pdf->Cell(40, 5, "SS: $info1->ss", 1, 1, 'L', true);
    $pdf->Ln(5);

    $promedioS1  = array();
    $promedioS2  = array();
    $cantS1 = array();
    $cantS2 = array();

    $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->orderBy('apellidos')->first();

    $promedioS1['09'] = 0;
    $promedioS1['10'] = 0;
    $promedioS1['11'] = 0;
    $promedioS1['12'] = 0;
    $promedioS2['09'] = 0;
    $promedioS2['10'] = 0;
    $promedioS2['11'] = 0;
    $promedioS2['12'] = 0;
    $cantS1['09'] = 0;
    $cantS1['10'] = 0;
    $cantS1['11'] = 0;
    $cantS1['12'] = 0;
    $cantS2['09'] = 0;
    $cantS2['10'] = 0;
    $cantS2['11'] = 0;
    $cantS2['12'] = 0;

    $GRA = array(0, '09', '10', '11', '12');
    $pdf->Cell(40, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(37.5, 6, Years($GRA[$i], $estu->ss), 1, ($i == 4) ? 1 : 0, 'C', true);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Maestro/a', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 7);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(37.5, 6, Maestro($GRA[$i], $estu->ss), 1, ($i == 4) ? 1 : 0, 'C', true);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Asignaturas', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(37.5, 6, '9no', 1, 0, 'C', true);
    $pdf->Cell(37.5, 6, '10mo', 1, 0, 'C', true);
    $pdf->Cell(37.5, 6, '11mo', 1, 0, 'C', true);
    $pdf->Cell(37.5, 6, '12mo', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, '', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(18.75, 6, '1 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '2 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '1 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '2 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '1 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '2 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '1 sem.', 1, 0, 'C', true);
    $pdf->Cell(18.75, 6, '2 sem.', 1, 1, 'C', true);


    #1
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('INGLÉS'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'ING', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'ING', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #2
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('ESPAÑOL'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'ESP', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'ESP', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #3
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('MATEMÁTICAS'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'MAT', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'MAT', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #4
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'CIENCIA', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'CIE', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'CIE', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #5
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'HISTORIA', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'SOC', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'SOC', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #6
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('EDUCACIÓN FÍSICA'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'EDF', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'EDF', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #7
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('SALUD'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'SAL', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'SAL', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #8
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('EDUC. CRIST.'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'REL', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'REL', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }
    #8
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'TEATRO', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 4; $i++) {
        $pdf->Cell(18.75, 6, CursoS1($GRA[$i], 'TTRO', $estu->ss), 1, 0, 'C');
        $pdf->Cell(18.75, 6, CursoS2($GRA[$i], 'TTRO', $estu->ss), 1, ($i == 4) ? 1 : 0, 'C');
    }

    /*var_dump($conducta);
echo "<hr>";
var_dump($cant);*/
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'PROMEDIO', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    $p1 = 0;
    $p2 = 0;

    for ($i = 1; $i <= 4; $i++) {
        if ($cantS1[$GRA[$i]] > 0) {
            $pdf->Cell(18.75, 6, round(($promedioS1[$GRA[$i]] / $cantS1[$GRA[$i]])), 1, 0, 'C');
            $p1 = $p1 + round(($promedioS1[$GRA[$i]] / $cantS1[$GRA[$i]]));
            $p2 = $p2 + 1;
        } else {
            $pdf->Cell(18.75, 6, ' ', 1, 0, 'C');
        }

        if ($cantS2[$GRA[$i]] > 0) {
            $pdf->Cell(18.75, 6, round(($promedioS2[$GRA[$i]] / $cantS2[$GRA[$i]])), 1, ($i == 4) ? 1 : 0, 'C');
            $p1 = $p1 + round(($promedioS2[$GRA[$i]] / $cantS2[$GRA[$i]]));
            $p2 = $p2 + 1;
        } else {
            $pdf->Cell(18.75, 6, ' ', 1, ($i == 4) ? 1 : 0, 'C');
        }
    }
    // Segunda parte
    $pdf->Ln(10);
    $pdf->Cell(70, 5, "Promedio General Escuela Superior:");
    $p3 = '';
    if ($p2 > 0) {
        $p3 = round($p1 / $p2, 0);
    }
    $pdf->Cell(20, 5, $p3, 'B', 0, 'C');
    $pdf->Cell(30);
    $pdf->Cell(35, 5, utf8_encode("Total de Créditos:"), 0);
    $pdf->Cell(20, 5, "", 'B', 1);
    $pdf->Ln(10);

    $pdf->Cell(0, 5, utf8_encode("Lugar/Fecha graduación"), 0, 1);
    $pdf->Cell(0, 5, 'Materias/Grado', 0, 1);

    $pdf->Cell(20, 5, "Grado", 1);
    $pdf->Cell(50, 5, "Ciencias", 1);
    $pdf->Cell(50, 5, utf8_encode("Matemáticas"), 1);
    $pdf->Cell(50, 5, "Historias", 1, 1);

    $pdf->Cell(20, 5, "9no", 1);
    $pdf->Cell(50, 5, "Cs. Terrestres 6", 1);
    $pdf->Cell(50, 5, "Algebra II", 1);
    $pdf->Cell(50, 5, "Historia Mundial", 1, 1);

    $pdf->Cell(20, 5, "10mo", 1);
    $pdf->Cell(50, 5, utf8_encode("Biología"), 1);
    $pdf->Cell(50, 5, utf8_encode("Geometría"), 1);
    $pdf->Cell(50, 5, "Historia Puerto Rico", 1, 1);

    $pdf->Cell(20, 5, "11mo", 1);
    $pdf->Cell(50, 5, utf8_encode("Química"), 1);
    $pdf->Cell(50, 5, utf8_encode("Algebra trigonometría"), 1);
    $pdf->Cell(50, 5, "Historia Estados Unidos", 1, 1);

    $pdf->Cell(20, 5, "12mo", 'LTR');
    $pdf->Cell(50, 5, utf8_encode("Ecología/Cs. Ambientales"), 'LTR');
    $pdf->Cell(50, 5, '_____Mat. General', 'LTR');
    $pdf->Cell(50, 5, utf8_encode("Psicología"), 'LTR', 1);
    $pdf->Cell(20, 5, "", 'LBR');
    $pdf->Cell(50, 5, '', 'LBR');
    $pdf->Cell(50, 5, '_____Pre-Calculo', 'LBR');
    $pdf->Cell(50, 5, '', 'LBR', 1);


    $pdf->Ln(15);

    $pdf->Cell(80, 5, '', 'B');
    $pdf->Cell(50);
    $pdf->Cell(50, 5, '', 'B', 1);

    $pdf->Cell(80, 5, 'Directora Escolar', 0, 0, 'C');
    $pdf->Cell(50);
    $pdf->Cell(50, 5, 'Fecha', 0, 0, 'C');
}
$pdf->Output();
