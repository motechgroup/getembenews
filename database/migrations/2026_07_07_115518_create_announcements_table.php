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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_email');
            $table->string('visitor_phone');
            $table->string('type'); // funeral, general
            $table->string('media'); // tv, radio, both
            $table->text('content');
            $table->integer('word_count');
            $table->integer('days_count');
            $table->integer('rate_per_word');
            $table->integer('total_amount');
            $table->string('payment_status')->default('pending'); // pending, paid
            $table->string('payment_reference')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
