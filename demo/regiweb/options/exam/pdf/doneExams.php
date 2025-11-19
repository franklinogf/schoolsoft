<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Server;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Exam;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Lang;

Server::is_post();
Session::is_logged();

$teacher = new Teacher(Session::id());
$examId = $_POST['printExamId'];
$exam = new Exam($examId);

$lang = new Lang([
	['Informe del examen','Exam report'],
	['Estudiante','Student'],
	['Puntos','Points'],
	['Porcentaje','Percentage'],
	['Fecha','Date'],
]);

function decode($text)
{
	return iconv('UTF-8', 'windows-1252', $text);
}
$pdf = new PDF();
$pdf->SetTitle( $lang->translation("Informe del examen")." - " . decode($exam->titulo));
$pdf->useFooter(false);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 7, decode($exam->titulo), 0, 1, 'C');
$pdf->Ln(5);
$tri = DB::table('valores')->select("trimestre")->where('id', $examId)->first();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, decode($teacher->fullName()), 0, 1);
$pdf->Cell(95, 5, $exam->curso);
$pdf->Cell(95, 5, $lang->trimesterTranslation($tri->trimestre), 0, 1, "R");
//columnas
$pdf->SetFont('Arial', 'B', 13);
$pdf->Fill();
$pdf->Cell(15, 7, "", 1, 0, "C", true);
$pdf->Cell(90, 7, $lang->translation("Estudiante"), 1, 0, "C", true);
$pdf->Cell(20, 7, $lang->translation("Puntos"), 1, 0, "C", true);
$pdf->Cell(25, 7, $lang->translation("Porcentaje"), 1, 0, "C", true);
$pdf->Cell(40, 7, $lang->translation("Fecha"), 1, 1, "C", true);
$doneExams = DB::table('T_examenes_terminados')->where('id_examen', $examId)->get();
$pdf->SetFont('Arial', '', 10);
foreach ($doneExams as $index => $doneExam) {
	$student = new Student($doneExam->id_estudiante);
	$pdf->Cell(15, 7, $index + 1, 1, 0, "C");
	$pdf->Cell(90, 7, decode($student->fullName()), 1, 0, "C");
	$points = (int)$doneExam->puntos + (int)$doneExam->bonos;
	$porcent = number_format((($points) / $exam->valor) * 100, 0);
	if ($points === 0) {
		$points = '';
		$porcent = '';
	}
	$pdf->Cell(20, 7, $points, 1, 0, "C");
	$pdf->Cell(25, 7, $porcent, 1, 0, "C");
	$pdf->Cell(40, 7, $doneExam->terminado_el, 1, 1, "C");
}



$pdf->Output();
