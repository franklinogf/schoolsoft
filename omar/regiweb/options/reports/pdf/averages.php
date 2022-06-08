<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Lang;
use Classes\Util;

global $_class;
global $_trimester;
global $_report;
$lang = new Lang([
    ["GRADO", "GRADE"],
    ["Apellidos","Surnames"],
    ["Nombre","Name"],
    ["Lista de promedios","List of averages"]
]);

Session::is_logged();
$teacher = new Teacher(Session::id());
$year = $teacher->info('year');
$grade = $teacher->grado;
$students0 = new Student();
$students0 = $students0->findByGrade($grade);

$students2 = new Student();
$students2 = $students2->findByGrade($grade);

foreach ($students2 as $student2) {
    //$data = DB::table('padres')->where([
    //        ['ss', $student2->ss],
    //        ['year', $year],
    //        ['verano', '']
    //])->orderBy('apellidos')->first();
    $students = new Student();
    $students = $students->findByCurs2($student2->ss);

    //      echo $_class.'787'.$student2->ss;
    $s1a = 0;
    $s1b = 0;
    $s2a = 0;
    $s2b = 0;
    foreach ($students as $nts5) {
        if ($nts5->sem1 > 0) {
            $s1a = $s1a + $nts5->sem1;
            $s1b = $s1b + 1;
        }
        if ($nts5->sem2 > 0) {
            $s2a = $s2a + $nts5->sem2;
            $s2b = $s2b + 1;
        }
        //              echo '<br>'.$nts5->sem1.'<br>';
        //              echo '777 1 '.$nts5->curso.'/'.$nts5->ss;
    }

    $sem1 = $s1b !== 0 ? round($s1a / $s1b, 0) : 0;
    $sem2 = $s2b !== 0 ? round($s2a / $s2b, 0) : 0;
    $div = 0;
    $div += $sem1 !== 0 ? 0 : 1;
    $div += $sem2 !== 0 ? 0 : 1;
    $semFinalNote = $div !== 0 ? round((+$sem1 + +$sem2) / $div) : '';
    $updateSem = [
        'se1' => $sem1,
        'se2' => $sem2,
        'fin' => $semFinalNote
    ];
    DB::table('year')
        ->where([
            ['ss', $student2->ss],
            ['year', $teacher->info('year')]
        ])->update($updateSem);
    //                var_dump($updateSem);


}


$pdf = new PDF();
$pdf->SetTitle($lang->translation("Lista de promedios"));
$pdf->addPage();
$pdf->Fill();
$pdf->SetFont('Times', 'B', 16);
$pdf->Cell(0, 5, $lang->translation("Lista de promedios"), 0, 1, 'C');
$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(0, 5, $lang->translation("GRADO")." $teacher->grado", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(10, 5, ' ', 1, 0, 'C', true);
$pdf->Cell(60, 5, $lang->translation("Apellidos"), 1, 0, 'C', true);
$pdf->Cell(50, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
$pdf->Cell(22, 5, 'S-1', 1, 0, 'C', true);
$pdf->Cell(22, 5, 'S-2', 1, 0, 'C', true);
$pdf->Cell(20, 5, 'FINAL', 1, 1, 'C', true);
$count = 1;
foreach ($students0 as $student) {
    $father = DB::table('padres')->Where([
        ['ss', $student->ss],
        ['year', $year]
    ])->first();
    $pdf->Cell(10, 5, $count, 1, 0, 'C');
    $pdf->Cell(60, 5, $student->apellidos, 1);
    $pdf->Cell(50, 5, $student->nombre, 1);
    $pdf->Cell(22, 5, $student->se1, 1, 0, 'C');
    $pdf->Cell(22, 5, $student->se2, 1, 0, 'C');
    $pdf->Cell(20, 5, $student->fin, 1, 1, 'C');
    $count++;
}
$pdf->Output();
