<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\DataBase\DB;
use Classes\Controllers\School;

Session::is_logged();

$lang = new Lang([
        ['Lista de PP - 06', 'PP - 06 List'],
        ['Nombre', 'Name'],
        ['Apellios', 'Last name'],
        ['GRADO', 'GRADE'],
        ['GENERO', 'GENDER'],
        ['FECHA N.', 'BIRTH DATE'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

$grupo = '';
class nPDF extends PDF
{
        function header()
        {
                global $lang;
                global $year;
                global $grupo;
                parent::header();
                $this->SetFont('Arial', 'B', 15);
                $this->Cell(0, 5, $lang->translation("Lista de PP - 06") . " / $year", 0, 1, 'C');
                $this->Ln(5);
                $this->SetFont('Arial', '', 10);

                $this->Cell(10, 5, '', 1, 0, 'C', true);
                $this->Cell(15, 5, 'CTA', 1, 0, 'C', true);
                $this->Cell(45, 5, $lang->translation("Nombre"), 1, 0, 'C', true);
                $this->Cell(55, 5, $lang->translation("Apellios"), 1, 0, 'C', true);
                $this->Cell(20, 5, $lang->translation("GRADO"), 1, 0, 'C', true);
                $this->Cell(20, 5, $lang->translation("GENERO"), 1, 0, 'C', true);
                $this->Cell(32, 5, $lang->translation("FECHA N."), 1, 1, 'C', true);
        }
}
$pdf = new nPDF();
$pdf->SetTitle($lang->translation("Lista de PP - 06") . " / $year", true);
$pdf->Fill();

$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);
$studentsa = DB::table('year')
        ->whereRaw("grado < '07-00' and grado NOT LIKE '%07-%' and activo='' and year = '$year' or grado > '12-00' and grado NOT LIKE '%12-%' and activo='' and year = '$year'")->orderBy('grado DESC, apellidos')->get();

$c = 0;
$a = 0;
foreach ($studentsa as $studenta) {
        if ($c == 31 or $c == 0) {
                $pdf->AddPage();
                $c = 0;
        }
        $c = $c + 1;

        $a = $a + 1;
        $x = 1;
        $pdf->Cell(10, 5, $a, 0, 0, 'C');
        $students = DB::table('year')
                ->whereRaw("grado > '12-00' and grado NOT LIKE '%12-%' and activo='' and year = '$year' and id = '$studenta->id' or grado < '07-00' and grado NOT LIKE '%07-%' and activo='' and year = '$year' and id = '$studenta->id'")->orderBy('fecha, grado DESC, apellidos')->get();
        foreach ($students as $student) {
                if ($x == 0) {
                        $pdf->Cell(10, 5, '', 0, 0, 'C');
                }
                $x = 0;
                $pdf->Cell(15, 5, $student->id, 0, 0, 'R');
                $pdf->Cell(45, 5, $student->nombre, 0, 0);
                $pdf->Cell(55, 5, $student->apellidos, 0, 0);
                $pdf->Cell(20, 5, $student->grado, 0, 0, 'C');
                $gen = '';
                if ($student->genero == '1' or $student->genero == 'F') {
                        $gen = 'F';
                }
                if ($student->genero == '2' or $student->genero == 'M') {
                        $gen = 'M';
                }
                $pdf->Cell(20, 5, $gen, 0, 0, 'C');
                $pdf->Cell(32, 5, $student->fecha, 0, 1, 'C');
        }
}
$pdf->Output();
