<?php
header('Content-Type: application/json');

set_time_limit(0);
ini_set("memory_limit", "512M");

$table = $_POST['tabla'] ?? null;
$year = $_POST['year'] ?? null;
$timestamp = time();
$exportId = "export_{$timestamp}";

if (!$table || !$year) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$dir = __DIR__ . '/exports';
$statusFile = "{$dir}/progress_{$exportId}.json";

@mkdir($dir, 0777, true);

// Initialize status file with all metadata
file_put_contents($statusFile, json_encode([
    'progress' => 0,
    'message' => 'Iniciando exportación...',
    'complete' => false,
    'table' => $table,
    'year' => $year,
    'created_at' => date('Y-m-d H:i:s'),
    'export_id' => $exportId
]));

// Start the export process in the background using PHP CLI
$phpPath = PHP_BINARY; // Path to PHP executable
$scriptPath = __DIR__ . '/do_export.php';

$tableArg = escapeshellarg($table);
$yearArg = escapeshellarg($year);
$exportIdArg = escapeshellarg($exportId);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows: use START command to run in background
    pclose(popen("start /B \"\" \"$phpPath\" \"$scriptPath\" $exportIdArg $tableArg $yearArg 2>&1", "r"));
} else {
    // Linux/Unix: use & to run in background
    exec("$phpPath \"$scriptPath\" $exportIdArg $tableArg $yearArg > /dev/null 2>&1 &");
}

// Return immediately
echo json_encode([
    'export_id' => $exportId,
    'status' => 'starting',
    'created_at' => date('Y-m-d H:i:s')
]);
