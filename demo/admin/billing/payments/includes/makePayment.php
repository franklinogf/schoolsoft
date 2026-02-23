<?php
require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Family;
use App\Models\Payment;
use App\Models\Scopes\YearScope;
use Classes\Route;
use Classes\Session;

Session::is_logged();


if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $school = Admin::user(Session::id())->first();
    $year = $school->year2;
    $paymentType = $_POST['paymentType'];
    $chkNum = $_POST['chkNum'] ?: '';
    $receiptNum = $_POST['receiptNum'] ?? null;
    $sameReceipt = $_POST['receiptType'] === '2' ? true : false;
    $bash = $_POST['bash'];
    $paymentDate = $_POST['paymentDate'];
    $monthToPay = $_POST['monthToPay'];
    $accountId = $_POST['accountId'];
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $family = Family::find($accountId);

    function receiptNumber($receiptNumber, $sameReceipt, $accountId)
    {
        // si no esta activado lo de los recibos manuales, entonces crear uno automaticamente, de lo contrario utilizar el recibo del input
        if ($receiptNumber === null || $receiptNumber === '') {
            // utilizar el mismo recibo

            $recNumber = Payment::query()
                ->withoutGlobalScope(YearScope::class)
                ->when($sameReceipt, function ($query) use ($accountId) {
                    return $query->where('id', $accountId);
                })
                ->where('deuda', '0000-00-00')
                ->max('rec');

            return $sameReceipt ? $recNumber : $recNumber + 1;
        }

        return $receiptNumber;
    }

    $receipt = receiptNumber($receiptNum, $sameReceipt, $accountId);

    if ($_POST['paymentMode'] === 'completo') {
        // buscar los pagos por el mes seleccionado
        $payments = Payment::query()->where([
            ['id', $accountId],
            ['baja', '']
        ])->whereMonth('fecha_d', $monthToPay)->get();
        // crear un array con todos los pagos con la informacion que necesito

        $debtData = $payments->groupBy(fn($item) => "{$item->codigo}-{$item->ss}")->map(function ($group) {
            $charge = $group->where('deuda', '>', 0)->first();

            return [
                'id' => $group->first()->id,
                'mt' => $charge->mt,
                'debt_date' => $group->first()->fecha_d,
                'code' => $group->first()->codigo,
                'grade' => $group->first()->grado,
                'ss' => $group->first()->ss,
                'full_name' => $group->first()->nombre,
                'desc' => $group->first()->desc1,
                'debts' => $group->sum(fn($item) => floatval($item->deuda)),
                'payments' => $group->sum(fn($item) => floatval($item->pago)),
            ];
        })->toArray();



        foreach ($debtData as $debt) {
            $totalPayment = $debt['debts'] - $debt['payments'];
            if ($totalPayment > 0) {
                $data = [
                    'id' => $debt['id'],
                    'nombre' => $debt['full_name'],
                    'desc1' => $debt['desc'],
                    'fecha_d' => $debt['debt_date'],
                    'year' => $year,
                    'codigo' => $debt['code'],
                    'ss' => $debt['ss'],
                    'grado' => $debt['grade'],
                    'fecha_p' => $paymentDate,
                    'pago' => $totalPayment,
                    'tdp' => $paymentType,
                    'nuchk' => $chkNum,
                    'rec' => $receipt,
                    'id2' => $debt['mt'],
                    'bash' => $bash,
                    'caja' => $school->caja,
                    'usuario' => $school->usuario,
                    'hora' => $time,
                    'fecha2' => $date
                ];

                Payment::query()->create($data);
            }
        }
    } else {
        $paymentDebtCodes = $_POST['parcialPaymentDebtsCodes'];
        $paymentDebtsAmounts = $_POST['parcialPaymentDebtsAmounts'];
        $paymentDebtsGrades = $_POST['parcialPaymentDebtsGrades'];

        foreach ($paymentDebtCodes as $index => $code) {
            $totalPayment = $paymentDebtsAmounts[$index];
            $grade = $paymentDebtsGrades[$index];
            $debt = [];

            $debtData = Payment::query()->where([
                ['id', $accountId],
                ['baja', ''],
                ['grado', $grade],
                ['codigo', $code]
            ])->whereMonth('fecha_d', $monthToPay)->first();


            $debt['id'] = $debtData->id;
            $debt['debt_date'] = $debtData->fecha_d;
            $debt['code'] = $debtData->codigo;
            $debt['grade'] = $grade;
            $debt['ss'] = $debtData->ss;
            $debt['full_name'] = $debtData->nombre;
            $debt['desc'] = $debtData->desc1;

            if ($totalPayment > 0) {

                $data = [
                    'id' => $debt['id'],
                    'nombre' => $debt['full_name'],
                    'desc1' => $debt['desc'],
                    'fecha_d' => $debt['debt_date'],
                    'year' => $year,
                    'codigo' => $debt['code'],
                    'ss' => $debt['ss'],
                    'grado' => $debt['grade'],
                    'fecha_p' => $paymentDate,
                    'pago' => $totalPayment,
                    'tdp' => $paymentType,
                    'nuchk' => $chkNum,
                    'rec' => $receipt,
                    'id2' => $debtData->mt,
                    'bash' => $bash,
                    'caja' => $school->caja,
                    'usuario' => $school->usuario,
                    'hora' => $time,
                    'fecha2' => $date
                ];
                Payment::query()->create($data);
            }
        }
    }

    Route::redirect("/billing/payments/index.php?accountId={$accountId}&month={$monthToPay}");
}
