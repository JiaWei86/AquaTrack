<?php

declare(strict_types=1);

/**
 * Classroom-only demonstration. It uses a simple in-memory request counter
 * and does not bootstrap Laravel, use the real RateLimiter, send HTTP
 * requests, or touch AquaTrack's routes.
 *
 * Run: php .\security-demo\rate_limiting_demo.php
 */

/**
 * @return array<int, string> one 'ALLOWED' or 'BLOCKED (429)' result per request
 */
function simulateRequests(int $totalRequests, ?int $limit): array
{
    $results = [];

    for ($requestNumber = 1; $requestNumber <= $totalRequests; $requestNumber++) {
        $results[] = ($limit === null || $requestNumber <= $limit) ? 'ALLOWED' : 'BLOCKED (429)';
    }

    return $results;
}

function printRequests(array $results): void
{
    foreach ($results as $requestNumber => $result) {
        echo sprintf("Request %-2d -> %s\n", $requestNumber + 1, $result);
    }
}

$totalRequests = 10;
$demoLimit = 5; // Kept small for classroom demonstration; production uses 60/minute.

echo "RATE LIMITING: AUTOMATED ABUSE / APPLICATION-LAYER DOS\n";
echo str_repeat('=', 58) . "\n\n";

echo "[1] WITHOUT RATE LIMITING (VULNERABLE)\n\n";
echo "Endpoint:\nGET /api/quality-readings\n\n";
// UNSAFE - FOR DEMO ONLY. No request limit is applied.
echo "Requests:\n";
printRequests(simulateRequests($totalRequests, null));
echo "\nResult:\nAll requests are accepted because no rate limit is applied. An automated\nclient could keep sending requests and consume application resources.\n\n";

echo str_repeat('-', 60) . "\n\n";

echo "[2] WITH RATE LIMITING (SECURE)\n\n";
echo "Endpoint:\nGET /api/quality-readings\n\n";
echo "Demo limit:\n{$demoLimit} requests per minute\n\n";
echo "Requests:\n";
printRequests(simulateRequests($totalRequests, $demoLimit));
echo "\nResult:\nExcessive requests are rejected after the rate limit is reached. HTTP 429\nmeans \"Too Many Requests\": the server is refusing further requests from this\nclient until the limit window resets, which prevents automated abuse from\ncontinuously reaching the application.\n\n";

echo str_repeat('-', 60) . "\n\n";

echo "AquaTrack implementation:\nroutes/api.php\n\n";
echo "The Quality Reading API uses:\nthrottle:60,1\n\n";
echo "This limits the API to 60 requests per minute. This demo uses {$demoLimit}\n";
echo "requests per minute only so the terminal output stays short enough to present.\n";
