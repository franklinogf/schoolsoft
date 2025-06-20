<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Listado de códigos de barra creados", "List of created barcodes"],
    ['Hasta', 'Until'],
    ['Código', 'Code'],
    ['Todos', 'All'],
    ['Selección', 'Select'],
    ['CTA', 'ACC'],
    ['Tipo de Fechas', 'Type of Dates'],
    ['OPCIONES', 'OPTIONS'],
    ['NOMBRE', 'NAME'],
    ['GRADO', 'GRADE'],
    ['CÓDIGO DE BARRA', 'BARCODE'],
    ['Opciones', 'Options'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    function Header()
    {

        global $year;
        parent::header();
        global $lang;

        $this->Ln(-5);
        $this->Cell(80);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(30, 5, utf8_encode($lang->translation('Listado de códigos de barra creados')) . ' ' . $year, 0, 1, 'C');
        $this->Ln(5);

        $this->Cell(8, 5, '', 1, 0, 'C', true);
        $this->Cell(18, 5, $lang->translation('CTA'), 1, 0, 'C', true);
        $this->Cell(90, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
        $this->Cell(20, 5, $lang->translation('GRADO'), 1, 0, 'C', true);
        $this->Cell(50, 5, utf8_encode($lang->translation('CÓDIGO DE BARRA')), 1, 1, 'C', true);
    }

    function Footer()
    {
        global $lang;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, $lang->translation('Pagina ') . $this->PageNo() . '/{nb}' . ' / ' . date('m-d-Y'), 0, 0, 'C');
    }
}


$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
function generate_string($input, $strength = 16)
{
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$pdf->AddPage();
$pdf->SetTitle(utf8_encode($lang->translation('Listado de códigos de barra creados')) . ' ' . $year);
$pdf->SetFont('Times', '', 11);

$cod = '';

if ($_POST['crear'] == 1) {
    $result7 = DB::table('year')->where([
        ['year', $year],
        ['cbarra', ''],
        ['activo', '']
    ])->orderBy('id')->get();
} else {
    $result7 = DB::table('year')->where([
        ['year', $year],
        ['activo', '']
    ])->orderBy('apellidos')->get();
}
$x = 0;
foreach ($result7 as $row7) {
    $cbarra = '';
    if ($_POST['codi'] == 'SS') {
        list($s1, $s2, $s3) = explode("-", $row7->ss);
        $cbarra = $s1 . $s2 . $s3;
    }
    if ($_POST['codi'] == '4S') {
        list($s1, $s2, $s3) = explode("-", $row7->ss);
        $cbarra = $s3 . $row7->id;
    }
    if ($_POST['codi'] == 'GE') {
        $cbarra = generate_string($permitted_chars, 20);
    }
    $thisCourse2 = DB::table('year')->where([
        ['ss', $row7->ss],
        ['year', $year]
    ])->update([
        'cbarra' => $cbarra,
    ]);



    $pdf->SetFont('Times', '', 10);


    $deu = 0;
    $pag = 0;

    $x = $x + 1;
    $pdf->Cell(8, 5, $x, 0, 0, 'R');
    $pdf->Cell(18, 5, $row7->id, 0, 0, 'R');
    $pdf->Cell(90, 5, $row7->apellidos . ' ' . $row7->nombre, 0, 0);
    $pdf->Cell(20, 5, $row7->grado, 0, 0, 'C');
    $pdf->Cell(50, 5, $cbarra, 0, 1, 'L');
}
$pdf->Output();
