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

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, date('Y')));

class nPDF extends PDF
{
    public function Footer(): void
    {
        $this->SetY(-80);
        $this->Cell(0, 5, 'Cordialmente,', 0, 1, 'L');
        $this->Ln(20);
        $this->Cell(0, 5, 'Director Ejecutivo', 0, 1, 'L');
    }
}

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
    $student = Student::byId($id)->first();
    $pdf = new nPDF('P');
    $pdf->SetLeftMargin(20);

    $pdf->SetFont('Arial', '', 12);

    $pdf->AddPage();
    $pdf->Cell(0, 5, __('Carta de cobro general'), 0, 1, 'C');
    $pdf->Ln(5);


    $pdf->Cell(0, 5, $todayFormatted, 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(20, 5, 'Familia: ', 0, 0);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(50, 5, "$student->apellidos ", 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 5, 'CUENTA # ' . $id, 0, 1);
    $pdf->Ln(5);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(0, 5, 'Estimados padres:', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, '¡La Paz de Cristo Resucitado sea con ustedes!', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Son nuestros mejores deseos que ustedes y todos los miembros de su familia se', 0, 1);
    $pdf->Cell(0, 5, 'encuentren bien de salud.', 0, 1);
    $pdf->Ln(5);

    $dateFormatted = __LANG === 'es' ? Carbon::parse($fecha)->translatedFormat('j \d\e F \d\e Y') : Carbon::parse($fecha)->translatedFormat('F j, Y');
    $pdf->Cell(0, 5, 'La presente es para recordarles que su cuenta refleja un balance pendiente de pago ', 0, 1);
    $pdf->Cell(0, 5, 'por la cantidad de $' . number_format($total, 2) . '.  Favor de realizar el pago en efectivo en o antes del', 0, 1);
    $pdf->Cell(0, 5, $dateFormatted, 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Para información específica sobre sus balances adeudados, agradeceremos que se ', 0, 1);
    $pdf->Cell(0, 5, 'comuniquen vía teléfono (787) 842-1331.', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Gracias anticipadas por su cooperación sobre el particular. ', 0, 1);
    $pdf->Ln(10);
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
            ->subject(__('Carta de cobro general'))
            ->body(__('Adjunto la carta de cobro para la cuenta #') . $familyId)
            ->attach($filePath, "letter_{$familyId}.pdf")
            ->queue($familyId);
    }
}
