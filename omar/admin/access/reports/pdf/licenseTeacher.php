<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
    ['Lista de licensias maestros', 'Teacher licenses List'],
    ['Profesor', 'Teacher'],
    ['Licencias', 'Licenses'],
    ['Todas las Licencias', 'All licenses'],
    ['Licencias Permanentes', 'Permanent licenses'],
    ['Licencias Expiradas', 'Expired licenses'],
    ['Fecha de Expiraci�n', 'Expiration date'],
]);
$prof = $_POST['prof'];
$school = new School();
$year = $school->year();
$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de licensias maestros") . " $year", true);
$pdf->Fill();

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 5, $lang->translation("Lista de licensias maestros") . " $year", 0, 1, 'C');
$pdf->Ln(5);
if ($prof=='T'){$pdf->Cell(0, 5, $lang->translation("Todas las Licencias"), 0, 1, 'C');}
if ($prof=='P'){$pdf->Cell(0, 5, $lang->translation("Licencias Permanentes"), 0, 1, 'C');}
if ($prof=='E'){$pdf->Cell(0, 5, $lang->translation("Licencias Expiradas"), 0, 1, 'C');}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 5, '', 1, 0, 'C', true);
$pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
$pdf->Cell(65, 5, $lang->translation("Profesor"), 1, 0, 'C', true);
$pdf->Cell(70, 5, $lang->translation("Licencias"), 1, 0, 'C', true);
$pdf->Cell(30, 5, $lang->translation("Fecha de Expiraci�n"), 1, 1, 'C', true);
$pdf->ln(2);
$pdf->SetFont('Arial', '', 10);


$teachers = DB::table('profesor')->where([
    ['baja', ''],
    ['docente', 'Docente']
])->orderBy('apellidos')->get();

$count=0;

foreach ($teachers as $teacher) {
    if ($teacher->lic1 != '' or $teacher->lic2 != '' or $teacher->lic3 != '' or $teacher->lic4 != '')
       {
       if ($prof== 'T')
          {
          if ($teacher->lic1 != '')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic1, 0, 0, 'L');
             $pdf->Cell(30,5,($teacher->fex1 == '0000-00-00')?'':$teacher->fex1,0,1,'C');
             $count++;
             }
          if ($teacher->lic2 != '')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic2, 0, 0, 'L');
             $pdf->Cell(30,5,($teacher->fex2 == '0000-00-00')?'':$teacher->fex2,0,1,'C');
             $count++;
             }
          if ($teacher->lic3 != '')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic3, 0, 0, 'L');
             $pdf->Cell(30,5,($teacher->fex3 == '0000-00-00')?'':$teacher->fex3,0,1,'C');
             $count++;
             }
          if ($teacher->lic4 != '')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic4, 0, 0, 'L');
             $pdf->Cell(30,5,($teacher->fex4 == '0000-00-00')?'':$teacher->fex4,0,1,'C');
             $count++;
             }
          }

       if ($prof== 'E')
          {
          if ($teacher->fex1 < date('Y-m-d') and $teacher->fex1 != '0000-00-00')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic1, 0, 0, 'L');
             $pdf->Cell(30, 5, $teacher->fex1, 0, 1, 'C');
             $count++;
             }
          if ($teacher->fex2 < date('Y-m-d') and $teacher->fex2 != '0000-00-00')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic2, 0, 0, 'L');
             $pdf->Cell(30, 5, $teacher->fex2, 0, 1, 'C');
             $count++;
             }
          if ($teacher->fex3 < date('Y-m-d') and $teacher->fex3 != '0000-00-00')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic3, 0, 0, 'L');
             $pdf->Cell(30, 5, $teacher->fex3, 0, 1, 'C');
             $count++;
             }
          if ($teacher->fex4 < date('Y-m-d') and $teacher->fex4 != '0000-00-00')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic4, 0, 0, 'L');
             $pdf->Cell(30, 5, $teacher->fex4, 0, 1, 'C');
             $count++;
             }
          }
       if ($prof== 'P')
          {
          if ($teacher->lp1 =='Si')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic1, 0, 1, 'L');
             $count++;
             }
          if ($teacher->lp2 =='Si')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic2, 0, 1, 'L');
             $count++;
             }
          if ($teacher->lp3 =='Si')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic3, 0, 1, 'L');
             $count++;
             }
          if ($teacher->lp4 =='Si')
             {
             $pdf->Cell(10, 5, $count + 1, 0, 0, 'C');
             $pdf->Cell(15, 5, $teacher->id, 0, 0, 'C');
             $pdf->Cell(65, 5, utf8_decode($teacher->apellidos).' '.utf8_decode($teacher->nombre));
             $pdf->Cell(70, 5, $teacher->lic4, 0, 1, 'L');
             $count++;
             }
          }
       }
}


$pdf->Output();
