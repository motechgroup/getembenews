<?php
/**
 * Web Database Seeder and Migration Trigger Utility.
 * Securely run migrations and seed data from Git push on live production server.
 */

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Security check: require matching secret seed key
$expectedKey = env('SEED_KEY', 'getembe-seed-2026');
$providedKey = request()->get('key');

if (empty($providedKey) || $providedKey !== $expectedKey) {
    header('HTTP/1.1 403 Forbidden');
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Access Denied - Getembe News</title>
        <style>
            body { font-family: sans-serif; background-color: #f3f4f6; color: #1f2937; text-align: center; padding: 50px 20px; }
            .card { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border-top: 4px solid #C8102E; }
            h1 { color: #C8102E; font-size: 24px; margin-bottom: 10px; }
            p { font-size: 14px; color: #4b5563; }
        </style>
    </head>
    <body>
        <div class="card">
            <h1>403 - Unauthorized Access</h1>
            <p>You must provide the correct security key parameter to execute this database utility.</p>
            <p style="font-size: 12px; font-family: monospace; background: #f9fafb; padding: 8px; border: 1px dashed #d1d5db; border-radius: 4px;">/seed.php?key=YOUR_SEED_KEY</p>
        </div>
    </body>
    </html>';
    exit;
}

try {
    // 1. Run migrations to handle structural schema changes
    Artisan::call('migrate', ['--force' => true]);
    $migrateLog = Artisan::output();

    // 2. Run LocalDataSeeder to import localhost database records
    Artisan::call('db:seed', ['--class' => 'LocalDataSeeder', '--force' => true]);
    $seederLog = Artisan::output();

    // 3. Clear cache to reflect new settings immediately
    Artisan::call('cache:clear');
    $cacheLog = Artisan::output();

    $success = true;
} catch (\Exception $e) {
    $success = false;
    $errorMessage = $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Seeding System - Getembe News</title>
    <style>
        body { font-family: sans-serif; background-color: #f3f4f6; color: #1f2937; padding: 40px 20px; line-height: 1.5; }
        .container { max-width: 700px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: #111827; padding: 20px 30px; border-bottom: 4px solid #C8102E; color: white; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; font-family: Georgia, serif; }
        .content { padding: 30px; }
        .status { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        .status-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .log-title { font-size: 12px; font-weight: bold; color: #4b5563; text-transform: uppercase; margin-top: 20px; margin-bottom: 5px; }
        pre { background: #f9fafb; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px; overflow-x: auto; white-space: pre-wrap; margin: 0; color: #374151; }
        .footer { background: #f9fafb; padding: 15px 30px; text-align: center; font-size: 11px; color: #999999; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Getembe Digital Seeder</h1>
            <span style="font-size: 10px; font-weight: bold; background: #C8102E; padding: 3px 8px; border-radius: 3px;">LIVE UTILITY</span>
        </div>

        <div class="content">
            <?php if ($success): ?>
                <div class="status status-success">
                    ✓ Database migrations and seeder executed successfully!
                </div>

                <div class="log-title">Migration Logs</div>
                <pre><?php echo trim($migrateLog) ?: 'No pending migrations schema updates applied.'; ?></pre>

                <div class="log-title">Seeder Logs</div>
                <pre><?php echo trim($seederLog); ?></pre>

                <div class="log-title">Cache Clearance Logs</div>
                <pre><?php echo trim($cacheLog); ?></pre>
            <?php else: ?>
                <div class="status status-error">
                    🔴 Database Seeding / Migration Failed!
                </div>
                <div class="log-title">Exception Error details</div>
                <pre style="background: #fff5f5; border-color: #feb2b2; color: #c53030;"><?php echo $errorMessage; ?></pre>
            <?php endif; ?>
        </div>

        <div class="footer">
            Getembe News Database Administration Utility
        </div>
    </div>
</body>
</html>
