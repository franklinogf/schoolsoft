<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';
date_default_timezone_set("America/Puerto_Rico");
$mysqli = new mysqli("localhost", "u291111878_cbl", "Amc@3151", "u291111878_cbl");
const LIMIT = 2;
// Check connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit;
}
$target_dir = __DIR__ . "/temp-folder";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777);
}

$resend = Resend::client('re_f75hJQZe_9GMN9VcnXKr6dYUb41foEvQG');
$emails = $mysqli->query("SELECT * FROM colegio WHERE usuario = 'administrador'");
if ($emails) {
    $row = $emails->fetch_assoc();
    $year = $row['year2'];
    $reply_to = $row['correo'];
    $from = "{$row['colegio']} <cbl@schoolsoftusa.com>";
    $emails->free_result();
}
$date = date('Y-m-d');

$emails = $mysqli->query("SELECT * FROM compra_cafeteria WHERE DATE(fecha) = '$date' AND `year` = '$year' AND (tdp = '3' OR tdp = '4' OR tdp = '7') and receipt_sent='0' ORDER BY id LIMIT 100");
if ($emails) {
    $count = 0;
    while ($compra = $emails->fetch_object()) {

        $studentSS = $compra->ss;
        $studentResult = $mysqli->query("SELECT id, balance_a, mt, cantidad FROM `year` WHERE ss = '$studentSS' AND `year` = '$year'");
        $student = $studentResult->fetch_object();
        $balanceA = $student->balance_a;
        $studentMT = $student->mt;
        $studentID = $student->id;
        $cantidad = $student->cantidad;

        $id_compra = $compra->id;
        $estudiante = "$compra->nombre $compra->apellido";
        $subject = $correoTitulo = "Recibo de compra #{$id_compra}";
        $text = $correoMensaje = "Recibo de compra del estudiante {$estudiante} el {$date}";

        $message = "<center>
    <h1>$correoTitulo</h1>
    </center>\n\n
    <p>$correoMensaje</p>";

        $uploadHost = "https://schoolsoftpr.org/emails/cbl";
        require __DIR__ . "/info_recibo.php";

        $emailsResult = $mysqli->query("SELECT email_m,email_p FROM madre  WHERE id='$studentID'");
        $emails = $emailsResult->fetch_object();
        if (!$emailsResult) {
            continue;
        }

        $to = [];
        if ($emails->email_p != "" || $emails->email_m != "") {

            if (trim(str_replace(['\r', '\n', '\r\n'], '', $emails->email_p)) != "") {
                $to[] = $emails->email_p;
            }
            if (trim(str_replace(['\r', '\n', '\r\n'], '', $emails->email_m)) != "") {
                $to[] = $emails->email_m;
            }

        }

        $emailsResult->free_result();

        if (sizeof($to) === 0) {
            continue;
        }

        try {

            $sentEmail = $resend->emails->send([
                'from' => $from,
                'to' => $to,
                'reply_to' => $reply_to,
                'subject' => $subject,
                'html' => $message,
                'text' => $text,
                'bcc' => 'recibos@colegiobautista.org',
                'attachments' => [
                    [
                        "path" => "{$uploadHost}/temp-folder/temp-file.pdf",
                        "filename" => "Recibo {$date}.pdf",
                    ],
                ],
            ]);
            $dt = new DateTime("now", new DateTimeZone("America/Puerto_Rico"));
            $timestamp = $dt->format('Y-m-d H:i:s');
            // $timestamp = date('Y-m-d H:i:s');
            $mysqli->query("UPDATE `compra_cafeteria` SET `receipt_sent` = '1', `sent_at`='$timestamp', failed_reason=null WHERE id = $compra->id");
        } catch (\Exception $e) {
            $mysqli->query("UPDATE `compra_cafeteria` SET `receipt_sent` = '2', failed_reason = '{$e->getMessage()}' WHERE id = $compra->id");
            echo 'Error: ' . $e->getMessage() . 'Emails: ' . json_encode($to);
            continue;
        }

        echo $sentEmail->toJson();
        $count++;
        if ($count >= LIMIT) {
            $count = 0;
            sleep(1);
        }
    }
}
$emails->free_result();

// /domains/schoolsoftpr.org/public_html/emails/cbl/cafeteria-receipts-cron.php
