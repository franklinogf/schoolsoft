<?php
require_once '../../../../app.php';

use Classes\Controllers\Parents;
use Classes\Controllers\School;
use Classes\PDF;
use Classes\Lang;
use Classes\Session;
use Classes\Controllers\Student;
use Classes\DataBase\DB;
use Classes\Util;

Session::is_logged();

$lang = new Lang([
    ["Hoja de matrícula", "Enrollment"],

]);
$school = new School();
$year = $school->year();
$todayDate = $_POST['todayDate'] === 'si' ? true : false;
$enrollmentYear = $_POST['enrollmentYear'];
$option = $_POST['option'];

$pdf = new PDF();
$pdf->useFooter(false);
$pdf->SetAutoPageBreak(false);
$pdf->SetTitle($lang->translation("Hoja de matrícula"), true);
if ($option === 'student') {
    $studentMt = $_POST['student'];
    $student = new Student($studentMt);
    $students = [$student];
} else {
    $grade = $_POST['grade'];
    $students = DB::table("year")->where([
        ['grado', $grade],
        ['year', $year],
        ['activo', '']
    ])->whereRaw("AND grado NOT LIKE '%12%'")->orderBy("grado, apellidos")->get();
}

foreach ($students as $student) {

    $parent = new Parents($student->id);
    $pdf->addPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, $lang->translation("Hoja de matrícula"), 0, 1, 'C');
    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(40, 5, 'NUMERO DE CUENTA: ____________', 0, 0);
    $pdf->Cell(95, 5, '', 0, 0, 'R');
    $pdf->Cell(40, 5, utf8_decode('AÑO ACADEMICO: ____________'), 0, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(44, -5, '', 0, 0, 'R');
    $pdf->Cell(130, -5, $student->id, 0, 0);
    $pdf->Cell(20, -5, $enrollmentYear === 'thisYear' ? $year : Util::getNextYear($year), 0, 1);
    $pdf->Cell(7, 5, '', 0, 1, 'R');
    $pdf->Cell(7, 2, '', 0, 1, 'R');
    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(110, 5, 'NUMERO DE ESTUDIANTE: ___________________', 0, 0);
    $pdf->Cell(70, 5, 'FECHA DE MATRICULA: ____________________', 0, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(55, -5, '', 0, 0, 'R');
    $pdf->Cell(70, -5, 'XXX-XX-' . Util::ssLast4Digits($student->ss), 0, 0);
    $pdf->Cell(40, -5, '', 0, 0, 'R');
    $pdf->Cell(50, -5, $todayDate ? Util::date() : '', 0, 1);
    $pdf->Ln(7);
    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(133, 5, 'Nombre del Estudiante: ____________________________________________________', 0, 0);
    $pdf->Cell(20, 5, 'Sexo: _____', 0, 0);
    $pdf->Cell(20, 5, 'Grado Actual: _______', 0, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(38, -5, '', 0, 0, 'R');
    $pdf->Cell(108, -5, utf8_decode("$student->apellidos $student->nombre"), 0, 0);

    $pdf->Cell(32, -5, Util::gender($student->genero), 0, 0);
    $pdf->Cell(20, -5, $student->grado, 0, 1);
    $pdf->Cell(7, 5, '', 0, 1, 'R');
    $pdf->Cell(7, 5, '', 0, 1, 'R');

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 10);
    $pdf->Cell(21, 10, 'Edad: ____ ', 0, 0);
    $pdf->Cell(64, 10, 'Fecha de Nacimiento: _______________ ', 0, 0);
    $pdf->Cell(46, 10, 'Grado que Solicita: ________', 0, 0);
    $pdf->Cell(40, 10, 'Hermanos: _____   Hermanas: _____', 0, 1);
    $pdf->SetFont('Times', '', 10);

    $pdf->SetY($pdf->GetY() - 10);
    $pdf->Cell(12);
    $pdf->Cell(47, 10, Util::getAge($student->fecha));
    $pdf->Cell(62, 10, $student->fecha);
    //  $pdf->Cell(62,10,'',0,0);
    $pdf->Cell(30, 10, $enrollmentYear === 'thisYear' ? substr($student->grado, 0, 2) : Util::getNextGrade($student->grado, true), 0, 1);

    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 190, 34);
    $pdf->SetFont('Times', 'B', 10);

    $pdf->Cell(190, 6, 'Escuela de Procedencia (Nuevo Ingreso): _____________________________________________________________________', 0, 1);
    $pdf->Cell(190, 6, utf8_decode('¿Padece alguna Condición Física?: ____ Especifique: ___________________________________________________________'), 0, 1);
    $pdf->Cell(190, 6, utf8_decode('Tratamiento Médico si alguno: ______________________________________________________________________________'), 0, 1);
    $pdf->Cell(90, 6, utf8_decode('Autorización para medicamentos: _____'), 0, 0);
    $pdf->Cell(90, 6, utf8_decode('Autorización para Fotos: _____'), 0, 1);
    $pdf->Cell(190, 10, utf8_decode('¿Tiene hermanos en el Colegio?: _____________________________________________________________________________'), 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(190, 10, 'Padre o encargado: _______________________________________ Indique: __________________ Celular: ______________', 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(32, -10, '', 0, 0);
    $pdf->Cell(90, -10, utf8_decode($parent->padre), 0, 0);
    $pdf->Cell(42, -10, '', 0, 0);
    $pdf->Cell(20, -10, $parent->cel_p, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Dirección: _______________________________________________________________________________________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(20, -10, '', 0, 0);
    $pdf->Cell(190, -10, "$parent->dir2 $parent->dir4 $parent->pueblo2 $parent->est2 $parent->zip2", 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Correo Electrónico: _______________________________________________________________________________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(35, -10, '', 0, 0);
    $pdf->Cell(190, -10, $parent->email_p, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Ocupación: ___________________________________________________________ Teléfono trabajo: ___________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
    
    $pdf->Cell(20, -10, '', 0, 0);
    $pdf->Cell(135, -10, $parent->trabajo_p, 0, 0);
    $pdf->Cell(190, -10, $parent->tel_t_p, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Nombre de La Madre: __________________________________________________ Número Celular: ___________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
   
    $pdf->Cell(37, -10, '', 0, 0);
    $pdf->Cell(120, -10, utf8_decode($parent->madre), 0, 0);
    $pdf->Cell(100, -10, $parent->cel_m, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Dirección: _______________________________________________________________________________________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(20, -10, '', 0, 0);
    $pdf->Cell(190, -10, "$parent->dir1 $parent->dir3 $parent->pueblo1 $parent->est1 $parent->zip1", 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Correo Electrónico: _______________________________________________________________________________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(35, -10, '', 0, 0);
    $pdf->Cell(190, -10, $parent->email_m, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 10, utf8_decode('Ocupación: ___________________________________________________________ Teléfono trabajo: ___________________'), 1, 1);
    $pdf->SetFont('Times', '', 10);
   
    $pdf->Cell(20, -10, '', 0, 0);
    $pdf->Cell(135, -10, $parent->trabajo_m, 0, 0);
    $pdf->Cell(190, -10, $parent->tel_t_m, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 10, '', 0, 1);
    $pdf->Cell(190, 8, 'El Estudiante actualmente vive con: ________________________ Indique: _________________________________________', 1, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(58, -8, '', 0, 0);
    $pdf->Cell(190, -8, $student->vivecon, 0, 1);

    $pdf->SetFont('Times', 'B', 10);
    $pdf->Cell(32, 8, '', 0, 1);
    $pdf->Cell(190, 20, '', 1, 1);
    $pdf->Cell(190, -30, utf8_decode('*Estudio Socio-Económico: Ingreso Anual de la Familia: _____________________  Grupo familiar: _____________'), 0, 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(32, 12, '', 0, 1);
    $pdf->Cell(100, 10, '', 0, 0);
    $soc = '';
    $fam = '';
    if ($reg[44] > 0) {
        $fam = $reg[44];
    }
    if ($reg[40] + $reg[41] > 0) {
        $soc = $reg[40] + $reg[41];
    }
    $pdf->Cell(60, 5, $parent->sueldop + $parent->sueldom, 0, 0);
    $pdf->Cell(90, 5, $parent->nfam, 0, 1);
    $pdf->Cell(58, 5, utf8_decode('* Esta información solo se utilizara para fines estadísticos del Departamento de Educación. El colegio tiene que preparar anualmente'), 0, 1);
    $pdf->Cell(58, 5, utf8_decode('* un estudio Socio-Económico el cual es un requisito'), 0, 1);

    $pdf->Ln(5);
    $pdf->Rect($pdf->GetX(), $pdf->GetY(), 63, 55);
    $pdf->Rect($pdf->GetX()+63, $pdf->GetY(), 64, 55);
    $pdf->Rect($pdf->GetX()+127, $pdf->GetY(), 63, 55);

  

    $pdf->Cell(63, 6, '', 0, 0);
    $pdf->Cell(64, 6, 'Costo Matricula _____________________', 0, 0);
    $pdf->Cell(63, 6, 'Fecha de Pago', 0, 1, 'R');

    $pdf->Cell(63, 6, '________________________________', 0, 0, 'C');
    $pdf->Cell(64, 6, 'Cuota Familiar: _____________________', 0, 0);
    $pdf->Cell(63, 6, 'Primer Pago: _________ ________', 0, 1);

    $pdf->Cell(63, 6, 'Firma del Padre o Persona Autorizada', 0, 0, 'C');
    $pdf->Cell(64, 6, utf8_decode('Cuota Graduación: __________________'), 0, 0);
    $pdf->Cell(63, 6, '', 0, 1);

    $pdf->Cell(63, 6, '', 0, 0);
    $pdf->Cell(64, 6, 'Examen Seguimiento: _______________', 0, 0);
    $pdf->Cell(63, 6, 'Segundo Pago: ________ ________', 0, 1);

    $pdf->Cell(63, 6, '', 0, 0, 'C');
    $pdf->Cell(64, 6, 'Recargos: _________________________', 0, 0);
    $pdf->Cell(63, 6, '', 0, 1);

    $pdf->Cell(63, 6, '________________________________', 0, 0, 'C');
    $pdf->Cell(64, 6, 'Otros: ____________________________', 0, 0);
    $pdf->Cell(63, 6, 'Tercer Pago: __________ ________', 0, 1);

    $pdf->Cell(63, 6, 'Firma Director(a) o Persona Autorizada', 0, 0, 'C');
    $pdf->Cell(12, 6, '', 0, 0);
    $pdf->Cell(52, 6, 'TOTAL: ___________________', 0, 0);
    $pdf->Cell(63, 12, '__________________________', 0, 1, 'C');

    $pdf->Cell(63, 3, '', 0, 0, 'C');
    $pdf->Cell(64, 3, 'Fecha de Pago: ____________________', 0, 0);
    $pdf->Cell(63, 3, 'Firma', 0, 1, 'C');
}

$pdf->Output();
