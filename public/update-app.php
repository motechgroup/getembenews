<?php
/**
 * Getembe News - Direct Raw File Synchronizer & Migration Tool
 * Accessible directly via browser: https://getembetv.co.ke/update-app.php
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define('LARAVEL_START', microtime(true));

// Load Laravel Bootstrap & Bootstrap Kernel for Facades/Artisan
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

@ini_set('memory_limit', '256M');
@set_time_limit(300);

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Getembe News - 1-Click Codebase & Database Deployment</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0b0f19; color: #f1f5f9; padding: 30px 15px; margin: 0; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #131c2e; border-radius: 16px; padding: 32px; border: 1px solid #1e2d4a; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.7); }
        .header { border-bottom: 1px solid #1e2d4a; padding-bottom: 20px; margin-bottom: 24px; }
        h1 { color: #38bdf8; font-size: 24px; margin: 0 0 6px 0; display: flex; align-items: center; gap: 10px; }
        .subtitle { color: #94a3b8; font-size: 13px; margin: 0; }
        .step-title { font-size: 14px; font-weight: bold; color: #f8fafc; margin-top: 20px; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .badge { background: #0284c7; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 9999px; text-transform: uppercase; }
        .badge-success { background: #15803d; }
        pre { background: #070a12; padding: 16px; border-radius: 10px; color: #38bdf8; overflow-x: auto; font-size: 12px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; border: 1px solid #1e2d4a; max-height: 280px; }
        .success { color: #4ade80; font-weight: bold; }
        .error { color: #f87171; font-weight: bold; }
        .file-list { background: #070a12; padding: 12px 16px; border-radius: 10px; max-height: 220px; overflow-y: auto; font-family: monospace; font-size: 11px; color: #cbd5e1; border: 1px solid #1e2d4a; }
        .file-item { padding: 4px 0; border-bottom: 1px solid #111827; }
        .file-item:last-child { border-bottom: none; }
        .btn-group { display: flex; gap: 12px; margin-top: 28px; border-top: 1px solid #1e2d4a; padding-top: 20px; flex-wrap: wrap; }
        .btn { display: inline-flex; align-items: center; justify-content: center; background: #c8102e; color: #fff; text-decoration: none; padding: 12px 22px; border-radius: 8px; font-weight: bold; font-size: 13px; transition: background 0.2s; }
        .btn:hover { background: #a60d25; }
        .btn-secondary { background: #334155; }
        .btn-secondary:hover { background: #475569; }
        .commit-info { background: #0f172a; padding: 14px 18px; border-radius: 10px; border: 1px solid #1e2d4a; margin-bottom: 20px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="header">
            <h1>🚀 Getembe News Shared Hosting Auto-Updater</h1>
            <p class="subtitle">Lightweight Instant Raw File Synchronizer & Database Migrator (v4.0)</p>
        </div>
<?php

$baseDir = realpath(__DIR__ . '/..');

// List of updated files to fetch directly from GitHub raw content
$targetFiles = [
    'resources/views/livewire/admin-settings-manager.blade.php',
    'resources/views/livewire/admin-users-manager.blade.php',
    'database/migrations/2026_10_09_000001_add_phone_to_users_table.php',
    'database/migrations/2026_10_09_000002_force_nullable_email_on_users_table.php',
    'database/migrations/2026_10_09_000003_fix_users_phone_column.php',
    'app/Http/Controllers/Api/MobileAppController.php',
    'routes/web.php',
    'public/app-ads.txt',
    'public/update-app.php',
];

$rawBaseUrl = 'https://raw.githubusercontent.com/motechgroup/getembenews/main/';

// 1. Fetch GitHub Commit Details via API
$commitHash = 'Unknown';
$commitMsg = 'Unknown';
$commitDate = 'Unknown';

try {
    $chCommit = curl_init('https://api.github.com/repos/motechgroup/getembenews/commits/main');
    curl_setopt($chCommit, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chCommit, CURLOPT_USERAGENT, 'GetembeUpdater/1.0');
    curl_setopt($chCommit, CURLOPT_SSL_VERIFYPEER, false);
    $commitJson = curl_exec($chCommit);
    curl_close($chCommit);

    if ($commitJson && ($commitData = json_decode($commitJson, true))) {
        $commitHash = substr($commitData['sha'] ?? 'Unknown', 0, 7);
        $commitMsg = $commitData['commit']['message'] ?? 'No commit message';
        $commitDate = isset($commitData['commit']['committer']['date']) ? date('M d, Y H:i:s T', strtotime($commitData['commit']['committer']['date'])) : 'Unknown';
    }
} catch (\Throwable $e) {}

echo '<div class="commit-info">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
        <strong style="color:#38bdf8;">📦 Latest Target Commit on GitHub:</strong>
        <span class="badge badge-success">Commit ' . htmlspecialchars($commitHash) . '</span>
    </div>
    <div style="font-weight:bold; color:#f8fafc;">"' . htmlspecialchars($commitMsg) . '"</div>
    <div style="color:#94a3b8; font-size:11px; margin-top:4px;">Committed on ' . htmlspecialchars($commitDate) . '</div>
</div>';

// 2. Fetch and write updated raw files instantly
echo '<div class="step-title"><span>📥 Step 1: Downloading & Replacing Updated Target Files</span></div>';
echo '<div class="file-list">';

$updatedCount = 0;
foreach ($targetFiles as $relPath) {
    $url = $rawBaseUrl . $relPath;
    $targetPath = $baseDir . '/' . $relPath;

    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeUpdater/1.0');
        $content = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && !empty($content)) {
            $dir = dirname($targetPath);
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            file_put_contents($targetPath, $content);
            $updatedCount++;
            echo '<div class="file-item"><span class="success">✓ ' . htmlspecialchars($relPath) . '</span> (' . strlen($content) . ' bytes)</div>';
        } else {
            echo '<div class="file-item"><span class="error">✖ Failed fetching ' . htmlspecialchars($relPath) . ' (HTTP ' . $code . ')</span></div>';
        }
    } catch (\Throwable $e) {
        echo '<div class="file-item"><span class="error">✖ Error updating ' . htmlspecialchars($relPath) . ': ' . htmlspecialchars($e->getMessage()) . '</span></div>';
    }
}

echo '</div>';
echo '<div class="success" style="margin-top:8px;">✔ Successfully updated ' . $updatedCount . ' / ' . count($targetFiles) . ' files instantly.</div>';

// 3. Database Migrations
echo '<div class="step-title"><span>🗄️ Step 2: Executing Database Migrations</span></div>';
try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migOutput = Illuminate\Support\Facades\Artisan::output();
    echo '<pre>' . htmlspecialchars($migOutput ?: 'INFO Nothing to migrate (Database schema up to date).') . '</pre>';
} catch (\Throwable $e) {
    echo '<div class="error">✖ Migration Note: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// 4. Purge Caches & Compiled Views
echo '<div class="step-title"><span>⚡ Step 3: Flushing Compiled Blade Views & Application Cache</span></div>';
try {
    Illuminate\Support\Facades\Artisan::call('optimize:clear');
    
    $viewFiles = glob(storage_path('framework/views/*.php'));
    $purgedViewsCount = 0;
    if ($viewFiles && is_array($viewFiles)) {
        foreach ($viewFiles as $file) {
            if (@unlink($file)) {
                $purgedViewsCount++;
            }
        }
    }

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
    echo '<div class="success">✔ Cleared ' . $purgedViewsCount . ' compiled Blade view templates & reset OPcache!</div>';
} catch (\Throwable $e) {
    echo '<div class="error">✖ Cache Clear Note: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

echo '
        <div class="btn-group">
            <a href="/admin/settings/social-login" class="btn">Go to Admin Settings (Social OAuth)</a>
            <a href="/admin/users" class="btn btn-secondary">Go to User Accounts Manager</a>
        </div>
    </div>
</div>
</body>
</html>';
