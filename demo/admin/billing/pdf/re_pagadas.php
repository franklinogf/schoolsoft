<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ["Re-Matriculas Pagadas ", "Paid registrations"],
    ['Cantidad', 'Amount'],
    ['Nombre', 'Name'],
    ['Fecha', 'Date'],
    ['Grado', 'Grade'],
    ['Gran Total: ', 'Grand Total: '],
    ['Tipo de Fechas', 'Type of Dates'],
    ['MENSUALIDAD', 'MONTHLY PAYMENT'],
    ['OPCIONES', 'OPTIONS'],
    [' al ', ' to '],
    ['Mes', 'Month'],
    ['Opciones', 'Options'],
    ['Agosto', 'August'],
    ['Septiembre', 'September'],
    ['Octubre', 'October'],
    ['Noviembre', 'November'],
    ['Diciembre', 'December'],
    ['Enero', 'January'],
    ['Febrero', 'February'],
    ['Marzo', 'March'],
    ['Abril', 'Abril'],
    ['Mayo', 'May'],
    ['Junio', 'June'],
    ['Julio', 'July'],
    ['NOMBRE', 'NAME'],
    ['CANTIDAD', 'AMOUNT'],
    ['GRADO', 'GRADE'],
    ['APELLIDOS', 'LAST NAME'],
    [' Mes de ', ' Month of '],
    ['Marculinos', 'Male'],
    ['DEPOSITOS', 'DEPOSITS'],
    ['HORA', 'TIME'],
    ['TRANS.', 'TRANS.'],
]);

$school = new School(Session::id());
$year = $school->info('year2');

class nPDF extends PDF
{
    function Header()
    {
        global $year;
        parent::header();
        global $lang;
        global $colegio;
        global $colegio2;
        list($y1, $y2) = explode("-", $year);
        $y3 = $y1 + 2;
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 5, $lang->translation('Re-Matriculas Pagadas ') . $year . $lang->translation(' al ') . $y2 . '-' . $y3, 0, 1, 'C');
        $this->Ln(5);
    }
}

$pdf = new nPDF();
$pdf->AliasNbPages();
$pdf->Fill();
$gt = 0;

$students2 = DB::table('year')->where([
    ['year', $year],
    ['pago', 'OK'],
    ['pm1_pago', 'OK']
])->orderBy('datem')->get();

$db = new DB();
$db->query('Truncate Re_matricula');

foreach ($students2 as $stu) {
    if ($stu->pm1_pago == 'OK') {
        $pm = $stu->pm1;
        if ($stu->pm1_fecha == $stu->pm2_fecha) {
            $pm = $pm + $stu->pm2;
        }
        if ($stu->pm1_fecha == $stu->pm3_fecha) {
            $pm = $pm + $stu->pm3;
        }
        $nom = $stu->apellidos . ' ' . $stu->nombre;
        DB::table('Re_matricula')->insert([
            'id' => $stu->id,
            'nombre' => $nom,
            'grado' => $stu->grado,
            'fecha' => $stu->pm1_fecha,
            'pago' => $pm,
            'hora' => $stu->pm1_hora,
        ]);
    }

    if ($stu->pm2_pago == 'OK' and $stu->pm1_fecha != $stu->pm2_fecha) {
        $nom = $stu->apellidos . ' ' . $stu->nombre;
        DB::table('Re_matricula')->insert([
            'id' => $stu->id,
            'nombre' => $nom,
            'grado' => $stu->grado,
            'fecha' => $stu->pm2_fecha,
            'pago' => $stu->pm2,
            'hora' => $stu->pm2_hora,
        ]);
    }

    if ($stu->pm3_pago == 'OK' and $stu->pm2_fecha != $stu->pm3_fecha) {
        $nom = $stu->apellidos . ' ' . $stu->nombre;
        DB::table('Re_matricula')->insert([
            'id' => $stu->id,
            'nombre' => $nom,
            'grado' => $stu->grado,
            'fecha' => $stu->pm3_fecha,
            'pago' => $stu->pm3,
            'hora' => $stu->pm3_hora,
        ]);
    }
}

$students2 = DB::table('year')->select("DISTINCT datem")->where([
    ['year', $year],
    ['pago', 'OK'],
    ['pm1_pago', 'OK']
])->orderBy('datem')->get();
$gt = 0;
$m = 0;
foreach ($students2 as $student2) {
    $pdf->AddPage();
    $pdf->SetTitle($lang->translation('Re-Matriculas Pagadas ') . ' ' . $year);

    $students = DB::table('Re_matricula')->where([
        ['fecha', $student2->datem]
    ])->orderBy('fecha DESC')->get();

    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(10, 5, '#', 1, 0, 'C', true);
    $pdf->Cell(15, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(90, 5, $lang->translation('Nombre'), 1, 0, 'C', true);
    $pdf->Cell(17, 5, $lang->translation('Grado'), 1, 0, 'C', true);
    $pdf->Cell(22, 5, $lang->translation('Cantidad'), 1, 0, 'C', true);
    $pdf->Cell(22, 5, $lang->translation('Fecha'), 1, 1, 'C', true);

    $pdf->SetFont('Times', '', 10);
    $count = 1;
    $t = 0;
    foreach ($students as $student) {
        $pdf->Cell(10, 5, $count, 1, 0, 'R');
        $pdf->Cell(15, 5, $student->id, 1, 0, 'C');
        $pdf->Cell(90, 5, "$student->apellidos $student->nombre", 1);
        $pdf->Cell(17, 5, $student->grado, 1, 0, 'C');
        $pdf->Cell(22, 5, '$' . $student->pago, 1, 0, 'R');
        $pdf->Cell(22, 5, $student->fecha, 1, 1, 'C');
        $count++;
        $t = $t + $student->pago;
        $gt = $gt + $student->pago;
    }
    $pdf->Cell(132, 5, $lang->translation('Total: '), 1, 0, 'R');
    $pdf->Cell(22, 5, '$' . number_format($t, 2), 1, 1, 'R');
}
if ($m == 0) {
    $pdf->AddPage();
}
$pdf->Cell(5, 5, '', 0, 1, 'R');
$pdf->Cell(132, 5, $lang->translation('Gran Total: '), 1, 0, 'R');
$pdf->Cell(22, 5, '$' . number_format($gt, 2), 1, 1, 'R');
$pdf->Output();
