<?php

namespace App\Services\Theme;

use App\Models\Theme;
use App\Models\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ThemeService
{
    private const CACHE_TTL = 3600;

    public function getAvailableThemes()
    {
        return Cache::remember('available_themes', self::CACHE_TTL, function() {
            return Theme::where('is_active', true)
                ->with('clients')
                ->get()
                ->map(function ($theme) {
                    return $this->formatThemeData($theme);
                });
        });
    }

    public function applyThemeToClient(Client $client, Theme $theme, ?array $customizations = null): void
    {
        $client->themes()->sync([$theme->id => [
            'customizations' => $customizations ? json_encode($customizations) : null,
            'applied_at' => now(),
        ]]);

        Cache::forget("client_theme:{$client->id}");
        Cache::forget("client_css:{$client->id}");
    }

    public function getClientTheme(Client $client): ?array
    {
        return Cache::remember("client_theme:{$client->id}", self::CACHE_TTL, function() use ($client) {
            $theme = $client->currentTheme();

            if (!$theme) {
                return null;
            }

            return [
                'theme' => $this->formatThemeData($theme),
                'customizations' => $theme->pivot->customizations
                    ? json_decode($theme->pivot->customizations, true)
                    : [],
                'applied_at' => $theme->pivot->applied_at,
            ];
        });
    }

    public function customizeTheme(Client $client, array $customizations): void
    {
        $currentTheme = $client->currentTheme();

        if ($currentTheme) {
            $client->themes()->updateExistingPivot($currentTheme->id, [
                'customizations' => json_encode($customizations),
                'updated_at' => now(),
            ]);
        }

        Cache::forget("client_theme:{$client->id}");
        Cache::forget("client_css:{$client->id}");
    }

    public function generateThemeCss(Theme $theme, ?array $customizations = null): string
    {
        $cssGenerator = app(CssGeneratorService::class);
        return $cssGenerator->generateCss($theme, $customizations);
    }

    public function getClientCss(Client $client): string
    {
        return Cache::remember("client_css:{$client->id}", self::CACHE_TTL, function() use ($client) {
            $themeData = $this->getClientTheme($client);

            if (!$themeData) {
                return $this->generateDefaultCss();
            }

            return $this->generateThemeCss(
                $themeData['theme'],
                $themeData['customizations']
            );
        });
    }

    private function formatThemeData(Theme $theme): array
    {
        return [
            'id' => $theme->id,
            'name' => $theme->name,
            'slug' => $theme->slug,
            'colors' => json_decode($theme->colors, true),
            'is_active' => $theme->is_active,
            'created_at' => $theme->created_at,
            'updated_at' => $theme->updated_at,
            'usage_count' => $theme->clients->count(),
        ];
    }

    private function generateDefaultCss(): string
    {
        return ':root {
            --primary: #2563eb;
            --secondary: #3b82f6;
            --accent: #f59e0b;
            --background: #f8fafc;
            --text: #1e293b;
            --button-bg: #2563eb;
            --button-text: #ffffff;
            --border-radius: 8px;
            --font-family: "Inter", sans-serif;
        }';
    }

    public function resetThemeCustomizations(Client $client): void
    {
        $currentTheme = $client->currentTheme();

        if ($currentTheme) {
            $client->themes()->updateExistingPivot($currentTheme->id, [
                'customizations' => null,
                'updated_at' => now(),
            ]);
        }

        Cache::forget("client_theme:{$client->id}");
        Cache::forget("client_css:{$client->id}");
    }

    public function getThemeUsageStatistics(): array
    {
        return Cache::remember('theme_usage_stats', self::CACHE_TTL, function() {
            return Theme::withCount('clients')
                ->orderBy('clients_count', 'desc')
                ->get()
                ->map(function ($theme) {
                    return [
                        'theme' => $theme->name,
                        'usage_count' => $theme->clients_count,
                        'popularity' => round(($theme->clients_count / max(Theme::count(), 1)) * 100, 2),
                    ];
                })
                ->toArray();
        });
    }
}
