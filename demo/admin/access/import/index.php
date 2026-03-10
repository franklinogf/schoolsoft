<?php

require_once __DIR__ . '/../../../app.php';

use App\Models\CafeteriaOrder;
use App\Models\CafeteriaOrderItem;
use App\Models\Family;
use App\Models\Scopes\YearScope;
use App\Models\Student;
use App\Services\SchoolService;
use Classes\Lang;
use Classes\Route;
use Classes\Session;
use Illuminate\Database\Capsule\Manager as Capsule;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;

Session::is_logged();

$lang = new Lang([
    ['Importar CSV',                   'Import CSV'],
    ['Tipo de importación',            'Import type'],
    ['Fecha',                          'Date'],
    ['Guardar',                        'Save'],
    ['Volver',                         'Back'],
    ['Selección',                      'Selection'],
    ['El archivo se cargó con éxito',  'File loaded successfully'],
    ['Error al cargar el archivo',     'Error loading file'],
    ['Archivo',                        'File'],
    ['Fila',                           'Row'],
    ['Resultados',                     'Results'],
    ['No se subió ningún archivo',     'No file was uploaded'],
    ['Tipo de importación inválido',   'Invalid import type'],
    ['Estudiante no encontrado',       'Student not found'],
]);

$results   = [];
$errors    = [];
$processed = 0;

$year = SchoolService::getCurrentYear();

/** Parse SS from CSV format: 12011XXXXX → 120-11-XXXXX */
function parseSS(string $ss): ?string
{
    $parts = explode('12011', trim($ss), 2);
    return (count($parts) === 2 && $parts[1] !== '') ? '120-11-' . $parts[1] : null;
}

/** Recalculate cafeteria balance for a student after a new order is inserted. */
function recalcCafeteriaBalance(string $ss, string $year): void
{
    $totalDeposits = (float) Capsule::table('depositos')
        ->where('year', $year)
        ->where('ss', $ss)
        ->sum('cantidad');

    $orders = CafeteriaOrder::withoutGlobalScope(YearScope::class)
        ->where('ss', $ss)
        ->where('year', $year)
        ->with('items')
        ->get();

    $totalPurchases = 0.0;
    foreach ($orders as $order) {
        foreach ($order->items as $item) {
            $totalPurchases += $item->precio_final > 0 ? (float)$item->precio_final : (float)$item->precio;
        }
    }

    $balanceInicio = (float) Capsule::table('year')
        ->where('ss', $ss)
        ->where('year', $year)
        ->value('balance_a');

    $balance = round($totalDeposits - $totalPurchases + $balanceInicio, 2);

    Capsule::table('year')
        ->where('ss', $ss)
        ->where('year', $year)
        ->update(['cantidad' => $balance]);
}

function processTypeC(array $row, string $date, string $year, array &$results, int &$processed, Lang $lang): void
{
    [$ss, $desc, $precio] = array_pad($row, 3, null);
    $precio = (float) trim((string) $precio);
    $ss2    = parseSS((string) $ss);
    if (!$ss2) return;

    $student = Student::where('ss', $ss2)->first();
    if (!$student) {
        throw new \RuntimeException($lang->translation('Estudiante no encontrado') . ": {$ss2}");
    }

    $order = CafeteriaOrder::create([
        'nombre'   => $student->nombre,
        'apellido' => $student->apellidos,
        'ss'       => $ss2,
        'grado'    => $student->grado,
        'fecha'    => $date,
        'year'     => $year,
        'id2'      => $student->id2,
        'total'    => $precio,
        'pago1'    => $precio,
        'tdp'      => 7,
        'cn'       => '2',
    ]);

    CafeteriaOrderItem::create([
        'id_compra'   => $order->id,
        'descripcion' => trim((string) $desc),
        'precio'      => $precio,
        'id_boton'    => 77,
        'fecha'       => $date,
        'cn'          => '2',
    ]);

    recalcCafeteriaBalance($ss2, $year);

    $results[] = $student->apellidos . ' ' . $student->nombre;
    $processed++;
}

function processTypeC2(array $row, string $year, array &$results, int &$processed, Lang $lang): void
{
    [$ss, $desc, $precio, $fec, $rec] = array_pad($row, 5, null);
    $precio = (float) trim((string) $precio);
    $rec    = (int) trim(explode(' ', trim((string) $rec))[0]);
    $ss2    = parseSS((string) $ss);
    if (!$ss2) return;

    [$mm, $dd, $yy] = explode('/', trim((string) $fec));
    $date = "{$yy}-{$mm}-{$dd}";

    $student = Student::where('ss', $ss2)->first();
    if (!$student) {
        throw new \RuntimeException($lang->translation('Estudiante no encontrado') . ": {$ss2}");
    }

    CafeteriaOrder::create([
        'id'       => $rec,
        'nombre'   => $student->nombre,
        'apellido' => $student->apellidos,
        'ss'       => $ss2,
        'grado'    => $student->grado,
        'fecha'    => $date,
        'year'     => $year,
        'id2'      => $student->id2,
        'total'    => $precio,
        'pago1'    => $precio,
        'tdp'      => 7,
        'cn'       => '2',
    ]);

    CafeteriaOrderItem::create([
        'id_compra'   => $rec,
        'descripcion' => trim((string) $desc),
        'precio'      => $precio,
        'id_boton'    => 77,
        'fecha'       => $date,
        'cn'          => '2',
    ]);

    $results[] = $student->apellidos . ' ' . $student->nombre;
    $processed++;
}

function processTypeD(array $row, string $year, array &$results, int &$processed, Lang $lang): void
{
    [$ss, $id, $dep, $tipo, $fec, $hora, $ampm] = array_pad($row, 7, null);
    $ss2 = parseSS((string) $ss);
    if (!$ss2) return;

    [$mm, $dd, $yy] = explode('/', trim((string) $fec));
    $date = "{$yy}-{$mm}-{$dd}";

    [$hh, $mm2, $cc] = explode(':', trim((string) $hora));
    if (strtoupper(trim((string) $ampm)) === 'PM' && (int) $hh < 12) {
        $hh = (int) $hh + 12;
    }
    $hora24 = str_pad((string) $hh, 2, '0', STR_PAD_LEFT) . ':' . $mm2 . ':' . $cc;

    $tdp = strtolower(trim((string) $tipo)) === 'visa' ? 'Tarjeta' : 'ACH';

    $student = Student::where('ss', $ss2)->first();
    if (!$student) {
        throw new \RuntimeException($lang->translation('Estudiante no encontrado') . ": {$ss2}");
    }

    Capsule::table('depositos')->insert([
        'id'         => (int) trim((string) $id),
        'ss'         => $ss2,
        'fecha'      => $date,
        'hora'       => $hora24,
        'cantidad'   => (float) trim((string) $dep),
        'year'       => $year,
        'grado'      => $student->grado,
        'studentId'  => $student->id2,
        'tipoDePago' => $tdp,
        'zip'        => '77777',
    ]);

    $results[] = $ss2 . ' — $' . number_format((float) trim((string) $dep), 2);
    $processed++;
}

function processTypeE(array $row, array &$results, int &$processed): void
{
    [$id, $email_m, $email_p] = array_pad($row, 3, null);
    $family = Family::find((int) trim((string) $id));
    if (!$family) return;

    $family->update([
        'email_m' => trim((string) $email_m),
        'email_p' => trim((string) $email_p),
    ]);

    $results[] = $family->madre . ' (ID: ' . $id . ')';
    $processed++;
}

// ─── Process POST ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $tipo = $_POST['tabla'] ?? '';
    $fecha = $_POST['fex1'] ?? null;

    if (empty($_FILES['archivo']['tmp_name'])) {
        $errors[] = $lang->translation('No se subió ningún archivo');
    } elseif (!in_array($tipo, ['C', 'C2', 'D', 'E'])) {
        $errors[] = $lang->translation('Tipo de importación inválido');
    } else {
        try {
            $reader = new CsvReader();
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
            $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            foreach ($rows as $rowIndex => $row) {
                if (empty(array_filter(array_map('trim', array_map('strval', $row))))) {
                    continue;
                }
                try {
                    switch ($tipo) {
                        case 'C':
                            processTypeC($row, $fecha, $year, $results, $processed, $lang);
                            break;
                        case 'C2':
                            processTypeC2($row, $year, $results, $processed, $lang);
                            break;
                        case 'D':
                            processTypeD($row, $year, $results, $processed, $lang);
                            break;
                        case 'E':
                            processTypeE($row, $results, $processed);
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = $lang->translation('Fila') . ' ' . ($rowIndex + 1) . ': ' . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $errors[] = $lang->translation('Error al cargar el archivo') . ': ' . $e->getMessage();
        }
    }
}

// ─── View ─────────────────────────────────────────────────────────────────────
$title = $lang->translation('Importar CSV');
?>
<!DOCTYPE html>
<html lang="<?= __LANG ?>">

<head>
    <?php Route::includeFile('/admin/includes/layouts/header.php'); ?>
</head>

<body>
    <?php Route::includeFile('/admin/includes/layouts/menu.php'); ?>

    <div class="container-lg mt-4 mb-5">
        <h1 class="text-center mb-4"><?= $lang->translation('Importar CSV') ?></h1>

        <a class="btn btn-outline-secondary mb-3" href="<?= school_asset('/admin/access/index.php') ?>">
            &larr; <?= $lang->translation('Volver') ?>
        </a>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
            <div class="alert alert-success">
                <strong><?= $lang->translation('El archivo se cargó con éxito') ?></strong>
                &mdash; <?= $processed ?> <?= $lang->translation('Resultados') ?>:
                <ul class="mb-0 mt-1">
                    <?php foreach ($results as $r): ?>
                        <li><?= htmlspecialchars($r) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="archivo" class="form-label">
                            <?= $lang->translation('Archivo') ?> (CSV)
                        </label>
                        <input type="file" name="archivo" id="archivo"
                            class="form-control" accept=".csv" required>
                    </div>

                    <div class="mb-3">
                        <label for="tabla" class="form-label">
                            <?= $lang->translation('Tipo de importación') ?>
                        </label>
                        <select name="tabla" id="tabla" class="form-control"
                            required onchange="toggleFecha()">
                            <option value=""><?= $lang->translation('Selección') ?></option>
                            <option value="C">Cafetería &mdash; ss / descripción / precio</option>
                            <option value="C2">Cafetería &mdash; ss / descripción / precio / fecha / recibo</option>
                            <option value="D">Depósitos &mdash; ss / id / cantidad / tipo / fecha / hora / AM-PM</option>
                            <option value="E">E-Mail &mdash; id / email_m / email_p</option>
                        </select>
                    </div>

                    <div class="mb-3" id="fechaGroup" style="display:none;">
                        <label for="fex1" class="form-label">
                            <?= $lang->translation('Fecha') ?>
                        </label>
                        <input type="date" name="fex1" id="fex1"
                            class="form-control" style="max-width:180px;">
                    </div>

                    <button type="submit" name="guardar" class="btn btn-primary">
                        <?= $lang->translation('Guardar') ?>
                    </button>

                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFecha() {
            const tipo = document.getElementById('tabla').value;
            const group = document.getElementById('fechaGroup');
            const input = document.getElementById('fex1');
            group.style.display = tipo === 'C' ? 'block' : 'none';
            input.required = tipo === 'C';
        }
    </script>

    <?php Route::includeFile('/includes/layouts/scripts.php', true); ?>
</body>

</html>