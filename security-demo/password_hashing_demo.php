<?php

declare(strict_types=1);

/**
 * Classroom-only demonstration. It uses fictional data and does not connect
 * to AquaTrack's database or authentication system.
 *
 * Run: php .\security-demo\password_hashing_demo.php
 */

$passwordEnteredAtLogin = 'DemoPass123';
$demoUser = [
    'email' => 'resident.demo@example.test',
    // UNSAFE - FOR DEMO ONLY. A data breach exposes the actual password.
    'password' => 'DemoPass123',
];

echo "PASSWORD MANAGEMENT: CREDENTIAL THEFT VIA DATA BREACH\n";
echo str_repeat('=', 58) . "\n\n";

echo "[1] WITHOUT PASSWORD HASHING (VULNERABLE)\n";
echo "Database row leaked in a breach:\n";
echo "  email: {$demoUser['email']}\n";
echo "  password: {$demoUser['password']}  <-- readable password\n";
echo 'Login result: ' . ($passwordEnteredAtLogin === $demoUser['password'] ? 'accepted' : 'rejected') . "\n";
echo "Risk: anyone who obtains this row can read and reuse the password.\n\n";

echo "[2] WITH PASSWORD HASHING (SECURE)\n";
$passwordHash = password_hash($demoUser['password'], PASSWORD_DEFAULT);
echo "Database row leaked in a breach:\n";
echo "  email: {$demoUser['email']}\n";
echo "  password: {$passwordHash}  <-- one-way hash, not the original password\n";
echo 'Login result: ' . (password_verify($passwordEnteredAtLogin, $passwordHash) ? 'accepted' : 'rejected') . "\n";
echo "Protection: the application verifies the password with password_verify(), without storing it in plaintext.\n\n";

echo "AquaTrack implementation: app/Models/User.php casts the password attribute as 'hashed'.\n";
echo "Never store passwords as plaintext, MD5, or SHA-1.\n";
