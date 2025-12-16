<?php
require_once __DIR__ . '/../app.php';

use App\Models\CafeteriaOrder;
use Classes\Email;

const LIMIT = 2;
$target_dir = __DIR__ . "/temp-folder";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777);
}

$date = date('Y-m-d');

$orders = CafeteriaOrder::query()
    ->pendingReceipts()
    ->whereDate('fecha', $date)
    ->where(function ($query) {
        $query->where('tdp', '3')
            ->orWhere('tdp', '4')
            ->orWhere('tdp', '7');
    })
    ->where('cn', '1')
    ->orderBy('id')
    ->limit(100)
    ->get();

if ($orders->isNotEmpty()) {
    $count = 0;
    foreach ($orders as $compra) {

        $studentSS = $compra->ss;
        $student = $compra->buyer;
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

        require __DIR__ . "/info_recibo.php";

        $family = $student->family;
        if (!$family) {
            continue;
        }

        $to = [];
        if ($family->email_p != "" || $family->email_m != "") {

            if (trim(str_replace(['\r', '\n', '\r\n'], '', $family->email_p)) != "") {
                $to[] = $family->email_p;
            }
            if (trim(str_replace(['\r', '\n', '\r\n'], '', $family->email_m)) != "") {
                $to[] = $family->email_m;
            }
        }

        if (sizeof($to) === 0) {
            continue;
        }

        try {
            $email = Email::to($to)
                ->subject($subject)
                ->body($message)
                ->text($text)
                ->attach("{$target_dir}/temp-file.pdf", "Recibo {$date}.pdf");

            if (school_config('app.acronym') === 'cbl') {
                $email->cc(['recibos@colegiobautista.org']);
            }

            $email->send();
            $compra->markReceiptSent();
            echo "Recibo enviado para compra #{$id_compra} a " . json_encode($to) . "\n";
        } catch (\Exception $e) {
            $compra->markReceiptFailed($e->getMessage());
            echo 'Error: ' . $e->getMessage() . 'Emails: ' . json_encode($to);
            continue;
        }

        $count++;
        if ($count >= LIMIT) {
            $count = 0;
            sleep(1);
        }
    }
}


// /domains/schoolsoftpr.org/public_html/emails/cbl/cafeteria-receipts-cron.php
