<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Page;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Support\Facades\Route;

class TrackPageView
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldTrack($request, $response)) {
            $this->trackView($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        return $request->isMethod('GET') &&
               $response->getStatusCode() === 200 &&
               $this->isPublicPageRoute() &&
               !$request->isPreview();
    }

    private function isPublicPageRoute(): bool
    {
        return Route::currentRouteNamed('public.page');
    }

    private function trackView(Request $request): void
    {
        try {
            $page = $request->route('page') ?? $this->resolvePageFromRequest($request);

            if ($page instanceof Page && $page->is_published) {
                $this->analyticsService->trackPageView($page, [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'referrer' => $request->header('referer'),
                    'country' => $this->getCountryFromIP($request->ip()),
                    'city' => $this->getCityFromIP($request->ip()),
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::error('Page view tracking failed: ' . $e->getMessage());
        }
    }

    private function resolvePageFromRequest(Request $request): ?Page
    {
        $clientSlug = $request->route('clientSlug');
        $pageSlug = $request->route('pageSlug');

        if ($clientSlug && $pageSlug) {
            return Page::whereHas('client', function ($query) use ($clientSlug) {
                    $query->where('slug', $clientSlug);
                })
                ->where('slug', $pageSlug)
                ->where('is_published', true)
                ->first();
        }

        return null;
    }

    private function getCountryFromIP(string $ip): ?string
    {
        try {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getCityFromIP(string $ip): ?string
    {
        try {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
