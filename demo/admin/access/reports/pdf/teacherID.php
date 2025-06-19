<?php
require_once '../../../../app.php';
// le falta las fotos
use Classes\pdf_codabar;
use Classes\Session;
use Classes\Server;
use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Util;

Server::is_post();
Session::is_logged();

$school = new School(Session::id());
$year = $school->info('year2');

$cole = DB::table('colegio')->where([
    ['usuario', 'administrador'],
])->orderBy('usuario')->first();

class PDF extends PDF_Codabar
{
    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        if (strpos($corners, '2') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
        else
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '3') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($corners, '4') === false)
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
        else
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);

        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '1') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
        } else
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }
}

$pdf = new PDF();
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$profesorIds = $_REQUEST['students'];
function StudentId()
{
    global $id;
    global $pdf;
    global $cole;

    $profe = DB::table('profesor')->where([
        ['id', $id],
    ])->orderBy('grado, apellidos')->first();
    $pdf->SetLineWidth(1);
    $pdf->RoundedRect($pdf->GetX(), $pdf->GetY(), 80, 40, 2, '1234');
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(55, 5, $cole->colegio);
    $pdf->Cell(25, 5, $cole->telefono, 0, 1, 'R');
    $pdf->SetFont('Times', '', 8);
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, "$cole->dir1 $cole->dir3, $cole->pueblo1, $cole->esta1 $cole->zip1", 0, 1, 'L', true);
    if (file_exists("../picture/{$profe->foto_name}.jpg")) {
        $pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 2, 20, 25);
        $pdf->Image("../picture/{$profe->foto_name}.jpg", $pdf->GetX() + 2, $pdf->GetY() + 2, 20, 25);
    }
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, !mb_detect_encoding($profe->nombre, 'UTF-8', true) ? $profe->nombre : utf8_decode($profe->nombre), 0, 1, 'R');
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, !mb_detect_encoding($profe->apellidos, 'UTF-8', true) ? $profe->apellidos : utf8_decode($profe->apellidos), 0, 1, 'R');
    $pdf->Cell(35);
    $pdf->Cell(40, 5, "S. Hogar: $profe->grado ID: #$profe->id", 0, 1, 'C', true);
    $pdf->Codabar($pdf->GetX() + 30, $pdf->GetY() + 3, $profe->id ?? '***', '*', '*', 0.29, 5);
    $pdf->Ln(30);
}
$count = 1;
foreach ($profesorIds as $id) {
    $pdf->SetFillColor(144, 184, 255);
    if ($count === 6) {
        $pdf->SetMargins(120, 10);
        $pdf->SetXY(120, 10);
        StudentId();
    } else if ($count === 11) {
        $pdf->SetMargins(10, 10);
        $pdf->SetXY(10, 10);
        $pdf->addPage();
        $count = 1;
        StudentId();
    } else {
        StudentId();
    }

    $count++;
}

$pdf->Output();
