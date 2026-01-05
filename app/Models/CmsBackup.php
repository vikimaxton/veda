<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsBackup extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'version',
        'backup_path',
        'file_size',
        'created_by',
        'created_at',
        'restored_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    /**
     * Get the user who created the backup
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Restore this backup
     */
    public function restore(): bool
    {
        $updateService = app(\App\CMS\Services\UpdateService::class);
        return $updateService->rollback($this->id);
    }
}
