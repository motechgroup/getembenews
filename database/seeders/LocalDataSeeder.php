<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('seeders/data/local_dump.json');

        if (!File::exists($filePath)) {
            $this->command->error("Dump file not found at: {$filePath}");
            $this->command->info("Please run 'php artisan db:export-local' on your localhost first.");
            return;
        }

        $dump = json_decode(File::get($filePath), true);
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        // Disable Foreign Key Constraints
        if ($driver === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = OFF;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        foreach ($dump as $table => $rows) {
            $this->command->info("Importing and seeding table: {$table}...");
            
            // Clean table data
            DB::table($table)->truncate();

            if (!empty($rows)) {
                // Bulk insert in chunks to stay within SQL driver parameter limits
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }
        }

        // Enable Foreign Key Constraints
        if ($driver === 'sqlite') {
            $connection->statement('PRAGMA foreign_keys = ON;');
        } else {
            $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info("Database successfully seeded from local dump!");
    }
}
