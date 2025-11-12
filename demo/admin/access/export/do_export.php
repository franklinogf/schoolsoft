<?php

// This script runs in the background to perform the actual export

set_time_limit(300); // 5 minutes
ini_set('memory_limit', '512M');
$errorLogPath = __DIR__ . '/exports/export_errors.log';
ini_set('error_log', $errorLogPath);
ini_set('log_errors', '1');
ini_set('display_errors', '0');

require_once '../../../app.php';

use App\Models\Admin;
use App\Models\Scopes\YearScope;
use App\Models\Student;
use App\Models\Family;
use App\Models\StudentDocument;
use App\Models\Classes;
use App\Models\Payment;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Capsule\Manager as DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Get command line arguments
$exportId = $argv[1] ?? '';
$table = $argv[2] ?? '';
$year = $argv[3] ?? '';

if (empty($exportId) || empty($table) || empty($year)) {
    die('Error: Missing parameters');
}

$progressFile = __DIR__ . '/exports/progress_' . basename($exportId) . '.json';




// Helper function to update progress
function updateProgress($progressFile, $percent, $message, $complete = false): void
{

    if (!$progressFile) return;

    // Read existing data to preserve metadata
    $existingData = [];
    if (file_exists($progressFile)) {
        $existingData = json_decode(file_get_contents($progressFile), true) ?? [];
    }

    $data = array_merge($existingData, [
        'progress' => $percent,
        'message' => $message,
        'complete' => $complete
    ]);

    if ($complete) {
        $data['completed_at'] = time();
    }

    file_put_contents($progressFile, json_encode($data));

    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($progressFile, true);
    }
}

try {
    updateProgress($progressFile, 5, 'Iniciando exportación...');

    // Create new Excel file
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    updateProgress($progressFile, 10, 'Preparando datos...');

    // Configure based on selected table
    switch ($table) {
        case 'food_assistance':
            exportFoodAssistance($sheet, $year, $progressFile);
            break;
        case 'cafeteria':
            exportCafeteria($sheet, $year, $progressFile);
            break;
        default:
            generalExport($table, $sheet, $year, $progressFile);
            break;
    }

    updateProgress($progressFile, 80, 'Aplicando estilos...');

    // Apply styles to header - check if there are any columns first
    $highestColumn = $sheet->getHighestColumn();
    $highestRow = $sheet->getHighestRow();

    if ($highestRow > 0 && $highestColumn) {
        try {
            $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error applying styles: " . $e->getMessage());
        }

        // Auto-adjust column width
        try {
            $columnIterator = $sheet->getColumnIterator();
            foreach ($columnIterator as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        } catch (Exception $e) {
            error_log("Error auto-sizing columns: " . $e->getMessage());
        }
    }

    updateProgress($progressFile, 90, 'Guardando archivo...');

    // Save file
    $filename = $table . '_' . $year . '_' . date('Ymd_His') . '.xlsx';
    $filepath = __DIR__ . '/exports/' . $filename;

    $writer = new Xlsx($spreadsheet);
    $writer->save($filepath);

    updateProgress($progressFile, 100, 'Exportación completada', true);

    // Store filename and metadata in progress file for download
    $progressData = json_decode(file_get_contents($progressFile), true);
    $progressData['filename'] = $filename;
    $progressData['filepath'] = $filepath;
    $progressData['table'] = $table;
    $progressData['year'] = $year;
    $progressData['created_at'] = date('Y-m-d H:i:s');
    $progressData['filesize'] = filesize($filepath);
    file_put_contents($progressFile, json_encode($progressData));
} catch (Exception $e) {
    // Log the full error details for debugging
    $fullErrorMessage = 'Error: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine();
    error_log("Export error: " . $fullErrorMessage);
    error_log("Stack trace: " . $e->getTraceAsString());

    // Update progress with generic error message and error flag
    $progressData = json_decode(file_get_contents($progressFile), true) ?? [];
    $progressData['progress'] = 0;
    $progressData['message'] = 'Error al procesar la exportación';
    $progressData['complete'] = false;
    $progressData['error'] = true; // Mark as error to stop the export
    file_put_contents($progressFile, json_encode($progressData));

    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($progressFile, true);
    }
}

// ==================== EXPORT FUNCTIONS ====================
function generalExport(string $table, Worksheet $sheet, string $year, ?string $progressFile = null): void
{
    $title =  match ($table) {
        'students' => 'Students',
        'families' => 'Families',
        'grades' => 'Grades',
        'payments' => 'Payments',
        'student_documents' => 'Student Documents',
        default => ucfirst($table),
    };
    $sheet->setTitle($title);

    if ($progressFile) updateProgress($progressFile, 15, "Obteniendo datos de $title...");

    $data = match ($table) {
        'students' => Student::query()
            ->withoutGlobalScope(YearScope::class)
            ->where('year', $year)
            ->orderBy('apellidos')
            ->get(),
        'families' => Family::query()
            ->whereHas('kids', function (Builder $query) use ($year): void {
                $query->withoutGlobalScope(YearScope::class)
                    ->where('year', $year);
            })
            ->get(),
        'grades' => Classes::query()
            ->withoutGlobalScope(YearScope::class)
            ->where('year', $year)
            ->get(),
        'payments' => Payment::query()
            ->withoutGlobalScope(YearScope::class)
            ->where('year', $year)
            ->get(),
        'student_documents' => StudentDocument::query()
            ->whereHas('student', function (Builder $query) use ($year): void {
                $query->withoutGlobalScope(YearScope::class)
                    ->where('year', $year);
            })
            ->get(),
        default => DB::table($table)
            ->where('year', $year)
            ->get(),
    };

    if ($data->isEmpty()) {
        $sheet->setCellValue('A1', 'No data found for this year');
        return;
    }

    if ($progressFile) updateProgress($progressFile, 25, 'Preparando columnas...');

    $firstItem = $data->first();
    $headers = array_keys($firstItem->getAttributes());
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    if ($progressFile) updateProgress($progressFile, 30, "Exportando datos de $title...");

    $row = 2;
    $total = $data->count();
    foreach ($data as $index => $item) {
        $col = 'A';
        foreach ($headers as $field) {
            $value = $item->{$field} ?? '';
            $sheet->setCellValue($col . $row, $value);
            $col++;
        }
        $row++;
        if ($progressFile && $index % 100 === 0) {
            $progress = 30 + ($index / $total) * 40;
            updateProgress($progressFile, $progress, "Exportando $title: $index de $total");
        }
    }

    if ($progressFile) updateProgress($progressFile, 70, "$title exportados");
}

function exportFoodAssistance(Worksheet $sheet, string $year, ?string $progressFile = null): void
{
    $sheet->setTitle('Food Assistance');

    if ($progressFile) updateProgress($progressFile, 15, 'Obteniendo asistencia alimentaria...');

    $headers =
        [
            'Seguro Social Estudiante',
            'Número Estudiante',
            'Nombre',
            'Inicial',
            'Apellido Paterno',
            'Apellido Materno',
            'Sexo',
            'Fecha Nacimiento',
            'Ciudadania',
            'Estado Civil',
            'Nombre Padre o Encargado',
            'Incapacidad',
            'Código de Escolaridad',
            'Escuela',
            'Municipio Escuela',
            'Asiste Regularidad',
            'Teléfono',
            'Email',
            'Domicilio Postal 1',
            'Domicilio Postal 2',
            'Código Postal 1',
            'Código Postal 2',
            'Ciudad Postal',
            'Domicilio Residencial 1',
            'Domicilio Residencial 2',
            'Código Postal Residencial 1',
            'Código Postal Residencial 2',
            'Ciudad Residencial'
        ];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    $students = Student::query()
        ->withoutGlobalScope(YearScope::class)
        ->where('year', $year)
        ->get();
    $school = Admin::primaryAdmin();

    if ($progressFile) updateProgress($progressFile, 30, 'Exportando asistencia...');

    $row = 2;
    $total = $students->count();
    foreach ($students as $index => $record) {
        $sheet->setCellValue('A' . $row, $record->ss ?? '');
        $sheet->setCellValue('B' . $row, $record->id ?? '');
        $sheet->setCellValue('C' . $row, $record->nombre ?? '');
        $sheet->setCellValue('D' . $row, $record->nombre[0] ?? '');
        $sheet->setCellValue('E' . $row, $record->apellidos ? explode(' ', $record->apellidos)[0] : '');
        $sheet->setCellValue('F' . $row, $record->apellidos ? explode(' ', $record->apellidos)[1] : '');
        $sheet->setCellValue('G' . $row, ($record->genero === 'M' || $record->genero === '2') ? 'M' : 'F');
        $sheet->setCellValue('H' . $row, $record->fecha === '0000-00-00' ? '' : $record->fecha ?? '');

        $sheet->setCellValue('I' . $row,  '');
        $sheet->setCellValue('J' . $row,  '');
        $sheet->setCellValue('K' . $row,  $record->family->madre ?? $record->family->padre ?? $record->family->encargado ?? '');
        $sheet->setCellValue('L' . $row,  '');

        $sheet->setCellValue('M' . $row,  $record->grado ?? '');
        $sheet->setCellValue('N' . $row,  $school->colegio ?? '');
        $sheet->setCellValue('O' . $row,  $school->pueblo1 ?? '');
        $sheet->setCellValue('P' . $row,  '');

        $sheet->setCellValue('Q' . $row,  $record->family->tel_m !== "" ? $record->family->tel_m : $record->family->tel_p);
        $sheet->setCellValue('R' . $row,  $record->family->email_m !== "" ? $record->family->email_m : $record->family->email_p);

        $sheet->setCellValue('S' . $row,  $record->family->dir1 ?? '');
        $sheet->setCellValue('T' . $row,  $record->family->dir3 ?? '');
        $sheet->setCellValue('U' . $row,  $record->family->zip1 ?? '');
        $sheet->setCellValue('V' . $row,   ''); // Código Postal 1 (si existe)
        $sheet->setCellValue('W' . $row,  $record->family->pueblo1 ?? '');

        $sheet->setCellValue('X' . $row, $record->family->dir2 ?? '');
        $sheet->setCellValue('Y' . $row, $record->family->dir4 ?? '');
        $sheet->setCellValue('Z' . $row, $record->family->zip2 ?? '');
        $sheet->setCellValue('AA' . $row, ''); // Código Postal Residencial 2 (si existe)
        $sheet->setCellValue('AB' . $row, $record->family->pueblo2 ?? '');
        $row++;

        if ($progressFile && $index % 100 === 0) {
            $progress = 30 + (($index / $total) * 40);
            updateProgress($progressFile, $progress, "Exportando asistencia económica: $index de $total");
        }
    }

    if ($progressFile) updateProgress($progressFile, 70, 'Asistencia económica exportada');
}

function exportCafeteria(Worksheet $sheet, string $year, ?string $progressFile = null): void
{
    $sheet->setTitle('Cafeteria');

    if ($progressFile) updateProgress($progressFile, 15, 'Obteniendo órdenes del comedor...');

    $headers = [
        'Número Estudiante',
        'Seguro Social',
        'Nombre',
        'Inicial',
        'Apellido Paterno',
        'Apellido Materno',
        'Sexo',
        'Fecha Nacimiento',
        'Ciudadania',
        'Estado Civil',
        'Nombre Padre O Encargado',
        'Incapacidad',
        'Codigo de Escolaridad',
        'Escuela',
        'Municipio Escuela',
        'Asiste Regularidad',
        'Telefono',
        'Email',
        'Dir1 Postal',
        'Dir2 Postal',
        'Zip Code Postal',
        'Ciudad Postal',
        'Dir1 Residencial',
        'Dir2 Residencial',
        'Zip Code Residencial',
        'Ciudad Residencial',
        'Fecha Matricula',
        'Fecha Baja',
        'School Code',
        'Dias Virtuales'
    ];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    $students = Student::query()
        ->withoutGlobalScope(YearScope::class)
        ->where('year', $year)
        ->get();
    $school = Admin::primaryAdmin();

    if ($progressFile) updateProgress($progressFile, 30, 'Exportando órdenes...');

    $row = 2;
    $total = $students->count();
    foreach ($students as $index => $student) {

        $sheet->setCellValue('A' . $row, $student->id ?? '');
        $sheet->setCellValue('B' . $row, $student->ss ?? '');
        $sheet->setCellValue('C' . $row, $student->nombre ?? '');
        $sheet->setCellValue('D' . $row, $student->nombre[0] ?? '');
        $sheet->setCellValue('E' . $row, $student->apellidos ? explode(' ', $student->apellidos)[0] : '');
        $sheet->setCellValue('F' . $row, $student->apellidos ? explode(' ', $student->apellidos)[1] : '');
        $sheet->setCellValue('G' . $row, ($student->genero === 'M' || $student->genero === '2') ? 'M' : 'F');
        $sheet->setCellValue('H' . $row, $student->fecha === '0000-00-00' ? '' : $student->fecha ?? '');
        $sheet->setCellValue('I' . $row,  ''); // Ciudadania
        $sheet->setCellValue('J' . $row,  ''); // Estado Civil
        $sheet->setCellValue('K' . $row,  $student->family->madre ?? $student->family->padre ?? $student->family->encargado ?? '');
        $sheet->setCellValue('L' . $row,  ''); // Incapacidad
        $sheet->setCellValue('M' . $row,  $student->grado ?? '');
        $sheet->setCellValue('N' . $row,  $school->colegio ?? '');
        $sheet->setCellValue('O' . $row,  $school->pueblo1 ?? '');
        $sheet->setCellValue('P' . $row,  ''); // Asiste Regularidad
        $sheet->setCellValue('Q' . $row,  $student->family->tel_m !== "" ? $student->family->tel_m : $student->family->tel_p);
        $sheet->setCellValue('R' . $row,  $student->family->email_m !== "" ? $student->family->email_m : $student->family->email_p);
        $sheet->setCellValue('S' . $row,  $student->family->dir1 ?? '');
        $sheet->setCellValue('T' . $row,  $student->family->dir3 ?? '');
        $sheet->setCellValue('U' . $row,  $student->family->zip1 ?? '');
        $sheet->setCellValue('V' . $row,   $student->family->pueblo1 ?? '');
        $sheet->setCellValue('W' . $row, $student->family->dir2 ?? '');
        $sheet->setCellValue('X' . $row, $student->family->dir4 ?? '');
        $sheet->setCellValue('Y' . $row, $student->family->zip2 ?? '');
        $sheet->setCellValue('Z' . $row, $student->family->pueblo2 ?? '');
        $sheet->setCellValue('AA' . $row, $student->fecha_matri === '0000-00-00' ? '' : $student->fecha_matri ?? '');
        $sheet->setCellValue('AB' . $row, $student->fecha_baja === '0000-00-00' ? '' : $student->fecha_baja ?? '');
        $sheet->setCellValue('AC' . $row, ''); // School Code
        $sheet->setCellValue('AD' . $row, ''); // Dias Virtuales
        $row++;

        if ($progressFile && $index % 50 === 0) {
            $progress = 30 + (($index / $total) * 40);
            updateProgress($progressFile, $progress, "Exportando: $index de $total");
        }
    }

    $sheet->getStyle('F2:F' . ($row - 1))->getNumberFormat()->setFormatCode('$#,##0.00');

    if ($progressFile) updateProgress($progressFile, 70, 'Cafeteria exportada');
}
