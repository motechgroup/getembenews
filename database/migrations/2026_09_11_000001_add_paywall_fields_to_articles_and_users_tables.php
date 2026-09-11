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
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('is_pinned');
            $table->decimal('price', 10, 2)->nullable()->after('is_premium');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('subscription_plan')->nullable()->after('role');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_plan');
            $table->string('mpesa_phone')->nullable()->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_premium', 'price']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['subscription_plan', 'subscription_expires_at', 'mpesa_phone']);
        });
    }
};
