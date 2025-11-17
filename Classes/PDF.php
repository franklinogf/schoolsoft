<?php

namespace Classes;

use App\Models\Admin;
use Classes\PDF\Traits\Codabar;
use Classes\Util;
use Classes\PDF\Traits\Dash;
use Classes\PDF\Traits\Html;
use Classes\PDF\Traits\MultiCells;
use Classes\PDF\Traits\Rotate;
use FPDF;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class PDF extends FPDF
{
    use Codabar, Rotate, Html, Dash, MultiCells;
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
                $admin = Admin::primaryAdmin();
                // dafault values
                $this->SetAuthor($admin->colegio, true);
                $this->setCreator('School Soft');
                // $this->SetAutoPageBreak(true, -15);
                if (file_exists(school_logo_path()) && $this->logo) {
                    $this->Image(school_logo_path(), 10, 10, school_config('app.pdf.logo_size'));
                }

                $this->Cell(0, 5, $admin->colegio, 0, 1, 'C');
                $this->SetFontSize(9);
                if ($admin->dir1 !== '') {
                    $this->Cell(0, 4, $admin->dir1, 0, 1, 'C');
                }
                if ($admin->dir2 !== '') {
                    $this->Cell(0, 4, $admin->dir2, 0, 1, 'C');
                }
                $this->Cell(0, 4, $admin->pueblo1 . ', ' . $admin->esta1 . ' ' . $admin->zip1, 0, 1, 'C');
                $this->SetFontSize(8);
                $this->Cell(0, 4, 'Tel: ' . $admin->telefono . ' Fax: ' . $admin->fax, 0, 1, 'C');
                $this->Cell(0, 4, $admin->correo, 0, 1, 'C');
                $this->Ln(10);
                $this->SetFontSize(10);
                $this->SetLeftMargin($this->leftMargin);
            }
        }
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        parent::Cell($w, $h, mb_convert_encoding($txt ?? '', 'ISO-8859-1', 'UTF-8'), $border, $ln, $align, $fill, $link);
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

    public function Fill(?int $red = null, ?int $green = null, ?int $blue =  null)
    {
        if ($red !== null && $green !== null && $blue !== null) {
            $this->SetFillColor($red, $green, $blue);
            return;
        }

        $pdf = Admin::primaryAdmin()->pdf;
        $pdfColor = $pdf ? json_decode((string) $pdf) : null;

        $red = $pdfColor?->red ?? config('pdf.fill_color.red');
        $green = $pdfColor?->green ?? config('pdf.fill_color.green');
        $blue = $pdfColor?->blue  ?? config('pdf.fill_color.blue');

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

    public static function OutputFiles(array $files)
    {
        $mergedPdf = new Fpdi();
        foreach ($files as $file) {
            $pageCount = $mergedPdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $mergedPdf->importPage($pageNo);
                $size = $mergedPdf->getTemplateSize($templateId);

                $mergedPdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $mergedPdf->useTemplate($templateId);
            }
        }
        $mergedPdf->Output();
    }

    public function saveAsAttachment(?string $path = null): string
    {
        $uniqueId = Str::uuid()->toString();

        $directory = attachments_path($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $filePath = "{$directory}/{$uniqueId}.pdf";

        $this->Output("F", $filePath);

        $url = attachments_url("{$path}/{$uniqueId}.pdf");

        return $url;
    }
}
