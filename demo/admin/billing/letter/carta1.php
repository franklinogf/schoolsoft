<?php

use App\Models\Admin;
use App\Models\Family;
use App\Models\Payment;

require_once '../../../app.php';


use Classes\Email;
use Classes\PDF;
use Classes\Session;
use Illuminate\Support\Carbon;

Session::is_logged();

$today = Carbon::now();
$files = [];
$school = Admin::user(Session::id())->first();

$year = $school->year2;
$chk = $school->chk;
$reply_to = $school->correo;
$user = $school->usuario;
$directory = __DIR__ . "/attachments";

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}


$mes = $_REQUEST['mes'];

[$y3, $y2] = explode("-", $year);

[$ya, $yb, $yc] = explode("-", date('Y-m-d'));

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, $ya));

class nPDF extends PDF
{
    public function Footer()
    {
        $this->SetFont('Arial', '', 11);
        $this->SetY(-40);
        $this->Cell(0, 5, __('Cordialmente'), 0, 1);
        $this->Ln(8);
        $this->Cell(0, 5, __('Oficina de finanzas'));
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, __('Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificación.'));
    }
}

$paymentGroup = Payment::whereDate('fecha_d', '<=', $fecha)->get()->groupBy('id');

$todayFormatted = __LANG === 'es' ? $today->translatedFormat('j \d\e F \d\e Y') : $today->translatedFormat('F j, Y');

foreach ($paymentGroup as $id => $payments) {
    $family = Family::where('id', $id)->first();
    $pdf = new nPDF('P');
    $pdf->SetFont('Arial', '', 12);

    $debt = $payments->sum('deuda');
    $pay = $payments->sum('pago');

    $total = $debt - $pay;


    if ($total === 0) continue;

    $pdf->AddPage();
    $pdf->Cell(0, 5, __('Primer aviso de cobro'), 0, 1);
    $pdf->Ln(5);

    $pdf->Cell(0, 5, $todayFormatted, 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, __('Padre, Madre o Encargado'), 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(0, 5, $family->madre ?? '', 0, 1);
    $pdf->Cell(0, 5, $family->padre ?? '', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 5, strtoupper(__('Cuenta')) . ' # ' . $id, 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 6, strtoupper(__('Hemos revisado nuestras cuentas a cobrar y encontramos que usted a la fecha de hoy :date no ha efectuado el pago correspondiente al mes de', ['date' => $todayFormatted])));
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);

    $paymentsGroupedByDate = $payments->groupBy('fecha_d');

    foreach ($paymentsGroupedByDate as $rd => $payments) {

        $debt = $payments->sum('deuda');
        $pay = $payments->sum('pago');

        $totalByDate = $debt - $pay;


        if ($totalByDate === 0) continue;
        $month = Carbon::parse($rd)->translatedFormat('F');
        [$y, $m] = explode("-", $rd);

        $y4 = $m < 6 ? "20$y2" : "20$y3";

        if ($m == '06') {
            $month = __('Matrícula');
        }

        $pdf->Cell(37, 5, strtoupper("$month $y4"));
        $pdf->Cell(90, 5, '.......................................................................');
        $pdf->Cell(20, 5, number_format($totalByDate, 2), 0, 1, 'R');
    }

    $pdf->Cell(37, 5, strtoupper(__('Balance')));
    $pdf->Cell(90, 5, '.......................................................................');
    $pdf->Cell(20, 5, number_format($total, 2), 0, 1, 'R');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, strtoupper(__('Nota importante')), 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 7, strtoupper(__('1. Despues del dia 10 de cada mes se cobraran $:amount de cargos por demora por cuenta.', ['amount' => $chk])));
    $pdf->Ln(3);
    $pdf->MultiCell(0, 7, strtoupper(__('2. Los pagos pueden hacerse mediante tarjeta de credito, ath, athmovil business, efectivo, giro postal.')));
    $pdf->Ln(3);
    $pdf->MultiCell(0, 7, strtoupper(__('3. Favor de hacer los arreglos pertinentes para que los servicios educativos de su hijo(a) no se vean afectados.')));
    $pdf->Ln(3);
    $filePath = "{$directory}/letter1_{$id}.pdf";
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

        Email::to($to)
            ->subject(__('Primer aviso de cobro'))
            ->body(__('Adjunto el primer aviso de cobro para la cuenta #') . $familyId)
            ->attach($file, "letter_{$familyId}.pdf")
            ->queue($familyId);
    }
}
