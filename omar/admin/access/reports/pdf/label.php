<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\Controllers\School;
use Classes\Controllers\Student;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;

Session::is_logged();

$lang = new Lang([
    ['Grado', 'Grade'],
    ['Cuenta', 'Account'],
]);
$school = new School();
$studentClass = new Student();
$year = $school->year();

$label = $_POST['label'];
$grade = $_POST['grade'];
$fontSize = $_POST['fontSize'];
$gradePlacement = $_POST['gradePlacement'];
$withGrade = $_POST['withGrade'] === 'si' ? true : false;
$withAddress = $_POST['withAddress'] === 'si' ? true : false;
$withAccount = $_POST['withAccount'] === 'si' ? true : false;
$nameOption = $_POST['nameOption'];
$repeatStudents = (int) $_POST['repeatStudents'];

if ($grade !== '') {
    $students = $studentClass->findByGrade($grade);
} else {
    $students = $studentClass->all();
}


class nPDF extends PDF
{
    // Private properties
    protected $_Margin_Left;        // Left margin of labels
    protected $_Margin_Top;            // Top margin of labels
    protected $_X_Space;            // Horizontal space between 2 labels
    protected $_Y_Space;            // Vertical space between 2 labels
    protected $_X_Number;            // Number of labels horizontally
    protected $_Y_Number;            // Number of labels vertically
    protected $_Width;                // Width of label
    protected $_Height;                // Height of label
    protected $_Line_Height;        // Line height
    protected $_Padding;            // Padding
    protected $_Metric_Doc;            // Type of metric for the document
    protected $_COUNTX;                // Current x position
    protected $_COUNTY;                // Current y position

    // List of label formats
    protected $_Avery_Labels = array(
        '5160' => array('paper-size' => 'letter',    'metric' => 'mm',    'marginLeft' => 1.762,    'marginTop' => 10.7,        'NX' => 3,    'NY' => 10,    'SpaceX' => 3.175,    'SpaceY' => 0,    'width' => 66.675,    'height' => 25.4,        'font-size' => 8),
        '5161' => array('paper-size' => 'letter',    'metric' => 'mm',    'marginLeft' => 0.967,    'marginTop' => 10.7,        'NX' => 2,    'NY' => 10,    'SpaceX' => 3.967,    'SpaceY' => 0,    'width' => 101.6,        'height' => 25.4,        'font-size' => 8),
        '5162' => array('paper-size' => 'letter',    'metric' => 'mm',    'marginLeft' => 0.97,        'marginTop' => 20.224,    'NX' => 2,    'NY' => 7,    'SpaceX' => 4.762,    'SpaceY' => 0,    'width' => 100.807,    'height' => 35.72,    'font-size' => 8),
        '5163' => array('paper-size' => 'letter',    'metric' => 'mm',    'marginLeft' => 1.762,    'marginTop' => 10.7,         'NX' => 2,    'NY' => 5,    'SpaceX' => 3.175,    'SpaceY' => 0,    'width' => 101.6,        'height' => 50.8,        'font-size' => 8),
        '5164' => array('paper-size' => 'letter',    'metric' => 'in',    'marginLeft' => 0.148,    'marginTop' => 0.5,         'NX' => 2,    'NY' => 3,    'SpaceX' => 0.2031,    'SpaceY' => 0,    'width' => 4.0,        'height' => 3.33,        'font-size' => 12),
        '8600' => array('paper-size' => 'letter',    'metric' => 'mm',    'marginLeft' => 7.1,         'marginTop' => 19,         'NX' => 3,     'NY' => 10,     'SpaceX' => 9.5,         'SpaceY' => 3.1,     'width' => 66.6,         'height' => 25.4,        'font-size' => 8),
        'L7163' => array('paper-size' => 'A4',        'metric' => 'mm',    'marginLeft' => 5,        'marginTop' => 15,         'NX' => 2,    'NY' => 7,    'SpaceX' => 25,        'SpaceY' => 0,    'width' => 99.1,        'height' => 38.1,        'font-size' => 9),
        '3422' => array('paper-size' => 'A4',        'metric' => 'mm',    'marginLeft' => 0,        'marginTop' => 8.5,         'NX' => 3,    'NY' => 8,    'SpaceX' => 0,        'SpaceY' => 0,    'width' => 70,        'height' => 35,        'font-size' => 9)
    );

    // Constructor
    function __construct($format, $unit = 'mm', $posX = 1, $posY = 1)
    {
        if (is_array($format)) {
            // Custom format
            $Tformat = $format;
        } else {
            // Built-in format
            if (!isset($this->_Avery_Labels[$format]))
                $this->Error('Unknown label format: ' . $format);
            $Tformat = $this->_Avery_Labels[$format];
        }

        parent::__construct('P', $unit, $Tformat['paper-size']);
        $this->_Metric_Doc = $unit;
        $this->_Set_Format($Tformat);
        $this->SetFont('Arial');
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        $this->_COUNTX = $posX - 2;
        $this->_COUNTY = $posY - 1;
    }

    function _Set_Format($format)
    {
        $this->_Margin_Left    = $this->_Convert_Metric($format['marginLeft'], $format['metric']);
        $this->_Margin_Top    = $this->_Convert_Metric($format['marginTop'], $format['metric']);
        $this->_X_Space     = $this->_Convert_Metric($format['SpaceX'], $format['metric']);
        $this->_Y_Space     = $this->_Convert_Metric($format['SpaceY'], $format['metric']);
        $this->_X_Number     = $format['NX'];
        $this->_Y_Number     = $format['NY'];
        $this->_Width         = $this->_Convert_Metric($format['width'], $format['metric']);
        $this->_Height         = $this->_Convert_Metric($format['height'], $format['metric']);
        $this->Set_Font_Size($format['font-size']);
        $this->_Padding        = $this->_Convert_Metric(3, 'mm');
    }

    // convert units (in to mm, mm to in)
    // $src must be 'in' or 'mm'
    function _Convert_Metric($value, $src)
    {
        $dest = $this->_Metric_Doc;
        if ($src != $dest) {
            $a['in'] = 39.37008;
            $a['mm'] = 1000;
            return $value * $a[$dest] / $a[$src];
        } else {
            return $value;
        }
    }

    // Give the line height for a given font size
    function _Get_Height_Chars($pt)
    {
        $a = array(6 => 2, 7 => 2.5, 8 => 3, 9 => 4, 10 => 5, 11 => 6, 12 => 7, 13 => 8, 14 => 9, 15 => 10);
        if (!isset($a[$pt]))
            $this->Error('Invalid font size: ' . $pt);
        return $this->_Convert_Metric($a[$pt], 'mm');
    }

    // Set the character size
    // This changes the line height too
    function Set_Font_Size($pt)
    {
        $this->_Line_Height = $this->_Get_Height_Chars($pt);
        $this->SetFontSize($pt);
    }

    // Print a label
    function Add_Label($text)
    {
        $this->_COUNTX++;
        if ($this->_COUNTX == $this->_X_Number) {
            // Row full, we start a new one
            $this->_COUNTX = 0;
            $this->_COUNTY++;
            if ($this->_COUNTY == $this->_Y_Number) {
                // End of page reached, we start a new one
                $this->_COUNTY = 0;
                $this->AddPage();
            }
        }

        $_PosX = $this->_Margin_Left + $this->_COUNTX * ($this->_Width + $this->_X_Space) + $this->_Padding;
        $_PosY = $this->_Margin_Top + $this->_COUNTY * ($this->_Height + $this->_Y_Space) + $this->_Padding;
        $this->SetXY($_PosX, $_PosY);
        $this->MultiCell($this->_Width - $this->_Padding, $this->_Line_Height, $text, 0, 'L');
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        // Disable the page scaling option in the printing dialog
        $this->_put('/ViewerPreferences <</PrintScaling /None>>');
    }
}

$pdf = new nPDF($label);
$pdf->SetTitle("Label", true);
$pdf->useHeader(false);
$pdf->useFooter(false);
$pdf->AddPage();

$pdf->SetFontSize($fontSize);

foreach ($students as $student) {
    $parent = new Parents($student->id);

    if (!$withGrade) {
        if (!$withAddress) {
            if (!$withAccount) {
                if ($nameOption === 'separated') {
                    $stringCode = "%s\n%s";
                } else {
                    $stringCode = "%s %s";
                }
                $text = sprintf($stringCode, $student->apellidos, $student->nombre);
            } else {
                if ($nameOption === 'separated') {
                    $stringCode = "%s\n%s\n%s";
                } else {
                    $stringCode = "%s\n%s %s";
                }
                $text = sprintf($stringCode, $lang->translation("Cuenta") . ": " . $student->id, $student->apellidos, $student->nombre);
            }
        } else {
            if ($nameOption === 'separated') {
                $stringCode = "%s\n%s\n%s\n%s\n%s %s %s";
            } else {
                $stringCode = "%s %s\n%s\n%s\n%s %s %s";
            }
            $text = sprintf($stringCode, $student->apellidos, $student->nombre, $parent->dir1, $parent->dir3, $parent->pueblo1, $parent->est1, $parent->zip1);
        }
    } else {
        $stringCode = $gradePlacement === 'top' ? ($nameOption === 'separated' ? "%s\n%s\n%s" : "%s\n%s %s") : ($nameOption === 'separated' ? "%s %s\n%s" : "%s %s %s");
        $grade = $gradePlacement === 'top' ? $lang->translation("Grado") . ": " . $student->grado : $student->grado;
        if (!$withAddress) {
            if (!$withAccount) {

                $text = sprintf($stringCode, $grade, $student->apellidos, $student->nombre);
            } else {
                $text = sprintf("%s\n" . $stringCode, $lang->translation("Cuenta") . ": " . $student->id, $grade, $student->apellidos, $student->nombre);
            }
        } else {
            $stringCode .= "\n%s\n%s\n%s %s %s";
            if (!$withAccount) {
                $text = sprintf($stringCode, $grade, $student->apellidos, $student->nombre, $parent->dir1, $parent->dir3, $parent->pueblo1, $parent->est1, $parent->zip1);
            } else {
                $text = sprintf("%s\n" . $stringCode, $lang->translation("Cuenta") . ": " . $student->id, $grade, $student->apellidos, $student->nombre, $parent->dir1, $parent->dir3, $parent->pueblo1, $parent->est1, $parent->zip1);
            }
        }
    }
    for ($i = 1; $i <= $repeatStudents; $i++) {
        $pdf->Add_Label($text);
    }
}




$pdf->Output();
