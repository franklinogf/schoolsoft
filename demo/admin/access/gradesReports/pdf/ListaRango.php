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
    ['Lista de rango', 'Rank List'],
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
    ['T-1', 'Q-1'],
    ['T-2', 'Q-2'],
    ['T-3', 'Q-3'],
    ['T-4', 'Q-4'],
    ['Sem-1', 'Sem-1'],
    ['Sem-2', 'Sem-2'],
    ['Final', 'Final'],

]);

class nPDF extends PDF
{
    function header()
    {
        global $lang;
        global $year;
        global $grupo;
        global $cur;
        parent::header();
        $ct = $_POST['nota'];
        if ($ct=='nota1'){$tt=$lang->translation("T-1");}
        if ($ct=='nota2'){$tt=$lang->translation("T-2");}
        if ($ct=='nota3'){$tt=$lang->translation("T-3");}
        if ($ct=='nota4'){$tt=$lang->translation("T-4");}
        if ($ct=='sem1'){$tt=$lang->translation("Sem-1");}
        if ($ct=='sem2'){$tt=$lang->translation("Sem-2");}
        if ($ct=='final'){$tt=$lang->translation("Final");}
        $this->SetFont('Arial', 'B', 12);

        $this->Cell(0, 5, $lang->translation("Lista de rango")." / $year", 0, 1, 'C');
        $this->Ln(5);
        $this->Cell(0, 5, $lang->translation("Curso").' '.$cur.' / '.$lang->translation("Promedio").' '.$tt, 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', '', 12);
    $this->Fill();
    $this->Cell(10, 5, '', 1, 0, 'C', true);
    $this->Cell(52, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
    $this->Cell(40, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
    $this->Cell(12, 5, $lang->translation("T-1"), 1, 0, 'C', true);
    $this->Cell(12, 5, $lang->translation("T-2"), 1, 0, 'C', true);
    $this->Cell(15, 5, $lang->translation("Sem-1"), 1, 0, 'C', true);
    $this->Cell(12, 5, $lang->translation("T-3"), 1, 0, 'C', true);
    $this->Cell(12, 5, $lang->translation("T-4"), 1, 0, 'C', true);
    $this->Cell(15, 5, $lang->translation("Sem-2"), 1, 0, 'C', true);
    $this->Cell(13, 5, $lang->translation("Final"), 1, 1, 'C', true);
    $this->SetFont('Arial', '', 10);


    }
}


$school = new School(Session::id());

$cur = $_POST['curso'];
$year = $school->year();
$pdf = new nPDF();
//$pdf->AddPage('');
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
foreach ($students as $estu) {
    $ct = $_POST['nota'] . '+0';
    $cur = $estu->curso;
    $cursos = DB::table('padres')->where([
          ['year', $year],
          ['curso', $estu->curso],
          ['Baja', ''],
        ])->orderBy($ct,'desc')->get();
    $pdf->AddPage('');
    $n = 0;
    foreach ($cursos as $curso) 
            {
            $n = $n +1;
            $pdf->Cell(10, 5, $n, 1, 0, 'R');
            $pdf->Cell(52, 5, $curso->apellidos, 1, 0, 'L');
            $pdf->Cell(40, 5, $curso->nombre, 1, 0, 'L');
            $pdf->Cell(12, 5, $curso->nota1, 1, 0, 'R');
            $pdf->Cell(12, 5, $curso->nota2, 1, 0, 'R');
            $pdf->Cell(15, 5, $curso->sem1, 1, 0, 'R');
            $pdf->Cell(12, 5, $curso->nota3, 1, 0, 'R');
            $pdf->Cell(12, 5, $curso->nota4, 1, 0, 'R');
            $pdf->Cell(15, 5, $curso->sem2, 1, 0, 'R');
            $pdf->Cell(13, 5, $curso->final, 1, 1, 'R');
            }
     }

    $pdf->SetFillColor(89,171,227);


$pdf->Output();