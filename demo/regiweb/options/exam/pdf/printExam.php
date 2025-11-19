<?php
require_once __DIR__ . '/../../../../app.php';

use Classes\PDF;
use Classes\Lang;
use Classes\Server;
use Classes\Session;
use Classes\Controllers\Exam;
use Classes\Controllers\Teacher;

Server::is_post();
Session::is_logged();

$teacher = new Teacher(Session::id());
$examId = $_POST['printExamId'];
$exam = new Exam($examId);
$topicNumber = 1;
$paperSize = $_POST['paperSize'];
$lang = new Lang([
	["Nombre:","Name:"],
	['Fecha:','Date:'],
	["Grado:","Grade:"],
	["Valor","Value"],
	["puntos","points"],
]);
function decode($text)
{
	return iconv('UTF-8', 'windows-1252', $text);
}
$pdf = new PDF('P','mm',$paperSize);
$pdf->SetTitle(decode($exam->titulo));
$pdf->useFooter(false);
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 7, $lang->translation("Nombre:"), 0, 0, "L");
$pdf->Cell(80, 7, '', 'B', 0, 'L');
$pdf->Cell(25, 7, '', 0, 0, 'L');
$pdf->Cell(18, 7, $lang->translation("Fecha:"), 0, 0, 'L');
$pdf->Cell(35, 7, '', 'B', 1, 'L');
$pdf->Cell(100, 7, decode($teacher->fullName()), 0, 0, "L");
$pdf->Cell(25, 7, '', 0, 0, 'L');
$pdf->Cell(18, 7, $lang->translation("Grado:"), 0, 0, 'L');
$pdf->Cell(35, 7, '', 'B', 1, 'L');

$pdf->Cell(20, 5, "", 0, 1, "L");
$pdf->Cell(85, 5, '', 0, 0, 'L');
$pdf->Cell(18, 5, $lang->translation("Valor").": ______/$exam->valor ". $lang->translation("puntos"), 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, decode($exam->titulo), 0, 1, 'C');


if (isset($exam->fvs->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, $exam->desc1 === 'si' ? $topicNumber . ' - ' . decode($exam->desc1_1) : $topicNumber . ' - ' . decode($exam->fvs->title));
	$count = 1;
	$pdf->SetFont('Arial', '', 12);
	foreach ($exam->fvs->topics as $topic) {
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, '', 'B');
		$pdf->MultiCell(0, 5, decode($topic->pregunta), 0, 1);
		$pdf->Ln(2);
		$count++;
	}
	$topicNumber++;
}

if (isset($exam->selects->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, $exam->desc2 === 'si' ? $topicNumber . ' - ' . decode($exam->desc2_1) : $topicNumber . ' - ' . decode($exam->selects->title));
	$count = 1;
	$pdf->SetFont('Arial', '', 12);
	foreach ($exam->selects->topics as $topic) {
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->MultiCell(0, 5, decode($topic->pregunta));
		$pdf->Ln(5);
		$index = 1;
		if ($topic->respuesta1) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta1), 0, 1);
			$index++;
		}
		if ($topic->respuesta2) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta2), 0, 1);
			$index++;
		}
		if ($topic->respuesta3) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta3), 0, 1);
			$index++;
		}
		if ($topic->respuesta4) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta4), 0, 1);
			$index++;
		}
		if ($topic->respuesta5) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta5), 0, 1);
			$index++;
		}
		if ($topic->respuesta6) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta6), 0, 1);
			$index++;
		}
		if ($topic->respuesta7) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta7), 0, 1);
			$index++;
		}
		if ($topic->respuesta8) {
			$pdf->Cell(20);
			$pdf->Cell(10, 5, "$index)", 0, 0, "R");
			$pdf->Cell(0, 5, decode($topic->respuesta8), 0, 1);
			$index++;
		}
		$count++;
		$pdf->Ln(5);
	}
	$topicNumber++;
}

if (isset($exam->pairs->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, $exam->desc3 === 'si' ? $topicNumber . ' - ' . decode($exam->desc3_1) : $topicNumber . ' - ' . decode($exam->pairs->title));
	$count = 1;
	$pdf->SetFont('Arial', '', 12);
	$Y = $pdf->GetY();
	foreach ($exam->pairs->topics as $topic) {
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, '', 'B');
		$pdf->MultiCell(0, 5, decode($topic->pregunta), 0, 1);
		$pdf->Ln(2);
		$count++;
	}
	$pdf->SetY($Y);
	foreach ($exam->pairCodes->topics as $index => $topic) {
		$pdf->Cell(110);
		$pdf->MultiCell(80, 5, Exam::$letters[$index + 1] . ") " . decode($topic->respuesta));
	}
	$pdf->Ln(5);
	$topicNumber++;
}

if (isset($exam->lines->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, $exam->desc4 === 'si' ? $topicNumber . ' - ' . decode($exam->desc4_1) : $topicNumber . ' - ' . decode($exam->lines->title));
	$count = 1;
	$pdf->SetFont('Arial', '', 12);
	$Y = $pdf->GetY();
	foreach ($exam->lines->topics as $topic) {
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, '', 'B');
		$pdf->MultiCell(0, 5, decode($topic->pregunta), 0, 1);
		$pdf->Ln(2);
		$count++;
	}

	$topicNumber++;
}

if (isset($exam->qas->topics)) {
	$pdf->SetFont('Arial', 'B', 13);
	$pdf->MultiCell(0, 15, $exam->desc5 === 'si' ? $topicNumber . ' - ' . decode($exam->desc5_1) : $topicNumber . ' - ' . decode($exam->qas->title));
	$count = 1;
	$pdf->SetFont('Arial', '', 12);
	$Y = $pdf->GetY();
	foreach ($exam->qas->topics as $topic) {
		$pdf->Cell(10, 5, "$count.", 0, 0, "R");
		$pdf->Cell(10, 4, '', 'B');
		$pdf->MultiCell(0, 5, decode($topic->pregunta), 0, 1);
		$pdf->Ln(2);
		$count++;
		for ($i = 1; $i <= $topic->lineas; $i++) {
			$pdf->Cell(0, 7, '', "B", 1);
		}
	}

	$topicNumber++;
}




// if (mysql_num_rows($tema5) > 0) {
// 	$pdf->Ln(10);
// 	$pdf->SetFont('Arial','B',13);	
// 	$pdf->Cell(0,15,"$temas- RESPONDER A LAS PREGUNTAS CORRECTAMENTE ($valorTema5)",0,1);
// 	if ($examen->desc5 == 'si') {
// 		$pdf->SetFont('Arial','',13);	
// 		$pdf->MultiCell(0,5,decode($examen->desc5_1),0,1);
// 		$pdf->Ln(5);
// 	}
// 	$pdf->SetFont('Arial','',12);
// 	$count = 1;
// 	while ($row = mysql_fetch_object($tema5)) {		
// 		$pdf->Cell(10,5,"$count.",0,0,"R");		
// 		$pdf->MultiCell(0,5,decode($row->pregunta),0,1);
// 		for ($i=1; $i <= $row->lineas ; $i++) { 
// 			$pdf->Cell(0,7,'',"B",1);
// 		}
// 		$pdf->Ln(4);
// 		$count++;
// 	}	
// }

$pdf->Output();
