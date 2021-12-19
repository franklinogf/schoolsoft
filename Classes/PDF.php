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


    function Header()
    {
        if ($this->header) {
            $this->SetTextColor(0);
            if (($this->headerFirstPage && $this->PageNo() === 1) || !$this->headerFirstPage) {
                $school = new School();
                // dafault values
                $this->SetAuthor(utf8_decode($school->info('colegio')), true);
                $this->setCreator('School Soft');
                $this->SetAutoPageBreak(true, -15);
                if (file_exists(__ROOT . School::logo()) && $this->logo) {
                    $this->Image(__ROOT . School::logo(), 10, 10, __PDF_LOGO_SIZE);
                }
                $this->SetFont('Arial', 'B', 15);
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
            }
        }
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
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, $footer, 0, 0, 'C');
        }
    }

    // HTML
    private $B = 0;
    private $I = 0;
    private $U = 0;
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
}
