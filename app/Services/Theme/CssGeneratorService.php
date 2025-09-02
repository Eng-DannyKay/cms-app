<?php

namespace App\Services\Theme;

use App\Models\Theme;

class CssGeneratorService
{
    public function generateCss(Theme $theme, ?array $customizations = null): string
    {
        $baseColors = json_decode($theme->colors, true);
        $colors = array_merge($baseColors, $customizations ?? []);

        $css = $this->generateCssVariables($colors);
        $css .= $this->generateUtilityClasses($colors);
        $css .= $this->generateComponentStyles($colors);

        return $this->minifyCss($css);
    }

    private function generateCssVariables(array $colors): string
    {
        $variables = [];

        foreach ($colors as $key => $value) {
            if ($this->isColorValue($value)) {
                $variables["--{$key}"] = $value;
            }
        }

        // Add spacing and typography variables
        $variables = array_merge($variables, [
            '--spacing-xs' => '0.5rem',
            '--spacing-sm' => '1rem',
            '--spacing-md' => '1.5rem',
            '--spacing-lg' => '2rem',
            '--spacing-xl' => '3rem',
            '--border-radius' => '8px',
            '--border-radius-lg' => '12px',
            '--shadow' => '0 1px 3px 0 rgba(0, 0, 0, 0.1)',
            '--shadow-md' => '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
            '--font-family' => 'Inter, system-ui, sans-serif',
        ]);

        $css = ":root {\n";
        foreach ($variables as $key => $value) {
            $css .= "  {$key}: {$value};\n";
        }
        $css .= "}\n\n";

        return $css;
    }

    private function generateUtilityClasses(array $colors): string
    {
        return "
            .bg-primary { background-color: var(--primary); }
            .bg-secondary { background-color: var(--secondary); }
            .bg-accent { background-color: var(--accent); }
            .bg-background { background-color: var(--background); }

            .text-primary { color: var(--primary); }
            .text-secondary { color: var(--secondary); }
            .text-accent { color: var(--accent); }
            .text-body { color: var(--text); }

            .border-primary { border-color: var(--primary); }
            .border-secondary { border-color: var(--secondary); }

            .btn-primary {
                background-color: var(--button-bg, var(--primary));
                color: var(--button-text, #ffffff);
                border-radius: var(--border-radius);
                padding: var(--spacing-sm) var(--spacing-md);
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-primary:hover {
                opacity: 0.9;
                transform: translateY(-1px);
            }

            .card {
                background: var(--background);
                border-radius: var(--border-radius-lg);
                padding: var(--spacing-lg);
                box-shadow: var(--shadow);
                border: 1px solid var(--border-color, #e5e7eb);
            }
        ";
    }

    private function generateComponentStyles(array $colors): string
    {
        return "
            .header {
                background: var(--primary);
                color: white;
                padding: var(--spacing-md) 0;
            }

            .footer {
                background: var(--secondary);
                color: white;
                padding: var(--spacing-lg) 0;
            }

            .navigation {
                background: var(--background);
                border-bottom: 1px solid var(--border-color, #e5e7eb);
            }

            .navigation a {
                color: var(--text);
                text-decoration: none;
                padding: var(--spacing-sm) var(--spacing-md);
                transition: color 0.2s ease;
            }

            .navigation a:hover {
                color: var(--primary);
            }

            .hero {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                color: white;
                padding: var(--spacing-xl) 0;
                text-align: center;
            }
        ";
    }

    private function minifyCss(string $css): string
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

        // Remove spaces
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
        $css = preg_replace(['/\\s*{\\s*/', '/\\s*}\\s*/', '/\\s*;\\s*/'], ['{', '}', ';'], $css);

        return trim($css);
    }

    private function isColorValue(string $value): bool
    {
        return preg_match('/^#([0-9a-f]{3}){1,2}$/i', $value) ||
               preg_match('/^rgb\\(\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*\\d+\\s*\\)$/i', $value) ||
               preg_match('/^rgba\\(\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*[0-9.]+\\)$/i', $value);
    }

    public function generatePreviewCss(Theme $theme, array $customizations): string
    {
        return $this->generateCss($theme, $customizations);
    }

    public function validateCustomizations(array $customizations): array
    {
        $validated = [];
        $allowedKeys = ['primary', 'secondary', 'accent', 'background', 'text', 'button_bg', 'button_text'];

        foreach ($customizations as $key => $value) {
            if (in_array($key, $allowedKeys) && $this->isValidColor($value)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    private function isValidColor(string $color): bool
    {
        // Validate hex, rgb, rgba, and color names
        return preg_match('/^#([0-9a-f]{3}){1,2}$/i', $color) ||
               preg_match('/^rgb\\(\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*\\d+\\s*\\)$/i', $color) ||
               preg_match('/^rgba\\(\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*\\d+\\s*,\\s*[0-9.]+\\)$/i', $color) ||
               in_array(strtolower($color), $this->getColorNames());
    }

    private function getColorNames(): array
    {
        return [
            'black', 'white', 'red', 'green', 'blue', 'yellow', 'purple',
            'orange', 'pink', 'gray', 'grey', 'brown', 'cyan', 'magenta'
        ];
    }
}
