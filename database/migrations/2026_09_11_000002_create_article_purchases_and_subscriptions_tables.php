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
        Schema::create('article_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('phone_number')->nullable();
            $table->string('mpesa_reference')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('status')->default('pending')->index(); // pending, completed, failed
            $table->timestamps();
        });

        Schema::create('article_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan'); // daily, weekly, monthly
            $table->decimal('amount', 10, 2);
            $table->string('phone_number')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->string('mpesa_reference')->nullable()->index();
            $table->string('checkout_request_id')->nullable()->index();
            $table->string('status')->default('pending')->index(); // pending, active, expired, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_subscriptions');
        Schema::dropIfExists('article_purchases');
    }
};
