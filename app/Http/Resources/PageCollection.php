<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PageCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => PageResource::collection($this->collection),
            'meta' => $this->getMeta(),
            'links' => $this->getLinks(),
        ];
    }

    protected function getMeta(): array
    {
        return [
            'current_page' => $this->currentPage(),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'per_page' => $this->perPage(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
            'path' => $this->path(),
            'filters' => $this->getFilters(),
        ];
    }

    protected function getFilters(): array
    {
        return [
            'published' => request()->get('published'),
            'search' => request()->get('search'),
            'sort' => request()->get('sort', 'created_at'),
            'direction' => request()->get('direction', 'desc'),
        ];
    }

    protected function getLinks(): array
    {
        return [
            'first' => $this->url(1),
            'last' => $this->url($this->lastPage()),
            'prev' => $this->previousPageUrl(),
            'next' => $this->nextPageUrl(),
            'self' => $this->url($this->currentPage()),
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->header('X-Total-Count', $this->total());
        $response->header('X-Per-Page', $this->perPage());
        $response->header('X-Current-Page', $this->currentPage());
        $response->header('X-Last-Page', $this->lastPage());

        // Add caching headers for optimal performance
        $response->header('Cache-Control', 'max-age=60, public');
        $response->header('X-API-Version', '1.0.0');
    }

    public function with(Request $request): array
    {
        return [
            'included' => $this->getIncludedResources($request),
            'stats' => $this->getCollectionStats(),
        ];
    }

    protected function getIncludedResources(Request $request): array
    {
        if (!$request->has('include')) {
            return [];
        }

        $includes = explode(',', $request->get('include'));
        $included = [];

        foreach ($includes as $include) {
            if ($include === 'client' && $this->collection->isNotEmpty()) {
                $included['clients'] = ClientResource::collection(
                    $this->collection->pluck('client')->unique()
                );
            }
        }

        return $included;
    }

    protected function getCollectionStats(): array
    {
        $publishedCount = $this->collection->where('is_published', true)->count();
        $draftCount = $this->collection->where('is_published', false)->count();

        return [
            'total_pages' => $this->total(),
            'published_pages' => $publishedCount,
            'draft_pages' => $draftCount,
            'publish_ratio' => $this->total() > 0 ? round(($publishedCount / $this->total()) * 100, 2) : 0,
            'average_versions' => $this->collection->avg('version') ?? 0,
        ];
    }
}
