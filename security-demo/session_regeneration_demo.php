<?php

declare(strict_types=1);

// Buffer the explanatory output so PHP can still initialise sessions below.
ob_start();

/**
 * Classroom-only demonstration of session fixation/hijacking risk. It uses an
 * isolated temporary session directory and no AquaTrack user or session data.
 *
 * Run: php .\security-demo\session_regeneration_demo.php
 */

$demoSessionPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'aquatrack-session-demo';

if (!is_dir($demoSessionPath)) {
    mkdir($demoSessionPath, 0700, true);
}

session_save_path($demoSessionPath);

// A known pre-login ID represents an ID an attacker has obtained before login.
// It is a fictional local ID, used only in this isolated demo.
$knownPreLoginId = 'demoknownpreloginid1234567890';

echo "SESSION MANAGEMENT: SESSION HIJACKING\n";
echo str_repeat('=', 58) . "\n\n";

echo "[1] WITHOUT SESSION REGENERATION (VULNERABLE)\n";
session_id($knownPreLoginId);
session_start();
$_SESSION['user_id'] = 101; // Login succeeds, but the old ID is retained.
$unsafeLoggedInId = session_id();
session_write_close();

echo "Pre-login session ID: {$knownPreLoginId}\n";
echo "After login session ID: {$unsafeLoggedInId}\n";
echo "Result: unchanged. A party that already knows this ID could reuse the logged-in session.\n\n";

echo "[2] WITH SESSION REGENERATION (SECURE)\n";
session_id($knownPreLoginId);
session_start();
$_SESSION['user_id'] = 101; // Login succeeds.
session_regenerate_id(true); // Replace the pre-login ID and remove its old session data.
$safeLoggedInId = session_id();
session_write_close();

echo "Pre-login session ID: {$knownPreLoginId}\n";
echo "After login session ID: {$safeLoggedInId}\n";
echo 'Result: ' . ($knownPreLoginId !== $safeLoggedInId ? 'new ID issued; the known pre-login ID is invalidated.' : 'unexpected: ID was not changed.') . "\n\n";

echo "AquaTrack implementation: app/Http/Controllers/AuthController.php calls\n";
echo "request()->session()->regenerate() immediately after Auth::attempt().\n";

ob_end_flush();
