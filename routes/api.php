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
use App\Http\Controllers\Api\HomeSettingsController;
use App\Http\Controllers\Api\FooterSettingsController;
use App\Http\Controllers\Api\GeneralGlobalSettingsController;
use App\Http\Controllers\Api\AboutSettingsController;
use App\Http\Controllers\Api\SuccessStoriesController;
use App\Http\Controllers\Api\OurTeamController;
use App\Http\Controllers\Api\AdvisoryBoardController;
use App\Http\Controllers\Api\OurApproachController;
use App\Http\Controllers\Api\ContactController;

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

        // System Settings (Public - Read Only)
    Route::prefix('system')->group(function () {
        // Home Settings
        Route::get('home-settings', [HomeSettingsController::class, 'index']);
        Route::get('home-settings/{home_setting}', [HomeSettingsController::class, 'show']);

        // Footer Settings
        Route::get('footer-settings', [FooterSettingsController::class, 'index']);
        Route::get('footer-settings/{footer_setting}', [FooterSettingsController::class, 'show']);

        // General Global Settings
        Route::get('general-global-settings', [GeneralGlobalSettingsController::class, 'index']);
        Route::get('general-global-settings/{general_global_setting}', [GeneralGlobalSettingsController::class, 'show']);

        // About Settings
        Route::get('about-settings', [AboutSettingsController::class, 'index']);
        Route::get('about-settings/{about_setting}', [AboutSettingsController::class, 'show']);

        // Success Stories
        Route::get('success-stories', [SuccessStoriesController::class, 'index']);
        Route::get('success-stories/{success_story}', [SuccessStoriesController::class, 'show']);

        // Our Team
        Route::get('our-team', [OurTeamController::class, 'index']);
        Route::get('our-team/{our_team}', [OurTeamController::class, 'show']);

        // Advisory Board
        Route::get('advisory-board', [AdvisoryBoardController::class, 'index']);
        Route::get('advisory-board/{advisory_board}', [AdvisoryBoardController::class, 'show']);

        // Our Approach
        Route::get('our-approach', [OurApproachController::class, 'index']);
        Route::get('our-approach/{our_approach}', [OurApproachController::class, 'show']);

        // Contact Submissions (Read Only)
        Route::get('contacts', [ContactController::class, 'index']);
        Route::get('contacts/{contact}', [ContactController::class, 'show']);
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
