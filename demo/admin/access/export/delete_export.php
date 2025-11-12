<?php

// Delete an export file and its progress file

$exportId = $_POST['export_id'] ?? '';

if (empty($exportId)) {
    echo json_encode(['success' => false, 'error' => 'Missing export ID']);
    exit;
}

$progressFile = __DIR__ . '/exports/progress_' . basename($exportId) . '.json';

if (!file_exists($progressFile)) {
    echo json_encode(['success' => false, 'error' => 'Export not found']);
    exit;
}

$progressData = json_decode(file_get_contents($progressFile), true);

// Delete the export file if it exists
if (isset($progressData['filepath']) && file_exists($progressData['filepath'])) {
    @unlink($progressData['filepath']);
}

// Delete the progress file
@unlink($progressFile);

echo json_encode(['success' => true]);
