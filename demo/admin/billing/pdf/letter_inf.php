<?php
require_once __DIR__ . '/../../../app.php';

use Classes\Controllers\School;
use Classes\DataBase\DB;
use Classes\Lang;
use Classes\Mail;
use Classes\PDF;
use Classes\Session;

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
$estudiantesSS = $_REQUEST['students'] ?? [];

$cm = $_REQUEST['correo'] ?? '';
list($y3, $y2) = explode("-", $year);
list($ya, $yb, $yc) = explode("-", date('Y-m-d'));

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
        $this->Cell(0, 5, utf8_encode($lang->translation('Si usted ha realizado el pago antes mencionado, favor de hacer caso omiso a esta notificaci&#65533;n.')));
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
$fecha = date('Y-m-d');

$pdf = new nPDF();

foreach ($estudiantesSS as $ss) {
    $estudiante = DB::table('year')
        ->whereRaw("ss = '$ss' AND year = '$year'")->orderBy('id')->first();
    $pdf->AddPage();
    $pdf->Cell(0, 5, date('d-m-Y'), 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 5, 'CUENTA # ' . $estudiante->id, 0, 1);
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(38, 5, 'Estimados padres:', 0, 0);
    $pdf->Cell(50, 5, "$estudiante->apellidos ", 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, utf8_encode('¡La paz de Dios sea con ustedes y los suyos! '), 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, utf8_encode('Su cuenta refleja un balance pendiente de pago por $' . number_format($estudiante->tr1, 2) . '.  Les envió un'), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('comunicado previo a este, sin recibir noticias de su parte.'), 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, 'Por tal motivo su(s) hijo(s):', 0, 1);
    $pdf->Ln(5);
    $studiants = DB::table('year')
        ->whereRaw("id = '$estudiante->id' AND year = '$year'")->orderBy('id')->get();
    foreach ($studiants as $estu) {
        $pdf->Cell(90, 5, "$estu->apellidos $estu->nombre", 0, 0);
        $pdf->Cell(20, 5, "$estu->grado", 0, 1);
    }
    $pdf->Ln(5);

    $MES1 = $MES[date('n', strtotime($fecha)) - 1];
    $day = '';
    list($y, $m, $d) = explode("-", date('Y-m-d'));
    $date =  date("l", mktime(0, 0, 0, $m, $d + 7, $y));
    $fecha2 = date('Y-m-d', mktime(0, 0, 0, $m, $d + 7, $y));
    $MES1 = $MES[date('n', strtotime($fecha2)) - 1];
    list($y, $m, $day) = explode("-", $fecha2);
    $pdf->Cell(0, 5, utf8_encode('Será(n) suspendido(s) de clases a partir del próxima fecha, ' . $day . ' de ' . $MES1 . ' de ' . $y), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('hasta que se satisfaga el pago de la deuda existente.'), 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 5, utf8_encode('Dicha suspensión conlleva la desconexión de la plataforma Microsoft Teams y otras.  El'), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('estudiante será responsable de obtener el material cubierto, una vez sea reingresado a '), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('las clases virtuales.  Los exhorto a comunicarse con este servidor para juntos encontrar '), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('una posible solución al respecto. '), 0, 1);
    $pdf->Cell(0, 5, utf8_encode(''), 0, 1);
    $pdf->Cell(0, 5, utf8_encode(''), 0, 1);
    $pdf->Cell(0, 5, utf8_encode('Esperando de ustedes quedo,'), 0, 1);
    $pdf->Ln(5);

    $pdf->Ln(10);
}

$pdf->Output();

if ($cm == 'Si') {
    //**********************************************

    foreach ($estudiantesSS as $ss) {
        $pdf = new PDF();
        $pdf->SetFont('Arial', '', 12);
        $pdf->AddPage();
        $estudiante = DB::table('year')
            ->whereRaw("ss = '$ss' AND year = '$year'")->orderBy('id')->first();
        $pdf->Cell(0, 5, date('d-m-Y'), 0, 1);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 5, 'CUENTA # ' . $estudiante->id, 0, 1);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);

        $pdf->Cell(38, 5, 'Estimados padres:', 0, 0);
        $pdf->Cell(50, 5, "$estudiante->apellidos ", 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 5, utf8_encode('¡La paz de Dios sea con ustedes y los suyos! '), 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 5, utf8_encode('Su cuenta refleja un balance pendiente de pago por $' . number_format($estudiante->tr1, 2) . '.  Les envió un'), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('comunicado previo a este, sin recibir noticias de su parte.'), 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 5, 'Por tal motivo su(s) hijo(s):', 0, 1);
        $pdf->Ln(5);
        $studiants = DB::table('year')
            ->whereRaw("id = '$estudiante->id' AND year = '$year'")->orderBy('id')->get();
        foreach ($studiants as $estu) {
            $pdf->Cell(90, 5, "$estu->apellidos $estu->nombre", 0, 0);
            $pdf->Cell(20, 5, "$estu->grado", 0, 1);
        }
        $pdf->Ln(5);

        $MES1 = $MES[date('n', strtotime($fecha)) - 1];
        $day = '';
        list($y, $m, $d) = explode("-", date('Y-m-d'));
        $date =  date("l", mktime(0, 0, 0, $m, $d + 7, $y));
        $fecha2 = date('Y-m-d', mktime(0, 0, 0, $m, $d + 7, $y));
        $MES1 = $MES[date('n', strtotime($fecha2)) - 1];
        list($y, $m, $day) = explode("-", $fecha2);
        $pdf->Cell(0, 5, utf8_encode('Será(n) suspendido(s) de clases a partir del próxima fecha, ' . $day . ' de ' . $MES1 . ' de ' . $y), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('hasta que se satisfaga el pago de la deuda existente.'), 0, 1);
        $pdf->Ln(5);
        $pdf->Cell(0, 5, utf8_encode('Dicha suspensión conlleva la desconexión de la plataforma Microsoft Teams y otras.  El'), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('estudiante será responsable de obtener el material cubierto, una vez sea reingresado a '), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('las clases virtuales.  Los exhorto a comunicarse con este servidor para juntos encontrar '), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('una posible solución al respecto. '), 0, 1);
        $pdf->Cell(0, 5, utf8_encode(''), 0, 1);
        $pdf->Cell(0, 5, utf8_encode(''), 0, 1);
        $pdf->Cell(0, 5, utf8_encode('Esperando de ustedes quedo,'), 0, 1);
        $pdf->Ln(5);

        $file_name = "letter_" . $estudiante->id . ".pdf";
        $co_re = __RESEND_KEY_OTHER__;
        $from = "{$colegio} <" . $co_re . ">";

        //***************************************************
        $mail = new Mail();
        $title = $lang->translation('Carta de suspensión');
        $subject = $lang->translation('Carta de suspensión');
        $message = 'Cta. ' . $estudiante->id;
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

        $parents = DB::table('madre')->where('id', $estudiante->id)->first();
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


        //***************************************************

    }
}
