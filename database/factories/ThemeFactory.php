<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    private const COLOR_WHITE = '#ffffff';

    public function definition(): array
    {
        $themes = [
            'Modern Dark' => [
                'primary' => '#1a1a1a',
                'secondary' => '#2d2d2d',
                'text' => self::COLOR_WHITE,
                'button_bg' => '#4f46e5',
                'button_text' => self::COLOR_WHITE,
                'button_bg' => '#4f46e5',
                'button_text' => self::COLOR_WHITE,
            ],
            'Light Professional' => [
                'primary' => '#2563eb',
                'secondary' => '#3b82f6',
                'accent' => '#f59e0b',
                'background' => '#f8fafc',
                'text' => '#1e293b',
                'button_bg' => '#2563eb',
                'button_text' => '#ffffff',
            ],
            'Warm Earth' => [
                'primary' => '#78350f',
                'secondary' => '#92400e',
                'accent' => '#d97706',
                'background' => '#fef3c7',
                'text' => '#422006',
                'button_bg' => '#d97706',
                'button_text' => '#ffffff',
            ],
        ];

        $themeName = $this->faker->randomElement(array_keys($themes));
        $colors = $themes[$themeName];

        return [
            'name' => $themeName,
            'slug' => \Illuminate\Support\Str::slug($themeName),
            'colors' => $colors,
            'description' => $this->faker->sentence(),
            'version' => '1.0.0',
            'author' => $this->faker->name(),
            'preview_image' => $this->faker->optional()->imageUrl(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withCustomColors(array $colors): static
    {
        return $this->state(fn (array $attributes) => [
            'colors' => array_merge($attributes['colors'], $colors),
        ]);
    }
}