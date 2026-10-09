<?php
/**
 * Standalone Shared Hosting Update & Maintenance Script
 * Accessible directly via browser: https://your-domain.com/update-app.php
 */

define('LARAVEL_START', microtime(true));

// Load Laravel Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// HTML Output Header
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Getembe News - One-Click Hosting Update</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
        .card { max-width: 700px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 24px; border: 1px solid #334155; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; font-size: 22px; margin-top: 0; display: flex; align-items: center; gap: 8px; }
        .success { color: #4ade80; font-weight: bold; }
        .error { color: #f87171; font-weight: bold; }
        pre { background: #090d16; padding: 16px; border-radius: 8px; color: #38bdf8; overflow-x: auto; font-size: 13px; }
        .btn { display: inline-block; background: #c8102e; color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; margin-top: 16px; }
    </style>
</head>
<body>
<div class="card">
    <h1>🚀 Getembe News Shared Hosting Maintenance & Update</h1>';

$results = [];

// Step 1: Git Pull (if shell_exec enabled on hosting)
if (function_exists('shell_exec')) {
    $basePath = base_path();
    $gitOutput = @shell_exec("cd {$basePath} && git pull origin main 2>&1");
    if ($gitOutput) {
        $results[] = '<strong>Git Pull Result:</strong><pre>' . htmlspecialchars($gitOutput) . '</pre>';
    }
}

// Step 2: Database Migration
try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migOutput = Illuminate\Support\Facades\Artisan::output();
    $results[] = '<div class="success">✔ Database Migrations:</div><pre>' . htmlspecialchars($migOutput ?: 'Database already up to date.') . '</pre>';
} catch (\Throwable $e) {
    $results[] = '<div class="error">✖ Migration Note:</div><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}

// Step 3: Clear Laravel Cache & Compiled Views
try {
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    // Delete stale compiled view files manually
    $viewFiles = glob(storage_path('framework/views/*.php'));
    if ($viewFiles) {
        foreach ($viewFiles as $file) {
            @unlink($file);
        }
    }

    // Clear OPcache if present
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    $results[] = '<div class="success">✔ Application Cache & Compiled Views Cleared Successfully!</div>';
} catch (\Throwable $e) {
    $results[] = '<div class="error">✖ Cache Clear Error:</div><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}

foreach ($results as $res) {
    echo '<div style="margin-bottom: 16px;">' . $res . '</div>';
}

echo '
    <div style="margin-top: 24px; border-top: 1px solid #334155; padding-top: 16px;">
        <p class="success">✨ All update tasks completed! Your admin dashboard is now updated with the Phone Auth & Google Sign-In toggles.</p>
        <a href="/admin/settings/social-login" class="btn">Go to Admin Settings</a>
    </div>
</div>
</body>
</html>';
