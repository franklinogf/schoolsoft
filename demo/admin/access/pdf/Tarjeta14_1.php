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

$conducta  = [];
$promedio  = [];
$promedioLetters  = [];
$cant = [];


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

function  Curso($grado, $curso, $ss)
{
    global $conducta;
    global $promedio;
    global $cantc;
    global $cant;

    $row = DB::table('acumulativa')
        ->whereRaw("ss = '$ss' and grado like '$grado%' and curso like '$curso%'")->first();

    $b = 0;
    $c = 0;
    if ($row->sem1 ?? 0 > 0 or $row->sem2 ?? 0 > 0) {
        if ($row->sem1 != '') {
            $c++;
            $b = $b + $row->sem1;
        }
        if ($row->sem2 != '') {
            $c++;
            $b = $b + $row->sem2;
        }
        if ($c > 0) {
            $valor = $b / $c;
            $promedio[$grado] += $valor;
            $cant[$grado]++;
        }

        if ($valor <> "") {
            $b = 0;
            $c = 0;
            if (Con($row->con2) != '') {
                $c++;
                $b = $b + Con($row->con2);
            }
            if (Con($row->con4) != '') {
                $c++;
                $b = $b + Con($row->con4);
            }
            if ($c > 0) {
                $conducta[$grado] = $b / $c;
                $cantc[$grado]++;
            }
        }
        return ($valor == 0) ? '' : round($valor);
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
    $pdf->Cell(0, 5, utf8_encode('ESCUELA ELEMENTAL'), 1, 1, 'C', true);
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

    $conducta  = array();
    $promedio  = array();
    $cant = array();

    $grados = DB::table('acumulativa')->select("DISTINCT grado")
        ->whereRaw("ss = '$estu->ss' and (grado not like '12%' and grado not like '11%' and grado not like '10%' and grado not like '09%')")->orderBy('apellidos')->first();

    $conducta['01'] = 0;
    $conducta['02'] = 0;
    $conducta['03'] = 0;
    $conducta['04'] = 0;
    $conducta['05'] = 0;
    $promedio['01'] = 0;
    $promedio['02'] = 0;
    $promedio['03'] = 0;
    $promedio['04'] = 0;
    $promedio['05'] = 0;
    $cant['01'] = 0;
    $cant['02'] = 0;
    $cant['03'] = 0;
    $cant['04'] = 0;
    $cant['05'] = 0;
    $cantc['01'] = 0;
    $cantc['02'] = 0;
    $cantc['03'] = 0;
    $cantc['04'] = 0;
    $cantc['05'] = 0;

    $GRA = array(0, '01', '02', '03', '04', '05');
    $pdf->Cell(40, 6, utf8_encode('AÑO ESCOLAR'), 1, 0, 'L', true);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Years($GRA[$i], $estu->ss), 1, ($i == 5) ? 1 : 0, 'C', true);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Maestro/a', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 7);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Maestro($GRA[$i], $estu->ss), 1, ($i == 5) ? 1 : 0, 'C', true);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Asignaturas', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, 'Primero', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Segundo', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Tercero', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Cuarto', 1, 0, 'C', true);
    $pdf->Cell(30, 6, 'Quinto', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);

    #3
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('MATEMÁTICAS'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'MAT', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #4
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('INGLÉS'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'ING', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #5
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('ESPAÑOL'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'ESP', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #6
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'CIENCIA', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'CIE', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #7
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'ESTUDIOS SOCIALES', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'SOC', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #8
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('EDUCACIÓN FÍSICA'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'EDF', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #9
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode('EDUC. CRIST.'), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'REL', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #10
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'ARTE', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'ART', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #12
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'TEATRO', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, Curso($GRA[$i], 'TTRO', $estu->ss), 1, ($i == 5) ? 1 : 0, 'C');
    }
    #13
    /*var_dump($conducta);
echo "<hr>";
var_dump($cant);*/
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'PROMEDIO', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        //  $pdf->Cell(30,6,($promedio[$GRA[$i]] ?? 0 == 0)?"":round(($promedio[$GRA[$i]] / $cant[$GRA[$i]])),1,($i == 5)?1:0,'C');
        if ($cant[$GRA[$i]] > 0) {
            $pdf->Cell(30, 6, round(($promedio[$GRA[$i]] / $cant[$GRA[$i]])), 1, ($i == 5) ? 1 : 0, 'C');
        } else {
            $pdf->Cell(30, 6, ' ', 1, ($i == 5) ? 1 : 0, 'C');
        }
    }
    // Segunda parte
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'CONDUCTA', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        //  $pdf->Cell(30,6,($conducta[$GRA[$i]] ?? 0 == 0)?"":Conducta(($conducta[$GRA[$i]] / $cant[$GRA[$i]])),1,($i == 5)?1:0,'C',true);
        //  $pdf->Cell(30,6,($conducta[$GRA[$i]] ?? 0 == 0)?"":$conducta[$GRA[$i]].' '.$cant[$GRA[$i]],1,($i == 5)?1:0,'C',true);
        if ($cantc[$GRA[$i]] > 0) {
            $pdf->Cell(30, 6, Conducta($conducta[$GRA[$i]] / $cantc[$GRA[$i]]), 1, ($i == 5) ? 1 : 0, 'C', true);
        } else {
            $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C', true);
        }
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, 'Puntualidad', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode("Hábitos Trabajo"), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, "Responsabilidad", 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, utf8_encode("Cooperación"), 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C');
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(40, 6, "Relaciones Personales", 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 11);
    for ($i = 1; $i <= 5; $i++) {
        $pdf->Cell(30, 6, '', 1, ($i == 5) ? 1 : 0, 'C');
    }

    $pdf->Ln(10);
    $pdf->Cell(37, 5, "OBSERVACIONES:");
    $pdf->Cell(0, 5, "", 'B', 1);
    $pdf->Ln(3);
    $pdf->Cell(0, 5, "", 'B', 1);
    $pdf->Ln(10);


    $pdf->Cell(50, 5, '', 'B');
    $pdf->Cell(50);
    $pdf->Cell(80, 5, '', 'B', 1);

    $pdf->Cell(50, 5, 'Fecha');
    $pdf->Cell(50);
    $pdf->Cell(80, 5, 'Directora Escolar');
}
$pdf->Output();
