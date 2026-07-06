<?php
/**
 * Standalone Web Installer Wizard for Getembe News on Shared Hosting
 * Requires PHP 8.2+
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300);

// Base directory path
$baseDir = dirname(__DIR__);

// 1. Requirements checks
$requirements = [
    'PHP Version (>= 8.2)' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'Extension: PDO' => extension_loaded('pdo'),
    'Extension: pdo_mysql' => extension_loaded('pdo_mysql'),
    'Extension: mbstring' => extension_loaded('mbstring'),
    'Extension: openssl' => extension_loaded('openssl'),
    'Extension: xml' => extension_loaded('xml'),
    'Extension: bcmath' => extension_loaded('bcmath'),
    'Extension: fileinfo' => extension_loaded('fileinfo'),
    'Extension: gd' => extension_loaded('gd'),
];

// Writable folders checks
$folders = [
    'storage' => is_writable($baseDir . '/storage'),
    'storage/app' => is_writable($baseDir . '/storage/app'),
    'storage/framework' => is_writable($baseDir . '/storage/framework'),
    'storage/logs' => is_writable($baseDir . '/storage/logs'),
    'bootstrap/cache' => is_writable($baseDir . '/bootstrap/cache'),
];

// Check overall diagnostics status
$allPassed = true;
foreach ($requirements as $status) {
    if (!$status) $allPassed = false;
}
foreach ($folders as $status) {
    if (!$status) $allPassed = false;
}

// Read current env configuration if exists
$envFile = $baseDir . '/.env';
$envExists = file_exists($envFile);
$currentDb = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => '',
    'username' => '',
    'password' => '',
];

if ($envExists) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " '\"\t\n\r\0\x0B");
            if ($key === 'DB_HOST') $currentDb['host'] = $value;
            if ($key === 'DB_PORT') $currentDb['port'] = $value;
            if ($key === 'DB_DATABASE') $currentDb['database'] = $value;
            if ($key === 'DB_USERNAME') $currentDb['username'] = $value;
            if ($key === 'DB_PASSWORD') $currentDb['password'] = $value;
        }
    }
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$log = [];
$errorMsg = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'install') {
    // Collect credentials
    $db_host = trim($_POST['db_host']);
    $db_port = trim($_POST['db_port']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);

    // Test connection
    try {
        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        $log[] = "✓ Database connection test succeeded.";
    } catch (Exception $e) {
        $errorMsg = "Database Connection Failed: " . $e->getMessage();
    }

    if (empty($errorMsg)) {
        // Write .env file
        try {
            if (!$envExists) {
                if (file_exists($baseDir . '/.env.example')) {
                    copy($baseDir . '/.env.example', $envFile);
                } else {
                    file_put_contents($envFile, "APP_NAME=\"Getembe News\"\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\n");
                }
            }
            
            $envContent = file_get_contents($envFile);
            
            // Replace DB connection credentials
            $replacements = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $db_host,
                'DB_PORT' => $db_port,
                'DB_DATABASE' => $db_name,
                'DB_USERNAME' => $db_user,
                'DB_PASSWORD' => $db_pass,
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false'
            ];

            $lines = explode("\n", $envContent);
            $keysHandled = [];
            foreach ($lines as $i => $line) {
                if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                    continue;
                }
                list($k, $v) = explode('=', $line, 2);
                $k = trim($k);
                if (array_key_exists($k, $replacements)) {
                    $val = $replacements[$k];
                    // Double quote values that contain spaces or special characters to prevent env parsing issues
                    if (preg_match('/[#$\s"\']/', $val)) {
                        $val = '"' . str_replace('"', '\\"', $val) . '"';
                    }
                    $lines[$i] = "{$k}={$val}";
                    $keysHandled[$k] = true;
                }
            }
            foreach ($replacements as $key => $val) {
                if (!isset($keysHandled[$key])) {
                    if (preg_match('/[#$\s"\']/', $val)) {
                        $val = '"' . str_replace('"', '\\"', $val) . '"';
                    }
                    $lines[] = "{$key}={$val}";
                }
            }
            $envContent = implode("\n", $lines);

            file_put_contents($envFile, $envContent);
            $log[] = "✓ Saved credentials to your .env file.";
        } catch (Exception $e) {
            $errorMsg = "Failed to update .env configuration file: " . $e->getMessage();
        }
    }

    if (empty($errorMsg)) {
        // Run Artisan commands using bootstrap
        try {
            // Clear any cached configuration/routes/events files to ensure Laravel reads the updated env values
            $cachedFiles = [
                $baseDir . '/bootstrap/cache/config.php',
                $baseDir . '/bootstrap/cache/routes.php',
                $baseDir . '/bootstrap/cache/events.php'
            ];
            foreach ($cachedFiles as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }

            // Include autoloader
            if (!file_exists($baseDir . '/vendor/autoload.php')) {
                throw new Exception("Composer autoload.php is missing. Please make sure the 'vendor' folder is uploaded to the server.");
            }
            require_once $baseDir . '/vendor/autoload.php';

            // Boot Laravel application kernel
            $app = require_once $baseDir . '/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            $log[] = "✓ Bootstrapped Laravel Framework successfully.";

            // 1. Generate app key if not set
            if (empty(env('APP_KEY')) || env('APP_KEY') === '') {
                Artisan::call('key:generate', ['--force' => true]);
                $log[] = "✓ Generated application encryption key (APP_KEY).";
            } else {
                $log[] = "✓ Application encryption key is already set.";
            }

            // 2. Run migrations
            $log[] = "Running database migrations (creating tables)...";
            Artisan::call('migrate:fresh', ['--force' => true]);
            $log[] = "✓ Database migrations completed successfully.";

            // 3. Seed demo content
            $log[] = "Seeding demo content (articles, default roles, widget settings)...";
            Artisan::call('db:seed', ['--force' => true]);
            $log[] = "✓ Database seeding completed successfully.";

            // 4. Create storage link
            Artisan::call('storage:link', ['--force' => true]);
            $log[] = "✓ Created public storage symlink connection.";

            // 5. Optimize cache
            Artisan::call('optimize');
            $log[] = "✓ Optimizations and configurations cached successfully.";

            $success = true;
        } catch (Exception $e) {
            $errorMsg = "Installation failed during execution: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Getembe News - Live Server Installer Wizard</title>
    <link href="https://fonts.googleapis.com/css?family=Outfit:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0c0f17;
            color: #d1d5db;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background-color: #111827;
            border: 1px border-solid #1f2937;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background-color: #1e1b4b;
            padding: 24px;
            border-bottom: 3px solid #C8102E;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            color: #94a3b8;
            margin: 4px 0 0 0;
            font-size: 13px;
        }
        .content {
            padding: 24px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .card {
            background-color: #1f2937;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid #374151;
        }
        .check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #374151;
            font-size: 13px;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 9999px;
        }
        .status-pass {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-fail {
            background-color: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .form-control {
            width: 100%;
            background-color: #0c0f17;
            border: 1px solid #374151;
            border-radius: 6px;
            padding: 8px 12px;
            color: #ffffff;
            font-size: 13px;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #C8102E;
            outline: none;
        }
        .btn {
            display: block;
            width: 100%;
            background-color: #C8102E;
            color: #ffffff;
            text-align: center;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn:hover:not(:disabled) {
            background-color: #a80c25;
            transform: translateY(-1px);
        }
        .btn:disabled {
            background-color: #4b5563;
            cursor: not-allowed;
            color: #9ca3af;
        }
        .alert {
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .log-box {
            background-color: #0c0f17;
            border: 1px solid #1f2937;
            font-family: monospace;
            font-size: 12px;
            padding: 12px;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            color: #10B981;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>GETEMBE NEWS</h1>
        <p>Live Shared Hosting Installer & Diagnostic Wizard</p>
    </div>
    
    <div class="content">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✓ Installation and configuration succeeded!</strong> Getembe News is now fully installed and configured on your shared server.
            </div>

            <div class="section-title">Execution Logs</div>
            <div class="card">
                <div class="log-box">
                    <?php foreach ($log as $line): ?>
                        <div><?php echo htmlspecialchars($line); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>⚠️ CRITICAL SECURITY WARNING:</strong> For security reasons, please open your cPanel File Manager and **DELETE** this file (`public/install.php`) immediately so that unauthorized visitors cannot re-run installation commands or overwrite your database.
            </div>

            <a href="/" class="btn" style="text-decoration: none; display: block;">Go to Website Homepage</a>

        <?php else: ?>
            
            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-error">
                    <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
                </div>
            <?php endif; ?>

            <div class="section-title">System Requirement Diagnostics</div>
            <div class="card">
                <?php foreach ($requirements as $name => $status): ?>
                    <div class="check-item">
                        <span><?php echo htmlspecialchars($name); ?></span>
                        <span class="status-badge <?php echo $status ? 'status-pass' : 'status-fail'; ?>">
                            <?php echo $status ? 'PASS' : 'FAIL'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-title">Folder Write Permissions</div>
            <div class="card">
                <?php foreach ($folders as $path => $status): ?>
                    <div class="check-item">
                        <span>Folder: /<?php echo htmlspecialchars($path); ?></span>
                        <span class="status-badge <?php echo $status ? 'status-pass' : 'status-fail'; ?>">
                            <?php echo $status ? 'WRITABLE' : 'UNWRITABLE'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$allPassed): ?>
                    <div style="font-size: 11px; color: #f87171; margin-top: 8px; font-weight: 500;">
                        * Note: If folders are unwritable, set permission levels to 755 or 775 using cPanel File Manager.
                    </div>
                <?php endif; ?>
            </div>

            <form action="?action=install" method="POST">
                <div class="section-title">Database Connections Settings (MySQL)</div>
                <div class="card">
                    <div class="grid" style="display: grid; grid-template-cols: 3fr 1fr; gap: 10px;">
                        <div class="form-group">
                            <label>MySQL Host</label>
                            <input type="text" name="db_host" class="form-control" value="<?php echo htmlspecialchars($currentDb['host']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="text" name="db_port" class="form-control" value="<?php echo htmlspecialchars($currentDb['port']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>MySQL Database Name</label>
                        <input type="text" name="db_name" class="form-control" value="<?php echo htmlspecialchars($currentDb['database']); ?>" required placeholder="Enter database name created in cPanel">
                    </div>

                    <div class="form-group">
                        <label>MySQL Username</label>
                        <input type="text" name="db_user" class="form-control" value="<?php echo htmlspecialchars($currentDb['username']); ?>" required placeholder="Enter database user created in cPanel">
                    </div>

                    <div class="form-group">
                        <label>MySQL Password</label>
                        <input type="password" name="db_pass" class="form-control" value="<?php echo htmlspecialchars($currentDb['password']); ?>" placeholder="Enter database password">
                    </div>
                </div>

                <button type="submit" class="btn" <?php echo !$allPassed ? 'disabled' : ''; ?>>
                    <?php echo $allPassed ? 'Install & Setup Website Database' : 'Please resolve failed diagnostic checks above'; ?>
                </button>
            </form>

        <?php endif; ?>
    </div>
</div>

</body>
</html>
