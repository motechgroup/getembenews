<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure phone column exists
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique()->after('email');
            });
        }

        // 2. Force email column to be NULLABLE on MySQL
        try {
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL;');
        } catch (\Throwable $e) {}

        // 3. Clean up any dummy placeholder emails (user_07... @getembetv.co.ke) created during transition
        try {
            DB::statement("UPDATE users SET email = NULL WHERE email LIKE 'user_%@getembetv.co.ke';");
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
