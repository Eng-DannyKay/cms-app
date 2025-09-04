<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Theme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'colors',
        'is_active',
        'description',
        'version',
        'author',
        'preview_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'colors' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The clients that belong to the theme.
     */
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)
            ->withPivot('customizations', 'applied_at')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active themes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by most popular.
     */
    public function scopePopular($query)
    {
        return $query->withCount('clients')->orderByDesc('clients_count');
    }

    /**
     * Scope a query to search themes.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    /**
     * Get the default theme.
     */
    public static function getDefault()
    {
        return static::active()->first();
    }

    /**
     * Check if the theme is being used by any client.
     */
    public function isInUse(): bool
    {
        return $this->clients()->exists();
    }

    /**
     * Get the number of clients using this theme.
     */
    public function getUsageCount(): int
    {
        return $this->clients()->count();
    }

    /**
     * Get the CSS variables for this theme.
     */
    public function getCssVariables(): array
    {
        $colors = $this->colors ?? [];
        $variables = [];

        foreach ($colors as $key => $value) {
            $variables["--{$key}"] = $value;
        }

        return $variables;
    }

    /**
     * Generate CSS content for this theme.
     */
    public function generateCss(?array $customizations = null): string
    {
        $colors = array_merge($this->colors ?? [], $customizations ?? []);
        
        $css = ":root {\n";
        foreach ($colors as $key => $value) {
            $css .= "  --{$key}: {$value};\n";
        }
        $css .= "}\n";

        return $css;
    }

    /**
     * Apply customizations to the theme.
     */
    public function applyCustomizations(array $customizations): array
    {
        return array_merge($this->colors ?? [], $customizations);
    }

    /**
     * Validate color values.
     */
    public static function isValidColor(string $color): bool
    {
        // Validate hex, rgb, rgba, and named colors
        return preg_match('/^#([0-9a-f]{3}){1,2}$/i', $color) ||
               preg_match('/^rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)$/i', $color) ||
               preg_match('/^rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(0|1|0\.\d+)\)$/i', $color) ||
               in_array(strtolower($color), self::getColorNames());
    }

    /**
     * Get common color names.
     */
    public static function getColorNames(): array
    {
        return [
            'black', 'white', 'red', 'green', 'blue', 'yellow', 'purple',
            'orange', 'pink', 'gray', 'grey', 'brown', 'cyan', 'magenta',
            'silver', 'gold', 'navy', 'teal', 'maroon', 'olive', 'lime',
            'aqua', 'fuchsia', 'transparent'
        ];
    }

    /**
     * Create a new theme from a color palette.
     */
    public static function createFromPalette(array $palette, string $name, string $slug): self
    {
        return static::create([
            'name' => $name,
            'slug' => $slug,
            'colors' => $palette,
            'is_active' => true,
        ]);
    }

    /**
     * Duplicate the theme with a new name.
     */
    public function duplicate(string $newName): self
    {
        return static::create([
            'name' => $newName,
            'slug' => \Illuminate\Support\Str::slug($newName),
            'colors' => $this->colors,
            'description' => $this->description . ' (copy)',
            'is_active' => $this->is_active,
            'author' => $this->author,
            'version' => $this->version,
        ]);
    }

    /**
     * Get theme preview URL.
     */
    public function getPreviewUrl(): string
    {
        return route('themes.public-preview', $this->slug);
    }

    /**
     * Get the theme's primary color.
     */
    public function getPrimaryColor(): ?string
    {
        return $this->colors['primary'] ?? null;
    }

    /**
     * Get the theme's background color.
     */
    public function getBackgroundColor(): ?string
    {
        return $this->colors['background'] ?? null;
    }

    /**
     * Get the theme's text color.
     */
    public function getTextColor(): ?string
    {
        return $this->colors['text'] ?? null;
    }

    /**
     * Check if the theme is dark mode.
     */
    public function isDarkMode(): bool
    {
        $background = $this->getBackgroundColor();
        if (!$background) return false;

        if (strpos($background, '#') === 0) {
    
            $hex = str_replace('#', '', $background);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
            return $brightness < 128;
        }

        return false;
    }
}