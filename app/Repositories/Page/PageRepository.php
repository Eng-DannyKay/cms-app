<?php

namespace App\Repositories\Page;

use App\Models\Page;
use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PageRepository
{
 
public function getClientPages(Client $client, array $filters = [], int $perPage = 15): LengthAwarePaginator
{
    $query = $client->pages()->with(['client.user']);

    if (!empty($filters['search'])) {
        $query->where('title', 'like', '%' . $filters['search'] . '%');
    }

    if (isset($filters['published'])) {
        $query->where('is_published', (bool)$filters['published']);
    }

    $sortField = $filters['sort'] ?? 'created_at';
    $sortDirection = $filters['direction'] ?? 'desc';

    $validSortFields = ['title', 'slug', 'created_at', 'updated_at', 'version'];
    $sortField = in_array($sortField, $validSortFields) ? $sortField : 'created_at';

    return $query->orderBy($sortField, $sortDirection)
                ->paginate($perPage)
                ->appends($filters);
}

    public function findPageBySlug(Client $client, string $slug): ?Page
    {
        return $client->pages()
            ->where('slug', $slug)
            ->with(['client.user', 'client.themes'])
            ->first();
    }

    public function getPublishedPages(Client $client): Collection
    {
        return $client->pages()
            ->where('is_published', true)
            ->with('client.themes')
            ->get();
    }

    public function getPageWithAnalytics(int $pageId): ?Page
    {
        return Page::withCount(['views as total_views', 'views as today_views' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->with(['views' => function ($query) {
                $query->select('page_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('page_id');
            }])
            ->find($pageId);
    }

    public function updatePageSeo(Page $page, array $seoData): Page
    {
        $content = $page->content;
        $content['seo'] = $seoData;

        $page->update(['content' => $content]);

        return $page->fresh();
    }
}
