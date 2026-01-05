<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cms_plugins';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'description',
        'author',
        'config',
        'is_active',
        'is_installed',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_installed' => 'boolean',
    ];

    /**
     * Scope a query to only include active plugins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_installed', true);
    }

    /**
     * Scope a query to only include installed plugins.
     */
    public function scopeInstalled($query)
    {
        return $query->where('is_installed', true);
    }

    /**
     * Get the plugin's permissions.
     */
    public function getPermissions(): array
    {
        return $this->config['permissions'] ?? [];
    }

    /**
     * Get the plugin's hooks.
     */
    public function getHooks(): array
    {
        return $this->config['hooks'] ?? [];
    }

    /**
     * Get the plugin's blocks.
     */
    public function getBlocks(): array
    {
        return $this->config['blocks'] ?? [];
    }

    /**
     * Check if plugin requires a specific permission.
     */
    public function requiresPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }
}
