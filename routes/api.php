<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\TrackPageView;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Pages routes
    Route::apiResource('pages', PageController::class)->except(['create', 'edit']);
    Route::post('pages/{page}/publish', [PageController::class, 'publish'])->name('pages.publish');
    Route::post('pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::get('pages/{page}/preview', [PageController::class, 'preview'])->name('pages.preview');

    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('pages/{page}', [AnalyticsController::class, 'getPageAnalytics'])->name('analytics.page');
        Route::get('pages/{page}/timeline', [AnalyticsController::class, 'getVisitorTimeline'])->name('analytics.timeline');
        Route::get('pages/{page}/geography', [AnalyticsController::class, 'getGeographicData'])->name('analytics.geography');
        Route::get('pages/{page}/devices', [AnalyticsController::class, 'getDeviceAnalytics'])->name('analytics.devices');
        Route::get('pages/{page}/export', [AnalyticsController::class, 'exportPageAnalytics'])->name('analytics.export');
        Route::get('summary', [AnalyticsController::class, 'getClientAnalytics'])->name('analytics.summary');
        Route::get('top-pages', [AnalyticsController::class, 'getTopPages'])->name('analytics.top-pages');
    });

    // Theme routes

Route::prefix('themes')->group(function () {
    Route::get('/', [ThemeController::class, 'index'])->name('themes.index');
    Route::get('current', [ThemeController::class, 'getCurrentTheme'])->name('themes.current');
    Route::get('css', [ThemeController::class, 'getCss'])->name('themes.css');
    Route::post('apply', [ThemeController::class, 'applyTheme'])->name('themes.apply');
    Route::post('customize', [ThemeController::class, 'customizeTheme'])->name('themes.customize');
    Route::post('reset', [ThemeController::class, 'resetCustomizations'])->name('themes.reset');
    Route::get('preview/{theme}', [ThemeController::class, 'previewTheme'])->name('themes.preview');
});

// Public theme preview
Route::get('theme-preview/{theme}', [ThemeController::class, 'publicPreview'])
    ->withoutMiddleware('auth:sanctum')
    ->name('themes.public-preview');

    // Client routes
    Route::prefix('client')->group(function () {
        Route::get('profile', [ClientController::class, 'getProfile'])->name('client.profile');
        Route::put('profile', [ClientController::class, 'updateProfile'])->name('client.profile.update');
        Route::post('upload-logo', [ClientController::class, 'uploadLogo'])->name('client.upload-logo');
    });
});

// Public preview route (no auth required, uses encrypted token)
Route::get('preview/{page}', [PageController::class, 'previewRender'])
    ->withoutMiddleware('auth:sanctum')
    ->name('api.pages.preview.render');

// Public theme preview route
Route::get('theme-preview/{theme}', [ThemeController::class, 'publicPreview'])
    ->withoutMiddleware('auth:sanctum')
    ->name('api.themes.public-preview');

// Public page route with tracking middleware
Route::get('/page/{clientSlug}/{pageSlug?}', [PublicPageController::class, 'show'])
    ->name('public.page')
    ->middleware(TrackPageView::class);

// Health check endpoint
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'environment' => config('app.env'),
    ]);
})->name('health.check');

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found.',
        'documentation' => url('/docs/api'),
    ], 404);
})->name('api.fallback');
