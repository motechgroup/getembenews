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
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->unique()->after('email');
            });
        }

        // Copy mpesa_phone into phone for users who registered with phone numbers
        try {
            DB::statement("UPDATE users SET phone = mpesa_phone WHERE (phone IS NULL OR phone = '') AND mpesa_phone IS NOT NULL AND mpesa_phone != '';");
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
