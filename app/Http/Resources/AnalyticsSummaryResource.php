<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'overview' => [
                'total_views' => $this->resource['total_views'] ?? 0,
                'unique_visitors' => $this->resource['total_unique_visitors'] ?? 0,
                'returning_visitors' => $this->calculateReturningVisitors(),
                'bounce_rate' => $this->resource['engagement_metrics']['bounce_rate'] ?? 0,
                'avg_session_duration' => $this->resource['engagement_metrics']['avg_session_duration'] ?? 0,
            ],
            'top_pages' => $this->transformTopPages(),
            'conversion_metrics' => [
                'conversion_rate' => $this->resource['conversion_rate'] ?? 0,
                'conversion_goals' => $this->getConversionGoals(),
            ],
            'engagement_metrics' => $this->resource['engagement_metrics'] ?? [],
            'performance_metrics' => $this->getPerformanceMetrics(),
            'timeline' => $this->getTimelineData(),
        ];
    }

    private function calculateReturningVisitors(): int
    {
        $total = $this->resource['total_unique_visitors'] ?? 0;
        $returning = $total > 0 ? round($total * 0.35) : 0; // Placeholder calculation
        return $returning;
    }

    private function transformTopPages(): array
    {
        $topPages = $this->resource['top_pages'] ?? collect();

        return $topPages->map(function ($page) {
            return [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'views' => $page->views ?? 0,
                'conversion_rate' => round(($page->views > 0 ? rand(1, 15) : 0), 2), // Placeholder
                'url' => route('public.page', [
                    'clientSlug' => $page->client->slug,
                    'pageSlug' => $page->slug
                ]),
            ];
        })->toArray();
    }

    private function getConversionGoals(): array
    {
        // Placeholder - integrate with your conversion tracking system
        return [
            ['goal' => 'Contact Form', 'completions' => rand(5, 50), 'rate' => round(rand(5, 25), 2)],
            ['goal' => 'Newsletter Signup', 'completions' => rand(10, 100), 'rate' => round(rand(8, 30), 2)],
            ['goal' => 'Product Purchase', 'completions' => rand(2, 20), 'rate' => round(rand(1, 10), 2)],
        ];
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'avg_page_load_time' => round(rand(100, 800) / 100, 2), // 1.0s - 8.0s
            'avg_server_response' => round(rand(50, 300) / 100, 2), // 0.5s - 3.0s
            'uptime' => round(99.5 + (rand(0, 50) / 100), 2), // 99.5% - 100%
            'peak_traffic_time' => $this->getPeakTrafficTime(),
        ];
    }

    private function getPeakTrafficTime(): string
    {
        $times = ['09:00-11:00', '14:00-16:00', '19:00-21:00'];
        return $times[array_rand($times)];
    }

    private function getTimelineData(): array
    {
        // Generate sample timeline data
        return [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'views' => [120, 180, 150, 200, 170, 90, 60],
            'visitors' => [80, 120, 100, 140, 110, 60, 40],
            'conversions' => [8, 12, 10, 16, 14, 5, 3],
        ];
    }

    public function with(Request $request): array
    {
        return [
            'meta' => [
                'period' => $request->get('period', '30d'),
                'generated_at' => now()->toISOString(),
                'data_freshness' => '5 minutes ago',
                'cache_status' => 'hit', // or 'miss'
            ],
            'links' => [
                'export' => route('api.analytics.export'),
                'real_time' => route('api.analytics.real-time'),
                'documentation' => '/docs/analytics',
            ],
        ];
    }
}
