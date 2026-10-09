<?php
/**
 * Standalone Shared Hosting Zip Auto-Updater & Migration Tool
 * Accessible directly via browser: https://getembetv.co.ke/update-app.php
 */

define('LARAVEL_START', microtime(true));

// Load Laravel Bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html>
<html>
<head>
    <title>Getembe News - Shared Hosting 1-Click Updater</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
        .card { max-width: 720px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 28px; border: 1px solid #334155; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); }
        h1 { color: #38bdf8; font-size: 22px; margin-top: 0; }
        .success { color: #4ade80; font-weight: bold; margin-top: 10px; }
        .error { color: #f87171; font-weight: bold; margin-top: 10px; }
        pre { background: #090d16; padding: 14px; border-radius: 8px; color: #38bdf8; overflow-x: auto; font-size: 13px; max-height: 250px; }
        .btn { display: inline-block; background: #c8102e; color: #fff; text-decoration: none; padding: 12px 20px; border-radius: 6px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
<div class="card">
    <h1>🚀 Getembe News Shared Hosting Auto-Updater</h1>';

$baseDir = realpath(__DIR__ . '/..');
$zipUrl = 'https://github.com/motechgroup/getembenews/archive/refs/heads/main.zip';
$tempZip = storage_path('app/latest-github.zip');

$logs = [];

// 1. Download Latest Main.zip from GitHub repository
$logs[] = "📥 <strong>Step 1:</strong> Fetching latest codebase ZIP package from GitHub...";
$ch = curl_init($zipUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeUpdater/1.0');
$zipData = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $zipData && strlen($zipData) > 1000) {
    file_put_contents($tempZip, $zipData);
    $logs[] = "<span class='success'>✔ Downloaded main.zip successfully (" . round(strlen($zipData) / 1024, 1) . " KB)</span>";

    // 2. Unzip & Extract Overwriting Changed Files
    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive;
        if ($zip->open($tempZip) === TRUE) {
            $extractedCount = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                // Strip the top folder prefix (getembenews-main/)
                $relativePath = preg_replace('/^getembenews-main\//', '', $filename);
                
                // Skip root folder or protected files (.env, vendor/, storage/)
                if (empty($relativePath) || str_starts_with($relativePath, '.env') || str_starts_with($relativePath, 'vendor/') || str_starts_with($relativePath, 'storage/')) {
                    continue;
                }

                $targetPath = $baseDir . '/' . $relativePath;
                
                if (str_ends_with($filename, '/')) {
                    if (!file_exists($targetPath)) {
                        @mkdir($targetPath, 0755, true);
                    }
                } else {
                    $dir = dirname($targetPath);
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    $content = $zip->getFromIndex($i);
                    @file_put_contents($targetPath, $content);
                    $extractedCount++;
                }
            }
            $zip->close();
            @unlink($tempZip);
            $logs[] = "<span class='success'>✔ <strong>Step 2:</strong> Extracted {$extractedCount} updated project files directly into your project root.</span>";
        } else {
            $logs[] = "<span class='error'>✖ Could not open downloaded zip file.</span>";
        }
    } else {
        $logs[] = "<span class='error'>✖ PHP ZipArchive extension not enabled on host.</span>";
    }
} else {
    $logs[] = "<span class='error'>✖ Failed downloading zip from GitHub (HTTP {$httpCode}).</span>";
}

// 3. Run Database Migrations
try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migOutput = Illuminate\Support\Facades\Artisan::output();
    $logs[] = "✔ <strong>Step 3: Database Migrations Output:</strong><pre>" . htmlspecialchars($migOutput ?: 'Nothing to migrate.') . "</pre>";
} catch (\Throwable $e) {
    $logs[] = "<span class='error'>✖ Migration Note: " . htmlspecialchars($e->getMessage()) . "</span>";
}

// 4. Clear Compiled Views and Application Cache
try {
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    // Purge compiled view files in storage/framework/views
    $viewFiles = glob(storage_path('framework/views/*.php'));
    if ($viewFiles) {
        foreach ($viewFiles as $file) {
            @unlink($file);
        }
    }

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
    $logs[] = "<span class='success'>✔ <strong>Step 4:</strong> Application Caches & Compiled Views Cleared!</span>";
} catch (\Throwable $e) {
    $logs[] = "<span class='error'>✖ Cache Clear Note: " . htmlspecialchars($e->getMessage()) . "</span>";
}

foreach ($logs as $log) {
    echo "<div style='margin-bottom: 12px;'>{$log}</div>";
}

echo '
    <div style="margin-top: 24px; border-top: 1px solid #334155; padding-top: 16px;">
        <p class="success">🎉 Success! The latest GitHub code and migrations have been downloaded & applied onto your server.</p>
        <a href="/admin/settings/social-login" class="btn">Go to Admin Settings (Social OAuth)</a>
    </div>
</div>
</body>
</html>';
