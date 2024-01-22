<?php
require_once '../../../../app.php';
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;

Session::is_logged();

$lang = new Lang([
    ['Distribucción de notas', 'Note distribution'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ["Año escolar:", "School year:"],
    ["Descripción", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Crédito', 'Credit'],
    ['Trimestre', 'Quarter'],
    ['Nota A', 'Note A'],
    ['Nota B', 'Note B'],
    ['Nota C', 'Note C'],
    ['Nota D', 'Note D'],
    ['Nota F', 'Note F'],
    ['Otros', 'Other'],
    ['Total', 'Total'],
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
    ['Sem-1', 'Sem-1'],
    ['Final', 'Final'],

]);

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        parent::header();
        $ct = $_POST['nota'];
        if ($ct=='nota1'){$tt=$lang->translation("T-1");}
        if ($ct=='nota2'){$tt=$lang->translation("T-2");}
        if ($ct=='nota3'){$tt=$lang->translation("T-3");}
        if ($ct=='nota4'){$tt=$lang->translation("T-4");}
        $this->SetFont('Arial', 'B', 12);

        $this->Cell(0, 5, $lang->translation("Distribucción de notas").' / '.$tt." / $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
    $this->Fill();
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Curso"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota A"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota B"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota C"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota D"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota F"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Otros"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Total"), 1, 1, 'C', true);
    $this->SetFont('Arial', '', 11);


    }
}


$school = new School(Session::id());

$year = $school->year();
$pdf = new nPDF();
$pdf->AddPage('');
$cur = $_POST['curso'];
if (empty($cur))
   {
   $students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['year', $year],
       ])->orderBy('curso')->get();
   }
else
   {
   $students = DB::table('padres')->select("distinct curso, descripcion, credito")->where([
       ['year', $year],
       ['curso', 'LIKE', '%'.$cur.'%']
       ])->orderBy('curso')->get();
   }
$n = 0;
$x = 0;
    $a1 = 0;
    $b1 = 0;
    $c1 = 0;
    $d1 = 0;
    $f1 = 0;
    $o1 = 0;
    $t1 = 0;
foreach ($students as $estu) {
    $ct = $_POST['nota'];
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['curso', $estu->curso],
          ['Baja', ''],
        ])->orderBy('apellidos, nombre')->get();
    $a = 0;
    $b = 0;
    $c = 0;
    $d = 0;
    $f = 0;
    $o = 0;
    $t = 0;
    foreach ($cursos as $curso) 
            {
            if ($curso->$ct > 89){$a=$a+1;$t=$t+1;$a1=$a1+1;$t1=$t1+1;}
            else
               if ($curso->$ct > 79){$b=$b+1;$t=$t+1;$b1=$b1+1;$t1=$t1+1;}
               else
                  if ($curso->$ct > 69){$c=$c+1;$t=$t+1;$c1=$c1+1;$t1=$t1+1;}
                  else
                     if ($curso->$ct > 59){$d=$d+1;$t=$t+1;$d1=$d1+1;$t1=$t1+1;}
                     else
                        if ($curso->$ct > 0){$f=$f+1;$t=$t+1;$f1=$f1+1;$t1=$t1+1;}
                        else
                           {$o=$o+1;$t=$t+1;$o1=$o1+1;$t1=$t1+1;}
            }
    $n = $n +1;
    $pdf->Cell(10, 5, $n, 1, 0, 'R');
    $pdf->Cell(20, 5, $estu->curso, 1, 0, 'L');
    $pdf->Cell(20, 5, $a, 1, 0, 'R');
    $pdf->Cell(20, 5, $b, 1, 0, 'R');
    $pdf->Cell(20, 5, $c, 1, 0, 'R');
    $pdf->Cell(20, 5, $d, 1, 0, 'R');
    $pdf->Cell(20, 5, $f, 1, 0, 'R');
    $pdf->Cell(20, 5, $o, 1, 0, 'R');
    $pdf->Cell(20, 5, $t, 1, 1, 'R');
    $x = $x +1;
    
    if ($x==42){$pdf->AddPage('');$x = 0;}
     }

    $pdf->SetFillColor(89,171,227);
    $pdf->Cell(30, 5, 'Total', 1, 0, 'L', true);
    $pdf->Cell(20, 5, $a1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $b1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $c1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $d1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $f1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $o1, 1, 0, 'R', true);
    $pdf->Cell(20, 5, $t1, 1, 1, 'R', true);


$pdf->Output();