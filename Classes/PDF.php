<?php

namespace Classes;

use Classes\Util;
use Classes\FPDF\FPDF;
use Classes\Controllers\School;

class PDF extends FPDF
{
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
                $this->SetAuthor(utf8_decode($school->info('colegio')), true);
                $this->setCreator('School Soft');
                // $this->SetAutoPageBreak(true, -15);
                if (file_exists(__ROOT . School::logo()) && $this->logo) {
                    $this->Image(__ROOT . School::logo(), 10, 10, __PDF_LOGO_SIZE);
                }

                $this->Cell(0, 5, utf8_decode($school->info('colegio')), 0, 1, 'C');
                $this->SetFontSize(9);
                if ($school->info('dir1') !== '') {
                    $this->Cell(0, 4, utf8_decode($school->info('dir1')), 0, 1, 'C');
                }
                if ($school->info('dir2') !== '') {
                    $this->Cell(0, 4, utf8_decode($school->info('dir2')), 0, 1, 'C');
                }
                $this->Cell(0, 4, utf8_decode($school->info('pueblo1')) . ', ' . $school->info('esta1') . ' ' . $school->info('zip1'), 0, 1, 'C');
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
        $this->Cell(0, 5, utf8_decode($value1), 0, 0, "L");
        $this->Cell(0, 5, utf8_decode($value2), 0, 1, "R");
    }

    public function Fill($red = __PDF_FILL_COLOR_RED, $green = __PDF_FILL_COLOR_GREEN, $blue =  __PDF_FILL_COLOR_BLUE)
    {
        $this->SetFillColor($red, $green, $blue);
    }

    function Footer()
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
    /* --------------------------------- Rotate --------------------------------- */
    var $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
    function RotatedText($x, $y, $txt, $angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function RotatedImage($file, $x, $y, $w, $h, $angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle, $x, $y);
        $this->Image($file, $x, $y, $w, $h);
        $this->Rotate(0);
    }
    /* ------------------------------- End Rotated ------------------------------ */

    /* ---------------------------------- HTML ---------------------------------- */

    private $HREF = '';
    private $ALIGN = '';

    function WriteHTML($html)
    {
        //HTML parser
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                if ($this->HREF)
                    $this->PutLink($this->HREF, $e);
                elseif ($this->ALIGN == 'center')
                    $this->Cell(0, 5, $e, 0, 1, 'C');
                else
                    $this->Write(5, $e);
            } else {
                //Tag
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extract properties
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $prop = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $prop[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $prop);
                }
            }
        }
    }

    function OpenTag($tag, $prop)
    {
        //Opening tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, true);
        if ($tag == 'A')
            $this->HREF = $prop['HREF'];
        if ($tag == 'BR')
            $this->Ln(5);
        if ($tag == 'P')
            $this->ALIGN = $prop['ALIGN'];
        if ($tag == 'HR') {
            if (!empty($prop['WIDTH']))
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin - $this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x, $y, $x + $Width, $y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
        if ($tag == 'P')
            $this->ALIGN = '';
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s)
            if ($this->$s > 0)
                $style .= $s;
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }
    /* -------------------------------- End HTML -------------------------------- */

    /* --------------------------------- Dashes --------------------------------- */
    function SetDash($black = null, $white = null)
    {
        if ($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }
    /* ------------------------------- End Dashes ------------------------------- */
}
