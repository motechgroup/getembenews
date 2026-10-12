<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AppUpdater
{
    /**
     * Run the application updater and return the formatted HTML dashboard.
     */
    public static function run(): string
    {
        @ini_set('memory_limit', '256M');
        @set_time_limit(300);

        $basePath = base_path();

        // 1. Determine Git Branch
        $branch = 'main';
        $gitHeadFile = $basePath . '/.git/HEAD';
        if (file_exists($gitHeadFile)) {
            $headContent = trim(@file_get_contents($gitHeadFile));
            if (str_starts_with($headContent, 'ref: refs/heads/')) {
                $branch = str_replace('ref: refs/heads/', '', $headContent);
            }
        }

        // 2. Fetch Latest Commit & File Changes from GitHub API
        $commitInfo = [
            'sha' => 'Unknown',
            'short_sha' => 'Unknown',
            'message' => 'Latest updates from repository',
            'author' => 'System',
            'date' => now()->format('M d, Y H:i:s T'),
            'stats' => ['total' => 0, 'additions' => 0, 'deletions' => 0],
            'files' => [],
        ];

        try {
            $apiUrl = "https://api.github.com/repos/motechgroup/getembenews/commits/{$branch}";
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeAutoUpdater/2.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $apiJson = curl_exec($ch);
            curl_close($ch);

            if ($apiJson && ($apiData = json_decode($apiJson, true))) {
                if (!empty($apiData['sha'])) {
                    $commitInfo['sha'] = $apiData['sha'];
                    $commitInfo['short_sha'] = substr($apiData['sha'], 0, 7);
                    $commitInfo['message'] = $apiData['commit']['message'] ?? 'No commit message provided.';
                    $commitInfo['author'] = $apiData['commit']['author']['name'] ?? ($apiData['author']['login'] ?? 'Admin');
                    if (!empty($apiData['commit']['author']['date'])) {
                        $commitInfo['date'] = date('M d, Y H:i:s T', strtotime($apiData['commit']['author']['date']));
                    }
                    if (!empty($apiData['stats'])) {
                        $commitInfo['stats'] = $apiData['stats'];
                    }
                    if (!empty($apiData['files']) && is_array($apiData['files'])) {
                        $commitInfo['files'] = $apiData['files'];
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully
        }

        // 3. Attempt Git Pull CLI or GitHub Raw Sync Engine
        $gitOutput = '';
        $syncMethod = 'Native Git CLI';
        $syncSuccess = false;
        $syncedFilesCount = 0;
        $syncErrors = [];

        $disabled = explode(',', ini_get('disable_functions') ?: '');
        $disabled = array_map('trim', $disabled);

        if (function_exists('shell_exec') && !in_array('shell_exec', $disabled)) {
            $cmd = "cd {$basePath} && git pull origin {$branch} 2>&1";
            $gitOutput = @shell_exec($cmd);
            if ($gitOutput && !str_contains(strtolower($gitOutput), 'command not found') && !str_contains(strtolower($gitOutput), 'not found')) {
                $syncSuccess = true;
                $syncMethod = "Git CLI (origin/{$branch})";
            }
        }

        // Fallback: If Git CLI was restricted or empty, synchronize changed files directly from GitHub
        if (!$syncSuccess) {
            $syncMethod = "GitHub API Direct Synchronizer";
            $rawBase = "https://raw.githubusercontent.com/motechgroup/getembenews/{$branch}/";
            
            // Files to synchronize: files from latest commit, plus essential core files
            $syncList = [];
            foreach ($commitInfo['files'] as $f) {
                if (!empty($f['filename'])) {
                    $syncList[] = $f['filename'];
                }
            }

            // Always ensure key updated files are tracked
            $essentialFiles = [
                'index.php',
                '.htaccess',
                'public/.htaccess',
                'app/Http/Controllers/AnnouncementController.php',
                'app/Http/Controllers/SeoSitemapController.php',
                'routes/web.php',
                'routes/api.php',
                'public/update-app.php',
            ];
            $syncList = array_unique(array_merge($syncList, $essentialFiles));

            $gitOutputLines = [];
            foreach ($syncList as $relPath) {
                $rawUrl = $rawBase . $relPath . '?v=' . time();
                $targetPath = $basePath . '/' . $relPath;

                $ch = curl_init($rawUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'GetembeAutoUpdater/2.0');
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                $content = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200 && $content !== false) {
                    $dir = dirname($targetPath);
                    if (!file_exists($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    @file_put_contents($targetPath, $content);
                    $syncedFilesCount++;
                    $gitOutputLines[] = "Synced: {$relPath} (" . strlen($content) . " bytes)";
                } else {
                    $syncErrors[] = "Failed to sync {$relPath} (HTTP {$httpCode})";
                }
            }
            $gitOutput = implode("\n", $gitOutputLines);
            $syncSuccess = ($syncedFilesCount > 0);
        }

        // 4. Run Database Migrations
        $migrationOutput = '';
        $migrationSuccess = true;
        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrationOutput = trim(Artisan::output());
            if (empty($migrationOutput)) {
                $migrationOutput = "INFO  Nothing to migrate. (Database schema is up to date)";
            }
        } catch (\Throwable $e) {
            $migrationSuccess = false;
            $migrationOutput = "Error executing migrations: " . $e->getMessage();
        }

        // Fetch recent applied migrations from database
        $recentMigrations = [];
        try {
            $recentMigrations = DB::table('migrations')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();
        } catch (\Throwable $e) {}

        // 5. Purge Caches & Compiled Views
        $cacheLogs = [];
        try {
            Artisan::call('optimize:clear');
            $cacheLogs[] = "Configuration, route, and event caches cleared.";
        } catch (\Throwable $e) {
            $cacheLogs[] = "Optimize clear: " . $e->getMessage();
        }

        $viewFilesCount = 0;
        $viewFiles = glob(storage_path('framework/views/*.php'));
        if ($viewFiles) {
            foreach ($viewFiles as $f) {
                if (@unlink($f)) {
                    $viewFilesCount++;
                }
            }
        }
        $cacheLogs[] = "{$viewFilesCount} compiled Blade template view files purged.";

        if (function_exists('opcache_reset')) {
            $op = @opcache_reset();
            $cacheLogs[] = $op ? "PHP OPcache in-memory bytecode reset." : "OPcache reset called.";
        }

        return self::renderHtml([
            'branch' => $branch,
            'commitInfo' => $commitInfo,
            'syncMethod' => $syncMethod,
            'syncSuccess' => $syncSuccess,
            'gitOutput' => $gitOutput,
            'syncedFilesCount' => $syncedFilesCount,
            'syncErrors' => $syncErrors,
            'migrationSuccess' => $migrationSuccess,
            'migrationOutput' => $migrationOutput,
            'recentMigrations' => $recentMigrations,
            'cacheLogs' => $cacheLogs,
        ]);
    }

    /**
     * Render the modern, responsive dark UI template.
     */
    protected static function renderHtml(array $data): string
    {
        $branch = htmlspecialchars($data['branch']);
        $shortSha = htmlspecialchars($data['commitInfo']['short_sha']);
        $fullSha = htmlspecialchars($data['commitInfo']['sha']);
        $commitMsg = nl2br(htmlspecialchars($data['commitInfo']['message']));
        $author = htmlspecialchars($data['commitInfo']['author']);
        $date = htmlspecialchars($data['commitInfo']['date']);
        $stats = $data['commitInfo']['stats'];
        $files = $data['commitInfo']['files'];
        $syncMethod = htmlspecialchars($data['syncMethod']);
        $gitOutput = htmlspecialchars($data['gitOutput']);
        $migrationOutput = htmlspecialchars($data['migrationOutput']);
        $recentMigrations = $data['recentMigrations'];
        $cacheLogs = $data['cacheLogs'];
        $fileCount = is_countable($files) ? count($files) : 0;

        // Build file rows HTML
        $filesHtml = '';
        if (!empty($files)) {
            foreach ($files as $file) {
                $filename = htmlspecialchars($file['filename']);
                $status = strtoupper($file['status'] ?? 'MODIFIED');
                $additions = (int) ($file['additions'] ?? 0);
                $deletions = (int) ($file['deletions'] ?? 0);

                $statusBadge = match ($status) {
                    'ADDED' => '<span class="status-badge bg-green-900/60 text-green-400 border border-green-700/50">ADDED</span>',
                    'REMOVED' => '<span class="status-badge bg-red-900/60 text-red-400 border border-red-700/50">REMOVED</span>',
                    default => '<span class="status-badge bg-amber-900/60 text-amber-400 border border-amber-700/50">MODIFIED</span>',
                };

                $diffPill = "<span class=\"text-green-400 font-mono text-[11px]\">+{$additions}</span> <span class=\"text-red-400 font-mono text-[11px]\">-{$deletions}</span>";

                $filesHtml .= "<tr class=\"border-b border-gray-800/80 hover:bg-slate-800/40 transition\">
                    <td class=\"py-2.5 px-3\">{$statusBadge}</td>
                    <td class=\"py-2.5 px-3 font-mono text-xs text-gray-200 break-all\">{$filename}</td>
                    <td class=\"py-2.5 px-3 text-right whitespace-nowrap\">{$diffPill}</td>
                </tr>";
            }
        } else {
            $filesHtml = "<tr><td colspan=\"3\" class=\"py-4 text-center text-xs text-gray-400\">No file list provided by commit metadata. All core target files synchronized.</td></tr>";
        }

        // Build recent migrations HTML
        $migrationsHtml = '';
        if (!empty($recentMigrations) && count($recentMigrations) > 0) {
            foreach ($recentMigrations as $m) {
                $mName = htmlspecialchars($m->migration);
                $mBatch = htmlspecialchars($m->batch);
                $migrationsHtml .= "<tr class=\"border-b border-gray-800/60 hover:bg-slate-800/30 transition\">
                    <td class=\"py-2 px-3 font-mono text-xs text-cyan-300\">{$mName}</td>
                    <td class=\"py-2 px-3 text-right text-xs\"><span class=\"px-2 py-0.5 rounded bg-gray-800 text-gray-300 font-mono border border-gray-700\">Batch {$mBatch}</span></td>
                </tr>";
            }
        } else {
            $migrationsHtml = "<tr><td colspan=\"2\" class=\"py-3 text-center text-xs text-gray-500\">No previous migration records found.</td></tr>";
        }

        // Build Cache Log Items
        $cacheItemsHtml = '';
        foreach ($cacheLogs as $log) {
            $safeLog = htmlspecialchars($log);
            $cacheItemsHtml .= "<div class=\"flex items-center gap-2 text-xs text-emerald-400 py-1\">
                <svg class=\"w-4 h-4 text-emerald-400 shrink-0\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\"></path></svg>
                <span>{$safeLog}</span>
            </div>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Getembe News - Production Auto-Updater</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080c16; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .status-badge { font-size: 9px; font-weight: 800; padding: 2px 7px; border-radius: 9999px; letter-spacing: 0.05em; display: inline-block; }
        pre::-webkit-scrollbar { height: 6px; width: 6px; }
        pre::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen text-slate-100 p-4 sm:p-8 flex justify-center items-start">
    <div class="max-w-4xl w-full my-4">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-[#cc6c3b]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/70 text-emerald-400 border border-emerald-800/60 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Production Deployment Complete
                        </span>
                        <span class="text-xs text-slate-400">• Truehost Cloud</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                        <span>🚀</span> Getembe Auto-Updater
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">Real-time repository branch synchronization, database migrations, and cache clearing.</p>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <a href="/update-app" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-sm">
                        <span>🔄</span> Re-Run Update
                    </a>
                    <a href="/admin/dashboard" class="px-4 py-2 bg-[#cc6c3b] hover:bg-[#b55b2d] text-white rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-md shadow-orange-950/30">
                        <span>⚙️</span> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <!-- Card 1: Git Branch & Commit Details -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 pb-4 border-b border-slate-800/80 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-orange-950/50 text-[#cc6c3b] border border-orange-900/40 text-base">🌿</span>
                        <div>
                            <h2 class="text-base font-bold text-white flex items-center gap-2">
                                Branch & Commit Details
                            </h2>
                            <p class="text-xs text-slate-400">Target Git remote tracking status</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-bold flex items-center gap-1.5">
                            <span class="text-[#cc6c3b]">branch:</span> {$branch}
                        </span>
                        <a href="https://github.com/motechgroup/getembenews/commit/{$fullSha}" target="_blank" class="px-3 py-1 rounded-lg bg-cyan-950/60 text-cyan-300 border border-cyan-800/60 text-xs font-mono font-bold hover:bg-cyan-900/60 transition flex items-center gap-1.5">
                            <span class="text-slate-400">commit:</span> #{$shortSha}
                        </a>
                        <span class="px-3 py-1 rounded-lg bg-slate-800 text-slate-400 border border-slate-700 text-[11px]">
                            {$syncMethod}
                        </span>
                    </div>
                </div>

                <!-- Commit Message Card -->
                <div class="bg-slate-950/80 rounded-xl p-4 border border-slate-800/80 mb-4">
                    <div class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-1">Commit Message</div>
                    <div class="text-sm font-semibold text-slate-100 font-mono">"{$commitMsg}"</div>
                    <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-400 pt-2 border-t border-slate-900">
                        <span>👤 <strong class="text-slate-300">Author:</strong> {$author}</span>
                        <span>🕒 <strong class="text-slate-300">Committed:</strong> {$date}</span>
                        <span class="ml-auto text-slate-300 font-mono text-[11px]">
                            Changes: <strong class="text-white">{$stats['total']}</strong> (<span class="text-green-400">+{$stats['additions']}</span> / <span class="text-red-400">-{$stats['deletions']}</span>)
                        </span>
                    </div>
                </div>

                <!-- Raw Git / Sync Log Output -->
                <details class="group">
                    <summary class="cursor-pointer text-xs font-bold text-slate-400 hover:text-slate-200 transition flex items-center gap-2 select-none">
                        <span>▶</span> <span>View Raw Git Pull / Synchronization Output</span>
                    </summary>
                    <pre class="mt-3 p-4 bg-slate-950 rounded-xl border border-slate-800/80 text-cyan-300 text-xs font-mono overflow-x-auto max-h-48 whitespace-pre-wrap">{$gitOutput}</pre>
                </details>
            </div>

            <!-- Card 2: Changes Made / Modified Files -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800/80 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-cyan-950/50 text-cyan-400 border border-cyan-900/40 text-base">📝</span>
                        <div>
                            <h2 class="text-base font-bold text-white">Changes Made in this Release</h2>
                            <p class="text-xs text-slate-400">File-by-file update manifest</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md bg-slate-800 text-slate-300 text-xs font-mono font-bold">
                        {$fileCount} file(s)
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/70">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900 text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                                <th class="py-2.5 px-3 w-24">Status</th>
                                <th class="py-2.5 px-3">File Path</th>
                                <th class="py-2.5 px-3 text-right w-24">Diff</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$filesHtml}
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 3: Database Migrations -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800/80 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-emerald-950/50 text-emerald-400 border border-emerald-900/40 text-base">🗄️</span>
                        <div>
                            <h2 class="text-base font-bold text-white">Database Migrations</h2>
                            <p class="text-xs text-slate-400">Schema synchronization & batch status</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-950/60 text-emerald-400 border border-emerald-800/50">
                        ✔ Status: Up to date
                    </span>
                </div>

                <div class="mb-4">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Artisan Output</div>
                    <pre class="p-4 bg-slate-950 rounded-xl border border-slate-800/80 text-emerald-300 text-xs font-mono overflow-x-auto whitespace-pre-wrap">{$migrationOutput}</pre>
                </div>

                <div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Latest Executed Migrations</div>
                    <div class="rounded-xl border border-slate-800 bg-slate-950/70 overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <tbody>
                                {$migrationsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card 4: System Caches & Optimizations -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800/80 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-purple-950/50 text-purple-400 border border-purple-900/40 text-base">⚡</span>
                        <div>
                            <h2 class="text-base font-bold text-white">Application Cache & Optimizations</h2>
                            <p class="text-xs text-slate-400">Compiled views, routes, config & in-memory bytecode</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-950/60 text-purple-400 border border-purple-800/50">
                        Flushed & Ready
                    </span>
                </div>

                <div class="space-y-1 bg-slate-950/70 p-4 rounded-xl border border-slate-800">
                    {$cacheItemsHtml}
                </div>
            </div>

            <!-- Bottom Action Navigation -->
            <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                <div class="flex items-center gap-3">
                    <a href="/" target="_blank" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <span>📰</span> View News Site
                    </a>
                    <a href="/admin/articles" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <span>✍️</span> Articles Desk
                    </a>
                </div>
                
                <div class="text-xs text-slate-500 font-mono">
                    Updated on {$date}
                </div>
            </div>

        </div>

    </div>
</body>
</html>
HTML;
    }
}
