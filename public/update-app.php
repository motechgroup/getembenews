<?php
/**
 * Getembe News - Direct Raw File Synchronizer & Migration Tool
 * Accessible directly via browser: https://getembetv.co.ke/update-app.php
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure latest AppUpdater is pulled from GitHub raw
$updaterFile = __DIR__ . '/../app/Support/AppUpdater.php';
$rawUrl = 'https://raw.githubusercontent.com/motechgroup/getembenews/main/app/Support/AppUpdater.php?v=' . time();
$ch = curl_init($rawUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeUpdater/2.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$content = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code === 200 && !empty($content)) {
    @mkdir(dirname($updaterFile), 0755, true);
    @file_put_contents($updaterFile, $content);
}

if (class_exists(\App\Support\AppUpdater::class)) {
    echo \App\Support\AppUpdater::run();
} else {
    echo "<h1>Error: AppUpdater class could not be loaded. Please re-run update.</h1>";
}
