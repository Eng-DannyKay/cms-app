<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Services\Theme\ThemeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ThemeController extends Controller
{
    public function __construct(private ThemeService $themeService) {}

    public function index(): JsonResponse
    {
        $themes = $this->themeService->getAvailableThemes();

        return response()->json([
            'data' => $themes,
            'meta' => [
                'total' => count($themes),
                'current_client_theme' => $this->themeService->getClientTheme(auth()->user()->client),
            ]
        ]);
    }

    public function getCurrentTheme(): JsonResponse
    {
        $themeData = $this->themeService->getClientTheme(auth()->user()->client);

        if (!$themeData) {
            return response()->json([
                'message' => 'No theme applied yet',
                'default_theme' => $this->themeService->generateDefaultCss()
            ], 404);
        }

        return response()->json([
            'data' => $themeData,
            'css' => $this->themeService->generateThemeCss(
                $themeData['theme'],
                $themeData['customizations']
            )
        ]);
    }

    public function applyTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme_id' => ['required', 'exists:themes,id'],
            'customizations' => ['sometimes', 'array'],
            'customizations.*' => ['string', 'regex:/^(#([0-9a-f]{3}){1,2}|rgb\(\d+,\s*\d+,\s*\d+\)|rgba\(\d+,\s*\d+,\s*\d+,\s*[0-9.]+\))$/i'],
        ]);

        $theme = Theme::findOrFail($validated['theme_id']);

        $this->themeService->applyThemeToClient(
            auth()->user()->client,
            $theme,
            $validated['customizations'] ?? null
        );

        return response()->json([
            'message' => 'Theme applied successfully',
            'theme' => $this->themeService->getClientTheme(auth()->user()->client),
            'css' => $this->themeService->getClientCss(auth()->user()->client)
        ]);
    }

    public function customizeTheme(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customizations' => ['required', 'array'],
            'customizations.*' => ['string', 'regex:/^(#([0-9a-f]{3}){1,2}|rgb\(\d+,\s*\d+,\s*\d+\)|rgba\(\d+,\s*\d+,\s*\d+,\s*[0-9.]+\))$/i'],
        ]);

        $this->themeService->customizeTheme(
            auth()->user()->client,
            $validated['customizations']
        );

        return response()->json([
            'message' => 'Theme customized successfully',
            'css' => $this->themeService->getClientCss(auth()->user()->client)
        ]);
    }

    public function previewTheme(Theme $theme, Request $request): JsonResponse
    {
        $customizations = $request->validate([
            'customizations' => ['sometimes', 'array'],
            'customizations.*' => ['string', 'regex:/^(#([0-9a-f]{3}){1,2}|rgb\(\d+,\s*\d+,\s*\d+\)|rgba\(\d+,\s*\d+,\s*\d+,\s*[0-9.]+\))$/i'],
        ])['customizations'] ?? [];

        $css = $this->themeService->generatePreviewCss($theme, $customizations);

        return response()->json([
            'theme' => $theme->only(['id', 'name', 'slug', 'colors']),
            'customizations' => $customizations,
            'css' => $css,
            'preview_html' => $this->generatePreviewHtml($css)
        ]);
    }

    public function publicPreview(Theme $theme, Request $request): JsonResponse
    {
        $customizations = $request->validate([
            'customizations' => ['sometimes', 'array'],
            'customizations.*' => ['string', 'regex:/^(#([0-9a-f]{3}){1,2}|rgb\(\d+,\s*\d+,\s*\d+\)|rgba\(\d+,\s*\d+,\s*\d+,\s*[0-9.]+\))$/i'],
        ])['customizations'] ?? [];

        $css = $this->themeService->generatePreviewCss($theme, $customizations);

        return response()->json([
            'theme' => $theme->only(['id', 'name', 'slug']),
            'css' => $css,
            'html' => $this->generatePreviewHtml($css)
        ]);
    }

    public function resetCustomizations(): JsonResponse
    {
        $this->themeService->resetThemeCustomizations(auth()->user()->client);

        return response()->json([
            'message' => 'Theme customizations reset successfully',
            'css' => $this->themeService->getClientCss(auth()->user()->client)
        ]);
    }

    public function getCss(): JsonResponse
    {
        $css = $this->themeService->getClientCss(auth()->user()->client);

        return response()->json([
            'css' => $css,
            'length' => strlen($css),
            'minified' => true
        ]);
    }

    private function generatePreviewHtml(string $css): string
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>{$css}</style>
            </head>
            <body>
                <div class='hero'>
                    <h1>Welcome to Your Website</h1>
                    <p>This is a preview of your theme</p>
                    <button class='btn-primary'>Get Started</button>
                </div>

                <div class='card' style='margin: 20px;'>
                    <h2>Content Section</h2>
                    <p>This is how your content will look with this theme.</p>
                    <div style='display: flex; gap: 10px; margin-top: 20px;'>
                        <button class='btn-primary'>Primary Button</button>
                        <button style='background: var(--secondary); color: white; padding: 10px 20px; border: none; border-radius: var(--border-radius);'>Secondary</button>
                    </div>
                </div>

                <div style='background: var(--background); padding: 20px; margin: 20px; border-radius: var(--border-radius);'>
                    <h3 style='color: var(--text);'>Color Preview</h3>
                    <div style='display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 15px;'>
                        <div style='background: var(--primary); height: 50px; border-radius: 4px;'></div>
                        <div style='background: var(--secondary); height: 50px; border-radius: 4px;'></div>
                        <div style='background: var(--accent); height: 50px; border-radius: 4px;'></div>
                        <div style='background: var(--background); height: 50px; border-radius: 4px; border: 1px solid var(--text);'></div>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}
