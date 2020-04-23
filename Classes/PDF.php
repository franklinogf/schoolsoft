<?php
namespace Classes;
  
use Classes\Util;
use Classes\FPDF\FPDF;
use Classes\Controllers\School;

class PDF extends FPDF
{
	public $footer = true;
	
	function Header()
	{				
		$school = new School();
		// dafault values
		$this->SetAuthor($school->get('colegio') ,true);
		$this->setCreator('School Soft');
		$this->SetAutoPageBreak(true,-15);

		if (file_exists(__ROOT . School::logo())) {
			$this->Image(__ROOT . School::logo(), 10, 10, 25);
		}
		$this->SetFont('Arial', 'B', 15);
		$this->Cell(0, 5, $school->get('colegio'), 0, 1, 'C');
		$this->SetFontSize(9);
		if ($school->get('dir1') !== '') {
			$this->Cell(0, 4, $school->get('dir1'), 0, 1, 'C');
		}
		if ($school->get('dir2') !== '') {
			$this->Cell(0, 4, $school->get('dir2'), 0, 1, 'C');
		}
		$this->Cell(0, 4, $school->get('pueblo1') . ', ' . $school->get('esta1') . ' ' . $school->get('zip1'), 0, 1, 'C');
		$this->SetFontSize(8);
		$this->Cell(0, 4, 'Tel: ' . $school->get('telefono') . ' Fax: ' . $school->get('fax'), 0, 1, 'C');
		$this->Cell(0, 4, $school->get('correo'), 0, 1, 'C');
		$this->Ln(10);
		$this->SetFontSize(10);
	}

	function Footer()
	{
		if ($this->footer) {
			$this->AliasNbPages();
			if (__LANG === "es") {
				$footer = 'Pagina ' . $this->PageNo() . ' de {nb} ' . ' | ' . Util::formatDate(Util::date(), true, true);
			} else {
				$footer = 'Page ' . $this->PageNo() . ' of {nb} ' . ' | ' . Util::formatDate(Util::date(), true, true);
			}
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(0, 10, $footer, 0, 0, 'C');
		}
	}
}
