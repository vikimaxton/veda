<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageVersion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cms_page_versions';

    protected $fillable = [
        'page_id',
        'content_snapshot',
        'created_by',
    ];

    protected $casts = [
        'content_snapshot' => 'array',
    ];

    /**
     * Get the page this version belongs to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the user who created this version.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
