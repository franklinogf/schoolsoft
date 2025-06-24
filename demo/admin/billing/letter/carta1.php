<?php

use App\Models\Family;

require_once '../../../app.php';
date_default_timezone_set('America/Puerto_Rico');

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Email;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;
use Illuminate\Support\Carbon;

Session::is_logged();

$today = Carbon::now();
$files = [];
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['Primer aviso de cobro', 'First collection notice'],
    ['CUENTA', 'ACCOUNT'],
    ['PAGOS', 'PAYS'],
    ['es', 'in'],
    ["HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY ", "WE HAVE REVIEWED OUR ACCOUNTS RECEIVABLE AND FOUND THAT YOU TO THE TODAY'S DATE "],
    ['Padre, Madre o Encargado', 'Father, Mother or Guardian'],
    [' NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:', ' YOU HAVE NOT MADE THE PAYMENT FOR THE MONTH OF:'],
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
    ['GRADO', 'GRADE'],
    ['Matri/Junio', 'Regis/June'],
    ['ESTUDIANTES', 'STUDENTS'],
    ['DESCRIPCION', 'DESCRIPTION'],
    ['NOTA IMPORTANTE:', 'IMPORTANT NOTE:'],
    ['1. DESPUES DEL DIA 10 DE CADA MES SE COBRARAN $', '1. AFTER THE 10TH OF EACH MONTH A $'],
    [' DE CARGOS POR DEMORA POR CUENTA.', ' LATE CHARGE WILL BE CHARGED FOR ACCOUNT.'],
    ['BALANCE', 'BALANCE SHEET'],
    ['Estado de cuenta', 'Statement'],
    ['BALANCE DEL ESTADO DE CUENTA:', 'TOTAL BALANCE SHEET:'],
    ['PAGO REQUERIDO:', 'PAYMENT REQUIRED:'],
    ['2. LOS PAGOS PUEDEN HACERSE MEDIANTE TARJETA DE CREDITO, ATH, ATHMOVIL BUSINESS, EFECTIVO, GIRO POSTAL.', '2. PAYMENTS CAN BE MADE BY CREDIT CARD, ATH, ATHMOVIL BUSINESS, CASH, MONEY ORDER.'],
    ['3. FAVOR DE HACER LOS ARREGLOS PERTINENTES PARA QUE LOS SERVICIOS EDUCATIVOS DE SU HIJO(A) NO SE VEAN AFECTADOS.', '3. PLEASE MAKE ARRANGEMENTS SO THAT THE EDUCATIONAL SERVICES OF YOUR CHILD WILL NOT BE AFFECTED.'],
    ['CORDIALMENTE', 'CORDIALLY'],
    ['OFICINA DE FINANZAS', 'FINANCE OFFICE'],
    ['Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.', 'If you have made the aforementioned payment, please ignore this notification.'],
]);

$db = new DB();
$col = db::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $col->colegio;

$school = new School(Session::id());
$year = $school->info('year2');
$chk = $school->info('chk');
$reply_to = $school->info('correo');
$user = $school->info('usuario');

$mes = $_REQUEST['mes'];
list($y3, $y2) = explode("-", $year);
if ($mes < 6) {
    $y1 = '20' . $y2;
} else {
    $y1 = '20' . $y3;
}
list($ya, $yb, $yc) = explode("-", date('Y-m-d'));

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, $ya));
$fechaFinal = "";


if ($mes > 6) {
    $year1 = "{$year[0]}{$year[1]}";
    $fechaFinal = "";
    // $fechaFinal = "AND fecha_d>='$year1-07-01'";
}
class nPDF extends PDF
{
    public function Footer()
    {
        global $lang;
        $this->SetFont('Arial', '', 11);
        $this->SetY(-40);
        $this->Cell(0, 5, __('CORDIALMENTE'), 0, 1);
        $this->Ln(8);
        $this->Cell(0, 5, __('OFICINA DE FINANZAS'));
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, __('Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.'));
    }
}


$MES = [
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
];

$result = DB::table('pagos')->select("DISTINCT id")
    ->whereRaw("fecha_d <= '$fecha' AND year = '$year' and baja=''")->orderBy('id')->get();

foreach ($result as $r) {
    $pdf = new nPDF('P');
    $pdf->SetFont('Arial', '', 12);
    $rs = DB::table('pagos')->select("DISTINCT fecha_d")
        ->whereRaw("id='$r->id' AND fecha_d <= '$fecha' AND year = '$year' $fechaFinal and baja=''")->orderBy('fecha_d')->get();
    $total = 0;
    if (count($rs) > 0) {
        foreach ($rs as $rd) {
            $deudas = 0;
            $pagos = 0;
            $p = DB::table('pagos')
                ->whereRaw("fecha_d='$rd->fecha_d' AND id='$r->id' AND year = '$year' AND fecha_d <= '$fecha' $fechaFinal and baja=''")->get();
            foreach ($p as $row) {
                $deudas += $row->deuda;
                $pagos += $row->pago;
            }
            $total = $total + $deudas - $pagos;
        }
        if ($total != 0) {
            $pdf->AddPage();
            $pdf->Cell(0, 5, __('Primer aviso de cobro'), 0, 1);
            $pdf->Ln(5);
            $fechaHoy = __LANG === 'es' ? $today->translatedFormat('j \d\e F \d\e Y') : $today->translatedFormat('F j, Y');
            $pdf->Cell(0, 5, $fechaHoy, 0, 1);
            $pdf->Ln(5);
            $pdf->Cell(0, 5, __('Padre, Madre o Encargado'), 0, 1);
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 12);
            $estu = DB::table('madre')
                ->whereRaw("id='$r->id'")->first();
            $pdf->Cell(0, 5, $estu->madre ?? '', 0, 1);
            $pdf->Cell(0, 5, $estu->padre ?? '', 0, 1);
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 5, __('CUENTA') . ' # ' . $r->id, 0, 1);
            $pdf->Ln(5);
            $pdf->MultiCell(0, 6, __('HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY ') . $fechaHoy . __(' NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:'));
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 12);
            $TOTAL = 0;
            foreach ($rs as $rd) {
                $deudas = 0;
                $pagos = 0;
                $p = DB::table('pagos')
                    ->whereRaw("fecha_d='$rd->fecha_d' AND id='$r->id' AND year = '$year' AND fecha_d <= '$fecha' $fechaFinal and baja=''")->get();
                foreach ($p as $row) {
                    $deudas += $row->deuda;
                    $pagos += $row->pago;
                }
                $total = $deudas - $pagos;
                $TOTAL += $total;
                if ($total != 0) {
                    $MES1 = strtoupper(Carbon::parse($rd->fecha_d)->translatedFormat('F'));
                    list($y, $m, $d) = explode("-", $rd->fecha_d);
                    if ($m < 6) {
                        $y4 = ' 20' . $y2;
                    } else {
                        $y4 = ' 20' . $y3;
                    }

                    if ($MES1 == __('Junio') . ' ' . $y4) {
                        $MES1 = __('MATRICULA');
                    }

                    $pdf->Cell(37, 5, __($MES1) . ' ' . $y4);
                    $pdf->Cell(90, 5, '.......................................................................');
                    $pdf->Cell(20, 5, number_format($total, 2), 0, 1, 'R');
                }
            }
            $pdf->Cell(37, 5, __('BALANCE'));
            $pdf->Cell(90, 5, '.......................................................................');
            $pdf->Cell(20, 5, number_format($TOTAL, 2), 0, 1, 'R');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Ln(5);
            $pdf->Cell(0, 5, __('NOTA IMPORTANTE:'), 0, 1);
            $pdf->Ln(5);
            $pdf->MultiCell(0, 7, __('1. DESPUES DEL DIA 10 DE CADA MES SE COBRARAN $') . $chk . __(' DE CARGOS POR DEMORA POR CUENTA.'));
            $pdf->Ln(3);
            $pdf->MultiCell(0, 7, __('2. LOS PAGOS PUEDEN HACERSE MEDIANTE TARJETA DE CREDITO, ATH, ATHMOVIL BUSINESS, EFECTIVO, GIRO POSTAL.'));
            $pdf->Ln(3);
            $pdf->MultiCell(0, 7, __('3. FAVOR DE HACER LOS ARREGLOS PERTINENTES PARA QUE LOS SERVICIOS EDUCATIVOS DE SU HIJO(A) NO SE VEAN AFECTADOS.'));
            $pdf->Ln(3);
            $filePath = __DIR__ . '/attachments/letter_' . $r->id . '.pdf';
            $pdf->Output("F", $filePath);
            $files[$r->id] = $filePath;
        }
    }
}

PDF::OutputFiles($files);

if ($_POST['tipo'] === 'email') {
    foreach ($files as $familyId => $file) {
        $family = Family::find($familyId)->first();
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

        Email::to('franklinomarflores@gmail.com')
            ->subject(__('Primer aviso de cobro'))
            ->body(__('Adjunto el primer aviso de cobro para la cuenta #') . $familyId)
            ->attach($file, 'letter_' . $familyId . '.pdf')
            ->queue();
    }
}
