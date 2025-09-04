<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Services\Analytics\AnalyticsService;


class PublicPageController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function show(string $clientSlug, Request $request)
    {
        $client = Client::where('slug', $clientSlug)->firstOrFail();
        $page = $client->pages()->where('is_published', true)->firstOrFail();

        // Track page view
        $this->analyticsService->trackPageView($page, $request);

        return view('public.page', [
            'page' => $page,
            'client' => $client,
            'theme' => $client->currentTheme()
        ]);
    }
}
