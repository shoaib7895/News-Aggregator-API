<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ArticleController;

// throttling middleware to rate limit on requests
Route::middleware('throttle:60,1')->group(function () {
    Route::post('register', [UserController::class, 'register']); 
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum'); //use auth sanctum middleware for authorized users
    Route::post('forgot-password', [UserController::class, 'sendResetLink']);
    Route::post('reset-password', [UserController::class, 'resetPassword']);
});

// protected routes in auth sanctum middleware
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('articles', [ArticleController::class, 'index']);  // Fetch articles with pagination and search
    Route::get('articles/{id}', [ArticleController::class, 'show']);  // Fetch single article

    Route::get('preferences', [UserPreferenceController::class, 'show']);  // Get user preferences
    Route::post('preferences', [UserPreferenceController::class, 'update']);  // Set or update preferences
    Route::get('personalized-feed', [UserPreferenceController::class, 'personalizedFeed']);  // Get personalized news feed
});