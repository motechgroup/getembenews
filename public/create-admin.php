<?php
/**
 * Standalone Admin Creator Script for Getembe News
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$baseDir = dirname(__DIR__);

if (!file_exists($baseDir . '/vendor/autoload.php')) {
    die("Autoloader missing. Please deploy vendor folder first.");
}

require_once $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if credentials are passed via query params
$email = isset($_GET['email']) ? trim($_GET['email']) : 'admin_new@getembenews.com';
$name = isset($_GET['name']) ? trim($_GET['name']) : 'New Administrator';
$password = isset($_GET['password']) ? trim($_GET['password']) : 'admin1234';

try {
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        $user->role = 'admin';
        $user->password = \Illuminate\Support\Facades\Hash::make($password);
        $user->save();
        echo "<div style='font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h3 style='color: #4f46e5;'>✓ Admin User Already Exists</h3>";
        echo "<p>Details updated successfully:</p>";
    } else {
        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'admin',
        ]);
        echo "<div style='font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>";
        echo "<h3 style='color: #16a34a;'>✓ Successfully Created New Admin User</h3>";
    }
    
    echo "<ul>";
    echo "<li><strong>Name:</strong> " . htmlspecialchars($user->name) . "</li>";
    echo "<li><strong>Email:</strong> " . htmlspecialchars($user->email) . "</li>";
    echo "<li><strong>Password:</strong> " . htmlspecialchars($password) . "</li>";
    echo "<li><strong>Role:</strong> admin</li>";
    echo "</ul>";
    echo "<p style='color: #dc2626; font-weight: bold;'>⚠️ SECURITY WARNING: Please delete this file (public/create-admin.php) from your server immediately!</p>";
    echo "</div>";
} catch (\Throwable $e) {
    echo "<div style='font-family: sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #fca5a5; border-radius: 8px; background: #fef2f2; color: #991b1b;'>";
    echo "<h3>Error: " . htmlspecialchars($e->getMessage()) . "</h3>";
    echo "</div>";
}
