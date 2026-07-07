<?php
/**
 * Temporary log viewer for debugging live server 500 errors.
 * Secure this file by deleting it after use!
 */

// Simple password protection or token check (optional, but let's keep it simple and easy for the user)
$logFile = __DIR__ . '/../storage/logs/laravel.log';

header('Content-Type: text/plain; charset=utf-8');

if (!file_exists($logFile)) {
    echo "Log file does not exist at: " . $logFile;
    exit;
}

$lines = file($logFile);
$lastLines = array_slice($lines, -100);

echo "=== LAST 100 LINES OF LARAVEL.LOG ===\n\n";
echo implode("", $lastLines);
