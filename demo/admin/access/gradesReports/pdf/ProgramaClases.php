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
    ['Programa de clases', 'Class Program'],
    ["Maestro(a)", "Teacher"],
    ["Grado:", "Grade:"],
    ["Curso", "Course"],
    ["Descripción", "Description"],
    ['Entrada', 'In'],
    ['Salida', 'Out'],
    ['Días', 'Days'],
    ['PROMEDIO:', 'AVERAGE:'],
    ['Nombre:', 'Name:'],
    ['Total de estudiantes', 'Total students'],
    ['Fecha:', 'Date:'],
    ['Documentos sin entregar', 'Undelivered documents'],
    ['Masculinos', 'Males'],
    ['Femeninas', 'Females'],
]);

class nPDF extends PDF
{
    function header()
    {
    }

}

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


//$pdf = new NPDF();
//$pdf->useFooter(false);
$school = new School(Session::id());
$teacherClass = new Teacher();
$studentClass = new Student();

//$year = $school->year();
$year = $school->info('year2');

// $allGrades = $school->allGrades();
$pdf = new nPDF();
$pdf->useFooter(false);

$pdf->SetTitle($lang->translation("Programa de clases") . " $year", true);
$pdf->Fill();

$grade = $_POST['grade'];
$p = $_POST['pagina'];

$teacher = $teacherClass->findByGrade($grade);
$students = $studentClass->findByGrade($grade);
$pdf->AddPage('');
$a=0;
foreach ($students as $estu) {
    if ($a==$p)
       {
       $pdf->AddPage('');
       $a=0;
       }
    $pdf->useFooter(false);
    $pdf->SetFont('Arial', 'B', 15);
//    $pdf->Ln(5);
    $pdf->Cell(0, 5, $lang->translation("Programa de clases") . " $year", 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
//    $pdf->splitCells($lang->translation("A&#65533;o escolar:") . " $year", $lang->translation("Fecha:") . " ".date("m-d-Y"));

    $materias = [];
    $cursos = [];
    $estudiantes = [];
    $pdf->Cell(120, 5, $lang->translation("Nombre:"). " $estu->nombre $estu->apellidos", 1, 0, 'L', true);
    $pdf->Cell(40, 5, "S.S. XXX-XX-XXXX", 1, 0, 'L', true);
    $pdf->Cell(30, 5, $lang->translation("grado:"). " $estu->grado", 1, 1, 'L', true);
    $pdf->Cell(20, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $pdf->Cell(60, 5, $lang->translation("Descripción"), 1, 0, 'C', true);
    $pdf->Cell(60, 5, $lang->translation("Maestro(a)"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Entrada"), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation("Salida"), 1, 0, 'C', true);
    $pdf->Cell(16, 5, $lang->translation("Días"), 1, 1, 'C', true);
//    $pdf->Cell(13, 5, "CRS", 1, 1, 'C', true);
    
    $pdf->SetFillColor(89, 171, 227);
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['ss', $estu->ss],
          ['grado', $grade],
          ['curso', '!=', ''],
          ['curso', 'NOT LIKE', '%AA-%']
        ])->orderBy('orden')->get();
    $crs = 0;
    foreach ($cursos as $curso) {

    $horas = DB::table('cursos')->where([
       ['year', $year],
       ['curso', $curso->curso],
       ])->orderBy('curso')->first();

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(20, 5, $curso->curso, 1, 0, 'L');
    $pdf->Cell(60, 5, $curso->descripcion, 1, 0, 'L');
    $pdf->Cell(60, 5, $curso->profesor, 1, 0, 'L');
    $pdf->Cell(17, 5, $horas->entrada, 1, 0, 'R');
    $pdf->Cell(17, 5, $horas->salida, 1, 0, 'R');
    $pdf->Cell(16, 5, $horas->dias, 1, 1, 'R');

        }
       if ($p==2){$pdf->Ln(25);}
       if ($p==3){$pdf->Ln(10);}

   $a=$a+1;
   }

$pdf->Output();