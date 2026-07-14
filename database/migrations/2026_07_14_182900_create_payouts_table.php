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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('agents')->onDelete('set null');
            $table->integer('amount'); // in KSh (whole number)
            $table->string('payment_method'); // M-Pesa, Bank Transfer, Cash, etc.
            $table->string('reference')->nullable(); // Transaction Reference Code
            $table->string('status')->default('completed'); // completed, pending, failed
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
