<?php

use App\Http\Controllers\Api\MobileAppController;
use Illuminate\Support\Facades\Route;

// Public Mobile App Routes (v1)
Route::prefix('v1')->group(function () {
    Route::get('/app-settings', [MobileAppController::class, 'settings']);
    Route::get('/categories', [MobileAppController::class, 'categories']);
    Route::get('/articles', [MobileAppController::class, 'articles']);
    Route::get('/articles/{slug}', [MobileAppController::class, 'article']);
    
    // Auth endpoints
    Route::post('/auth/register', [MobileAppController::class, 'register']);
    Route::post('/auth/login', [MobileAppController::class, 'login']);

    // Authenticated Mobile App Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [MobileAppController::class, 'logout']);
        Route::get('/auth/profile', [MobileAppController::class, 'profile']);
        
        // Saved/Bookmarked Articles
        Route::get('/articles/saved', [MobileAppController::class, 'savedArticles']);
        Route::post('/articles/{id}/save', [MobileAppController::class, 'toggleSave']);
        
        // Interactivity
        Route::post('/articles/{id}/comment', [MobileAppController::class, 'comment']);
    });
});
