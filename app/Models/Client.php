<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'slug',
        'website_url',
        'logo'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(Theme::class)
            ->withPivot('customizations')
            ->withTimestamps();
    }

    public function currentTheme()
    {
        return $this->themes()->latest()->first();
    }
}
