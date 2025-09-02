<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Modern Dark',
                'slug' => 'modern-dark',
                'colors' => json_encode([
                    'primary' => '#1a1a1a',
                    'secondary' => '#2d2d2d',
                    'accent' => '#4f46e5',
                    'background' => '#000000',
                    'text' => '#ffffff',
                    'button_bg' => '#4f46e5',
                    'button_text' => '#ffffff'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Light Professional',
                'slug' => 'light-professional',
                'colors' => json_encode([
                    'primary' => '#2563eb',
                    'secondary' => '#3b82f6',
                    'accent' => '#f59e0b',
                    'background' => '#f8fafc',
                    'text' => '#1e293b',
                    'button_bg' => '#2563eb',
                    'button_text' => '#ffffff'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Warm Earth',
                'slug' => 'warm-earth',
                'colors' => json_encode([
                    'primary' => '#78350f',
                    'secondary' => '#92400e',
                    'accent' => '#d97706',
                    'background' => '#fef3c7',
                    'text' => '#422006',
                    'button_bg' => '#d97706',
                    'button_text' => '#ffffff'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Cool Minimal',
                'slug' => 'cool-minimal',
                'colors' => json_encode([
                    'primary' => '#374151',
                    'secondary' => '#4b5563',
                    'accent' => '#6b7280',
                    'background' => '#f9fafb',
                    'text' => '#111827',
                    'button_bg' => '#374151',
                    'button_text' => '#ffffff'
                ]),
                'is_active' => true
            ],
            [
                'name' => 'Vibrant Creative',
                'slug' => 'vibrant-creative',
                'colors' => json_encode([
                    'primary' => '#7c3aed',
                    'secondary' => '#8b5cf6',
                    'accent' => '#ec4899',
                    'background' => '#fdf4ff',
                    'text' => '#4c1d95',
                    'button_bg' => '#7c3aed',
                    'button_text' => '#ffffff'
                ]),
                'is_active' => true
            ]
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
