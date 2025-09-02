<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'slug',
        'content',
        'published_content',
        'is_published',
        'version'
    ];

    protected $casts = [
        'content' => 'array',
        'published_content' => 'array',
        'is_published' => 'boolean'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContentVersion::class);
    }
}
