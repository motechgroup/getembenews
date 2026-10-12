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
        Schema::create('airtime_topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phone', 30)->index();
            $table->string('phone_e164', 30)->index();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('KES');
            $table->string('status', 20)->default('pending')->index(); // sent, failed, pending
            $table->string('provider', 50)->default('africastalking');
            $table->string('request_id')->nullable()->index();
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->text('error_message')->nullable();
            $table->json('raw_response')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airtime_topups');
    }
};
