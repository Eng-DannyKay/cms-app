<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'published_content' => $this->when($request->user()?->can('view', $this->resource), $this->published_content),
            'is_published' => $this->is_published,
            'version' => $this->version,
            'client' => new ClientResource($this->whenLoaded('client')),
            'analytics' => $this->when($request->has('with_analytics'), [
                'total_views' => $this->views_count ?? 0,
                'today_views' => $this->today_views ?? 0,
            ]),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'links' => [
                'self' => route('api.pages.show', $this->id),
                'public' => $this->is_published ? route('public.page', [
                    'clientSlug' => $this->client->slug,
                    'pageSlug' => $this->slug
                ]) : null,
                'preview' => route('api.pages.preview', $this->id)
            ]
        ];
    }
}
