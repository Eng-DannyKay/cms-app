<?php

namespace App\Services\Page;

use App\Models\Page;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageService
{
    public function createPage(Client $client, array $data): Page
    {
        return DB::transaction(function () use ($client, $data) {
            $page = $client->pages()->create([
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($client, $data['slug'] ?? Str::slug($data['title'])),
                'content' => $this->processContent($data['content'] ?? [], $client),
                'is_published' => $data['is_published'] ?? false,
                'version' => 1
            ]);

            if ($page->is_published) {
                $page->update(['published_content' => $page->content]);
            }

            return $page->load('client');
        });
    }

    public function updatePageContent(Page $page, array $content, bool $publish = false): Page
    {
        return DB::transaction(function () use ($page, $content, $publish) {
            $processedContent = $this->processContent($content, $page->client);

            $updateData = [
                'content' => $processedContent,
                'version' => $page->version + 1
            ];

            if ($publish) {
                $updateData['published_content'] = $processedContent;
                $updateData['is_published'] = true;
            }

            $page->update($updateData);

            return $page->fresh();
        });
    }

    public function publishPage(Page $page): Page
    {
        return DB::transaction(function () use ($page) {
            $page->update([
                'published_content' => $page->content,
                'is_published' => true
            ]);

            return $page->fresh();
        });
    }

    public function unpublishPage(Page $page): Page
    {
        $page->update(['is_published' => false]);
        return $page->fresh();
    }

    public function duplicatePage(Page $page): Page
    {
        return DB::transaction(function () use ($page) {
            $newPage = $page->replicate();
            $newPage->slug = $this->generateUniqueSlug($page->client, $page->slug . '-copy');
            $newPage->is_published = false;
            $newPage->save();

            return $newPage->load('client');
        });
    }

    private function generateUniqueSlug(Client $client, string $slug): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($client->pages()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function processContent(array $content, Client $client): array
    {
        return $this->processImagesInContent($content, $client);
    }

    private function processImagesInContent(array $content, Client $client): array
    {
        array_walk_recursive($content, function (&$value, $key) use ($client) {
            if ($key === 'image' && $this->isBase64Image($value)) {
                $value = $this->storeBase64Image($value, $client);
            }
        });

        return $content;
    }

    private function isBase64Image(string $data): bool
    {
        if (strpos($data, ';base64,') === false) {
            return false;
        }

        $parts = explode(';base64,', $data);
        $imageData = base64_decode($parts[1], true);

        return $imageData !== false && $this->isValidImageType($parts[0]);
    }

    private function isValidImageType(string $mimeType): bool
    {
        $validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        return in_array(str_replace('data:', '', $mimeType), $validTypes);
    }

    private function storeBase64Image(string $base64Data, Client $client): string
    {
        $parts = explode(';base64,', $base64Data);
        $mimeType = str_replace('data:', '', $parts[0]);
        $imageData = base64_decode($parts[1]);
        $extension = $this->getExtensionFromMime($mimeType);
        $filename = 'content/' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->put("clients/{$client->id}/{$filename}", $imageData);

        return Storage::url("clients/{$client->id}/{$filename}");
    }

    private function getExtensionFromMime(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            default => 'bin'
        };
    }
}
