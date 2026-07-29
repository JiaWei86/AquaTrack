<?php

declare(strict_types=1);

/**
 * SQL Injection comparison for classroom demonstration only.
 *
 * This script uses an in-memory SQLite database. It does not bootstrap
 * Laravel, connect to AquaTrack's configured database, or alter any project
 * tables. Run with: php security-demo/sql_injection_demo.php
 */

function printRows(string $label, array $rows): void
{
    echo "{$label}: " . count($rows) . " row(s) returned\n";

    foreach ($rows as $row) {
        echo "  - #{$row['id']} {$row['source_name']} ({$row['source_type']})\n";
    }
}

$database = new PDO('sqlite::memory:');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$database->exec(
    "CREATE TABLE water_sources (id INTEGER PRIMARY KEY, source_name TEXT, source_type TEXT);"
);
$database->exec(
    "INSERT INTO water_sources (id, source_name, source_type) VALUES
        (1, 'Sungai Muda', 'River'),
        (2, 'Bukit Merah', 'Reservoir'),
        (3, 'Kampung Well', 'Well');"
);

// This benign payload changes a WHERE id condition into a condition that is
// true for every row. It demonstrates injection without destructive SQL.
$maliciousInput = '1 OR 1=1';

echo "SQL INJECTION: WITHOUT AND WITH PARAMETERIZATION\n";
echo str_repeat('=', 58) . "\n";
echo "User-supplied id: {$maliciousInput}\n\n";

echo "[1] VULNERABLE VERSION — FOR DEMO ONLY\n";
// UNSAFE - FOR DEMO ONLY, NEVER USE
$unsafeSql = 'SELECT id, source_name, source_type FROM water_sources WHERE id = ' . $maliciousInput;
echo "Executed SQL: {$unsafeSql}\n";
$unsafeRows = $database->query($unsafeSql)->fetchAll(PDO::FETCH_ASSOC);
printRows('Behaviour', $unsafeRows);

echo "\n[2] SECURE VERSION — PARAMETERIZED QUERY\n";
$safeSql = 'SELECT id, source_name, source_type FROM water_sources WHERE id = :id';
echo "SQL template: {$safeSql}\n";
echo "Bound parameter :id: {$maliciousInput}\n";
$safeStatement = $database->prepare($safeSql);
$safeStatement->execute(['id' => $maliciousInput]);
$safeRows = $safeStatement->fetchAll(PDO::FETCH_ASSOC);
printRows('Behaviour', $safeRows);

echo "\nThe parameterized version sends the input as data, not SQL syntax.\n";

