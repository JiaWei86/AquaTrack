<?php

declare(strict_types=1);

/**
 * Classroom-only demonstration. It uses a fictional input string and does
 * not bootstrap Laravel, render real Blade views, or touch AquaTrack's
 * database or sessions.
 *
 * Run: php .\security-demo\output_encoding_demo.php
 */

$maliciousInput = "<script>alert('XSS')</script>";

echo "OUTPUT ENCODING: CROSS-SITE SCRIPTING (XSS)\n";
echo str_repeat('=', 58) . "\n\n";

echo "[1] WITHOUT OUTPUT ENCODING (VULNERABLE)\n\n";
echo "Malicious input:\n{$maliciousInput}\n\n";
// UNSAFE - FOR DEMO ONLY. Renders attacker input directly into HTML.
$unsafeHtml = "<div class=\"remarks\">{$maliciousInput}</div>";
echo "Output:\n{$unsafeHtml}\n\n";
echo "Result:\nXSS is possible because the script is output as executable HTML.\n\n";

echo str_repeat('-', 60) . "\n\n";

echo "[2] WITH OUTPUT ENCODING (SECURE)\n\n";
echo "Malicious input:\n{$maliciousInput}\n\n";
$encodedInput = htmlspecialchars($maliciousInput, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8');
echo "Encoded output:\n{$encodedInput}\n\n";
echo "Result:\nThe script is displayed as text and is not executed.\n\n";

echo str_repeat('-', 60) . "\n\n";

echo "AquaTrack implementation:\nresources/views/quality_readings/*.blade.php\n\n";
echo "Blade {{ }} automatically escapes dynamic output.\n";
