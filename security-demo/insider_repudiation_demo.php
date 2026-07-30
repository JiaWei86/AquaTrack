<?php

declare(strict_types=1);

function writeDemoAdminActionLog(string $path, array $actor, array $waterSource, array $before, array $after): void
{
    // This context mirrors the fields written by
    // app/Observers/WaterSourceObserver.php in the production application.
    $context = [
        'action' => 'updated',
        'water_source_id' => $waterSource['id'],
        'actor_id' => $actor['id'],
        'actor_name' => $actor['name'],
        'actor_role' => $actor['role'],
        'ip_address' => $actor['ip_address'],
        'before' => $before,
        'after' => $after,
    ];

    $line = sprintf(
        "[%s] local.INFO: water_source.updated %s%s",
        date('Y-m-d H:i:s'),
        json_encode($context, JSON_UNESCAPED_SLASHES),
        PHP_EOL,
    );

    file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
}

$waterSource = [
    'id' => 7,
    'source_name' => 'Sungai Muda',
    'source_type' => 'River',
    'location' => 'Kedah',
];
$actor = [
    'id' => 42,
    'name' => 'Demo Administrator',
    'role' => 'Administrator',
    'ip_address' => '203.0.113.42',
];
$before = ['location' => $waterSource['location']];
$after = ['location' => 'Perlis'];

echo "INSIDER THREAT / REPUDIATION: WITHOUT AND WITH AUDIT LOGGING\n";
echo str_repeat('=', 65) . "\n\n";

echo "[1] WITHOUT LOGGING\n";
$waterSource['location'] = $after['location'];
echo "Administrator changed water source #{$waterSource['id']} location to {$waterSource['location']}.\n";
echo "Audit trail after the action: none. No actor, action, IP, or before/after record is kept.\n\n";

echo "[2] WITH LOGGING\n";
$demoDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'output';
if (! is_dir($demoDirectory)) {
    mkdir($demoDirectory, 0777, true);
}

$demoLog = $demoDirectory . DIRECTORY_SEPARATOR . 'admin-actions-demo.log';
file_put_contents($demoLog, '');
writeDemoAdminActionLog($demoLog, $actor, $waterSource, $before, $after);

echo "The same update is attributed in this isolated demo log:\n";
echo "{$demoLog}\n\n";
echo trim((string) file_get_contents($demoLog)) . "\n\n";
