<?php
require_once '../../../app.php';

use App\Models\Family;
use App\Models\Student;
use Carbon\Carbon;
use Carbon\Month;
use Classes\Controllers\School;
use Classes\Email;
use Classes\Lang;
use Classes\PDF;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

Session::is_logged();
$directory = attachments_path('statements');

if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}
$files = [];
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['NOMBRE', 'NAME'],
    ['CUENTA', 'ACCOUNT'],
    ['PAGOS', 'PAYS'],
    ['FECHA P.', 'PAY DAY'],
    ['T. PAGO', 'TIPE PAY'],
    ['DESDE', 'FROM'],
    ['HASTA', 'TO'],
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
    ['DEUDA', 'DEBT'],
    ['PAGO', 'PAY'],
    ['BALANCE', 'BALANCE SHEET'],
    ['Estado de cuenta', 'Statement'],
    ['BALANCE DEL ESTADO DE CUENTA:', 'TOTAL BALANCE SHEET:'],
    ['PAGO REQUERIDO:', 'PAYMENT REQUIRED:'],
    ['Mensaje', 'Message'],
    ['No has seleccionado el mes del estado. Por favor vuelve e int&#65533;ntalo de nuevo.', 'You have not selected the state month. Please come back and try again.'],
]);


$school = new School(Session::id());
$year = $school->info('year2');
$reply_to = $school->info('correo');
$user = $school->info('usuario');

$id = '';
$usua = '';
$est1 = [];
$est2 = [];
$est3 = [];
if (isset($_POST['envia']) && $_POST['envia'] == 'Si') {
    Manager::table('estado')->where('fecha', date('m-d-Y'))->delete();
}

date_default_timezone_set('America/Puerto_Rico');
if (isset($_POST['mes']) && $_POST['mes'] == 0) {
    echo "<br><br><center>" . $lang->translation('No has seleccionado el mes del estado. Por favor vuelve e inténtalo de nuevo.') . "</center>";
    exit;
}

function generateTable(PDF $pdf, Family $family): void
{

    global $year;
    global $lang;
    global $est1;
    global $est2;
    global $est3;

    $gr1 = '';
    $gr2 = '';
    $gr3 = '';
    $gr4 = '';
    $no1 = '';
    $no2 = '';
    $no3 = '';
    $no4 = '';
    $row11 = Manager::table('codigos')->whereRaw("idc='2' and codigo='" . $_POST['num2'] . "'")->first();
    for ($x = 0; $x <= 20; $x++) {
        $est1[$x] = '';
        $est2[$x] = '';
        $est3[$x] = '';
    }

    if ($_POST['idi'] == 'Ingles') {
        $text1 = $row11->tema2 ?? '';
    } else {
        $text1 = $row11->tema ?? '';
    }


    if (!empty($family->qpaga)) {
        $pdf->Cell(60, 5, $family->encargado, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->dir_e1, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->dir_e2, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->pue_e . ', ' . $family->esta_e . ' ' . $family->zip_e, 0, 1, 'L');
    } else {
        $pdf->Cell(60, 5, $family->madre, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->dir1, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->dir3, 0, 1, 'L');
        $pdf->Cell(60, 5, $family->pueblo1 . ', ' . $family->est1 . ' ' . $family->zip1, 0, 1, 'L');
    }
    $pdf->Cell(80, 5, $lang->translation('CUENTA') . ' # ' . $family->id, 0, 1, 'L');




    $pdf->SetFont('Times', '', 12);




    $totdeu = 0;
    $atra = 0;
    $est = 0;
    foreach ($family->kids as $row23) {
        $est = $est + 1;
        if ($est == 1) {
            $no1 = $row23->nombre . ' ' . $row23->apellidos;
            $gr1 = $row23->grado;
        }
        if ($est == 2) {
            $no2 = $row23->nombre . ' ' . $row23->apellidos;
            $gr2 = $row23->grado;
        }
        if ($est == 3) {
            $no3 = $row23->nombre . ' ' . $row23->apellidos;
            $gr3 = $row23->grado;
        }
        if ($est == 4) {
            $no4 = $row23->nombre . ' ' . $row23->apellidos;
            $gr4 = $row23->grado;
        }
        if ($_POST['conest'] == 1) {
            $pdf->Cell(80, 5, '', 0, 0, 'L');
            $pdf->Cell(90, 5, $row23->nombre . ' ' . $row23->apellidos, 0, 0, 'L');
            $pdf->Cell(20, 5, $row23->grado, 0, 1, 'c');
        }
    }
    $pdf->Cell(100, 5, $lang->translation('DESCRIPCION'), 1, 0, 'C', true);
    $pdf->Cell(26, 5, $lang->translation('DEUDA'), 1, 0, 'C', true);
    $pdf->Cell(26, 5, $lang->translation('PAGO'), 1, 0, 'C', true);
    $pdf->Cell(38, 5, $lang->translation('BALANCE'), 1, 1, 'C', true);
    $i = 0;
    list($yy2, $mm1, $dd1) = explode("-", date('Y-m-d'));

    $fec = $yy2 . '-' . $_POST['mes'] . '-' . $dd1;
    $charges = $family->charges()->whereDate('fecha_d', '<=', $fec)->get()->groupBy('codigo');

    $totdeu = 0;
    foreach ($charges as $charge) {


        $debt = $charge->sum('deuda');
        $pay = $charge->sum('pago');

        $latePayment = $charge->where('fecha_d', '<=', $fec)
            ->sum(fn($payment) => $payment->deuda - $payment->pago);

        $total = $debt - $pay;
        if ($total > 0) {
            $est1[$i] = $charge->first()->desc1;
            $est2[$i] = $debt;
            $est3[$i] = $pay;
            $totdeu += $total;

            $pdf->Cell(100, 5, $charge->first()->desc1, 0, 0, 'L');
            $pdf->Cell(26, 5, number_format($debt, 2), 0, 0, 'R');
            $pdf->Cell(26, 5, number_format($pay, 2), 0, 0, 'R');
            $pdf->Cell(38, 5, number_format($total, 2), 0, 1, 'R');

            $i++;
        }
    }

    $pdf->Cell(152, 5, $lang->translation('BALANCE DEL ESTADO DE CUENTA:') . ' ', 1, 0, 'R', true);
    $pdf->Cell(38, 5, number_format($totdeu, 2), 1, 1, 'R', true);
    $pdf->Cell(160, 5, '', 0, 1, 'R');
    $pdf->Cell(152, 5, $lang->translation('PAGO REQUERIDO:') . ' ', 0, 0, 'R');
    $pdf->Cell(38, 5, number_format($latePayment, 2), 0, 1, 'R');
    $pdf->Cell(160, 10, '', 0, 1, 'R');

    if ($_POST['num2'] > 0) {
        $pdf->Cell(189, 5, $lang->translation('Mensaje'), 1, 1, 'C');
        $pdf->SetLeftMargin(11);
        $pdf->WriteHTML($text1);
        $pdf->SetLeftMargin(10);
    }

    if ($_POST['envia'] == 'Si') {
        Manager::table('estado')->insert([
            'cta' => $family->id,
            'year' => $year,
            'gra1' => $gr1,
            'gra2' => $gr2,
            'gra3' => $gr3,
            'gra4' => $gr4,
            'nom1' => $no1,
            'nom2' => $no2,
            'nom3' => $no3,
            'nom4' => $no4,
            'mes' => $_POST['mes'],
            'fecha' => date('m-d-Y'),
        ]);

        Manager::table("estado")->where([
            ['cta', $family->id],
            ['year', $year],
            ['mes', $_POST['mes']],
        ])->update([
            'men1' => $text1,
            'bala' => $totdeu,
            'req' => $atra,
        ]);

        $n = 0;
        for ($x = 0; $x <= $i; $x++) {
            $n++;
            $n1 = 'des' . $n;
            $n2 = 'deu' . $n;
            $n3 = 'pag' . $n;
            Manager::table("estado")->where([
                ['cta', $family->id],
                ['year', $year],
                ['mes', $_POST['mes']],
            ])->update([
                $n1 => $est1[$x],
                $n2 => $est2[$x],
                $n3 => $est3[$x],
            ]);
        }
    }
}


$pdf = new PDF;
$pdf->SetTitle($lang->translation('ESTADO DE CUENTAS') . ' ' . $year);
$pdf->Fill();
$pdf->AliasNbPages();
$pdf->SetFont('Times', '', 11);



$n1 = $_POST['nombre'];
$ctas = $_POST['ctas'];
$debtType = $_POST['deuda'];

$students = Student::query()
    ->select("id")
    ->with('family.payments')
    ->distinct()
    ->when($n1 != 1 && $ctas === '', function (Builder $query) use ($n1): void {
        $query->where('id', $n1);
    })
    ->when($ctas !== '', function (Builder $query) use ($ctas): void {
        $query->where('id', $ctas);
    })
    ->get();


[$Y, $M, $D] = explode("-", date('Y-m-d'));
$fec = $year . '-' . $_POST['mes'] . '-' . $D;
foreach ($students as $student) {
    if (!$student->family) {
        continue;
    }
    $payments = $student->family->charges()
        ->orderBy('codigo')
        ->get();
    $totalDebt = $payments->sum('deuda');
    $totalPayments = $payments->sum('pago');
    $totalLatePayments = $payments->where('fecha_d', '<=', $fec)
        ->sum(fn($payment) => $payment->deuda - $payment->pago);


    // dd([$totalDebt, $totalPayments, $totalLatePayments]);

    $total = $totalDebt - $totalPayments;

    if ($total <= 0) {
        continue;
    }

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 4, $lang->translation('ESTADO DE CUENTAS'), 0, 0, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, date('m-d-Y'), 0, 0, 'C');
    $pdf->Ln(10);
    $pdf->Cell(0, 5, ucfirst(Carbon::parse(Month::fromNumber($_POST['mes']))->translatedFormat('F')), 0, 0, 'C');

    $pdf->Ln(15);
    $pdf->Cell(80, 5, $lang->translation('NOMBRE'), 1, 0, 'C', true);
    $pdf->Cell(90, 5, $lang->translation('ESTUDIANTES'), 1, 0, 'C', true);
    $pdf->Cell(20, 5, $lang->translation('GRADO'), 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 12);


    if ($totalDebt > 0 && $debtType == 1) {
        generateTable($pdf, $student->family);
    } elseif ($total > 0 && $debtType == 2) {
        generateTable($pdf, $student->family);
    } elseif ($totalLatePayments > 0 && $debtType == 3) {
        generateTable($pdf, $student->family);
    }


    $uniqueId = Str::uuid()->toString();
    $filePath = "{$directory}/$uniqueId.pdf";
    $pdf->Output("F", $filePath);
    $files[$student->id] = $filePath;
    //******************************************

    // $row4 = Manager::table('madre')->whereRaw("id='$student->id'")->orderBy('id')->first();

    // if ($deu > 0 and $_POST['enviae'] == 'Si' or $atra > 0 and $_POST['enviae'] == 'Si') {
    //     $file_name = "Statement_" . $student->id . ".pdf";
    //     $co_re = __RESEND_KEY_OTHER__;
    //     $from = "{$colegio} <" . $co_re . ">";

    //     $dir = '../../';
    //     //********************************************
    //     $uploadHost = dirname($_SERVER['SCRIPT_URI']);
    //     $target_dir = "attachments/";
    //     if (!is_dir($target_dir)) {
    //         mkdir($target_dir);
    //     }
    //     $files = [];
    //     $target_file = $file_name;
    //     $files[] = $uploadHost . '/' . $target_dir . $target_file;
    //     if (__RESEND__ == '1') {
    //         $file2 = $pdf->Output("attachments/" . $file_name, 'F');
    //     }

    //     //*********************************************
    //     $mail = new Mail();
    //     $title = $lang->translation('Estado de cuenta') . ' ' . $mes;
    //     $subject = $lang->translation('Estado de cuenta') . ' ' . $mes;
    //     $message = '';
    //     $mail->Subject = $subject;
    //     $emailsSent = 0;
    //     $emailsError = 0;
    //     $error = null;

    //     if (__PHPMAIL__ == '1') {
    //         $file = $pdf->Output("", "S");
    //         $mail->addStringAttachment($file, $file_name);
    //     }

    //     $parents = Manager::table('madre')->where('id', $student->id)->first();
    //     $emails = [
    //         ['correo' => $parents->email_p, 'nombre' => $parents->padre],
    //         ['correo' => $parents->email_m, 'nombre' => $parents->madre]
    //     ];
    //     $to = [];
    //     foreach ($emails as $email) {
    //         if ($email['correo'] !== '') {
    //             $mail->addAddress($email['correo'], $email['nombre']);
    //             $to[] = $email['correo'];
    //         }
    //     }
    //     //        $mail->addAddress("alf_med@hotmail.com", 'Alfredo Medina');
    //     $message = "<center><h1>$title</h1></center><br/><br/><p>" . nl2br($title) . "</p>";

    //     $mail->isHTML(true);
    //     $mail->Body = "
    //         <!DOCTYPE html>
    //         <html lang='" . __LANG . "'>
    //         <head>
    //             <meta charset='UTF-8'>
    //             <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    //             <title>{$title}</title>
    //         </head>
    //         <body>
    //         <center><h2>{$title}</h2></center>
    //         <br>
    //         <p>{$message}</p>  
    //         </body>
    //         </html>
    //         ";

    //     if (__PHPMAIL__ == '1') {
    //         $mail->send();
    //     }
    //     $mail->ClearAddresses();
    //     $mail->ClearAttachments();
    //     if (__RESEND__ == '1') {
    //         Manager::table('email_queue')->insert([
    //             'from' => $from,
    //             'reply_to' => $reply_to,
    //             'to' => json_encode($to),
    //             'message' => $message,
    //             'text' => '',
    //             'subject' => $subject,
    //             'attachments' => json_encode($files),
    //             'user' => $user,
    //             'year' => $year,
    //         ]);
    //     }
    // }
}

PDF::OutputFiles($files);

if ($_POST['envia'] === 'Si' || $_POST['enviae'] === 'Si') {
    foreach ($files as $familyId => $file) {
        $family = Family::where('id', $familyId)->first();
        if (!$family) {
            continue;
        }
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
        $filePath = attachments_url("statements/{$basename}");

        Email::to(['franklinomarflores@gmail.com'])
            ->subject(__('Estado de cuenta'))
            ->body(__('Adjunto el estado de cuenta para la cuenta #') . $familyId)
            ->attach($filePath, "statement_{$familyId}.pdf")
            ->queue($familyId);
    }
}
