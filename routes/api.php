<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NomineeController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\PastWinnerController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\PageContentController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/stats', [CategoryController::class, 'stats']);
        Route::get('/{category}', [CategoryController::class, 'show']);
    });

    // Nominees
    Route::prefix('nominees')->group(function () {
        Route::get('/', [NomineeController::class, 'index']);
        Route::get('/trending', [NomineeController::class, 'trending']);
        Route::get('/{nominee}', [NomineeController::class, 'show']);
    });

    // Voting
    Route::prefix('votes')->group(function () {
        Route::post('/{nominee}', [VoteController::class, 'vote']);
        Route::get('/{nominee}/check', [VoteController::class, 'hasVoted']);
        Route::get('/user/categories', [VoteController::class, 'categoryVotes']);
    });

    // Past Winners
    Route::prefix('past-winners')->group(function () {
        Route::get('/', [PastWinnerController::class, 'index']);
        Route::get('/years', [PastWinnerController::class, 'years']);
        Route::get('/categories', [PastWinnerController::class, 'categories']);
    });

    // Gallery
    Route::prefix('gallery')->group(function () {
        Route::get('/', [GalleryController::class, 'index']);
        Route::get('/years', [GalleryController::class, 'years']);
        Route::get('/{galleryEvent}', [GalleryController::class, 'show']);
    });

    // Registrations
    Route::prefix('registrations')->group(function () {
        Route::post('/', [RegistrationController::class, 'store']);
        Route::post('/lookup', [RegistrationController::class, 'show']);
    });

    // Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::get('/{key}', [SettingsController::class, 'show']);
    });


    // Page Content
    Route::prefix('content')->group(function () {
        Route::get('/', [PageContentController::class, 'index']);
        Route::get('/pages', [PageContentController::class, 'pages']);
        Route::get('/{page}', [PageContentController::class, 'show']);
        Route::get('/{page}/{section}', [PageContentController::class, 'section']);
        Route::get('/{page}/{section}/{key}', [PageContentController::class, 'item']);
    });

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => '1.0.0'
        ]);
    });
});
