<?php
require_once __DIR__ . '/../../../../app.php';
// le faltan las fotos a los estudiantes

use App\Models\Admin;
use App\Models\Student;
use Classes\PDF as BasePDF;
use Classes\Session;
use Classes\Server;

Server::is_post();
Session::is_logged();

$school = Admin::user(Session::id())->first();
$year = $school->year2;

$cole = Admin::primaryAdmin();

class PDF extends BasePDF
{
    public function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = ''): void
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

    private function _Arc($x1, $y1, $x2, $y2, $x3, $y3): void
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
$pdf->useHeader(false);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$estudiantesSS = $_REQUEST['students'];
function StudentId(): void
{
    global $ss;
    global $year;
    global $pdf;
    global $cole;

    $estu = Student::query()->where([
        ['ss', $ss],
        ['year', $year],
    ])->orderBy('grado')->first();
    $pdf->SetLineWidth(1);
    $pdf->RoundedRect($pdf->GetX(), $pdf->GetY(), 80, 40, 2, '1234');
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(55, 5, $cole->colegio);
    $pdf->Cell(25, 5, $cole->telefono, 0, 1, 'R');
    $pdf->SetFont('Times', '', 8);
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, "$cole->dir1 $cole->dir3, $cole->pueblo1, $cole->esta1 $cole->zip1", 0, 1, 'L', true);
    //$pdf->Image('../logo/logo.gif', $pdf->GetX() + 53, $pdf->GetY() - 7.5, 25);

    if (file_exists("../picture/{$estu->tipo}.jpg")) {
        $pdf->Rect($pdf->GetX() + 2, $pdf->GetY() + 2, 20, 25);
        $pdf->Image("../picture/{$estu->tipo}.jpg", $pdf->GetX() + 2, $pdf->GetY() + 2, 20, 25);
    }

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, $estu->nombre, 0, 1, 'R');
    $pdf->Cell(.5);
    $pdf->Cell(79, 5, $estu->apellidos, 0, 1, 'R');
    $pdf->Cell(35);
    $pdf->Cell(40, 5, "Grado $estu->grado ID #$estu->id", 0, 1, 'C', true);
    if ($estu->cbarra) $pdf->Codabar($pdf->GetX() + 30, $pdf->GetY() + 3, $estu->cbarra, '*', '*', 0.29, 5);
    // $pdf->Cell(0, 5, $estu->cbarra);
    $pdf->Ln(30);
}
$count = 1;
foreach ($estudiantesSS as $ss) {
    $pdf->SetFillColor(144, 184, 255);
    if ($count === 6) {
        $pdf->SetMargins(120, 10);
        $pdf->SetXY(120, 10);
    } else if ($count === 11) {
        $pdf->SetMargins(10, 10);
        $pdf->SetXY(10, 10);
        $pdf->addPage();
        $count = 1;
    }
    StudentId();

    $count++;
}

$pdf->Output();
