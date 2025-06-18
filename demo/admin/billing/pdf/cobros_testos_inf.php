<?php
require_once '../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Mail;
use Classes\PDF;
use Classes\Session;
use Classes\Util;

Session::is_logged();
$lang = new Lang([
    ['ESTADO DE CUENTAS', 'STATEMENT'],
    ['Carta de suspensión', 'Suspension letter'],
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
    ['Mensajes de cobros enviados', 'Send late payment messages'],
    ['Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.', 'If you have made the aforementioned payment, please ignore this notification.'],
    ['Descripción de envio', 'Sending Description'],
    ['Cuentas:', 'Accounts:'],
]);

$db = new DB();
$col = db::table('colegio')->whereRaw("usuario = 'administrador'")->first();
$colegio = $col->colegio;

$school = new School(Session::id());
$year = $school->info('year2');
$reply_to = $school->info('correo');
$user = $school->info('usuario');
$sms = $_POST['email'];
//$sms =$_POST['sms'];
$mail = new Mail();

$toalc = 0;
$toals = 0;
$toalm = 0;

$resultad2 = DB::table('year')->select("DISTINCT id")
    ->whereRaw("year='$year' and activo =''")->orderBy('grado')->get();

foreach ($resultad2 as $reg2) {
    $deuda = 0;
    $code = $_POST['desc'];
    if ($code == 'Todos') {
        $consult3 = DB::table('pagos')
            ->whereRaw("id='$reg2->id' and year='$year' and baja='' and fecha_d <= '" . $_POST['fec1'] . "'")->get();
    } else {
        $consult3 = DB::table('pagos')
            ->whereRaw("codigo='$code' and id='$reg2->id' and year='$year' and baja='' and fecha_d <= '" . $_POST['fec1'] . "'")->get();
    }
    foreach ($consult3 as $reg3) {
        $deuda = $deuda + $reg3->deuda;
        $deuda = $deuda - $reg3->pago;
    }
    if ($deuda > 0) {
        $toalc = $toalc + 1;
        $reg4 = DB::table('madre')
            ->whereRaw("id='$reg2->id'")->first();
        $co_re = __RESEND_KEY_OTHER__;
        $from = "{$colegio} <" . $co_re . ">";

        //***************************************************
        if ($sms == 'E') {
            $mail = new Mail();
            $title = $_POST['titulo'];
            $subject = $_POST['titulo'];
            $message = $_POST['text'];
            $mail->Subject = $subject;
            $emailsSent = 0;
            $emailsError = 0;
            $error = null;
        }

        $files = [];

        $parents = DB::table('madre')->where('id', $reg2->id)->first();
        $emails = [
            ['correo' => $parents->email_p, 'nombre' => $parents->padre],
            ['correo' => $parents->email_m, 'nombre' => $parents->madre]
        ];
        $to = [];
        if ($sms == 'E') {
            foreach ($emails as $email) {
                if ($email['correo'] !== '') {
                    $mail->addAddress($email['correo'], $email['nombre']);
                    $to[] = $email['correo'];
                    $toalm = $toalm + 1;
                }
            }

            //        $mail->addAddress("alf_med@hotmail.com", 'Alfredo Medina');
            $message = "<center><h1>$title</h1></center><br/><br/><p>" . nl2br($message) . "</p>";

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
        }

        //            <center><h2>{$title}</h2></center>
        if (__PHPMAIL__ == '1' and $sms == 'E') {
            $mail->send();
            $mail->ClearAddresses();
        }
        $mail->ClearAttachments();
        if (__RESEND__ == '1' and $sms == 'E') {
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
        //***********************************************

        if (__PHPMAIL__ == '1' and $sms == 'C') {
            $title = $_POST['titulo'];
            $message = $_POST['text'];
            $emailsSent = 0;
            $emailsError = 0;
            $error = null;

            $numbers = [
                ['cel' => $parents->cel_m, 'nombre' => $parents->padre, "recibir" => $parents->re_mc_m, 'comp' => $parents->cel_com_m],
                ['cel' => $parents->cel_p, 'nombre' => $parents->madre, "recibir" => $parents->re_mc_p, 'comp' => $parents->cel_com_p]
            ];
            foreach ($numbers as $number) {
                if ($number['recibir'] === 'SI' && $number['cel'] !== '' && $number['comp'] !== '') {
                    $cel = Util::phoneAddress($number['cel'], $number['comp']);
                    $toals = $toals + 1;
                    $mail->addAddress($cel, $number['nombre']);
                }
            }
        }

        //***********************************************

    }
}

if (__PHPMAIL__ == '1' and $sms == 'C') {
    $mail->msgHTML("
   <center><h2>{$title}</h2></center>
   <br>
   <p>{$message}</p>");
    $mail->send();
    $mail->ClearAddresses();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Untitled 1</title>
    <style type="text/css">
        .style1 {
            text-align: center;
            font-size: large;
        }

        .style2 {
            background-color: #CCCCCC;
            text-align: center;
        }

        .style3 {
            text-align: center;
            background-color: #FFFFCC;
        }

        .style4 {
            text-align: right;
            background-color: #CCCCCC;
        }
    </style>
</head>

<body>

    <p class="style1"><strong><?= $lang->translation('Mensajes de cobros enviados') ?></strong></p>
    <p class="style1">&nbsp;</p>
    <table align="center" cellpadding="2" cellspacing="0" style="width: 29%">
        <tr>
            <td class="style2" colspan="2"><strong><?= $lang->translation('Descripción de envio') ?></strong></td>
        </tr>
        <tr>
            <td class="style4" style="width: 50%"><?= $lang->translation('Cuentas:') ?></td>
            <td class="style3" style="width: 50%"><?= $toalc ?></td>
        </tr>
        <tr>
            <td class="style4" style="width: 50%">SMS:</td>
            <td class="style3" style="width: 50%"><?= $toals ?></td>
        </tr>
        <tr>
            <td class="style4" style="width: 50%">E-Mail:</td>
            <td class="style3" style="width: 50%"><?= $toalm ?></td>
        </tr>
        <tr>
            <td class="style4" style="width: 50%">Total:</td>
            <td class="style3" style="width: 50%"><?= $toals + $toalm ?></td>
        </tr>
    </table>

</body>

</html>