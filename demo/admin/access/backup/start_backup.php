<?php
header('Content-Type: application/json');

set_time_limit(0);
ini_set("memory_limit", "1024M");

$year = $_POST['year'] ?? null;
$timestamp = date('Y-m-d_H-i-s');
$backupId = "backup_{$timestamp}";


$dir = __DIR__ . '/tmp';
$statusFile = "{$dir}/{$backupId}.status";


@mkdir($dir, 0777, true);

// 1️⃣ Remove previous backup files (all types: .status, .sql, .zip)
foreach (glob($dir . '/backup_*.*') as $oldFile) {
    @unlink($oldFile);
}

file_put_contents($statusFile, json_encode(['status' => 'starting']));

// Start the backup process in the background using PHP CLI
$phpPath = PHP_BINARY; // Path to PHP executable
$scriptPath = __DIR__ . '/do_backup.php';

$yearArg = $year ? escapeshellarg($year) : '""';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows: use START command to run in background
    pclose(popen("start /B \"\" \"$phpPath\" \"$scriptPath\" \"$backupId\" $yearArg 2>&1", "r"));
} else {
    // Linux/Unix: use & to run in background
    exec("$phpPath \"$scriptPath\" \"$backupId\" $yearArg > /dev/null 2>&1 &");
}

// Return immediately
echo json_encode(['backup_id' => $backupId, 'status' => 'starting', 'created_at' => $timestamp]);
