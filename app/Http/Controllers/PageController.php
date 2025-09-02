<?php

namespace App\Http\Controllers;

use App\Http\Requests\Page\CreatePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Services\Page\PageService;
use App\Repositories\Page\PageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageRepository $pageRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'published']);
        $pages = $this->pageRepository->getClientPages(
            auth()->user()->client,
            $filters,
            $request->get('per_page', 15)
        );

        return PageResource::collection($pages)->response();
    }

    public function store(CreatePageRequest $request): JsonResponse
    {
        $page = $this->pageService->createPage(
            auth()->user()->client,
            $request->validated()
        );

        return (new PageResource($page))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()
            ->with(['client.themes'])
            ->findOrFail($id);

        $this->authorize('view', $page);

        return (new PageResource($page))->response();
    }

    public function update(UpdatePageRequest $request, string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()->findOrFail($id);
        $this->authorize('update', $page);

        $validated = $request->validated();
        $publish = $request->get('publish', false);

        if (isset($validated['content'])) {
            $page = $this->pageService->updatePageContent(
                $page,
                $validated['content'],
                $publish
            );
        }

        if (isset($validated['title']) || isset($validated['slug'])) {
            $page->update($request->only(['title', 'slug']));
        }

        if (isset($validated['is_published'])) {
            $page = $validated['is_published']
                ? $this->pageService->publishPage($page)
                : $this->pageService->unpublishPage($page);
        }

        return (new PageResource($page->fresh()))->response();
    }

    public function destroy(string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()->findOrFail($id);
        $this->authorize('delete', $page);

        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }

    public function publish(string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()->findOrFail($id);
        $this->authorize('publish', $page);

        $page = $this->pageService->publishPage($page);

        return (new PageResource($page))->response();
    }

    public function preview(string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()->findOrFail($id);
        $this->authorize('view', $page);

        return response()->json([
            'page' => new PageResource($page),
            'theme' => $page->client->currentTheme(),
            'preview_url' => route('api.pages.preview.render', $page->id) . '?token=' . encrypt([
                'page_id' => $page->id,
                'expires' => now()->addMinutes(30)
            ])
        ]);
    }

    public function duplicate(string $id): JsonResponse
    {
        $page = auth()->user()->client->pages()->findOrFail($id);
        $this->authorize('duplicate', $page);

        $newPage = $this->pageService->duplicatePage($page);

        return (new PageResource($newPage))
            ->response()
            ->setStatusCode(201);
    }
}
