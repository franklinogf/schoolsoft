<?php
// This script performs the actual backup work
require_once '../../../app.php';

use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Database\Capsule\Manager as DB;

set_time_limit(0);
ini_set("memory_limit", "1024M");

// Get backup ID and year from command line arguments
$backupId = $argv[1] ?? null;
$year = $argv[2] ?? null;

if (!$backupId) {
    exit('No backup ID provided');
}

// Remove quotes if present (from empty string argument)
if ($year === '""' || $year === "''") {
    $year = null;
}

$dir = __DIR__ . '/tmp';
$statusFile = "{$dir}/{$backupId}.status";
$sqlFile = "{$dir}/{$backupId}.sql";
$zipFile = "{$sqlFile}.zip";

function update_status($status): void
{
    global $statusFile;
    file_put_contents($statusFile, json_encode(['status' => $status]));
}

/**
 * Get all tables that have a 'year' column
 */
function getTablesWithYearColumn($dbName): array
{
    $results = DB::select("
        SELECT DISTINCT TABLE_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = :dbName 
        AND COLUMN_NAME = 'year'
    ", ['dbName' => $dbName]);

    $tables = [];
    foreach ($results as $row) {
        $tables[] = $row->TABLE_NAME;
    }

    return $tables;
}

try {
    $dbHost = school_config('database.host');
    $dbName = school_config('database.database');
    $dbUser = school_config('database.username');
    $dbPass = school_config('database.password');

    update_status('dumping');

    $dumpOptions = [
        'single-transaction' => true,
        'lock-tables' => false,
        'extended-insert' => true,
        'skip-comments' => true,
        'skip-triggers' => false,
    ];

    $dump = new Mysqldump(
        "mysql:host=$dbHost;dbname=$dbName",
        $dbUser,
        $dbPass,
        $dumpOptions
    );

    // If year is specified, configure table filtering
    if ($year) {
        $tablesWithYear = getTablesWithYearColumn($dbName);

        // Add WHERE conditions only for tables that have year column
        // Tables without year column will be fully backed up (no WHERE condition)
        $whereConditions = [];
        foreach ($tablesWithYear as $table) {
            $whereConditions[$table] = "year = '{$year}'";
        }

        if (!empty($whereConditions)) {
            $dump->setTableWheres($whereConditions);
        }
    }

    $dump->start($sqlFile);

    update_status('zipping');

    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
        $zip->addFile($sqlFile, basename($sqlFile));
        $zip->close();
        unlink($sqlFile);
    }

    update_status('done');
} catch (Exception $e) {
    update_status('error');
    file_put_contents($statusFile, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
}
