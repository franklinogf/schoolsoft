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
    ['Clasificación de notas', 'Note classification'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Curso', 'Course'],
    ['Trimestre', 'Quarter'],
    ['Promedio', 'Average'],
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
    ['Lun', 'Mon'],
    ['Mar', 'Tue'],
    ['Mie', 'Wed'],
    ['Jue', 'Thu'],
    ['Vie', 'Fri'],
    ['A', 'A'],
    ['T', 'L'],

]);

$pdf = new PDF();

$school = new School(Session::id());
$grado = $_POST['grade'];
$nota = $_POST['nota'];
$divicion= $_POST['divicion'];
list($nota,$tt) = explode("-",$_POST['nota']);

$cl = $_POST['cl'];
$grade = $_POST['grade'];

$year = $school->info('year2');
//$pdf = new nPDF();
$pdf = new PDF();

//llGrades = $school->allGrades();

$students = DB::table('year')->where([
           ['year', $year],
           ['codigobaja', 0],
           ['grado', $grade],
           ])->orderBy('Apellidos','desc')->get();
$t=0;
foreach ($students as $student) 
        {
        $a1=0;$a2=0;$a3=0;$a4=0;
        $b1=0;$b2=0;$b3=0;$b4=0;
        $c1=0;$c2=0;$c3=0;$c4=0;
        $d1=0;$d2=0;$d3=0;$d4=0;
        $f1=0;$f2=0;$f3=0;$f4=0;
        $tf=0;$tc=0;
        $tgf=0;$tgc=0;
        $notas = DB::table('padres')->where([
              ['year', $year],
              ['ss', $student->ss],
              ['grado', $grade],
              ])->orderBy('Apellidos','desc')->get();

        foreach ($notas as $nota) 
                {
                $tf=0;$tc=0;
                if ($nota->nota1>89){$a1=$a1+1;$tf=$tf+$nota->nota1;$tc=$tc+1;}
                else
                   if ($nota->nota1>79){$b1=$b1+1;$tf=$tf+$nota->nota1;$tc=$tc+1;}
                   else
                      if ($nota->nota1>69){$c1=$c1+1;$tf=$tf+$nota->nota1;$tc=$tc+1;}
                      else
                         if ($nota->nota1>59){$d1=$d1+1;$tf=$tf+$nota->nota1;$tc=$tc+1;}
                         else
                            if ($nota->nota1>0){$f1=$f1+1;$tf=$tf+$nota->nota1;$tc=$tc+1;}

                if ($nota->nota2>89){$a2=$a2+1;$tf=$tf+$nota->nota2;$tc=$tc+1;}
                else
                   if ($nota->nota2>79){$b2=$b2+1;$tf=$tf+$nota->nota2;$tc=$tc+1;}
                   else
                      if ($nota->nota2>69){$c2=$c2+1;$tf=$tf+$nota->nota2;$tc=$tc+1;}
                      else
                         if ($nota->nota2>59){$d2=$d2+1;$tf=$tf+$nota->nota2;$tc=$tc+1;}
                         else
                            if ($nota->nota2>0){$f2=$f2+1;$tf=$tf+$nota->nota2;$tc=$tc+1;}


                if ($nota->nota3>89){$a3=$a3+1;$tf=$tf+$nota->nota3;$tc=$tc+1;}
                else
                   if ($nota->nota3>79){$b3=$b3+1;$tf=$tf+$nota->nota3;$tc=$tc+1;}
                   else
                      if ($nota->nota3>69){$c3=$c3+1;$tf=$tf+$nota->nota3;$tc=$tc+1;}
                      else
                         if ($nota->nota3>59){$d3=$d3+1;$tf=$tf+$nota->nota3;$tc=$tc+1;}
                         else
                            if ($nota->nota3>0){$f3=$f3+1;$tf=$tf+$nota->nota3;$tc=$tc+1;}

                if ($nota->nota4>89){$a4=$a4+1;$tf=$tf+$nota->nota4;$tc=$tc+1;}
                else
                   if ($nota->nota4>79){$b4=$b4+1;$tf=$tf+$nota->nota4;$tc=$tc+1;}
                   else
                      if ($nota->nota4>69){$c4=$c4+1;$tf=$tf+$nota->nota4;$tc=$tc+1;}
                      else
                         if ($nota->nota4>59){$d4=$d4+1;$tf=$tf+$nota->nota4;$tc=$tc+1;}
                         else
                            if ($nota->nota4>0){$f4=$f4+1;$tf=$tf+$nota->nota4;$tc=$tc+1;}
                
                }

               if ($tc > 0)
                  {
                  $tgf=round($tf/$tc,0);
                  $cl=$a1.'-'.$a2.'-'.$a3.'-'.$a4.'-'.$b1.'-'.$b2.'-'.$b3.'-'.$b4.'-'.$c1.'-'.$c2.'-'.$c3.'-'.$c4;
                  $updates = ['fin' => $tgf, 'cnf' => $cl,];
                  DB::table('year')->where('mt', $student->mt)->update($updates);
                  }

        
        }



//foreach ($allGrades as $grade) 
//        {
        $pdf->AddPage('L');
        $pdf->Cell(0, 5, $lang->translation("Clasificación de notas").' / '.$grade." / $tt / $year", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Fill();
        $pdf->Cell(7, 5, '', 1, 0, 'C', true);
        $pdf->Cell(55, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
        $pdf->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
        $pdf->Cell(8, 5, "A-1", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "A-2", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "A-3", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "A-4", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "B-1", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "B-2", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "B-3", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "B-4", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "C-1", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "C-2", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "C-3", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "C-4", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "D-1", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "D-2", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "D-3", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "D-4", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "F-1", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "F-2", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "F-3", 1, 0, 'C', true);
        $pdf->Cell(8, 5, "F-4", 1, 1, 'C', true);



        $n = 0;
        $materias = [];
        $curs = [];
        $estudiantes = [];
        $c = 0;
//**********************


         $students = DB::table('year')->where([
                  ['year', $year],
                  ['codigobaja', 0],
                  ['grado', $grade],
                  ])->orderBy('Apellidos','desc')->get();
        $t=0;
        foreach ($students as $student) 
                {
                list($a1, $a2, $a3, $a4, $b1, $b2, $b3, $b4, $c1, $c2, $c3, $c4) = explode("-",$student->cnf);
                $t=$t+1;
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(7, 5, $t, 1, 0, 'R');
                $pdf->Cell(55, 5, $student->apellidos, 1, 0, 'L');
                $pdf->Cell(45, 5, $student->nombre, 1, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $a=0;$b=0;
                $pdf->Cell(8, 5, $a1, 1, 0, 'R');
                $pdf->Cell(8, 5, $a2, 1, 0, 'R');
                $pdf->Cell(8, 5, $a3, 1, 0, 'R');
                $pdf->Cell(8, 5, $a4, 1, 0, 'R');
                $pdf->Cell(8, 5, $b1, 1, 0, 'R');
                $pdf->Cell(8, 5, $b2, 1, 0, 'R');
                $pdf->Cell(8, 5, $b3, 1, 0, 'R');
                $pdf->Cell(8, 5, $b4, 1, 0, 'R');
                $pdf->Cell(8, 5, $c1, 1, 0, 'R');
                $pdf->Cell(8, 5, $c2, 1, 0, 'R');
                $pdf->Cell(8, 5, $c3, 1, 0, 'R');
                $pdf->Cell(8, 5, $c4, 1, 0, 'R');
                $pdf->Cell(8, 5, $d1, 1, 0, 'R');
                $pdf->Cell(8, 5, $d2, 1, 0, 'R');
                $pdf->Cell(8, 5, $d3, 1, 0, 'R');
                $pdf->Cell(8, 5, $d4, 1, 0, 'R');
                $pdf->Cell(8, 5, $f1, 1, 0, 'R');
                $pdf->Cell(8, 5, $f2, 1, 0, 'R');
                $pdf->Cell(8, 5, $f3, 1, 0, 'R');
                $pdf->Cell(8, 5, $f4, 1, 1, 'R');
                }



//**********************
//         }





$pdf->Output();