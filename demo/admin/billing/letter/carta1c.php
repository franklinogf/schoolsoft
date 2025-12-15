<?php

use App\Models\Admin;
use App\Models\Family;
use App\Models\Payment;

use Classes\Email;
use Classes\PDF;
use Classes\Session;
use Classes\DataBase\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

Session::is_logged();

$today = Carbon::now();
$files = [];
$school = Admin::user(Session::id())->first();

$year = $school->year2;
$chk = $school->chk;
$reply_to = $school->correo;
$user = $school->usuario;

$colegio = DB::table('colegio')->where([
    ['usuario', 'administrador']
])->orderBy('id')->first();


$mes = $_REQUEST['mes'];
$student = $_REQUEST['student'] !== 'all' ? $_REQUEST['student'] : null;

$MES = array(
	'enero',
	'febrero',
	'marzo',
	'abril',
	'mayo',
	'junio',
	'julio',
	'agosto',
	'septiembre',
	'octubre',
	'noviembre',
	'diciembre'
);


[$y3, $y2] = explode("-", $year);

$fechaHoy = date('j') . ' de ' . $MES[date('n') - 1] . ' de ' . date('Y');
$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, date('Y')));

class nPDF extends PDF
{
    public function Footer()
    {
        global $colegio;
        $this->SetFont('Arial', '', 11);
        $this->SetY(-115);
        $this->Cell(0, 5, __('Cordialmente, '), 0, 1);
        $this->Ln(3);
        $this->Cell(0, 5, __(utf8_encode('Administración')), 0, 1);
        $this->Ln(3);
        $this->Cell(0, 5, __(utf8_encode($colegio->colegio)));
        $this->Ln(40);
    }
}


//        $query->where('id', $student);
//    ->whereDate(['fecha_d', '<=', $fecha],['baja', ''])

$paymentGroup = Payment::query()
    ->where([
        ['baja', ''],
        ['year', $year]
    ])
    ->whereDate('fecha_d', '<=', $fecha)
    ->when($student !== 'All', function ($query) use ($student) {
        $query->where('id', $student);
    })
    ->get()->groupBy('id');

//->when($student !== null, function ($query) use ($student) {


$todayFormatted = __LANG === 'es' ? $today->translatedFormat('j \d\e F \d\e Y') : $today->translatedFormat('F j, Y');

foreach ($paymentGroup as $id => $payments) {


    $debt = $payments->sum('deuda');
    $pay = $payments->sum('pago');

    $total = $debt - $pay;



    if ($total <= 0) continue;
    $family = Family::where('id', $id)->first();

    $pdf = new nPDF('P');
    $pdf->SetFont('Arial', '', 12);

    $pdf->AddPage();
    $pdf->Cell(0, 5, __('Aviso de cobro'), 0, 1);
    $pdf->Ln(5);

    $pdf->Cell(0, 5, $todayFormatted, 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, __('Padre, madre o encargado'), 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(0, 5, $family->madre ?? '', 0, 1);
    $pdf->Cell(0, 5, $family->padre ?? '', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 5, strtoupper(__('Cuenta')) . ' # ' . $id, 0, 1);
    $pdf->Ln(5);
    $pdf->MultiCell(0, 6, strtoupper(__('Hemos revisado nuevamente nuestras cuentas a cobrar y encontramos que usted a la fecha de hoy '.$fechaHoy.' no ha efectuado el pago correspondiente al mes de:')));
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);

    $paymentsGroupedByDate = $payments->groupBy('fecha_d');

    foreach ($paymentsGroupedByDate as $date => $payments) {

        $debt = $payments->sum('deuda');
        $pay = $payments->sum('pago');

        $totalByDate = $debt - $pay;


        if ($totalByDate <= 0) continue;
        $month = Carbon::parse($date)->translatedFormat('F');
        [$y, $m] = explode("-", $date);

        $y4 = $m < 6 ? "20$y2" : "20$y3";

        if ($m == '06') {
            $month = __(utf8_encode('Matrícula'));
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
	$pdf->Cell(0, 7, '1. ENVIAR EL PAGO EN O ANTES DEL 15 DE '.strtoupper($MES[$mes -1]).' '.date('Y').'.', 0, 1);
    $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 5, __(utf8_encode('Si usted ha realizado el pago antes mencionado, por favor hacer caso omiso a esta notificación.')));
//    $pdf->MultiCell(0, 7, strtoupper(__('2. Los pagos pueden hacerse mediante tarjeta de credito, ath, athmovil business, efectivo, giro postal.')));
    $pdf->Ln(3);
//    $pdf->MultiCell(0, 7, strtoupper(__('3. Favor de hacer los arreglos pertinentes para que los servicios educativos de su hijo(a) no se vean afectados.')));
    $pdf->Ln(3);
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
            ->subject(__('Aviso de cobro'))
            ->body(__('Adjunto el Aviso de cobro para la cuenta #') . $familyId)
            ->attach($filePath, "letter_{$familyId}.pdf")
            ->queue($familyId);
    }
}
