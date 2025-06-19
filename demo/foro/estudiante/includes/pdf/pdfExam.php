<?php
require_once '../../../../app.php';

use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\Exam;
use Classes\Controllers\Student;
use Classes\Controllers\Teacher;
use Classes\Util;

Session::is_logged();
$examenID = $_GET['examenId'];

function green()
{
	global $pdf;
	$pdf->SetTextColor(0, 128, 0);
}
function red()
{
	global $pdf;
	$pdf->SetTextColor(255, 0, 0);
}
function black()
{
	global $pdf;
	$pdf->SetTextColor(0);
}

$exam = new Exam($examenID);
$teacher = new Teacher($exam->id_maestro);
$student = new Student(Session::id());
$doneExam = $student->doneExam($exam->id);
$pdf = new PDF();
$pdf->SetTitle(utf8_decode($exam->titulo) . " - " . $student->fullName(), true);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 10);
$pdf->headerFirstPage = true;
$points = $doneExam->puntos + $doneExam->bonos;

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 7, "Nombre: ", 0, 0, "L");
$pdf->Cell(80, 7, utf8_decode($student->fullName()), 'B', 0, 'L');
$pdf->Cell(25, 7, '', 0, 0, 'L');
$pdf->Cell(18, 7, 'Fecha: ', 0, 0, 'L');
$pdf->Cell(35, 7, Util::formatDate($doneExam->terminado_el, true, true), 'B', 1, 'C');
$pdf->Ln(5);
$pdf->Cell(100, 7, utf8_decode($teacher->fullName()) . $doneExam->bono, 0, 0, "L");
$pdf->Cell(25, 7, '', 0, 0, 'L');
$pdf->Cell(18, 7, 'Grado: ', 0, 0, 'L');
$pdf->Cell(35, 7, $student->grado, 'B', 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 14);
if ($points >= ($exam->valor * 0.70)) {
	green();
} else {
	red();
}
$pdf->Cell(0, 5, "Valor: {$points}/{$exam->valor} puntos", 0, 1, 'C');
black();
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 16);
$pdf->MultiCell(0, 5, utf8_decode(utf8_decode($exam->titulo)), '0', "C");

// starts in #1
$topicNumber = 1;

if (isset($exam->fvs->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, utf8_decode("{$topicNumber} - {$exam->fvs->title} ({$exam->fvs->value})"), 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$count = 1;
	foreach ($exam->fvs->topics as $topic) {
		$done = DB::table('T_examen_terminado_fyv', !__COSEY)->where([
			['id_examen', $doneExam->id],
			['id_pregunta', $topic->id]
		])->first();
		if ($topic->respuesta == $done->respuesta) {
			green();
		} else {
			red();
		}
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, strtoupper($done->respuesta), 'B', 0, 'C');
		$pdf->MultiCell(0, 5, utf8_decode(utf8_decode($topic->pregunta)), 0, 1);
		$pdf->Ln(2);
		$count++;
		black();
	}
	$topicNumber++;
}

if (isset($exam->selects->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, utf8_decode("{$topicNumber} - {$exam->selects->title} ({$exam->selects->value})"), 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$count = 1;
	foreach ($exam->selects->topics as $topic) {
		if ($pdf->GetY() > 250) $pdf->addPage();
		$done = DB::table('T_examen_terminado_selec', !__COSEY)->where([
			['id_examen', $doneExam->id],
			['id_pregunta', $topic->id]
		])->first();
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->MultiCell(0, 5, utf8_decode(utf8_decode($topic->pregunta)));
		$pdf->Ln(3);
		for ($i = 1; $i <= 8; $i++) {
			if (!empty($topic->{"respuesta{$i}"})) {
				$pdf->Cell(20);
				if ($done->respuesta === $i) {
					if ($topic->correcta == $done->respuesta) {
						green();
					} else {
						red();
					}
				}
				$pdf->Cell(10, 5, "$i)", 0, 0, "R");
				$pdf->Cell(0, 5, utf8_decode(utf8_decode($topic->{"respuesta{$i}"})), 0, 1);
				black();
			}
		}
		$pdf->Ln(5);
		$count++;
	}
	$topicNumber++;
}

if (isset($exam->pairs->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, utf8_decode("{$topicNumber} - {$exam->pairs->title} ({$exam->pairs->value})"), 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$count = 1;
	$Y = $pdf->GetY();
	$lettersArray = [];
	foreach ($exam->pairCodes->topics as $index => $answer) {
		$lettersArray[$answer->id] = Exam::$letters[$index + 1];
	}
	foreach ($exam->pairs->topics as $topic) {
		$done = DB::table('T_examen_terminado_parea', !__COSEY)->where([
			['id_examen', $doneExam->id],
			['id_pregunta', $topic->id]
		])->first();
		$done2 = DB::table('T_examen_codigo_parea', !__COSEY)->find($done->respuesta);
		$response = $topic->respuesta_c;
		if ($response == $done2->id) {
			green();
		} else {
			red();
		}
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, $lettersArray[$done2->id], 'B', 0, 'C');
		$pdf->MultiCell(90, 5, utf8_decode(utf8_decode($topic->pregunta)), 0, 1);
		$count++;
	}
	$Y2 = $pdf->GetY();
	black();
	$pdf->SetY($Y);
	foreach ($exam->pairCodes->topics as $index => $answer) {
		$pdf->Cell(110);
		$pdf->MultiCell(80, 5, $lettersArray[$answer->id] . ') ' . utf8_decode(utf8_decode($answer->respuesta)));
	}
	$topicNumber++;
	$pdf->SetY($Y2);
}
if (isset($exam->lines->topics)) {
	$pdf->Ln(10);
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, utf8_decode("{$topicNumber} - {$exam->lines->title} ({$exam->lines->value})"), 0, 1);
	$pdf->Ln(5);
	$pdf->SetFont('Arial', '', 12);
	$count = 1;
	foreach ($exam->lines->topics as $index => $topic) {
		$done = DB::table('T_examen_terminado_linea', !__COSEY)->where([
			['id_examen', $doneExam->id],
			['id_pregunta', $topic->id]
		])->first();
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$quest = str_replace("___", '~ ', $topic->pregunta);
		$words = explode(" ", $quest);
		foreach ($words as $word) {
			if ($word !== '~') {
				$pdf->Cell($pdf->GetStringWidth($word) + 1, 5, utf8_decode(utf8_decode($word)));
			} else {
				$answerNumber = $index + 1;
				if ($topic->{"respuesta{$answerNumber}"} == $done->{"respuesta{$answerNumber}"}) {
					green();
				} else {
					red();
				}
				$pdf->Cell($pdf->GetStringWidth($done->{"respuesta{$answerNumber}"}) + 2, 5, utf8_decode(utf8_decode($done->{"respuesta{$answerNumber}"})), 'B');
			}
			black();
		}
		$count++;
		$pdf->Ln(4);
	}
	$topicNumber++;
}
if (isset($exam->qas->topics)) {
	if ($topicNumber > 1) $pdf->addPage();
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, utf8_decode("{$topicNumber} - {$exam->qas->title} ({$exam->qas->value})"), 0, 1);
	$pdf->Ln(5);
	$count = 1;
	foreach ($exam->qas->topics as $topic) {
		$done = DB::table('T_examen_terminado_pregunta', !__COSEY)->where([
			['id_examen', $doneExam->id],
			['id_pregunta', $topic->id]
		])->first();
		$pdf->SetFont('Arial', 'B', 12);

		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->MultiCell(0, 5, utf8_decode(utf8_decode($topic->pregunta)), 0, 1);
		$pdf->Ln(3);
		$pdf->SetFont('Arial', '', 12);
		$pdf->MultiCell(0, 5, utf8_decode($done->respuesta), 0, 1);
		$pdf->Ln(4);
		$count++;
	}
}

$pdf->Output();
