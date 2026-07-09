<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportLocalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export-local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all current localhost database records to a JSON file for live server seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = [
            'users',
            'categories',
            'tags',
            'articles',
            'comments',
            'videos',
            'breaking_news',
            'advertisements',
            'settings',
            'agents',
            'announcements',
            'newsletters'
        ];

        $dump = [];

        foreach ($tables as $table) {
            if (DB::connection()->getSchemaBuilder()->hasTable($table)) {
                $this->info("Exporting table: {$table}...");
                // Map Eloquent/Query-builder arrays properly
                $rows = DB::table($table)->get()->map(function ($row) {
                    return (array) $row;
                })->toArray();
                $dump[$table] = $rows;
            }
        }

        $dir = database_path('seeders/data');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filePath = $dir . '/local_dump.json';
        File::put($filePath, json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info("Database exported successfully to {$filePath}!");
        $this->info("Commit and push this file, then run 'php artisan db:seed --class=LocalDataSeeder' on your live server.");
    }
}
