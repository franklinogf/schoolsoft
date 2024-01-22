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
    ['Informe de distribución de promedio por grado', 'GPA Distribution Report by Grade'],
    ["Profesor", "Teacher:"],
    ["Grado:", "Grade:"],
    ["A&#65533;o escolar:", "School year:"],
    ["Descripci&#65533;n", "Description"],
    ['Apellidos', 'Lasname'],
    ['Nombre', 'Name'],
    ['Grado', 'Grade'],
    ['Cr&#65533;dito', 'Credit'],
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

        $this->Cell(0, 5, $lang->translation("Informe de distribución de promedio por grado").' / '.$tt." / $year", 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
    $this->Fill();
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota A"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota B"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota C"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota D"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Nota F"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Otros"), 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Total"), 1, 1, 'C', true);
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(20, 5, $lang->translation("Grado"), 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 0, 'C', true);
    $this->Cell(10, 5, "M", 1, 0, 'C', true);
    $this->Cell(10, 5, "F", 1, 1, 'C', true);



    $this->SetFont('Arial', '', 11);


    }
}


$school = new School(Session::id());

//$year = $school->year();
$year = $_POST['year'];
$ct = $_POST['nota'];
$suma = $_POST['divicion'];
$allGrades = $school->allGrades();

foreach ($allGrades as $grade) {
        $studens = DB::table('year')->where([
          ['year', $year],
          ['grado', $grade],
        ])->orderBy('apellidos')->get();
        foreach ($studens as $studen) 
               {
               $cursos = DB::table('padres')->where([
                 ['ss', $studen->ss],
                 ['year', $year],
                 ['grado', $grade],
                 ['curso', '!=', ''],
                 ['curso', 'NOT LIKE', '%AA-%']
               ])->orderBy('orden')->get();
               $a=0;$t=0;
               foreach ($cursos as $curso) 
                       {
                       if ($suma == 'N' and $curso->$ct > 0)
                          {
                          $a=$a+$curso->$ct;$t=$t+1;
                          }
                       if ($suma == 'C' and $curso->$ct > 0 and $curso->credito > 0)
                          {
                          $a=$a+round($curso->$ct*$curso->credito,0);$t=$t+$curso->credito;
                          }
                       }
               if ($t > 0)
                  {
                  $b=round($a/$t,0);
                  $updates = ['fin' => $b,];
                  DB::table('year')->where('mt', $studen->mt)->update($updates);
                  }
               }
               
        }


$pdf = new nPDF();
$pdf->AddPage('');
$cur = $_POST['curso'];
$n = 0;
$x = 0;
    $am1 = 0;
    $bm1 = 0;
    $cm1 = 0;
    $dm1 = 0;
    $fm1 = 0;
    $om1 = 0;
    $tm1 = 0;
    $af1 = 0;
    $bf1 = 0;
    $cf1 = 0;
    $df1 = 0;
    $ff1 = 0;
    $of1 = 0;
    $tf1 = 0;
$allGrades = $school->allGrades();

foreach ($allGrades as $grade) 
        {
    $students = DB::table('year')->where([
          ['year', $year],
          ['grado', $grade],
          ['codigobaja', ''],
        ])->orderBy('apellidos, nombre')->get();

    $am = 0;
    $bm = 0;
    $cm = 0;
    $dm = 0;
    $fm = 0;
    $om = 0;
    $tm = 0;
    $af = 0;
    $bf = 0;
    $cf = 0;
    $df = 0;
    $ff = 0;
    $of = 0;
    $tf = 0;
      foreach ($students as $estu) 
              {

            if ($estu->fin > 89)
               {
               if ($estu->genero == 'M' or $estu->genero == '2'){$am=$am+1;$tm=$tm+1;$am1=$am1+1;$tm1=$tm1+1;}
               if ($estu->genero == 'F' or $estu->genero == '1'){$af=$af+1;$tf=$tf+1;$af1=$af1+1;$tf1=$tf1+1;}
               }
            else
               if ($estu->fin > 79)
                  {
                  if ($estu->genero == 'M' or $estu->genero == '2'){$bm=$bm+1;$tm=$tm+1;$bm1=$bm1+1;$tm1=$tm1+1;}
                  if ($estu->genero == 'F' or $estu->genero == '1'){$bf=$bf+1;$tf=$tf+1;$bf1=$bf1+1;$tf1=$tf1+1;}
                  }
               else
                  if ($estu->fin > 69)
                     {
                     if ($estu->genero == 'M' or $estu->genero == '2'){$cm=$cm+1;$tm=$tm+1;$cm1=$cm1+1;$tm1=$tm1+1;}
                     if ($estu->genero == 'F' or $estu->genero == '1'){$cf=$cf+1;$tf=$tf+1;$cf1=$cf1+1;$tf1=$tf1+1;}
                     }
                  else
                     if ($estu->fin > 59)
                        {
                        if ($estu->genero == 'M' or $estu->genero == '2'){$dm=$dm+1;$tm=$tm+1;$dm1=$dm1+1;$tm1=$tm1+1;}
                        if ($estu->genero == 'F' or $estu->genero == '1'){$df=$df+1;$tf=$tf+1;$df1=$df1+1;$tf1=$tf1+1;}
                        }
                     else
                        if ($estu->fin > 0)
                           {
                           if ($estu->genero == 'M' or $estu->genero == '2'){$fm=$fm+1;$tm=$tm+1;$fm1=$fm1+1;$tm1=$tm1+1;}
                           if ($estu->genero == 'F' or $estu->genero == '1'){$ff=$ff+1;$tf=$tf+1;$ff1=$ff1+1;$tf1=$tf1+1;}
                           }
                        else
                           {
                           if ($estu->genero == 'M' or $estu->genero == '2'){$om=$om+1;$tm=$tm+1;$om1=$om1+1;$tm1=$tm1+1;}
                           if ($estu->genero == 'F' or $estu->genero == '1'){$of=$of+1;$tf=$tf+1;$of1=$of1+1;$tf1=$tf1+1;}
                           }
            }
    $n = $n +1;
    $pdf->Cell(10, 5, $n, 1, 0, 'R');
    $pdf->Cell(20, 5, $grade, 1, 0, 'L');
    $pdf->Cell(10, 5, $am, 1, 0, 'R');
    $pdf->Cell(10, 5, $af, 1, 0, 'R');
    $pdf->Cell(10, 5, $bm, 1, 0, 'R');
    $pdf->Cell(10, 5, $bf, 1, 0, 'R');
    $pdf->Cell(10, 5, $cm, 1, 0, 'R');
    $pdf->Cell(10, 5, $cf, 1, 0, 'R');
    $pdf->Cell(10, 5, $dm, 1, 0, 'R');
    $pdf->Cell(10, 5, $df, 1, 0, 'R');
    $pdf->Cell(10, 5, $fm, 1, 0, 'R');
    $pdf->Cell(10, 5, $ff, 1, 0, 'R');
    $pdf->Cell(10, 5, $om, 1, 0, 'R');
    $pdf->Cell(10, 5, $of, 1, 0, 'R');
    $pdf->Cell(10, 5, $tm, 1, 0, 'R');
    $pdf->Cell(10, 5, $tf, 1, 1, 'R');
    $x = $x +1;
    
      }

    $pdf->SetFillColor(89,171,227);
    $pdf->Cell(30, 5, 'Total', 1, 0, 'L', true);
    $pdf->Cell(10, 5, $am1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $af1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $bm1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $bf1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $cm1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $cf1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $dm1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $df1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $fm1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $ff1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $om1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $of1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $tm1, 1, 0, 'R', true);
    $pdf->Cell(10, 5, $tf1, 1, 1, 'R', true);


$pdf->Output();