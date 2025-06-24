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
    ['', ''],
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

$d = date('d');
$y = date('y');
$m = date('m');

//$fecha = date('Y-m-d',mktime(0,0,0,$m,$d+7,$y));

if ($mes > 6) {
    $year1 = "{$year[0]}{$year[1]}";
    //	$fechaFinal = "AND fecha_d>='$year1-07-01'";
}
class nPDF extends PDF
{
    function Header()
    {
        parent::header();
    }

    function Footer()
    {

        //Posici&oacute;n: a 1,5 cm del final
        $this->SetY(-80);
        //    $this->Image('../../logo/firma.gif',12,220,60);
        $this->Cell(0, 5, 'Cordialmente,', 0, 1, 'L');
        $this->Ln(20);
        //	$this->Cell(0,5,utf8_encode('H&#65533;ctor L. Castro'),0,1,'L');
        $this->Cell(0, 5, 'Director Ejecutivo', 0, 1, 'L');

        //Arial italic 8

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
$pdf = new nPDF();
$pdf->SetLeftMargin(20);

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
            $total = $deudas - $pagos;
        }
        if ($total != 0) {
            if ($_POST['opcion'] != '11deudores') {
                //echo '777777777777 ';		
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
                }
                if ($TOTAL != 0) {

                    $pdf->AddPage();
                    $pdf->Cell(0, 5, $lang->translation('Carta de cobro general'), 0, 1, 'C');
                    $pdf->Ln(5);
                    ////				$pdf->Cell(0,5,$_POST['mensajeTitulo'],0,1,'C');
                    ////				$pdf->Cell(0,5,$_POST['mensajeSaludo'],0,1);
                    //				$fechaHoy = date('j').' de '.$MES[date('n')-1].' de '.date('Y');
                    if ($lang->translation('es') == 'es') {
                        $fechaHoy = date('j') . ' de ' . $MES[date('n') - 1] . ' de ' . date('Y');
                    } else {
                        $fechaHoy = $lang->translation($MES[date('n') - 1]) . ' ' . date('j') . ', ' . date('Y');
                    }
                    $pdf->Cell(0, 5, $fechaHoy, 0, 1);
                    $pdf->Ln(10);
                    $pdf->Cell(20, 5, 'Familia: ', 0, 0);
                    $pdf->SetFont('Arial', 'B', 12);
                    $estu = DB::table('year')
                        ->whereRaw("id='$r->id' AND year='$year'")->first();
                    $pdf->Cell(50, 5, "$estu->apellidos ", 0, 1);
                    $pdf->Ln(10);
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->Cell(0, 5, 'CUENTA # ' . $r->id, 0, 1);
                    $pdf->Ln(5);
                    $pdf->Ln(10);
                    $pdf->SetFont('Arial', '', 12);

                    $pdf->Cell(0, 5, 'Estimados padres:', 0, 1);
                    $pdf->Ln(5);
                    $pdf->Cell(0, 5, utf8_encode('&#65533;La Paz de Cristo Resucitado sea con ustedes!'), 0, 1);
                    $pdf->Ln(5);
                    $pdf->Cell(0, 5, 'Son nuestros mejores deseos que ustedes y todos los miembros de su familia se', 0, 1);
                    $pdf->Cell(0, 5, 'encuentren bien de salud.', 0, 1);
                    $pdf->Ln(5);
                    //				$MES1 = strtoupper($MES[date('n', strtotime($fecha)) - 1]);
                    $MES1 = $MES[date('n', strtotime($fecha)) - 1];
                    list($y, $m, $d) = explode("-", $fecha);
                    $pdf->Cell(0, 5, 'La presente es para recordarles que su cuenta refleja un balance pendiente de pago ', 0, 1);
                    $pdf->Cell(0, 5, 'por la cantidad de $' . number_format($TOTAL, 2) . '.  Favor de realizar el pago en efectivo en o antes del', 0, 1);
                    $pdf->Cell(0, 5, $d . ' de ' . $MES1 . ' de ' . $y . '.', 0, 1);
                    $pdf->Ln(5);
                    $pdf->Cell(0, 5, utf8_encode('Para informaci&#65533;n espec&#65533;fica sobre sus balances adeudados, agradeceremos que se '), 0, 1);
                    $pdf->Cell(0, 5, utf8_encode('comuniquen v&#65533;a tel&#65533;fono (787) 842-1331. '), 0, 1);
                    $pdf->Ln(5);
                    $pdf->Cell(0, 5, utf8_encode('Gracias anticipadas por su cooperaci&#65533;n sobre el particular. '), 0, 1);
                    $pdf->Cell(0, 5, '', 0, 1);
                    $pdf->Ln(10);
                }
            }
        } else {
        }
    }
}

$pdf->Output();

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

if ($_POST['tipo'] == 'email') {
    $result = DB::table('pagos')->select("DISTINCT id")
        ->whereRaw("fecha_d <= '$fecha' AND year = '$year' and baja=''")->orderBy('id')->get();

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
                $total = $deudas - $pagos;
            }
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
            }
            //		if($total != 0){
            if ($TOTAL > 0) {
                $pdf->AddPage();
                $pdf->Cell(0, 5, $lang->translation('Carta de cobro general'), 0, 1, 'C');
                $pdf->Ln(5);
                $fechaHoy = date('j') . ' de ' . $MES[date('n') - 1] . ' de ' . date('Y');
                $pdf->Cell(0, 5, $fechaHoy, 0, 1);
                $pdf->Ln(10);
                $pdf->Cell(20, 5, 'Familia: ', 0, 0);
                $pdf->SetFont('Arial', 'B', 12);
                $estu = DB::table('year')
                    ->whereRaw("id='$r->id' AND year='$year'")->first();
                $pdf->Cell(50, 5, "$estu->apellidos ", 0, 1);
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(0, 5, 'CUENTA # ' . $r->id, 0, 1);
                $pdf->Ln(5);
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 12);

                $pdf->Cell(0, 5, 'Estimados padres:', 0, 1);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, utf8_encode('&#65533;La Paz de Cristo Resucitado sea con ustedes!'), 0, 1);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, 'Son nuestros mejores deseos que ustedes y todos los miembros de su familia se', 0, 1);
                $pdf->Cell(0, 5, 'encuentren bien de salud.', 0, 1);
                $pdf->Ln(5);
                $MES1 = $MES[date('n', strtotime($fecha)) - 1];
                list($y, $m, $d) = explode("-", $fecha);
                $pdf->Cell(0, 5, 'La presente es para recordarles que su cuenta refleja un balance pendiente de pago ', 0, 1);
                $pdf->Cell(0, 5, 'por la cantidad de $' . number_format($TOTAL, 2) . '.  Favor de realizar el pago en efectivo en o antes del', 0, 1);
                $pdf->Cell(0, 5, $d . ' de ' . $MES1 . ' de ' . $y . '.', 0, 1);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, utf8_encode('Para informaci&#65533;n espec&#65533;fica sobre sus balances adeudados, agradeceremos que se '), 0, 1);
                $pdf->Cell(0, 5, utf8_encode('comuniquen v&#65533;a tel&#65533;fono (787) 842-1331. '), 0, 1);
                $pdf->Ln(5);
                $pdf->Cell(0, 5, utf8_encode('Gracias anticipadas por su cooperaci&#65533;n sobre el particular. '), 0, 1);
                $pdf->Cell(0, 5, '', 0, 1);
                $pdf->Ln(10);

                $file_name = "letter_" . $r->id . ".pdf";
                $co_re = __RESEND_KEY_OTHER__;
                $from = "{$colegio} <" . $co_re . ">";

                //***************************************************
                $mail = new Mail();
                $title = $lang->translation('Carta de cobro general A');
                $subject = $lang->translation('Carta de cobro general A');
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
                if (__RESEND == '1') {
                    $file = $pdf->Output("attachments/" . $file_name, 'F');
                }

                if (__PHPMAIL == '1') {
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

                if (__PHPMAIL == '1') {
                    $mail->send();
                    $mail->ClearAddresses();
                }
                $mail->ClearAddresses();
                $mail->ClearAttachments();
                if (__RESEND == '1') {
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
                        'id2' => $r->id,
                        'fecha' => date('Y-m-d'),
                        'nombre' => '',
                    ]);
                }
            }
        }
    }
}
