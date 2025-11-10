<?php
require_once '../../../app.php';

use Classes\Session;
use Illuminate\Database\Capsule\Manager as DB;

Session::is_logged();

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'copiedData' => []
];

try {
    // if (!isset($_POST['copy_data'])) {
    //     throw new Exception(__('Solicitud inválida'));
    // }

    $sourceYear = $_POST['source_year'] ?? '';
    $destYear = $_POST['dest_year'] ?? '';

    // Validaciones
    if (empty($sourceYear) || empty($destYear)) {
        throw new Exception(__('Debe seleccionar el año de origen y destino'));
    }

    if ($sourceYear === $destYear) {
        throw new Exception(__('El año de origen y destino deben ser diferentes'));
    }

    $selectedOptions = [
        'catalog' => isset($_POST['opt_catalog']) && $_POST['opt_catalog'] === '1',
        'courses' => isset($_POST['opt_courses']) && $_POST['opt_courses'] === '1',
        'budget' => isset($_POST['opt_budget']) && $_POST['opt_budget'] === '1',
        'costs' => isset($_POST['opt_costs']) && $_POST['opt_costs'] === '1',
        'photos' => isset($_POST['opt_photos']) && $_POST['opt_photos'] === '1',
        'balances' => isset($_POST['opt_balances']) && $_POST['opt_balances'] === '1',
    ];

    if (!in_array(true, $selectedOptions, true)) {
        throw new Exception(__('Seleccione al menos un tipo de dato para copiar'));
    }

    // Copiar Catálogo de cursos (tabla: cursos)
    if ($selectedOptions['catalog']) {
        $count = DB::table('cursos')
            ->where('year', $sourceYear)
            ->get()
            ->each(function (stdClass $course) use ($destYear): void {
                $courseData = (array) $course;
                $courseData['year'] = $destYear;
                unset($courseData['mt']); // Remove auto-increment if exists
                DB::table('cursos')->insert($courseData);
            })
            ->count();

        $response['copiedData'][] = __('Catálogo de cursos') . ": $count " . __('registros');
    }

    // Copiar Cursos/Materias (tabla: materias)
    if ($selectedOptions['courses']) {
        $count = DB::table('materias')
            ->where('year', $sourceYear)
            ->get()
            ->each(function ($materia) use ($destYear) {
                $materiaData = (array) $materia;
                $materiaData['year'] = $destYear;
                unset($materiaData['mt']); // Remove auto-increment if exists
                DB::table('materias')->insert($materiaData);
            })
            ->count();

        $response['copiedData'][] = __('Cursos (Materias)') . ": $count " . __('registros');
    }

    // Copiar Presupuesto (tabla: presupuesto)
    if ($selectedOptions['budget']) {
        $count = DB::table('presupuesto')
            ->where('year', $sourceYear)
            ->get()
            ->each(function ($presupuesto) use ($destYear) {
                $presupuestoData = (array) $presupuesto;
                $presupuestoData['year'] = $destYear;
                unset($presupuestoData['mt']); // Remove auto-increment if exists
                DB::table('presupuesto')->insert($presupuestoData);
            })
            ->count();

        $response['copiedData'][] = __('Presupuesto') . ": $count " . __('registros');
    }

    // Copiar Costos (tabla: costos)
    if ($selectedOptions['costs']) {
        $count = DB::table('costos')
            ->where('year', $sourceYear)
            ->get()
            ->each(function ($costo) use ($destYear) {
                $costoData = (array) $costo;
                $costoData['year'] = $destYear;
                unset($costoData['mt']); // Remove auto-increment if exists
                DB::table('costos')->insert($costoData);
            })
            ->count();

        $response['copiedData'][] = __('Costos') . ": $count " . __('registros');
    }

    // Copiar Fotos (tabla: year - campo tipo)
    if ($selectedOptions['photos']) {
        $yearData = DB::table('year')->where('year', $sourceYear)->get();
        $count = 0;
        foreach ($yearData as $yearRecord) {
            DB::table('year')->where([
                ['ss', $yearRecord->ss],
                ['year', $destYear],
            ])->update([
                'tipo' => $yearRecord->tipo ?? '',
            ]);
            $count++;
        }
        $response['copiedData'][] = __('Fotos') . ": $count " . __('registros');
    }

    // Copiar Balances de cafetería (tabla: year - campos balance_a y cantidad)
    if ($selectedOptions['balances']) {
        $yearData = DB::table('year')->where('year', $sourceYear)->get();
        $count = 0;
        foreach ($yearData as $yearRecord) {
            DB::table('year')->where([
                ['ss', $yearRecord->ss],
                ['year', $destYear],
            ])->update([
                'balance_a' => $yearRecord->balance_a ?? 0,
                'cantidad' => $yearRecord->cantidad ?? 0,
            ]);
            $count++;
        }
        $response['copiedData'][] = __('Balances de cafetería') . ": $count " . __('registros');
    }

    $response['success'] = true;
    $response['message'] = __('Datos copiados exitosamente');
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
