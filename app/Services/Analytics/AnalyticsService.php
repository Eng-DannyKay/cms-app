<?php

namespace App\Services\Analytics;

use App\Models\Page;
use App\Models\PageView;
use App\Models\VisitorSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    private const CACHE_TTL = 3600; // 1 hour
    private const REAL_TIME_TTL = 300; // 5 minutes

    public function trackPageView(Page $page, array $requestData): void
    {
        $visitorId = $this->generateVisitorId($requestData);
        $sessionId = session()->getId();

        // Store in Redis for real-time analytics
        $this->storeRealTimeData($page, $visitorId, $requestData);

        // Create page view record
        PageView::create([
            'page_id' => $page->id,
            'visitor_id' => $visitorId,
            'ip_address' => $requestData['ip'] ?? null,
            'user_agent' => $requestData['user_agent'] ?? null,
            'referrer' => $this->parseReferrer($requestData['referrer'] ?? null),
            'session_id' => $sessionId,
            'country' => $requestData['country'] ?? null,
            'city' => $requestData['city'] ?? null,
            'device_type' => $this->getDeviceType($requestData['user_agent'] ?? null),
            'browser' => $this->getBrowser($requestData['user_agent'] ?? null),
            'platform' => $this->getPlatform($requestData['user_agent'] ?? null),
        ]);

        // Update visitor session
        $this->updateVisitorSession($visitorId, $sessionId);
    }

    public function getPageStats(Page $page, string $period = '30d'): array
    {
        $cacheKey = "page_stats:{$page->id}:{$period}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($page, $period) {
            $dateRange = $this->getDateRange($period);

            return [
                'total_views' => $this->getTotalViews($page, $dateRange),
                'unique_visitors' => $this->getUniqueVisitors($page, $dateRange),
                'returning_visitors' => $this->getReturningVisitors($page, $dateRange),
                'bounce_rate' => $this->getBounceRate($page, $dateRange),
                'avg_session_duration' => $this->getAvgSessionDuration($page, $dateRange),
                'views_by_date' => $this->getViewsByDate($page, $dateRange),
                'views_by_country' => $this->getViewsByCountry($page, $dateRange),
                'views_by_device' => $this->getViewsByDevice($page, $dateRange),
                'views_by_browser' => $this->getViewsByBrowser($page, $dateRange),
                'referral_sources' => $this->getReferralSources($page, $dateRange),
            ];
        });
    }

    public function getRealTimeData(Page $page): array
    {
        $redis = Redis::connection();

        return [
            'active_visitors' => (int) $redis->zcount("page:{$page->id}:active_visitors", '-inf', '+inf'),
            'views_last_hour' => (int) $redis->get("page:{$page->id}:views_last_hour") ?? 0,
            'views_today' => (int) $redis->get("page:{$page->id}:views_today") ?? 0,
            'current_visitors' => $this->getCurrentVisitors($page),
        ];
    }

    public function getClientAnalyticsSummary($client, string $period = '30d'): array
    {
        $cacheKey = "client_analytics:{$client->id}:{$period}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($client, $period) {
            $dateRange = $this->getDateRange($period);

            return [
                'total_views' => $this->getClientTotalViews($client, $dateRange),
                'total_unique_visitors' => $this->getClientUniqueVisitors($client, $dateRange),
                'top_pages' => $this->getClientTopPages($client, $dateRange),
                'conversion_rate' => $this->getClientConversionRate($client, $dateRange),
                'engagement_metrics' => $this->getClientEngagementMetrics($client, $dateRange),
            ];
        });
    }

    public function generateExportData(Page $page, string $period): Collection
    {
        $dateRange = $this->getDateRange($period);

        return PageView::where('page_id', $page->id)
            ->whereBetween('created_at', $dateRange)
            ->with(['page.client'])
            ->get()
            ->map(function ($view) {
                return [
                    'Date' => $view->created_at->toDateTimeString(),
                    'Page Title' => $view->page->title,
                    'Visitor ID' => $view->visitor_id,
                    'IP Address' => $view->ip_address,
                    'Country' => $view->country,
                    'City' => $view->city,
                    'Device' => $view->device_type,
                    'Browser' => $view->browser,
                    'Platform' => $view->platform,
                    'Referrer' => $view->referrer,
                    'Session ID' => $view->session_id,
                ];
            });
    }

    private function storeRealTimeData(Page $page, string $visitorId, array $requestData): void
    {
        $redis = Redis::connection();
        $timestamp = now()->getTimestamp();

        // Track active visitors (sorted set with timestamp)
        $redis->zadd("page:{$page->id}:active_visitors", $timestamp, $visitorId);
        $redis->expire("page:{$page->id}:active_visitors", self::REAL_TIME_TTL);

        // Increment hourly and daily counters
        $redis->incr("page:{$page->id}:views_last_hour");
        $redis->expire("page:{$page->id}:views_last_hour", 3600);

        $redis->incr("page:{$page->id}:views_today");
        $redis->expire("page:{$page->id}:views_today", 86400);
    }

    private function getCurrentVisitors(Page $page): array
    {
        $redis = Redis::connection();
        $visitors = $redis->zrange("page:{$page->id}:active_visitors", 0, -1, 'WITHSCORES');

        return collect($visitors)->map(function ($timestamp, $visitorId) {
            return [
                'visitor_id' => $visitorId,
                'last_active' => Carbon::createFromTimestamp($timestamp)->diffForHumans(),
            ];
        })->values()->toArray();
    }

    private function generateVisitorId(array $requestData): string
    {
        return hash('sha256',
            ($requestData['ip'] ?? '') .
            ($requestData['user_agent'] ?? '') .
            config('app.key')
        );
    }

    private function parseReferrer(?string $referrer): ?string
    {
        if (!$referrer) return null;

        $host = parse_url($referrer, PHP_URL_HOST);
        return $host ?: $referrer;
    }

    private function getDeviceType(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'mobile') !== false) return 'mobile';
        if (strpos($userAgent, 'tablet') !== false) return 'tablet';
        if (strpos($userAgent, 'ipad') !== false) return 'tablet';

        return 'desktop';
    }

    private function getBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';

        $browsers = [
            'chrome', 'firefox', 'safari', 'opera', 'msie', 'edge', 'brave'
        ];

        foreach ($browsers as $browser) {
            if (stripos($userAgent, $browser) !== false) {
                return $browser;
            }
        }

        return 'unknown';
    }

    private function getPlatform(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';

        $platforms = [
            'windows', 'linux', 'macintosh', 'android', 'iphone', 'ipad'
        ];

        foreach ($platforms as $platform) {
            if (stripos($userAgent, $platform) !== false) {
                return $platform;
            }
        }

        return 'unknown';
    }

    private function updateVisitorSession(string $visitorId, string $sessionId): void
    {
        VisitorSession::updateOrCreate(
            ['visitor_id' => $visitorId],
            [
                'last_visit_at' => now(),
                'session_id' => $sessionId,
                'page_views_count' => \DB::raw('page_views_count + 1'),
            ]
        );

        if (!VisitorSession::where('visitor_id', $visitorId)->exists()) {
            VisitorSession::create([
                'visitor_id' => $visitorId,
                'first_visit_at' => now(),
                'last_visit_at' => now(),
                'session_id' => $sessionId,
                'page_views_count' => 1,
            ]);
        }
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            '24h' => [now()->subDay(), now()],
            '7d' => [now()->subDays(7), now()],
            '30d' => [now()->subDays(30), now()],
            '90d' => [now()->subDays(90), now()],
            default => [now()->subDays(30), now()],
        };
    }
}
