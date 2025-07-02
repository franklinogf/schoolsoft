<?php

use App\Models\Admin;
use App\Models\Family;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Classes\Email;
use Classes\PDF;
use Classes\Session;
use Illuminate\Support\Str;

Session::is_logged();


$today = Carbon::now();
$files = [];
$school = Admin::user(Session::id())->first();

$year = $school->year2;
$chk = $school->chk;
$reply_to = $school->correo;
$user = $school->usuario;

$mes = $_REQUEST['mes'];
$student = $_REQUEST['student'] !== 'all' ? $_REQUEST['student'] : null;

[$y3, $y2] = explode("-", $year);

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, date('Y')));


//$paymentGroup = Payment::whereDate('fecha_d', '<=', $fecha)->get()->groupBy('id');
$paymentGroup = Payment::query()
    ->whereDate('fecha_d', '<=', $fecha)
    ->when($student !== 'All', function ($query) use ($student) {
        $query->where('id', $student);
    })
    ->get()->groupBy('id');


$todayFormatted = __LANG === 'es' ? $today->translatedFormat('j \d\e F \d\e Y') : $today->translatedFormat('F j, Y');

foreach ($paymentGroup as $id => $payments) {

    $debt = $payments->sum('deuda');
    $pay = $payments->sum('pago');

    $total = $debt - $pay;

    if ($total === 0) continue;


    $students = Student::byId($id)->get();


    $pdf = new PDF();
    $pdf->SetFont('Arial', '', 12);

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(0, 5, __('Carta de suspensión'), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial',  '', 12);
    $pdf->Cell(0, 5, $todayFormatted, 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, __('ESTUDIANTE ') . '(S)', 0, 1);
    $pdf->Ln(5);

    foreach ($students as $student) {
        $pdf->Cell(80, 5, $student->fullName);
        $pdf->Cell(20, 5, $student->grado, 0, 1, 'R');
    }


    $pdf->Ln(5);
    $pdf->Cell(0, 5, strtoupper(__('Cuenta')) . ' # ' . $id, 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, strtoupper(__('Estimados padres y/o encargados')), 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 6, strtoupper(__('Hemos revisado nuestras cuentas a cobrar y encontramos que usted a la fecha de hoy :date no ha efectuado el pago correspondiente al mes de', ['date' => $todayFormatted])));
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);
    $paymentsGroupedByDate = $payments->groupBy('fecha_d');
    $monthNames = [];
    $totalDebtByMonth = 0;
    $totalPayByMonth = 0;

    foreach ($paymentsGroupedByDate as $date => $payments) {

        $debt = $payments->sum('deuda');
        $pay = $payments->sum('pago');

        $totalByDate = $debt - $pay;
        $totalDebtByMonth += $debt;
        $totalPayByMonth += $pay;


        if ($totalByDate === 0) continue;

        $month = Carbon::parse($date)->translatedFormat('F');

        $monthNames[] = strtoupper($month);
    }

    $pdf->MultiCell(0, 5, implode(', ', $monthNames));
    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 5, 'EL (LA) ESTUDIANTE NO PODRA ASISTIR AL SALON DE CLASES. DEBE PRESENTAR EVIDENCIA DE PAGO PARA ENTRAR AL SALON.');
    $pdf->MultiCell(0, 5, 'FAVOR DE INCLUIR EL RECARGO CORRESPONDIENTE DE $20.00 POR ESTUDIANTE');
    $pdf->Ln(3);
    $pdf->Cell(45, 7, "SU PAGO SERIA DE: ");
    $pdf->Cell(30, 7, $totalDebtByMonth, 'B', 0, 'C');
    $pdf->Cell(45, 7, " MENSUALIDAD", 0, 1);
    $pdf->Cell(45);
    $pdf->Cell(30, 7, $totalPayByMonth, 'B', 0, 'C');
    $pdf->Cell(45, 7, " EN CARGOS", 0, 1);
    $pdf->Cell(45);
    $pdf->Cell(30, 7, $total, 'B', 0, 'C');
    $pdf->Cell(45, 7, " TOTAL", 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'NOTA:', 0, 1);
    $pdf->Ln(10);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 5, 'FAVOR DE HACER LOS ARREGLOS NECESARIOS PARA QUE SU HIJO (A) NO SE VEA AFECTADO (A)');
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'CORDIALMENTE,', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Sr. Elimer Pabon Nievez');
    $pdf->Ln(10);
    $pdf->Cell(0, 5, 'Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.');
    $uniqueId = Str::uuid()->toString();
    $filePath = "{$directory}/$uniqueId.pdf";
    $pdf->Output("F", $filePath);
    $files[$id] = $filePath;
}

PDF::OutputFiles($files);

if ($_POST['tipo'] === 'email') {
    foreach ($files as $familyId => $file) {
        $family = Family::where('id', $familyId)->first();
        $emails = [
            ['correo' => $family->email_p, 'nombre' => $family->padre],
            ['correo' => $family->email_m, 'nombre' => $family->madre]
        ];
        $to = [];
        foreach ($emails as $email) {
            if ($email['correo'] !== '') {
                $to[] = $email['correo'];
            }
        }
        $basename = basename($file);
        $filePath = attachments_url("letters/{$basename}");

        Email::to($to)
            ->subject(__('Carta de suspensión'))
            ->body(__('Adjunto la carta de suspensión para la cuenta #') . $familyId)
            ->attach($filePath, "letter_{$familyId}.pdf")
            ->queue($familyId);
    }
}
