<?php

// Download the generated export file

$exportId = $_GET['export_id'] ?? '';

if (empty($exportId)) {
    die('Error: Missing export ID');
}

$progressFile = __DIR__ . '/exports/progress_' . basename($exportId) . '.json';

if (!file_exists($progressFile)) {
    die('Error: Export not found');
}

$progressData = json_decode(file_get_contents($progressFile), true);

if (!isset($progressData['filepath']) || !file_exists($progressData['filepath'])) {
    die('Error: Export file not found');
}

$filepath = $progressData['filepath'];
$filename = $progressData['filename'];

// Download file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);

// Don't delete files - keep them for history
// @unlink($filepath);
// @unlink($progressFile);

exit;
