<?php

namespace App\Repositories\Analytics;

use App\Models\Page;
use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class AnalyticsRepository
{
    public function getTotalViews(Page $page, array $dateRange): int
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->count();
    }

    public function getUniqueVisitors(Page $page, array $dateRange): int
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->distinct('visitor_id')
            ->count('visitor_id');
    }

    public function getReturningVisitors(Page $page, array $dateRange): int
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }

    public function getBounceRate(Page $page, array $dateRange): float
    {
        $totalVisitors = $this->getUniqueVisitors($page, $dateRange);

        if ($totalVisitors === 0) return 0.0;

        $singlePageVisitors = $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        return round(($singlePageVisitors / $totalVisitors) * 100, 2);
    }

    public function getAvgSessionDuration(Page $page, array $dateRange): float
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->join('visitor_sessions', 'page_views.visitor_id', '=', 'visitor_sessions.visitor_id')
            ->avg('visitor_sessions.page_views_count');
    }

    public function getViewsByDate(Page $page, array $dateRange): Collection
    {
        $views = $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with zero views
        $period = CarbonPeriod::create($dateRange[0], $dateRange[1]);
        $viewsByDate = collect();

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $viewCount = $views->firstWhere('date', $dateStr)->views ?? 0;

            $viewsByDate->push([
                'date' => $dateStr,
                'views' => $viewCount,
                'formatted_date' => $date->format('M j'),
            ]);
        }

        return $viewsByDate;
    }

    public function getViewsByCountry(Page $page, array $dateRange): Collection
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->whereNotNull('country')
            ->select('country', DB::raw('COUNT(*) as views'))
            ->groupBy('country')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
    }

    public function getViewsByDevice(Page $page, array $dateRange): Collection
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select('device_type', DB::raw('COUNT(*) as views'))
            ->groupBy('device_type')
            ->orderByDesc('views')
            ->get();
    }

    public function getViewsByBrowser(Page $page, array $dateRange): Collection
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select('browser', DB::raw('COUNT(*) as views'))
            ->groupBy('browser')
            ->orderByDesc('views')
            ->limit(8)
            ->get();
    }

    public function getReferralSources(Page $page, array $dateRange): Collection
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->whereNotNull('referrer')
            ->select('referrer', DB::raw('COUNT(*) as visits'))
            ->groupBy('referrer')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();
    }

    public function getClientTotalViews(Client $client, array $dateRange): int
    {
        return $client->pages()
            ->join('page_views', 'pages.id', '=', 'page_views.page_id')
            ->whereBetween('page_views.created_at', $dateRange)
            ->count();
    }

    public function getClientUniqueVisitors(Client $client, array $dateRange): int
    {
        return $client->pages()
            ->join('page_views', 'pages.id', '=', 'page_views.page_id')
            ->whereBetween('page_views.created_at', $dateRange)
            ->distinct('page_views.visitor_id')
            ->count('page_views.visitor_id');
    }

    public function getClientTopPages(Client $client, array $dateRange, int $limit = 10): Collection
    {
        return $client->pages()
            ->leftJoin('page_views', function ($join) use ($dateRange) {
                $join->on('pages.id', '=', 'page_views.page_id')
                     ->whereBetween('page_views.created_at', $dateRange);
            })
            ->select('pages.*', DB::raw('COUNT(page_views.id) as views'))
            ->groupBy('pages.id')
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }

    public function getClientConversionRate(Client $client, array $dateRange): float
    {
        // Implementation for conversion rate tracking
        // This would integrate with your specific conversion goals
        return 0.0; // Placeholder
    }

    public function getClientEngagementMetrics(Client $client, array $dateRange): array
    {
        return [
            'avg_pages_per_session' => $this->getAvgPagesPerSession($client, $dateRange),
            'avg_session_duration' => $this->getAvgSessionDurationClient($client, $dateRange),
            'bounce_rate' => $this->getBounceRateClient($client, $dateRange),
        ];
    }

    private function getAvgPagesPerSession(Client $client, array $dateRange): float
    {
        return $client->pages()
            ->join('page_views', 'pages.id', '=', 'page_views.page_id')
            ->whereBetween('page_views.created_at', $dateRange)
            ->join('visitor_sessions', 'page_views.visitor_id', '=', 'visitor_sessions.visitor_id')
            ->avg('visitor_sessions.page_views_count');
    }

    private function getAvgSessionDurationClient(Client $client, array $dateRange): float
    {
        // Placeholder - would require session duration tracking
        return 0.0;
    }

    private function getBounceRateClient(Client $client, array $dateRange): float
    {
        $totalVisitors = $this->getClientUniqueVisitors($client, $dateRange);

        if ($totalVisitors === 0) return 0.0;

        $singlePageVisitors = $client->pages()
            ->join('page_views', 'pages.id', '=', 'page_views.page_id')
            ->whereBetween('page_views.created_at', $dateRange)
            ->select('page_views.visitor_id')
            ->groupBy('page_views.visitor_id')
            ->havingRaw('COUNT(*) = 1')
            ->count();

        return round(($singlePageVisitors / $totalVisitors) * 100, 2);
    }
}
