<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\TestGalleryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Grouping API routes under a prefix 'v1' for versioning (opsional, tapi bagus untuk praktik API)
Route::prefix('v1')->group(function () {

    // Projects API
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    // Team Members API
    Route::get('/team-members', [TeamMemberController::class, 'index']);
    Route::get('/team-members/{team_member}', [TeamMemberController::class, 'show']);

    // News API (gunakan slug untuk detail)
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{slug}', [NewsController::class, 'show']); // Menggunakan slug

    // Banners API
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/banners/{banner}', [BannerController::class, 'show']);

    // Gallery API
    Route::get('/gallery', [GalleryController::class, 'index']);
    Route::get('/gallery/{gallery}', [GalleryController::class, 'show']);

    // Contact Settings API (singleton)
    Route::get('/contact-settings', [ContactSettingController::class, 'index']);

    Route::get('/test-gallery', [TestGalleryController::class, 'index']);

});