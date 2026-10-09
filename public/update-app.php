<?php
/**
 * Getembe News - Comprehensive Shared Hosting Auto-Updater & Diagnostics Tool
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
        .file-list { background: #070a12; padding: 12px 16px; border-radius: 10px; max-height: 180px; overflow-y: auto; font-family: monospace; font-size: 11px; color: #cbd5e1; border: 1px solid #1e2d4a; }
        .file-item { padding: 2px 0; border-bottom: 1px solid #111827; }
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
            <p class="subtitle">Direct GitHub ZIP Deployment & Database Synchronizer (v3.1)</p>
        </div>
<?php

$baseDir = realpath(__DIR__ . '/..');
$zipUrl = 'https://github.com/motechgroup/getembenews/archive/refs/heads/main.zip';
$tempDir = sys_get_temp_dir();
$tempZip = $tempDir . '/latest-github-' . time() . '.zip';

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

// 2. Download ZIP from GitHub
echo '<div class="step-title"><span>📥 Step 1: Downloading Latest Codebase ZIP</span></div>';
$zipDownloaded = false;

try {
    $ch = curl_init($zipUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeUpdater/1.0');
    $zipData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $zipData && strlen($zipData) > 1000) {
        @file_put_contents($tempZip, $zipData);
        $zipDownloaded = true;
        echo '<div class="success">✔ Downloaded main.zip successfully (' . round(strlen($zipData) / 1024, 1) . ' KB)</div>';
    } else {
        echo '<div class="error">✖ Failed downloading zip from GitHub (HTTP ' . $httpCode . ').</div>';
    }
} catch (\Throwable $e) {
    echo '<div class="error">✖ Download error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// 3. Extract Files
if ($zipDownloaded && file_exists($tempZip)) {
    echo '<div class="step-title"><span>📂 Step 2: Extracting & Overwriting Project Files</span></div>';
    try {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            if ($zip->open($tempZip) === TRUE) {
                $extractedFiles = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $relativePath = preg_replace('/^getembenews-main\//', '', $filename);
                    
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
                        $extractedFiles[] = $relativePath;
                    }
                }
                $zip->close();
                @unlink($tempZip);

                echo '<div class="success">✔ Extracted ' . count($extractedFiles) . ' updated files directly into project root:</div>';
                echo '<div class="file-list">';
                foreach (array_slice($extractedFiles, 0, 40) as $f) {
                    echo '<div class="file-item">✓ ' . htmlspecialchars($f) . '</div>';
                }
                if (count($extractedFiles) > 40) {
                    echo '<div class="file-item" style="color:#38bdf8;">... and ' . (count($extractedFiles) - 40) . ' more files updated.</div>';
                }
                echo '</div>';
            } else {
                echo '<div class="error">✖ Could not open downloaded zip file.</div>';
            }
        } else {
            echo '<div class="error">✖ PHP ZipArchive extension is disabled on host.</div>';
        }
    } catch (\Throwable $e) {
        echo '<div class="error">✖ Extraction Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// 4. Database Migrations
echo '<div class="step-title"><span>🗄️ Step 3: Executing Database Migrations</span></div>';
try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $migOutput = Illuminate\Support\Facades\Artisan::output();
    echo '<pre>' . htmlspecialchars($migOutput ?: 'INFO Nothing to migrate (Database schema up to date).') . '</pre>';
} catch (\Throwable $e) {
    echo '<div class="error">✖ Migration Note: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// 5. Purge Caches & Compiled Views
echo '<div class="step-title"><span>⚡ Step 4: Flushing Compiled Blade Views & Application Cache</span></div>';
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
