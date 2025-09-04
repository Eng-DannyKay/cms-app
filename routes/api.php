<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\TrackPageView;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// -------------------- AUTH ROUTES --------------------
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// -------------------- PROTECTED ROUTES --------------------
Route::middleware('auth:sanctum')->group(function () {

    // Pages
     Route::apiResource('pages', PageController::class)
        ->except(['create', 'edit'])
        ->names([
            'index' => 'api.pages.index',
            'store' => 'api.pages.store',
            'show' => 'api.pages.show',
            'update' => 'api.pages.update',
            'destroy' => 'api.pages.destroy',
        ]);
        
    // Additional page routes
    Route::prefix('pages')->group(function () {
        Route::post('{page}/publish', [PageController::class, 'publish'])->name('api.pages.publish');
        Route::post('{page}/duplicate', [PageController::class, 'duplicate'])->name('api.pages.duplicate');
        Route::get('{page}/preview', [PageController::class, 'preview'])->name('api.pages.preview');

        
        // Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('{page}', [AnalyticsController::class, 'getPageAnalytics'])->name('analytics.page');
            Route::get('{page}/timeline', [AnalyticsController::class, 'getVisitorTimeline'])->name('analytics.timeline');
            Route::get('{page}/geography', [AnalyticsController::class, 'getGeographicData'])->name('analytics.geography');
            Route::get('{page}/devices', [AnalyticsController::class, 'getDeviceAnalytics'])->name('analytics.devices');
            Route::get('{page}/export', [AnalyticsController::class, 'exportPageAnalytics'])->name('analytics.export');
            Route::get('summary', [AnalyticsController::class, 'getClientAnalytics'])->name('analytics.summary');
            Route::get('top-pages', [AnalyticsController::class, 'getTopPages'])->name('analytics.top-pages');
        });
    });

    // Themes
    Route::prefix('themes')->group(function () {
        Route::get('/', [ThemeController::class, 'index'])->name('themes.index');
        Route::get('current', [ThemeController::class, 'getCurrentTheme'])->name('themes.current');
        Route::get('css', [ThemeController::class, 'getCss'])->name('themes.css');
        Route::post('apply', [ThemeController::class, 'applyTheme'])->name('themes.apply');
        Route::post('customize', [ThemeController::class, 'customizeTheme'])->name('themes.customize');
        Route::post('reset', [ThemeController::class, 'resetCustomizations'])->name('themes.reset');
        Route::get('preview/{theme}', [ThemeController::class, 'previewTheme'])->name('themes.preview');
    });

    // Client
    Route::prefix('client')->group(function () {
        Route::get('profile', [ClientController::class, 'getProfile'])->name('client.profile');
        Route::put('profile', [ClientController::class, 'updateProfile'])->name('client.profile.update');
        Route::post('upload-logo', [ClientController::class, 'uploadLogo'])->name('client.upload-logo');
        Route::delete('logo', [ClientController::class, 'deleteLogo'])->name('client.delete-logo');
        Route::get('stats', [ClientController::class, 'getStats'])->name('client.stats');
    });
});

// -------------------- PUBLIC ROUTES --------------------

Route::get('preview/{page}', [PageController::class, 'previewRender'])
    ->withoutMiddleware('auth:sanctum')
    ->name('api.pages.preview.render');

// Public theme preview
Route::get('theme-preview/{theme}', [ThemeController::class, 'publicPreview'])
    ->withoutMiddleware('auth:sanctum')
    ->name('api.themes.public-preview');

// Public page (with tracking middleware)
Route::get('page/{clientSlug}/{pageSlug?}', [PublicPageController::class, 'show'])
    ->middleware(TrackPageView::class)
    ->name('public.page');

// -------------------- SYSTEM ROUTES --------------------

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'environment' => config('app.env'),
    ]);
})->name('health.check');

// Fallback
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found.',
        'documentation' => url('/docs/api'),
    ], 404);
})->name('api.fallback');
