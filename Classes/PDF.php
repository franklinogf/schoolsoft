<?php

namespace Classes;

use Classes\FPDF\Traits\Codabar;
use Classes\Util;
use Classes\FPDF\FPDF;
use Classes\Controllers\School;
use Classes\FPDF\Traits\Dash;
use Classes\FPDF\Traits\Html;
use Classes\FPDF\Traits\Rotate;

class PDF extends FPDF
{
    use Codabar, Rotate, Html, Dash;
    public $footer = 15;
    public $header = true;
    public $headerFirstPage = false;
    public $logo = true;
    private $leftMargin = 10;


    public function Header()
    {

        if ($this->header) {
            $this->leftMargin = $this->lMargin;
            $this->SetXY(10, 10);
            $this->SetMargins(10, 10);
            $this->SetFont('times', 'B', 15);
            $this->SetTextColor(0);
            if (($this->headerFirstPage && $this->PageNo() === 1) || !$this->headerFirstPage) {
                $school = new School('administrador');
                // dafault values
                $this->SetAuthor($school->info('colegio'), true);
                $this->setCreator('School Soft');
                // $this->SetAutoPageBreak(true, -15);
                if (file_exists(__ROOT . School::logo()) && $this->logo) {
                    $this->Image(__ROOT . School::logo(), 10, 10, __PDF_LOGO_SIZE);
                }

                $this->Cell(0, 5, $school->info('colegio'), 0, 1, 'C');
                $this->SetFontSize(9);
                if ($school->info('dir1') !== '') {
                    $this->Cell(0, 4, $school->info('dir1'), 0, 1, 'C');
                }
                if ($school->info('dir2') !== '') {
                    $this->Cell(0, 4, $school->info('dir2'), 0, 1, 'C');
                }
                $this->Cell(0, 4, $school->info('pueblo1') . ', ' . $school->info('esta1') . ' ' . $school->info('zip1'), 0, 1, 'C');
                $this->SetFontSize(8);
                $this->Cell(0, 4, 'Tel: ' . $school->info('telefono') . ' Fax: ' . $school->info('fax'), 0, 1, 'C');
                $this->Cell(0, 4, $school->info('correo'), 0, 1, 'C');
                $this->Ln(10);
                $this->SetFontSize(10);
                $this->SetLeftMargin($this->leftMargin);
            }
        }
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, utf8_decode($txt), $border, $ln, $align, $fill, $link);
    }
    public function useHeader($bool = true)
    {
        $this->header = $bool;
    }

    public function useFooter($bool = true)
    {
        $this->footer = $bool;
    }

    public function splitCells($value1, $value2)
    {
        $this->Cell(0, 5, $value1, 0, 0, "L");
        $this->Cell(0, 5, $value2, 0, 1, "R");
    }

    public function Fill($red = __PDF_FILL_COLOR_RED, $green = __PDF_FILL_COLOR_GREEN, $blue =  __PDF_FILL_COLOR_BLUE)
    {
        $this->SetFillColor($red, $green, $blue);
    }

    public function Footer()
    {
        if ($this->footer) {
            $this->SetTextColor(0);
            $this->AliasNbPages();
            if (__LANG === "es") {
                $footer = 'Pagina ' . $this->PageNo() . ' de {nb} ' . ' | ' . Util::formatDate(Util::date(), true, true);
            } else {
                $footer = 'Page ' . $this->PageNo() . ' of {nb} ' . ' | ' . Util::formatDate(Util::date(), true, true);
            }
            $this->SetY(-$this->footer);
            $this->SetFont('times', 'I', 8);
            $this->Cell(0, 10, $footer, 0, 0, 'C');
        }
    }
}
