<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Mail;
use Classes\PDF;
use Classes\Session;

Session::is_logged();
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['AVISO DE COBRO', 'COLLECTION NOTICE'],
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
    ['ESTUDIANTE ', 'STUDENT '],
]);
$db = new DB();
$col = db::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $col->colegio;

$school = new School(Session::id());
$year = $school->info('year2');
$chk = $school->info('chk');
$reply_to = $school->info('correo');
$user = $school->info('usuario');

//$not1 = $reg->not1;
//$not2 = $reg->not2;

$mes = $_REQUEST['mes'];
list($y3, $y2) = explode("-", $year);
if ($mes < 6) {
    $y1 = '20' . $y2;
} else {
    $y1 = '20' . $y3;
}
//	$y1 = '2024';
list($ya, $yb, $yc) = explode("-", date('Y-m-d'));

$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, $ya));

//$fecha = date('Y-m-d', mktime(0, 0, 0, $mes, 1, $y1));
$fechaFinal = "";

if ($mes > 6) {
    $fechaFinal = "";
}
class nPDF extends PDF
{

    function Header()
    {
        parent::header();
    }
    function Footer()
    {
        global $lang;
        $this->SetFont('Arial', '', 11);
        $this->SetY(-40);
        $this->Cell(0, 5, $lang->translation('CORDIALMENTE'), 0, 1);
        $this->Ln(8);
        $this->Cell(0, 5, $lang->translation('OFICINA DE FINANZAS'));
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 5, utf8_encode($lang->translation('Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificación.')));
    }
}

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
//$pdf = new PDF();
//$result = mysql_query("SELECT DISTINCT id FROM pagos WHERE fecha_d <= '$fecha' AND year = '$year' and baja=''", $con);

$pdf = new nPDF();
$result = DB::table('pagos')->select("DISTINCT id")
    ->whereRaw("fecha_d <= '$fecha' AND year = '$year' and baja=''")->orderBy('id')->get();

foreach ($result as $r) {
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
            $total = $total + ($deudas - $pagos);
        }
        if ($total != 0) {
            $pdf->AddPage();
            $pdf->Cell(0, 5, $lang->translation('AVISO DE COBRO'), 0, 1);
            $pdf->Ln(5);
            if ($lang->translation('es') == 'es') {
                $fechaHoy = date('j') . ' de ' . $MES[date('n') - 1] . ' de ' . date('Y');
            } else {
                $fechaHoy = $lang->translation($MES[date('n') - 1]) . ' ' . date('j') . ', ' . date('Y');
            }
            $pdf->Cell(0, 5, $fechaHoy, 0, 1);
            $pdf->Ln(5);
            $pdf->Cell(0, 5, $lang->translation('ESTUDIANTE ') . '(S)', 0, 1);
            $pdf->Ln(3);
            $pdf->SetFont('Arial', 'B', 12);
            $e = DB::table('year')
                ->whereRaw("id='$r->id' AND year='$year'")->get();
            foreach ($e as $estu) {
                $pdf->Cell(80, 5, "$estu->nombre $estu->apellidos", 0, 0);
                $pdf->Cell(20, 5, "$estu->grado", 0, 1);
            }
            $pdf->Ln(3);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 5, $lang->translation('CUENTA') . ' # ' . $r->id, 0, 1);
            $pdf->Ln(5);
            $pdf->MultiCell(0, 6, $lang->translation('HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY ') . $fechaHoy . $lang->translation(' NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:'));
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
                    $MES1 = strtoupper($MES[date('n', strtotime($rd->fecha_d)) - 1]);
                    list($y, $m, $d) = explode("-", $rd->fecha_d);
                    if ($m < 6) {
                        $y4 = ' 20' . $y2;
                    } else {
                        $y4 = ' 20' . $y3;
                        //                       $y4 = ' 2024';
                    }
                    if ($MES1 == $lang->translation('Junio') . ' ' . $y4) {
                        $MES1 = $lang->translation('MATRICULA');
                    }
                    $pdf->Cell(37, 5, $lang->translation($MES1) . ' ' . $y4);
                    $pdf->Cell(87, 5, '.......................................................................');
                    $pdf->Cell(20, 5, number_format($total, 2), 0, 1, 'R');
                }
            }
            $pdf->Cell(37, 5, $lang->translation('BALANCE'));
            $pdf->Cell(90, 5, '.......................................................................');
            $pdf->Cell(20, 5, number_format($TOTAL, 2), 0, 1, 'R');
            $pdf->SetFont('Arial', '', 10);
            $pdf->Ln(5);
            $pdf->Cell(0, 5, $lang->translation('NOTA IMPORTANTE:'), 0, 1);
            $pdf->Ln(5);
            $pdf->MultiCell(0, 7, $lang->translation('1. DESPUES DEL DIA 10 DE CADA MES SE COBRARAN $') . $chk . $lang->translation(' DE CARGOS POR DEMORA POR CUENTA.'));
            $pdf->Ln(3);
            $pdf->MultiCell(0, 7, $lang->translation('2. LOS PAGOS PUEDEN HACERSE MEDIANTE TARJETA DE CREDITO, ATH, ATHMOVIL BUSINESS, EFECTIVO, GIRO POSTAL.'));
            $pdf->Ln(3);
            $pdf->MultiCell(0, 7, $lang->translation('3. FAVOR DE HACER LOS ARREGLOS PERTINENTES PARA QUE LOS SERVICIOS EDUCATIVOS DE SU HIJO(A) NO SE VEAN AFECTADOS.'));
            $pdf->Ln(3);
        }
    }
}

$pdf->Output();


if ($_POST['tipo'] == 'email') {
    $result = DB::table('pagos')->select("DISTINCT id")
        ->whereRaw("fecha_d <= '$fecha' AND year = '$year' and baja=''")->orderBy('id')->get();
    //$from = "{$colegio} <cdp@schoolsoftusa.com>";

    foreach ($result as $r) {
        $pdf = new PDF();
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
                $total = $total + ($deudas - $pagos);
            }
            if ($total != 0) {
                $pdf->AddPage();
                $pdf->Cell(0, 5, $lang->translation('AVISO DE COBRO'), 0, 1);
                $pdf->Ln(5);
                if ($lang->translation('es') == 'es') {
                    $fechaHoy = date('j') . ' de ' . $MES[date('n') - 1] . ' de ' . date('Y');
                } else {
                    $fechaHoy = $lang->translation($MES[date('n') - 1]) . ' ' . date('j') . ', ' . date('Y');
                }
                $pdf->Cell(0, 5, $fechaHoy, 0, 1);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, $lang->translation('ESTUDIANTE ') . '(S)', 0, 1);
                $pdf->Ln(3);
                $pdf->SetFont('Arial', 'B', 12);
                $e = DB::table('year')
                    ->whereRaw("id='$r->id' AND year='$year'")->get();
                foreach ($e as $estu) {
                    $pdf->Cell(80, 5, "$estu->nombre $estu->apellidos", 0, 0);
                    $pdf->Cell(20, 5, "$estu->grado", 0, 1);
                }
                $pdf->Ln(3);
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 5, $lang->translation('CUENTA') . ' # ' . $r->id, 0, 1);
                $pdf->Ln(5);
                $pdf->MultiCell(0, 6, $lang->translation('HEMOS REVISADO NUESTRAS CUENTAS A COBRAR Y ENCONTRAMOS QUE USTED A LA FECHA DE HOY ') . $fechaHoy . $lang->translation(' NO HA EFECTUADO EL PAGO CORRESPONDIENTE AL MES DE:'));
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
                        $MES1 = strtoupper($MES[date('n', strtotime($rd->fecha_d)) - 1]);
                        list($y, $m, $d) = explode("-", $rd->fecha_d);
                        if ($m < 6) {
                            $y4 = ' 20' . $y2;
                        } else {
                            $y4 = ' 20' . $y3;
                        }
                        if ($MES1 == $lang->translation('Junio') . ' ' . $y4) {
                            $MES1 = $lang->translation('MATRICULA');
                        }
                        $pdf->Cell(37, 5, $lang->translation($MES1) . ' ' . $y4);
                        $pdf->Cell(90, 5, '.......................................................................');
                        $pdf->Cell(20, 5, number_format($total, 2), 0, 1, 'R');
                    }
                }
                $pdf->Cell(37, 5, $lang->translation('BALANCE'));
                $pdf->Cell(90, 5, '.......................................................................');
                $pdf->Cell(20, 5, number_format($TOTAL, 2), 0, 1, 'R');
                $pdf->SetFont('Arial', '', 10);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, $lang->translation('NOTA IMPORTANTE:'), 0, 1);
                $pdf->Ln(5);
                $pdf->MultiCell(0, 7, $lang->translation('1. DESPUES DEL DIA 10 DE CADA MES SE COBRARAN $') . $chk . $lang->translation(' DE CARGOS POR DEMORA POR CUENTA.'));
                $pdf->Ln(3);
                $pdf->MultiCell(0, 7, $lang->translation('2. LOS PAGOS PUEDEN HACERSE MEDIANTE TARJETA DE CREDITO, ATH, ATHMOVIL BUSINESS, EFECTIVO, GIRO POSTAL.'));
                $pdf->Ln(3);
                $pdf->MultiCell(0, 7, $lang->translation('3. FAVOR DE HACER LOS ARREGLOS PERTINENTES PARA QUE LOS SERVICIOS EDUCATIVOS DE SU HIJO(A) NO SE VEAN AFECTADOS.'));
                $pdf->Ln(3);

                $file_name = "letter_" . $r->id . ".pdf";
                $co_re = __RESEND_KEY_OTHER__;
                $from = "{$colegio} <" . $co_re . ">";

                $mail = new Mail();
                $title = $lang->translation('AVISO DE COBRO');
                $subject = $lang->translation('AVISO DE COBRO');
                $message = 'Cta. ' . $r->id;
                $mail->Subject = $subject;
                $emailsSent = 0;
                $emailsError = 0;
                $error = null;

                $uploadHost = dirname($_SERVER['SCRIPT_URI']);
                $target_dir = "attachments/";

                if (!is_dir($target_dir)) {
                    mkdir($target_dir);
                }
                $files = [];
                $target_file = $file_name;
                $files[] = $uploadHost . '/' . $target_dir . $target_file;
                if (__RESEND__ == '1') {
                    $file = $pdf->Output("attachments/" . $file_name, 'F');
                }

                if (__PHPMAIL__ == '1') {
                    $file2 = $pdf->Output('', 'S');
                    $mail->addStringAttachment($file2, $file_name);
                }

                $parents = DB::table('madre')->where('id', $r->id)->first();
                $emails = [
                    ['correo' => $parents->email_p, 'nombre' => $parents->padre],
                    ['correo' => $parents->email_m, 'nombre' => $parents->madre]
                ];
                $to = [];
                foreach ($emails as $email) {
                    if ($email['correo'] !== '') {
                        $mail->addAddress($email['correo'], $email['nombre']);
                        $to[] = $email['correo'];
                    }
                }
                //        $mail->addAddress("alf_med@hotmail.com", 'Alfredo Medina');
                $message = "<center><h1>$title</h1></center><br/><br/><p>" . nl2br($title) . "</p>";

                $mail->isHTML(true);
                $mail->Body = "
            <!DOCTYPE html>
            <html lang='" . __LANG . "'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>{$title}</title>
            </head>
            <body>
            <br>
            <p>{$message}</p>  
            </body>
            </html>
            ";
                //            <center><h2>{$title}</h2></center>

                if (__PHPMAIL__ == '1') {
                    $mail->send();
                    $mail->ClearAddresses();
                }
                $mail->ClearAddresses();
                $mail->ClearAttachments();
                if (__RESEND__ == '1') {
                    DB::table('email_queue')->insert([
                        'from' => $from,
                        'reply_to' => $reply_to,
                        'to' => json_encode($to),
                        'message' => $message,
                        'text' => '',
                        'subject' => $subject,
                        'attachments' => json_encode($files),
                        'user' => $user,
                        'year' => $year,
                    ]);
                }
            }
        }
    }
}
