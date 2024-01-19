<?php
require_once '../../../../app.php';


// I, as the parent, guardian, or responsible party, hereby acknowledge and affirm that I will actively monitor and oversee my child's online activities. The system will maintain a record to verify that I have reviewed and acknowledged the disciplinary actions.
// Yo, como padre, tutor o responsable, por la presente reconozco y afirmo que supervisar&#65533; activamente las actividades en l&#65533;nea de mi hijo/a. El sistema mantendr&#65533; un registro para verificar que he revisado y aceptado las acciones disciplinarias.


use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Registro de notas', 'Record of notes'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
  ["Año escolar:", "School year:"],
  ["Descripción", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
  ['Crédito', 'Credit'],
    ['Trimestre', 'Quarter'],
    ['NF', 'FN'],
    ['T-D', 'DW'],
    ['T-L', 'HW'],
    ['P-C', 'QZ'],
    ['TPA', 'TAP'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Trabajos Diarios', 'Daily Homework'],
    ['Trabajos Libreta', 'Homework'],
    ['Fecha', 'Date'],
    ['Tema', 'Topic'],
    ['Valor', 'Value'],
    ['Pruebas Cortas', 'Quiz'],

    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['', ''],
    ['', ''],

]);

function NLetra($valor){
  if($valor == ''){
    return '';
  }else if ($valor <= '100' && $valor >= '90') {
    return 'A';
  }else if ($valor <= '89' && $valor >= '80') {
    return 'B';
  }else if ($valor <= '79' && $valor >= '70') {
    return 'C';
  }else if ($valor <= '69' && $valor >= '60') {
    return 'D';
  }else  if ($valor <= '59') {
    return 'F';
  }
}


//$pdf = new PDF();
//$pdf->useFooter(false);
$school = new School(Session::id());

$year = $school->year();
$pdf = new PDF();
$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Registro de notas") . " $year", true);
$pdf->Fill();

$id = $_POST['teacher'];
$teacher = DB::table('profesor')->where([
       ['id', $id],
       ])->orderBy('id')->first();
if ($_POST['tri1b'] ==  'Si')
   {

$students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

foreach ($students as $estu) {
$stu = DB::table('padres')->where([
       ['id', $id],
       ['curso', $estu->curso],
       ['year', $year],
       ])->orderBy('curso')->get();
    $te = count($stu);
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Registro de notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Crédito"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 5, $teacher->apellidos.' '.$teacher->nombre, 1, 0, 'C');
    $pdf->Cell(19, 5, $estu->curso, 1, 0, 'C');
    $pdf->Cell(50, 5, $estu->descripcion, 1, 0, 'C');
    $pdf->Cell(15, 5, $estu->credito, 1, 0, 'C');
    $pdf->Cell(20, 5, $te, 1, 0, 'C');
    $pdf->Cell(18, 5, $_POST['nota'], 1, 0, 'C');
    $pdf->Cell(21, 5, date("m-d-Y"), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $pdf->Cell(48, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(37, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-1', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-2', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-3', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-4', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-5', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-6', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-7', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-8', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-9', 1, 0, 'C', true);
    $pdf->Cell(6, 5, 'Bn', 1, 0, 'C', true);
    $pdf->Cell(7, 5, $lang->translation("T-L"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, $lang->translation("T-D"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, $lang->translation("P-C"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, $lang->translation("TPA"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, '%', 1, 0, 'C', true);
    $pdf->Cell(7, 5, $lang->translation("NF"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
   
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['curso', $estu->curso],
        ])->orderBy('apellidos, nombre')->get();
    $crs = 0;
    $ct = $_POST['nota'];
    $trim = 'Trimestre-'.$ct;
    if ($ct == 1){$a=0;$b=0;$t1='tpa1';$t2='por1';$t3='nota1';}
    if ($ct == 2){$a=10;$b=0;$t1='tpa2';$t2='por2';$t3='nota2';}
    if ($ct == 3){$a=20;$b=0;$t1='tpa3';$t2='por3';$t3='nota3';}
    if ($ct == 4){$a=30;$b=0;$t1='tpa4';$t2='por4';$t3='nota4';}
    $t4 = 'tl'.$ct;
    $t5 = 'td'.$ct;
    $t6 = 'pc'.$ct;
    $not = array(10);
    for ( $num = 1; $num < 11; $num += 1)
        {
        $b = $num+$a;
        $not[$num] = 'not'.$b;
        }
    foreach ($cursos as $curso) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(48, 5, $curso->apellidos, 1, 0, 'L');
    $pdf->Cell(37, 5, $curso->nombre, 1, 0, 'L');
    for ( $n = 1; $n < 10; $n += 1)
        {
        $pdf->Cell(7, 5, $curso->$not[$n], 1, 0, 'R');
        }
      $pdf->Cell(6, 5, $curso->$not[10], 1, 0, 'R');

    $pdf->Cell(7, 5, $curso->$t4, 1, 0, 'R');
    $pdf->Cell(7, 5, $curso->$t5, 1, 0, 'R');
    $pdf->Cell(7, 5, $curso->$t6, 1, 0, 'R');
    $pdf->Cell(7, 5, $curso->$t1, 1, 0, 'R');
    $pdf->Cell(7, 5, $curso->$t2, 1, 0, 'R');
    $pdf->Cell(7, 5, $curso->$t3, 1, 1, 'R');
        }
    $pdf->Ln(1);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 1, 'C', true);
    $tema = DB::table('valores')->where([
       ['year', $year],
       ['curso', $estu->curso],
       ['nivel', 'Notas'],
       ['trimestre', $trim],
       ])->first();

    $fec = array(10);
    $tem = array(10);
    $val = array(10);
    for ($num = 1; $num < 11; $num += 1)
        {
        $b = $num;
        $fec[$num] = 'fec'.$b;
        $tem[$num] = 'tema'.$b;
        $val[$num] = 'val'.$b;
        }

    $pdf->SetFont('Arial', '', 8);
    for ($n = 1; $n < 6; $n += 1)
        {
        $pdf->Cell(17, 5, $tema->$fec[$n], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n], 1, 0, 'R');
        $pdf->Cell(17, 5, $tema->$fec[$n+5], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->tem[$n+5], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n+5], 1, 1, 'R');
        }
     }
  }

// Pruebas Cortas
if ($_POST['tri2b'] ==  'Si')
   {

$students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

foreach ($students as $estu) {
$stu = DB::table('padres')->where([
       ['id', $id],
       ['curso', $estu->curso],
       ['year', $year],
       ])->orderBy('curso')->get();
    $te = count($stu);
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Registro de notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Pruebas Cortas"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Crédito"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 5, $teacher->apellidos.' '.$teacher->nombre, 1, 0, 'C');
    $pdf->Cell(19, 5, $estu->curso, 1, 0, 'C');
    $pdf->Cell(50, 5, $estu->descripcion, 1, 0, 'C');
    $pdf->Cell(15, 5, $estu->credito, 1, 0, 'C');
    $pdf->Cell(20, 5, $te, 1, 0, 'C');
    $pdf->Cell(18, 5, $_POST['nota'], 1, 0, 'C');
    $pdf->Cell(21, 5, date("m-d-Y"), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(1);
    $pdf->Cell(48, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(37, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-1', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-2', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-3', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-4', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-5', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-6', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-7', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-8', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-9', 1, 0, 'C', true);
    $pdf->Cell(9, 5, 'N-10', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("TPA"), 1, 0, 'C', true);
    $pdf->Cell(9, 5, '%', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("NF"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
   
    $cursos = DB::table('padres4')->where([
          ['year', $year],
          ['curso', $estu->curso],
        ])->orderBy('apellidos, nombre')->get();
    $crs = 0;
    $ct = $_POST['nota'];
    $trim = 'Trimestre-'.$ct;
    if ($ct == 1){$a=0;$b=0;$t1='tpa1';$t2='por1';$t3='nota1';}
    if ($ct == 2){$a=10;$b=0;$t1='tpa2';$t2='por2';$t3='nota2';}
    if ($ct == 3){$a=20;$b=0;$t1='tpa3';$t2='por3';$t3='nota3';}
    if ($ct == 4){$a=30;$b=0;$t1='tpa4';$t2='por4';$t3='nota4';}
    $t4 = 'tl'.$ct;
    $t5 = 'td'.$ct;
    $t6 = 'pc'.$ct;
    $not = array(10);
    for ( $num = 1; $num < 11; $num += 1)
        {
        $b = $num+$a;
        $not[$num] = 'not'.$b;
        }
    foreach ($cursos as $curso) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(48, 5, $curso->apellidos, 1, 0, 'L');
    $pdf->Cell(37, 5, $curso->nombre, 1, 0, 'L');
    for ( $n = 1; $n < 10; $n += 1)
        {
        $pdf->Cell(7, 5, $curso->$not[$n], 1, 0, 'R');
        }
    $pdf->Cell(9, 5, $curso->$not[10], 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t1, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t2, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t3, 1, 1, 'R');
        }
    $pdf->Ln(1);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 1, 'C', true);
    $tema = DB::table('valores')->where([
       ['year', $year],
       ['curso', $estu->curso],
       ['nivel', 'Pruebas-Cortas'],
       ['trimestre', $trim],
       ])->first();

    $fec = array(10);
    $tem = array(10);
    $val = array(10);
    for ($num = 1; $num < 11; $num += 1)
        {
        $b = $num;
        $fec[$num] = 'fec'.$b;
        $tem[$num] = 'tema'.$b;
        $val[$num] = 'val'.$b;
        }

    $pdf->SetFont('Arial', '', 8);
    for ($n = 1; $n < 6; $n += 1)
        {
        $pdf->Cell(17, 5, $tema->$fec[$n], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n], 1, 0, 'R');
        $pdf->Cell(17, 5, $tema->$fec[$n+5], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n+5], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n+5], 1, 1, 'R');
        }
     }
  }



// Trabajos Diarios
if ($_POST['tri3b'] ==  'Si')
   {

$students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

foreach ($students as $estu) {
$stu = DB::table('padres')->where([
       ['id', $id],
       ['curso', $estu->curso],
       ['year', $year],
       ])->orderBy('curso')->get();
    $te = count($stu);
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Registro de notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Trabajos Diarios"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Crédito"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 5, $teacher->apellidos.' '.$teacher->nombre, 1, 0, 'C');
    $pdf->Cell(19, 5, $estu->curso, 1, 0, 'C');
    $pdf->Cell(50, 5, $estu->descripcion, 1, 0, 'C');
    $pdf->Cell(15, 5, $estu->credito, 1, 0, 'C');
    $pdf->Cell(20, 5, $te, 1, 0, 'C');
    $pdf->Cell(18, 5, $_POST['nota'], 1, 0, 'C');
    $pdf->Cell(21, 5, date("m-d-Y"), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(1);
    $pdf->Cell(48, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(37, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-1', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-2', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-3', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-4', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-5', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-6', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-7', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-8', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-9', 1, 0, 'C', true);
    $pdf->Cell(9, 5, 'N-10', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("TPA"), 1, 0, 'C', true);
    $pdf->Cell(9, 5, '%', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("NF"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
   
    $cursos = DB::table('padres2')->where([
          ['year', $year],
          ['curso', $estu->curso],
        ])->orderBy('apellidos, nombre')->get();
    $crs = 0;
    $ct = $_POST['nota'];
    $trim = 'Trimestre-'.$ct;
    if ($ct == 1){$a=0;$b=0;$t1='tpa1';$t2='por1';$t3='nota1';}
    if ($ct == 2){$a=10;$b=0;$t1='tpa2';$t2='por2';$t3='nota2';}
    if ($ct == 3){$a=20;$b=0;$t1='tpa3';$t2='por3';$t3='nota3';}
    if ($ct == 4){$a=30;$b=0;$t1='tpa4';$t2='por4';$t3='nota4';}
    $t4 = 'tl'.$ct;
    $t5 = 'td'.$ct;
    $t6 = 'pc'.$ct;
    $not = array(10);
    for ( $num = 1; $num < 11; $num += 1)
        {
        $b = $num+$a;
        $not[$num] = 'not'.$b;
        }
    foreach ($cursos as $curso) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(48, 5, $curso->apellidos, 1, 0, 'L');
    $pdf->Cell(37, 5, $curso->nombre, 1, 0, 'L');
    for ( $n = 1; $n < 10; $n += 1)
        {
        $pdf->Cell(7, 5, $curso->$not[$n], 1, 0, 'R');
        }
    $pdf->Cell(9, 5, $curso->$not[10], 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t1, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t2, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t3, 1, 1, 'R');
        }
    $pdf->Ln(1);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 1, 'C', true);
    $tema = DB::table('valores')->where([
       ['year', $year],
       ['curso', $estu->curso],
       ['nivel', 'Trab-Diarios'],
       ['trimestre', $trim],
       ])->first();

    $fec = array(10);
    $tem = array(10);
    $val = array(10);
    for ($num = 1; $num < 11; $num += 1)
        {
        $b = $num;
        $fec[$num] = 'fec'.$b;
        $tem[$num] = 'tema'.$b;
        $val[$num] = 'val'.$b;
        }

    $pdf->SetFont('Arial', '', 8);
    for ($n = 1; $n < 6; $n += 1)
        {
        $pdf->Cell(17, 5, $tema->$fec[$n], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n], 1, 0, 'R');
        $pdf->Cell(17, 5, $tema->$fec[$n+5], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n+5], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n+5], 1, 1, 'R');
        }
     }
  }

// Trabajos Libretas
if ($_POST['tri4b'] ==  'Si')
   {

$students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

foreach ($students as $estu) {
$stu = DB::table('padres')->where([
       ['id', $id],
       ['curso', $estu->curso],
       ['year', $year],
       ])->orderBy('curso')->get();
    $te = count($stu);
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Registro de notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Trabajos Libreta"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Crédito"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 5, $teacher->apellidos.' '.$teacher->nombre, 1, 0, 'C');
    $pdf->Cell(19, 5, $estu->curso, 1, 0, 'C');
    $pdf->Cell(50, 5, $estu->descripcion, 1, 0, 'C');
    $pdf->Cell(15, 5, $estu->credito, 1, 0, 'C');
    $pdf->Cell(20, 5, $te, 1, 0, 'C');
    $pdf->Cell(18, 5, $_POST['nota'], 1, 0, 'C');
    $pdf->Cell(21, 5, date("m-d-Y"), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(1);
    $pdf->Cell(48, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(37, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-1', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-2', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-3', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-4', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-5', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-6', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-7', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-8', 1, 0, 'C', true);
    $pdf->Cell(7, 5, 'N-9', 1, 0, 'C', true);
    $pdf->Cell(9, 5, 'N-10', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("TPA"), 1, 0, 'C', true);
    $pdf->Cell(9, 5, '%', 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("NF"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
   
    $cursos = DB::table('padres3')->where([
          ['year', $year],
          ['curso', $estu->curso],
        ])->orderBy('apellidos, nombre')->get();
    $crs = 0;
    $ct = $_POST['nota'];
    $trim = 'Trimestre-'.$ct;
    if ($ct == 1){$a=0;$b=0;$t1='tpa1';$t2='por1';$t3='nota1';}
    if ($ct == 2){$a=10;$b=0;$t1='tpa2';$t2='por2';$t3='nota2';}
    if ($ct == 3){$a=20;$b=0;$t1='tpa3';$t2='por3';$t3='nota3';}
    if ($ct == 4){$a=30;$b=0;$t1='tpa4';$t2='por4';$t3='nota4';}
    $t4 = 'tl'.$ct;
    $t5 = 'td'.$ct;
    $t6 = 'pc'.$ct;
    $not = array(10);
    for ( $num = 1; $num < 11; $num += 1)
        {
        $b = $num+$a;
        $not[$num] = 'not'.$b;
        }
    foreach ($cursos as $curso) {

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(48, 5, $curso->apellidos, 1, 0, 'L');
    $pdf->Cell(37, 5, $curso->nombre, 1, 0, 'L');
    for ( $n = 1; $n < 10; $n += 1)
        {
        $pdf->Cell(7, 5, $curso->$not[$n], 1, 0, 'R');
        }
    $pdf->Cell(9, 5, $curso->$not[10], 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t1, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t2, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->$t3, 1, 1, 'R');
        }
    $pdf->Ln(1);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Fecha"), 1, 0, 'C', true);
    $pdf->Cell(70, 5, $lang->translation("Tema"), 1, 0, 'C', true);
    $pdf->Cell(11, 5, $lang->translation("Valor"), 1, 1, 'C', true);
    $tema = DB::table('valores')->where([
       ['year', $year],
       ['curso', $estu->curso],
       ['nivel', 'Trab-Libreta'],
       ['trimestre', $trim],
       ])->first();

    $fec = array(10);
    $tem = array(10);
    $val = array(10);
    for ($num = 1; $num < 11; $num += 1)
        {
        $b = $num;
        $fec[$num] = 'fec'.$b;
        $tem[$num] = 'tema'.$b;
        $val[$num] = 'val'.$b;
        }

    $pdf->SetFont('Arial', '', 8);
    for ($n = 1; $n < 6; $n += 1)
        {
        $pdf->Cell(17, 5, $tema->$fec[$n], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n], 1, 0, 'R');
        $pdf->Cell(17, 5, $tema->$fec[$n+5], 1, 0, 'C');
        $pdf->Cell(70, 5, $tema->$tem[$n+5], 1, 0, 'C');
        $pdf->Cell(11, 5, $tema->$val[$n+5], 1, 1, 'R');
        }
     }
  }


// Nota Final
if ($_POST['tri5b'] ==  'Si')
   {

$students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['id', $id],
       ['year', $year],
       ])->orderBy('curso')->get();

foreach ($students as $estu) {
$stu = DB::table('padres')->where([
       ['id', $id],
       ['curso', $estu->curso],
       ['year', $year],
       ])->orderBy('curso')->get();
    $te = count($stu);
    $pdf->AddPage('');
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Registro de notas") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Trabajos Libreta"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(50, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
    $pdf->Cell(19, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(15, 5, $lang->translation("Crédito"), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation("Total Est."), 1, 0, 'C', true);
    $pdf->Cell(18, 5, $lang->translation("Trimestre"), 1, 0, 'C', true);
    $pdf->Cell(21, 5, $lang->translation("Fecha"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(50, 5, $teacher->apellidos.' '.$teacher->nombre, 1, 0, 'C');
    $pdf->Cell(19, 5, $estu->curso, 1, 0, 'C');
    $pdf->Cell(50, 5, $estu->descripcion, 1, 0, 'C');
    $pdf->Cell(15, 5, $estu->credito, 1, 0, 'C');
    $pdf->Cell(20, 5, $te, 1, 0, 'C');
    $pdf->Cell(18, 5, $_POST['nota'], 1, 0, 'C');
    $pdf->Cell(21, 5, date("m-d-Y"), 1, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(1);
    $pdf->Cell(8, 5, '', 1, 0, 'C', true);
    $pdf->Cell(50, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $pdf->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("T-1"), 1, 0, 'C', true);
    $pdf->Cell(8, 5, "C-1", 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("T-2"), 1, 0, 'C', true);
    $pdf->Cell(8, 5, "C-2", 1, 0, 'C', true);
    $pdf->Cell(9, 5, "S-1", 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("T-3"), 1, 0, 'C', true);
    $pdf->Cell(8, 5, "C-3", 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("T-4"), 1, 0, 'C', true);
    $pdf->Cell(8, 5, "C-4", 1, 0, 'C', true);
    $pdf->Cell(9, 5, "S-2", 1, 0, 'C', true);
    $pdf->Cell(9, 5, $lang->translation("NF"), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
   
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['curso', $estu->curso],
        ])->orderBy('apellidos, nombre')->get();
    $c = 0;
    $ct = $_POST['nota'];
    $trim = 'Trimestre-'.$ct;
    if ($ct == 1){$a=0;$b=0;$t1='tpa1';$t2='por1';$t3='nota1';}
    if ($ct == 2){$a=10;$b=0;$t1='tpa2';$t2='por2';$t3='nota2';}
    if ($ct == 3){$a=20;$b=0;$t1='tpa3';$t2='por3';$t3='nota3';}
    if ($ct == 4){$a=30;$b=0;$t1='tpa4';$t2='por4';$t3='nota4';}
    $t4 = 'tl'.$ct;
    $t5 = 'td'.$ct;
    $t6 = 'pc'.$ct;
    $not = array(10);
    for ( $num = 1; $num < 11; $num += 1)
        {
        $b = $num+$a;
        $not[$num] = 'not'.$b;
        }
    foreach ($cursos as $curso) {
         $c = $c + 1;
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(8, 5, $c, 1, 0, 'R');
    $pdf->Cell(50, 5, $curso->apellidos, 1, 0, 'L');
    $pdf->Cell(40, 5, $curso->nombre, 1, 0, 'L');
    $pdf->Cell(9, 5, $curso->nota1, 1, 0, 'R');
    $pdf->Cell(8, 5, $curso->con1, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->nota2, 1, 0, 'R');
    $pdf->Cell(8, 5, $curso->con2, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->sem1, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->nota3, 1, 0, 'R');
    $pdf->Cell(8, 5, $curso->con3, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->nota4, 1, 0, 'R');
    $pdf->Cell(8, 5, $curso->con4, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->sem2, 1, 0, 'R');
    $pdf->Cell(9, 5, $curso->final, 1, 1, 'R');
        }
    $pdf->Ln(1);
     }
  }

$pdf->Output();