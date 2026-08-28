<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

function printRows(string $label, array $rows): void
{
    echo "{$label}: " . count($rows) . " row(s) returned\n";
    foreach ($rows as $row) {
        echo "  #{$row['id']} {$row['source_name']} ({$row['source_type']})\n";
    }
}

// Isolated in-memory database, set up using the same query builder
// (Illuminate\Database) that powers Laravel's Eloquent ORM.
$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => 'sqlite',
    'database' => ':memory:',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->create('water_sources', function ($table) {
    $table->increments('id');
    $table->string('source_name');
    $table->string('source_type');
});

Capsule::table('water_sources')->insert([
    ['id' => 1, 'source_name' => 'Sungai Muda', 'source_type' => 'River'],
    ['id' => 2, 'source_name' => 'Bukit Merah', 'source_type' => 'Reservoir'],
    ['id' => 3, 'source_name' => 'Kampung Well', 'source_type' => 'Well'],
]);

$maliciousInput = '999 OR 1=1';

echo "SQL INJECTION: WITHOUT SECURE CODING PRACTICE\n";
echo str_repeat('=', 60) . "\n";
echo "User-supplied id: {$maliciousInput}\n\n";

echo "[1] VULNERABLE VERSION\n";
$unsafeSql = "SELECT id, source_name, source_type FROM water_sources WHERE id = {$maliciousInput}";
echo "Executed SQL: {$unsafeSql}\n";
$unsafeRows = array_map(
    fn ($row) => (array) $row,
    Capsule::connection()->select($unsafeSql)
);
printRows('Behaviour', $unsafeRows);

echo "\n\nSQL INJECTION: WITH SECURE CODING PRACTICE\n";
echo "(Laravel Eloquent Query Builder)\n";
echo str_repeat('=', 60) . "\n";
echo "User-supplied id: {$maliciousInput}\n\n";

echo "[2] SECURE VERSION\n";
echo "Executed via: Capsule::table('water_sources')->where('id', \$maliciousInput)->get();\n";
$secureRows = Capsule::table('water_sources')
    ->where('id', $maliciousInput)
    ->get()
    ->map(fn ($row) => (array) $row)
    ->all();
printRows('Behaviour', $secureRows);