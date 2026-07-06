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
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // 2. Articles
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('body');
            $table->string('featured_image')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Author/Reporter
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, scheduled, published
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('read_time')->default(0); // in minutes
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();
        });

        // 3. Comments
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->string('status')->default('approved'); // pending, approved, rejected, spam
            $table->timestamps();
        });

        // 4. Videos
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('embed_url'); // YouTube or custom video player URL
            $table->string('thumbnail_url')->nullable();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('published'); // draft, published
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 5. Saved Articles (pivot table for user bookmarks)
        Schema::create('saved_articles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->primary(['user_id', 'article_id']);
        });

        // 6. Breaking News Alerts
        Schema::create('breaking_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link')->nullable(); // link to an article if applicable
            $table->string('priority')->default('normal'); // high, normal
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 7. Advertisements
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_url')->nullable();
            $table->text('script_code')->nullable(); // For Google AdSense or HTML ads
            $table->string('destination_url')->nullable();
            $table->string('location')->default('sidebar'); // top, sidebar, inline, footer, mobile_sticky
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->timestamps();
        });

        // 8. Newsletters
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('breaking_news');
        Schema::dropIfExists('saved_articles');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('categories');
    }
};
