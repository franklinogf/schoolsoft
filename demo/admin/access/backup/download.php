<?php
$baseDir = __DIR__ . '/tmp';
$id = $_GET['id'] ?? null;

if ($id) {
    // Download specific backup by ID
    $zipFile = "{$baseDir}/{$id}.sql.zip";
    if (!file_exists($zipFile)) {
        http_response_code(404);
        echo "Backup not found.";
        exit;
    }
} else {
    // Find the latest zip if no ID specified
    $files = glob("{$baseDir}/backup_*.sql.zip");
    if (empty($files)) {
        http_response_code(404);
        echo "No backup found.";
        exit;
    }
    rsort($files);
    $zipFile = $files[0];
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);
exit;
