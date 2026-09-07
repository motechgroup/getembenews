<?php

use App\Http\Controllers\Api\MobileAppController;
use Illuminate\Support\Facades\Route;

// Public Mobile App Routes (v1)
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('/deploy-git-pull', function() {
        $output = @shell_exec("cd " . base_path() . " && git pull origin main 2>&1");
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        return response()->json([
            'status' => 'success',
            'output' => $output ?: 'Pulled successfully'
        ]);
    });
    Route::get('/app-settings', [MobileAppController::class, 'settings']);
    Route::get('/categories', [MobileAppController::class, 'categories']);
    Route::get('/articles', [MobileAppController::class, 'articles']);
    Route::get('/home-feed', [MobileAppController::class, 'homeFeed']);
    Route::get('/authors/{id}', [MobileAppController::class, 'authorProfile']);
    Route::get('/videos', [MobileAppController::class, 'videos']);
    Route::get('/live-streams', [MobileAppController::class, 'liveStreams']);
    Route::post('/contact', [MobileAppController::class, 'contact'])->middleware('throttle:submissions');
    Route::post('/newsletter/subscribe', [MobileAppController::class, 'subscribeNewsletter'])->middleware('throttle:submissions');
    Route::get('/advertisements', [MobileAppController::class, 'advertisements']);
    Route::get('/native-ads', [MobileAppController::class, 'nativeAds']);
    Route::get('/breaking-news', [MobileAppController::class, 'breakingNews']);
    Route::get('/announcements', [MobileAppController::class, 'announcements']);
    Route::post('/announcements/ocr', [MobileAppController::class, 'ocrAnnouncement']);
    Route::post('/announcements', [MobileAppController::class, 'submitAnnouncement'])->middleware('throttle:submissions');
    Route::post('/announcements/{id}/pay', [MobileAppController::class, 'payAnnouncement'])->middleware('throttle:submissions');
    Route::get('/announcements/{id}/status', [MobileAppController::class, 'checkAnnouncementPaymentStatus']);
    Route::post('/payments/mpesa/callback', [\App\Http\Controllers\Api\MpesaCallbackController::class, 'handleCallback']);
    
    // Agent Portal API endpoints (v1)
    Route::prefix('agent')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Api\AgentApiController::class, 'login'])->middleware('throttle:auth');
        Route::get('/profile', [\App\Http\Controllers\Api\AgentApiController::class, 'profile']);
        Route::post('/pin/regenerate', [\App\Http\Controllers\Api\AgentApiController::class, 'regeneratePin']);
        Route::get('/announcements', [\App\Http\Controllers\Api\AgentApiController::class, 'announcements']);
        Route::post('/announcements', [\App\Http\Controllers\Api\AgentApiController::class, 'submitAnnouncement'])->middleware('throttle:submissions');
        Route::post('/announcements/{id}/pay', [\App\Http\Controllers\Api\AgentApiController::class, 'payAnnouncement'])->middleware('throttle:submissions');
        Route::get('/earnings', [\App\Http\Controllers\Api\AgentApiController::class, 'earnings']);
        Route::get('/disputes', [\App\Http\Controllers\Api\AgentApiController::class, 'disputes']);
        Route::post('/disputes', [\App\Http\Controllers\Api\AgentApiController::class, 'disputes']);
    });

    // Auth endpoints
    Route::get('/auth/google', [MobileAppController::class, 'googleRedirect']);
    Route::post('/auth/google', [MobileAppController::class, 'googleTokenLogin']);
    Route::post('/auth/register', [MobileAppController::class, 'register'])->middleware('throttle:auth');
    Route::post('/auth/login', [MobileAppController::class, 'login'])->middleware('throttle:auth');

    // Authenticated Mobile App Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [MobileAppController::class, 'logout']);
        Route::get('/auth/profile', [MobileAppController::class, 'profile']);
        Route::put('/auth/profile', [MobileAppController::class, 'updateProfile']);
        
        // Saved/Bookmarked Articles
        Route::get('/articles/saved', [MobileAppController::class, 'savedArticles']);
        Route::post('/articles/{id}/save', [MobileAppController::class, 'toggleSave']);
        
        // Interactivity
        Route::post('/articles/{id}/comment', [MobileAppController::class, 'comment']);
    });

    Route::get('/articles/{slug}', [MobileAppController::class, 'article']);
});
