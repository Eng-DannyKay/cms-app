<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnalyticsSummaryResource;
use App\Services\Analytics\AnalyticsService;
use App\Repositories\Analytics\AnalyticsRepository;
use App\Exports\AnalyticsExport;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private AnalyticsRepository $analyticsRepository
    ) {}

    public function getPageAnalytics(string $pageId, Request $request): JsonResponse
    {
        $page = Page::where('id', $pageId)
            ->where('client_id', auth()->user()->client->id)
            ->firstOrFail();

        $period = $request->get('period', '30d');
        $stats = $this->analyticsService->getPageStats($page, $period);

        return response()->json([
            'data' => $stats,
            'period' => $period,
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
            ],
            'real_time' => $this->analyticsService->getRealTimeData($page),
        ]);
    }

    public function getClientAnalytics(Request $request): JsonResponse
    {
        $client = auth()->user()->client;
        $period = $request->get('period', '30d');

        $summary = $this->analyticsService->getClientAnalyticsSummary($client, $period);

        return (new AnalyticsSummaryResource($summary))
            ->additional([
                'period' => $period,
                'client' => [
                    'id' => $client->id,
                    'company_name' => $client->company_name,
                ],
            ])
            ->response();
    }

    public function getTopPages(Request $request): JsonResponse
    {
        $client = auth()->user()->client;
        $period = $request->get('period', '30d');
        $limit = $request->get('limit', 10);

        $dateRange = $this->analyticsService->getDateRange($period);
        $topPages = $this->analyticsRepository->getClientTopPages($client, $dateRange, $limit);

        return response()->json([
            'data' => $topPages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'views' => $page->views,
                    'url' => route('public.page', [
                        'clientSlug' => $page->client->slug,
                        'pageSlug' => $page->slug
                    ]),
                ];
            }),
            'period' => $period,
            'total_pages' => $client->pages()->count(),
        ]);
    }

    public function exportPageAnalytics(string $pageId, Request $request): BinaryFileResponse
    {
        $page = Page::where('id', $pageId)
            ->where('client_id', auth()->user()->client->id)
            ->firstOrFail();

        $period = $request->get('period', '30d');
        $format = $request->get('format', 'csv');

        $fileName = "analytics-{$page->slug}-{$period}.{$format}";

        return Excel::download(new AnalyticsExport($page, $period), $fileName,
            $format === 'xlsx' ? \Maatwebsite\Excel\Excel::XLSX : \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function getVisitorTimeline(string $pageId, Request $request): JsonResponse
    {
        $page = Page::where('id', $pageId)
            ->where('client_id', auth()->user()->client->id)
            ->firstOrFail();

        $period = $request->get('period', '7d');
        $dateRange = $this->analyticsService->getDateRange($period);

        $timeline = $this->analyticsRepository->getViewsByDate($page, $dateRange);

        return response()->json([
            'data' => $timeline,
            'period' => $period,
            'stats' => [
                'total_views' => $timeline->sum('views'),
                'peak_views' => $timeline->max('views'),
                'avg_views' => round($timeline->avg('views'), 1),
            ],
        ]);
    }

    public function getGeographicData(string $pageId, Request $request): JsonResponse
    {
        $page = Page::where('id', $pageId)
            ->where('client_id', auth()->user()->client->id)
            ->firstOrFail();

        $period = $request->get('period', '30d');
        $dateRange = $this->analyticsService->getDateRange($period);

        $countries = $this->analyticsRepository->getViewsByCountry($page, $dateRange);
        $cities = $this->getViewsByCity($page, $dateRange);

        return response()->json([
            'countries' => $countries,
            'cities' => $cities,
            'period' => $period,
            'total_locations' => $countries->count() + $cities->count(),
        ]);
    }

    public function getDeviceAnalytics(string $pageId, Request $request): JsonResponse
    {
        $page = Page::where('id', $pageId)
            ->where('client_id', auth()->user()->client->id)
            ->firstOrFail();

        $period = $request->get('period', '30d');
        $dateRange = $this->analyticsService->getDateRange($period);

        $devices = $this->analyticsRepository->getViewsByDevice($page, $dateRange);
        $browsers = $this->analyticsRepository->getViewsByBrowser($page, $dateRange);
        $platforms = $this->getViewsByPlatform($page, $dateRange);

        return response()->json([
            'devices' => $devices,
            'browsers' => $browsers,
            'platforms' => $platforms,
            'period' => $period,
        ]);
    }

    private function getViewsByCity(Page $page, array $dateRange)
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->whereNotNull('city')
            ->select('city', 'country', DB::raw('COUNT(*) as views'))
            ->groupBy('city', 'country')
            ->orderByDesc('views')
            ->limit(15)
            ->get();
    }

    private function getViewsByPlatform(Page $page, array $dateRange)
    {
        return $page->views()
            ->whereBetween('created_at', $dateRange)
            ->select('platform', DB::raw('COUNT(*) as views'))
            ->groupBy('platform')
            ->orderByDesc('views')
            ->get();
    }
}
