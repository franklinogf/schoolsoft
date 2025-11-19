<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Teacher;
use Classes\PDF;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();
$lang = new Lang([
    ['Mensajes por clase para la tarjeta de notas', 'Class Note Card Messages'],
    ['Código', 'Code'],
    ['Descripción', 'Description'],
    ['Comentario', 'Comment'],
    ['Estás seguro que quieres borrar el comentario?', 'Are you sure you want to delete the comment?'],
    ['Credito', 'Credit'],
    ['Peso', 'Peso'],
    ['Maestro', 'Teacher'],
    ['Horario entrada', 'Enter time'],
    ['Horario salida', 'Exit time'],
    ['Días', 'Days'],
    ['Avanzada', 'Advance'],
    ['Valor', 'Value'],
    ['Regular', 'Regular'],
    ['Verano', 'Summer'],
    ['Si', 'Yes'],
    ['Lista', 'List'],
    ['Guardar', 'Save'],
    ['Crear', 'Create'],
    ['Buscar', 'Search'],
    ['Limpiar', 'Clear'],
    ['Eliminar', 'Delete'],
]);



$pdf = new PDF;

$pdf->SetTitle($lang->translation('Comentario'));
$title = $lang->translation('Mensajes por clase para la tarjeta de notas');
$pdf->Fill();
$pdf->addPage();


$school = new School();
$courses = DB::table('comentarios')->orderBy('code')->get();


$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(10, 5, '  ', 1, 0, 'C', true);
$pdf->Cell(20, 5, $lang->translation("Código"), 1, 0, 'C', true);
$pdf->Cell(120, 5, $lang->translation("Comentario"), 1, 1, 'C', true);
$pdf->SetFont('Times', '', 10);
foreach ($courses as $index => $course) {


    $pdf->Cell(10, 5, $index + 1, 1);
    $pdf->Cell(20, 5, $course->code, 1, 0);
    $pdf->Cell(120, 5, $course->comenta, 1, 1);
}

$pdf->Output();
