<?php

use App\Http\Controllers\Api\MobileAppController;
use Illuminate\Support\Facades\Route;

// Public Mobile App Routes (v1)
Route::prefix('v1')->middleware('throttle:api')->group(function () {
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
    Route::get('/breaking-news', [MobileAppController::class, 'breakingNews']);
    Route::get('/announcements', [MobileAppController::class, 'announcements']);
    Route::post('/announcements', [MobileAppController::class, 'submitAnnouncement'])->middleware('throttle:submissions');
    Route::post('/announcements/{id}/pay', [MobileAppController::class, 'payAnnouncement'])->middleware('throttle:submissions');
    Route::post('/payments/mpesa/callback', [\App\Http\Controllers\Api\MpesaCallbackController::class, 'handleCallback']);
    
    // Auth endpoints
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
