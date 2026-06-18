<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../app.php';

use App\Models\Admin;
use App\Models\Payments\AutoPay;
use App\Models\Payments\AutoPayItem;
use App\Models\Payments\Budget;
use App\Models\Payments\Payment;
use App\Models\Student;
use Classes\Session;
use Illuminate\Database\Capsule\Manager;

Session::is_logged();

header('Content-Type: application/json; charset=utf-8');

$accountId = (int) Session::id();
$year = Admin::primaryAdmin()->year;

/**
 * @param array<string, mixed> $payload
 */
function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function recalculateTotal(int $autoPayId): float
{
    $total = (float) AutoPayItem::query()
        ->where('posteoId', $autoPayId)
        ->sum('cantidad');

    AutoPay::query()->where('id', $autoPayId)->update(['total' => $total]);

    return $total;
}

/**
 * @return array<string, mixed>
 */
function mapAutoPay(AutoPay $autoPay, string $year): array
{
    $items = $autoPay->items
        ->sortBy('id')
        ->map(function (AutoPayItem $item) use ($year): array {
            $budgetDescription = $item->budget->descripcion
                ?? Budget::query()
                ->where('codigo', $item->presupuesto)
                ->where('year', $year)
                ->value('descripcion')
                ?? '';

            return [
                'id' => (int) $item->id,
                'studentId' => (int) $item->estudianteId,
                'studentName' => trim(($item->student->nombre ?? '') . ' ' . ($item->student->apellidos ?? '')),
                'budgetCode' => (int) $item->presupuesto,
                'budgetDescription' => $budgetDescription,
                'amount' => (float) $item->cantidad,
            ];
        })
        ->values()
        ->all();

    return [
        'id' => (int) $autoPay->id,
        'accountId' => (int) $autoPay->cuenta,
        'email' => (string) $autoPay->email,
        'tipoDePago' => $autoPay->tipoDePago?->value,
        'formaDePago' => $autoPay->formaDePago?->value,
        'diaDePago' => $autoPay->diaDePago,
        'ccNombre' => $autoPay->ccNombre,
        'ccNumero' => null,
        'ccLast4' => $autoPay->ccNumero ? substr((string) $autoPay->ccNumero, -4) : null,
        'fechaExpiracion' => $autoPay->fechaExpiracion,
        'cvv' => null,
        'ccZip' => $autoPay->ccZip,
        'achNombre' => $autoPay->achNombre,
        'achNumero' => null,
        'achLast4' => $autoPay->achNumero ? substr((string) $autoPay->achNumero, -4) : null,
        'numeroRuta' => null,
        'numeroRutaLast4' => $autoPay->numeroRuta ? substr((string) $autoPay->numeroRuta, -4) : null,
        'tipoCuenta' => $autoPay->tipoCuenta?->value,
        'achZip' => $autoPay->achZip,
        'total' => (float) $autoPay->total,
        'items' => $items,
    ];
}

function findAutoPay(int $autoPayId, int $accountId, string $year): ?AutoPay
{
    return AutoPay::query()
        ->where('id', $autoPayId)
        ->where('cuenta', $accountId)
        ->where('year', $year)
        ->first();
}

function paymentAmountByStudentAndCode(int $accountId, string $studentSs, int $budgetCode): float
{
    $rows = Payment::query()
        ->where('id', $accountId)
        ->where('ss', $studentSs)
        ->where('codigo', $budgetCode)
        ->where('baja', '')
        ->get();

    if ($rows->isEmpty()) {
        return 0.0;
    }

    // Payments stores recurring rows by period; autopay needs the unit amount per code,
    // not the accumulated balance across all periods.
    $totalsByDueDate = [];

    foreach ($rows as $row) {
        $dueDate = (string) $row->fecha_d;

        if (!isset($totalsByDueDate[$dueDate])) {
            $totalsByDueDate[$dueDate] = [
                'deuda' => 0.0,
                'pago' => 0.0,
            ];
        }

        $totalsByDueDate[$dueDate]['deuda'] += (float) $row->deuda;
        $totalsByDueDate[$dueDate]['pago'] += (float) $row->pago;
    }

    $balances = [];
    foreach ($totalsByDueDate as $total) {
        $balance = $total['deuda'] - $total['pago'];
        if ($balance > 0) {
            $balances[] = $balance;
        }
    }

    if (count($balances) === 0) {
        return 0.0;
    }

    return round(max($balances), 2);
}

$action = (string) ($_REQUEST['action'] ?? '');

if ($action === '') {
    jsonResponse(['success' => false, 'message' => 'Action is required'], 422);
}

if ($action === 'list') {
    $autoPays = AutoPay::query()
        ->where('cuenta', $accountId)
        ->where('year', $year)
        ->with(['items.student', 'items.budget'])
        ->orderByDesc('id')
        ->get();

    $data = $autoPays->map(fn(AutoPay $autoPay) => mapAutoPay($autoPay, $year))->values();

    jsonResponse(['success' => true, 'data' => $data]);
}

if ($action === 'detail') {
    $autoPayId = (int) ($_GET['id'] ?? 0);
    $autoPay = AutoPay::query()
        ->where('id', $autoPayId)
        ->where('cuenta', $accountId)
        ->where('year', $year)
        ->with(['items.student', 'items.budget'])
        ->first();

    if (!$autoPay) {
        jsonResponse(['success' => false, 'message' => 'Autopay not found'], 404);
    }

    jsonResponse(['success' => true, 'data' => mapAutoPay($autoPay, $year)]);
}

if ($action === 'studentCodes') {
    $studentId = (int) ($_GET['studentId'] ?? 0);

    if ($studentId <= 0) {
        jsonResponse(['success' => true, 'data' => []]);
    }

    $student = Student::query()
        ->withoutGlobalScope('lastName')
        ->where('id', $accountId)
        ->where('mt', $studentId)
        ->first();

    if (!$student) {
        jsonResponse(['success' => false, 'message' => 'Student not found for this account'], 422);
    }

    $rows = Payment::query()
        ->where('id', $accountId)
        ->where('ss', (string) $student->ss)
        ->where('baja', '')
        ->where('deuda', '>', 0)
        ->get();

    if ($rows->isEmpty()) {
        jsonResponse(['success' => true, 'data' => []]);
    }

    $codes = $rows->groupBy(fn(Payment $row) => (int) $row->codigo)->map(function ($group, $code) use ($year): array {
        $first = $group->first();
        $description = Budget::query()
            ->where('codigo', (int) $code)
            ->where('year', $year)
            ->value('descripcion');

        return [
            'code' => (int) $code,
            'description' => (string) ($description ?? $first?->desc1 ?? ''),
        ];
    })->values()->all();

    usort($codes, static function (array $a, array $b): int {
        return [$a['description'], $a['code']] <=> [$b['description'], $b['code']];
    });

    jsonResponse(['success' => true, 'data' => $codes]);
}

if ($action === 'saveHeader') {
    $autoPayId = (int) ($_POST['id'] ?? 0);
    $paymentType = (string) ($_POST['tipoDePago'] ?? '');
    $payMode = 'automatico';
    $email = trim((string) ($_POST['email'] ?? ''));
    $dayOfPayment = isset($_POST['diaDePago']) && $_POST['diaDePago'] !== '' ? (int) $_POST['diaDePago'] : null;
    $existingAutoPay = $autoPayId > 0 ? findAutoPay($autoPayId, $accountId, $year) : null;

    if ($autoPayId > 0 && !$existingAutoPay) {
        jsonResponse(['success' => false, 'message' => 'Autopay not found'], 404);
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Valid email is required'], 422);
    }

    if (!in_array($paymentType, ['tarjeta', 'ach'], true)) {
        jsonResponse(['success' => false, 'message' => 'Invalid payment method'], 422);
    }

    if ($payMode === 'automatico' && ($dayOfPayment === null || $dayOfPayment < 1 || $dayOfPayment > 30)) {
        jsonResponse(['success' => false, 'message' => 'Day of payment must be between 1 and 30'], 422);
    }

    $payload = [
        'cuenta' => $accountId,
        'year' => $year,
        'email' => $email,
        'tipoDePago' => $paymentType,
        'formaDePago' => $payMode,
        'diaDePago' => $payMode === 'automatico' ? $dayOfPayment : null,
    ];

    if ($paymentType === 'tarjeta') {
        $isExistingCard = $existingAutoPay && $existingAutoPay->tipoDePago?->value === 'tarjeta';

        $ccName = trim((string) ($_POST['ccNombre'] ?? ''));
        $ccNumberSanitized = preg_replace('/\D/', '', (string) ($_POST['ccNumero'] ?? ''));
        $ccExpiration = trim((string) ($_POST['fechaExpiracion'] ?? ''));
        $ccCvvSanitized = preg_replace('/\D/', '', (string) ($_POST['ccv'] ?? ''));
        $ccZipSanitized = preg_replace('/\D/', '', (string) ($_POST['ccZip'] ?? ''));

        $ccName = $ccName !== '' ? $ccName : ($isExistingCard ? (string) $existingAutoPay->ccNombre : '');
        $ccNumber = $ccNumberSanitized !== '' ? (int) $ccNumberSanitized : ($isExistingCard ? (int) $existingAutoPay->ccNumero : 0);
        $ccExpiration = $ccExpiration !== '' ? $ccExpiration : ($isExistingCard ? (string) $existingAutoPay->fechaExpiracion : '');
        $ccZip = $ccZipSanitized !== '' ? (int) $ccZipSanitized : ($isExistingCard ? (int) $existingAutoPay->ccZip : 0);

        if ($ccName === '' || $ccNumber === 0 || $ccExpiration === '' || $ccZip === 0) {
            jsonResponse(['success' => false, 'message' => 'Complete todos los campos de tarjeta'], 422);
        }

        $payload = array_merge($payload, [
            'ccNombre' => $ccName,
            'ccNumero' => $ccNumber,
            'fechaExpiracion' => $ccExpiration,
            // CVV should never be stored persistently.
            'cvv' => null,
            'ccZip' => $ccZip,
            'achNombre' => null,
            'achNumero' => null,
            'numeroRuta' => null,
            'tipoCuenta' => null,
            'achZip' => null,
        ]);
    }

    if ($paymentType === 'ach') {
        $isExistingAch = $existingAutoPay && $existingAutoPay->tipoDePago?->value === 'ach';

        $accountType = (string) ($_POST['tipoCuenta'] ?? '');
        if (!in_array($accountType, ['w', 's'], true) && !$isExistingAch) {
            jsonResponse(['success' => false, 'message' => 'Invalid ACH account type'], 422);
        }

        if (!in_array($accountType, ['w', 's'], true) && $isExistingAch) {
            $accountType = (string) $existingAutoPay->tipoCuenta?->value;
        }

        $achName = trim((string) ($_POST['achNombre'] ?? ''));
        $achNumberSanitized = preg_replace('/\D/', '', (string) ($_POST['achNumero'] ?? ''));
        $routeSanitized = preg_replace('/\D/', '', (string) ($_POST['numeroRuta'] ?? ''));
        $achZipSanitized = preg_replace('/\D/', '', (string) ($_POST['achZip'] ?? ''));

        $achName = $achName !== '' ? $achName : ($isExistingAch ? (string) $existingAutoPay->achNombre : '');
        $achNumber = $achNumberSanitized !== '' ? (int) $achNumberSanitized : ($isExistingAch ? (int) $existingAutoPay->achNumero : 0);
        $routeNumber = $routeSanitized !== '' ? (int) $routeSanitized : ($isExistingAch ? (int) $existingAutoPay->numeroRuta : 0);
        $achZip = $achZipSanitized !== '' ? (int) $achZipSanitized : ($isExistingAch ? (int) $existingAutoPay->achZip : 0);

        if ($achName === '' || $achNumber === 0 || $routeNumber === 0 || !in_array($accountType, ['w', 's'], true) || $achZip === 0) {
            jsonResponse(['success' => false, 'message' => 'Complete todos los campos de ACH'], 422);
        }

        $payload = array_merge($payload, [
            'achNombre' => $achName,
            'achNumero' => $achNumber,
            'numeroRuta' => $routeNumber,
            'tipoCuenta' => $accountType,
            'achZip' => $achZip,
            'ccNombre' => null,
            'ccNumero' => null,
            'fechaExpiracion' => null,
            'cvv' => null,
            'ccZip' => null,
        ]);
    }

    $autoPay = Manager::connection()->transaction(function () use ($autoPayId, $accountId, $year, $payload): AutoPay {
        if ($autoPayId > 0) {
            $existing = findAutoPay($autoPayId, $accountId, $year);
            if (!$existing) {
                throw new RuntimeException('Autopay not found');
            }

            $existing->update($payload);
            return $existing->refresh();
        }

        return AutoPay::query()->create($payload);
    });

    $autoPay = AutoPay::query()
        ->with(['items.student', 'items.budget'])
        ->findOrFail($autoPay->id);

    jsonResponse([
        'success' => true,
        'message' => 'Autopay saved',
        'data' => mapAutoPay($autoPay, $year),
    ]);
}

if ($action === 'paymentAmount') {
    $studentId = (int) ($_GET['studentId'] ?? 0);
    $budgetCode = (int) ($_GET['budgetCode'] ?? 0);

    if ($studentId <= 0 || $budgetCode <= 0) {
        jsonResponse(['success' => false, 'message' => 'Student and code are required'], 422);
    }

    $student = Student::query()
        ->withoutGlobalScope('lastName')
        ->where('id', $accountId)
        ->where('mt', $studentId)
        ->first();

    if (!$student) {
        jsonResponse(['success' => false, 'message' => 'Student not found for this account'], 422);
    }

    $amount = paymentAmountByStudentAndCode($accountId, (string) $student->ss, $budgetCode);

    jsonResponse([
        'success' => true,
        'data' => [
            'amount' => $amount,
        ],
    ]);
}

if ($action === 'saveItem') {
    $autoPayId = (int) ($_POST['autoPayId'] ?? 0);
    $itemId = (int) ($_POST['itemId'] ?? 0);
    $studentId = (int) ($_POST['studentId'] ?? 0);
    $budgetCode = (int) ($_POST['budgetCode'] ?? 0);

    $autoPay = findAutoPay($autoPayId, $accountId, $year);
    if (!$autoPay) {
        jsonResponse(['success' => false, 'message' => 'Autopay not found'], 404);
    }

    $student = Student::query()
        ->withoutGlobalScope('lastName')
        ->where('id', $accountId)
        ->where('mt', $studentId)
        ->first();

    if (!$student) {
        jsonResponse(['success' => false, 'message' => 'Student not found for this account'], 422);
    }

    $budgetExists = Budget::query()->where('codigo', $budgetCode)->where('year', $year)->exists();
    if (!$budgetExists) {
        jsonResponse(['success' => false, 'message' => 'Budget code not found'], 422);
    }

    $amount = paymentAmountByStudentAndCode($accountId, (string) $student->ss, $budgetCode);

    if ($amount <= 0) {
        jsonResponse(['success' => false, 'message' => 'No hay balance pendiente en pagos para ese estudiante y codigo'], 422);
    }

    Manager::connection()->transaction(function () use ($itemId, $autoPayId, $studentId, $budgetCode, $amount): void {
        if ($itemId > 0) {
            AutoPayItem::query()
                ->where('id', $itemId)
                ->where('posteoId', $autoPayId)
                ->update([
                    'estudianteId' => $studentId,
                    'presupuesto' => $budgetCode,
                    'cantidad' => $amount,
                ]);

            return;
        }

        AutoPayItem::query()->create([
            'posteoId' => $autoPayId,
            'estudianteId' => $studentId,
            'presupuesto' => $budgetCode,
            'cantidad' => $amount,
        ]);
    });

    $total = recalculateTotal($autoPayId);
    $autoPay = AutoPay::query()
        ->where('id', $autoPayId)
        ->with(['items.student', 'items.budget'])
        ->first();

    jsonResponse([
        'success' => true,
        'message' => 'Item saved',
        'total' => $total,
        'data' => $autoPay ? mapAutoPay($autoPay, $year) : null,
    ]);
}

if ($action === 'deleteItem') {
    $autoPayId = (int) ($_POST['autoPayId'] ?? 0);
    $itemId = (int) ($_POST['itemId'] ?? 0);

    $autoPay = findAutoPay($autoPayId, $accountId, $year);
    if (!$autoPay) {
        jsonResponse(['success' => false, 'message' => 'Autopay not found'], 404);
    }

    AutoPayItem::query()
        ->where('id', $itemId)
        ->where('posteoId', $autoPayId)
        ->delete();

    $total = recalculateTotal($autoPayId);
    jsonResponse([
        'success' => true,
        'message' => 'Item deleted',
        'total' => $total,
    ]);
}

if ($action === 'deleteAutopay') {
    $autoPayId = (int) ($_POST['id'] ?? 0);

    $autoPay = findAutoPay($autoPayId, $accountId, $year);
    if (!$autoPay) {
        jsonResponse(['success' => false, 'message' => 'Autopay not found'], 404);
    }

    Manager::connection()->transaction(function () use ($autoPay): void {
        AutoPayItem::query()->where('posteoId', $autoPay->id)->delete();
        $autoPay->delete();
    });

    jsonResponse(['success' => true, 'message' => 'Autopay deleted']);
}

jsonResponse(['success' => false, 'message' => 'Unsupported action'], 422);
