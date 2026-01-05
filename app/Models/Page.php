<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'status',
        'template',
        'content_schema',
        'seo_meta',
        'parent_id',
        'created_by',
    ];

    protected $casts = [
        'content_schema' => 'array',
        'seo_meta' => 'array',
    ];

    /**
     * Get the parent page.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    /**
     * Get all child pages.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    /**
     * Get the user who created this page.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all versions of this page.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }

    /**
     * Scope a query to only include published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include draft pages.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if page has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }
}
