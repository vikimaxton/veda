<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cms_themes';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'author',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active themes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the theme's templates.
     */
    public function getTemplates(): array
    {
        return $this->config['templates'] ?? [];
    }

    /**
     * Get the theme's settings.
     */
    public function getSettings(): array
    {
        return $this->config['settings'] ?? [];
    }

    /**
     * Check if theme supports a specific feature.
     */
    public function supports(string $feature): bool
    {
        $templates = $this->getTemplates();
        foreach ($templates as $template) {
            if (in_array($feature, $template['supports'] ?? [])) {
                return true;
            }
        }
        return false;
    }
}
