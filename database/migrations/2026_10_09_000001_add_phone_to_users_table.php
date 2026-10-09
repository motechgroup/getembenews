<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique()->after('email');
            });
        }

        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL;');
        } catch (\Throwable $e) {
            // Ignore if driver does not support MODIFY (e.g. SQLite in tests)
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};
