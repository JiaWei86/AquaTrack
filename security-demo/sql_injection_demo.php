<?php

declare(strict_types=1);

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
$maliciousInput = '999 OR 1=1'; 

echo "SQL INJECTION: WITHOUT SECURE CODING PRACTICE\n";
echo str_repeat('=', 58) . "\n";
echo "User-supplied id: {$maliciousInput}\n\n";

echo "[1] VULNERABLE VERSION \n";
$unsafeSql = 'SELECT id, source_name, source_type FROM water_sources WHERE id = ' . $maliciousInput;
echo "Executed SQL: {$unsafeSql}\n";
$unsafeRows = $database->query($unsafeSql)->fetchAll(PDO::FETCH_ASSOC);
printRows('Behaviour', $unsafeRows);

