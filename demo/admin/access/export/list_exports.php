<?php
header('Content-Type: application/json');

$dir = __DIR__ . '/exports';
$exports = [];

if (!is_dir($dir)) {
    echo json_encode(['exports' => []]);
    exit;
}

// Get all progress files
$files = glob($dir . '/progress_export_*.json');

// Use export_id as key to prevent duplicates
$exportsMap = [];

foreach ($files as $file) {
    $data = json_decode(file_get_contents($file), true);

    if ($data) {
        $exportId = str_replace(['progress_', '.json'], '', basename($file));

        // Check if the actual file still exists
        $fileExists = isset($data['filepath']) && file_exists($data['filepath']);

        // Use a unique key: table_year_timestamp to detect duplicates
        $uniqueKey = ($data['table'] ?? 'unknown') . '_' .
            ($data['year'] ?? 'unknown') . '_' .
            ($data['filename'] ?? 'unknown');

        // If we already have this export, keep only the most recent one
        if (isset($exportsMap[$uniqueKey])) {
            $existingTime = strtotime($exportsMap[$uniqueKey]['created_at']);
            $newTime = strtotime($data['created_at'] ?? date('Y-m-d H:i:s'));

            // If new one is older, skip it
            if ($newTime <= $existingTime) {
                continue;
            }
        }

        $exportsMap[$uniqueKey] = [
            'export_id' => $exportId,
            'progress' => $data['progress'] ?? 0,
            'message' => $data['message'] ?? '',
            'complete' => $data['complete'] ?? false,
            'filename' => $data['filename'] ?? 'unknown.xlsx',
            'table' => $data['table'] ?? 'unknown',
            'year' => $data['year'] ?? 'unknown',
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
            'filesize' => $data['filesize'] ?? 0,
            'file_exists' => $fileExists,
            'filepath' => $data['filepath'] ?? null
        ];
    }
}

// Convert map to array
$exports = array_values($exportsMap);

// Sort by creation date, newest first
usort($exports, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode(['exports' => $exports]);
