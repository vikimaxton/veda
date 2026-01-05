<?php

namespace App\CMS\Services;

use App\Models\CmsBackup;
use App\Models\CmsUpdate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;
use Exception;

class UpdateService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Create backup of current CMS
     */
    public function createBackup(string $version, $userId): ?CmsBackup
    {
        try {
            $backupPath = config('cms.updates.backup_path', storage_path('app/backups'));
            
            // Create backup directory if it doesn't exist
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupFileName = "cms_backup_{$version}_{$timestamp}.zip";
            $backupFilePath = $backupPath . '/' . $backupFileName;

            // Create ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($backupFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception('Failed to create backup ZIP file');
            }

            // Add essential directories to backup
            $dirsToBackup = ['app', 'config', 'database', 'public', 'resources', 'routes'];
            $basePath = base_path();

            foreach ($dirsToBackup as $dir) {
                $dirPath = $basePath . '/' . $dir;
                if (File::exists($dirPath)) {
                    $this->addDirectoryToZip($zip, $dirPath, $dir);
                }
            }

            // Add essential files
            $filesToBackup = ['composer.json', 'composer.lock', 'package.json', 'artisan'];
            foreach ($filesToBackup as $file) {
                $filePath = $basePath . '/' . $file;
                if (File::exists($filePath)) {
                    $zip->addFile($filePath, $file);
                }
            }

            $zip->close();

            // Get file size
            $fileSize = File::size($backupFilePath);

            // Save backup record
            $backup = CmsBackup::create([
                'version' => $version,
                'backup_path' => $backupFilePath,
                'file_size' => $fileSize,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            Log::info('CMS backup created', [
                'version' => $version,
                'backup_id' => $backup->id,
                'file_size' => $fileSize,
            ]);

            return $backup;

        } catch (Exception $e) {
            Log::error('Failed to create CMS backup', [
                'version' => $version,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Apply CMS update from ZIP file
     */
    public function applyUpdate(string $zipPath, string $version, $userId, ?string $changelog = null): array
    {
        DB::beginTransaction();

        try {
            $currentVersion = config('cms.version', '1.0.0');

            // Create update record
            $update = CmsUpdate::create([
                'version' => $version,
                'previous_version' => $currentVersion,
                'update_type' => 'manual',
                'status' => 'pending',
                'changelog' => $changelog,
                'updated_by' => $userId,
            ]);

            // Create backup first
            $backup = $this->createBackup($currentVersion, $userId);
            if (!$backup) {
                throw new Exception('Failed to create backup before update');
            }

            $update->backup_path = $backup->backup_path;
            $update->save();

            // Extract update to temporary directory
            $tempPath = storage_path('app/temp/cms_update_' . Str::random(10));
            
            if (!$this->fileUploadService->extractZip($zipPath, $tempPath)) {
                throw new Exception('Failed to extract update ZIP file');
            }

            // Validate CMS structure
            $errors = $this->fileUploadService->validateCmsStructure($tempPath);
            if (!empty($errors)) {
                throw new Exception('Invalid CMS update structure: ' . implode(', ', $errors));
            }

            // Apply update by copying files
            $this->copyUpdateFiles($tempPath, base_path());

            // Update version in config
            $this->updateVersionConfig($version);

            // Mark update as completed
            $update->status = 'completed';
            $update->completed_at = now();
            $update->save();

            // Cleanup temp files
            $this->fileUploadService->cleanupTemp($tempPath);

            DB::commit();

            Log::info('CMS update applied successfully', [
                'version' => $version,
                'previous_version' => $currentVersion,
                'update_id' => $update->id,
            ]);

            return [
                'success' => true,
                'message' => "CMS updated successfully to version {$version}",
                'update' => $update,
                'backup' => $backup,
            ];

        } catch (Exception $e) {
            DB::rollBack();

            if (isset($update)) {
                $update->status = 'failed';
                $update->save();
            }

            // Cleanup temp files
            if (isset($tempPath)) {
                $this->fileUploadService->cleanupTemp($tempPath);
            }

            Log::error('CMS update failed', [
                'version' => $version,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Rollback to a previous backup
     */
    public function rollback(string $backupId): bool
    {
        try {
            $backup = CmsBackup::findOrFail($backupId);

            if (!File::exists($backup->backup_path)) {
                throw new Exception('Backup file not found');
            }

            // Extract backup to temporary directory
            $tempPath = storage_path('app/temp/cms_rollback_' . Str::random(10));
            
            if (!$this->fileUploadService->extractZip($backup->backup_path, $tempPath)) {
                throw new Exception('Failed to extract backup ZIP file');
            }

            // Restore files
            $this->copyUpdateFiles($tempPath, base_path());

            // Update version in config
            $this->updateVersionConfig($backup->version);

            // Mark backup as restored
            $backup->restored_at = now();
            $backup->save();

            // Mark latest update as rolled back
            $latestUpdate = CmsUpdate::latest()->first();
            if ($latestUpdate) {
                $latestUpdate->status = 'rolled_back';
                $latestUpdate->save();
            }

            // Cleanup temp files
            $this->fileUploadService->cleanupTemp($tempPath);

            Log::info('CMS rolled back successfully', [
                'backup_id' => $backupId,
                'version' => $backup->version,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('CMS rollback failed', [
                'backup_id' => $backupId,
                'error' => $e->getMessage(),
            ]);

            // Cleanup temp files
            if (isset($tempPath)) {
                $this->fileUploadService->cleanupTemp($tempPath);
            }

            return false;
        }
    }

    /**
     * Get update history
     */
    public function getUpdateHistory(int $limit = 20): array
    {
        return CmsUpdate::with('updatedBy')
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get available backups
     */
    public function getAvailableBackups(): array
    {
        return CmsBackup::with('createdBy')
            ->latest('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Add directory to ZIP recursively
     */
    private function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipPath): void
    {
        $files = File::allFiles($dirPath);

        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }
    }

    /**
     * Copy update files to destination
     */
    private function copyUpdateFiles(string $source, string $destination): void
    {
        $dirsToUpdate = ['app', 'config', 'database', 'public', 'resources', 'routes'];

        foreach ($dirsToUpdate as $dir) {
            $sourcePath = $source . '/' . $dir;
            $destPath = $destination . '/' . $dir;

            if (File::exists($sourcePath)) {
                // Remove existing directory
                if (File::exists($destPath)) {
                    File::deleteDirectory($destPath);
                }
                // Copy new directory
                File::copyDirectory($sourcePath, $destPath);
            }
        }

        // Copy essential files
        $filesToUpdate = ['composer.json', 'composer.lock', 'package.json'];
        foreach ($filesToUpdate as $file) {
            $sourceFile = $source . '/' . $file;
            $destFile = $destination . '/' . $file;

            if (File::exists($sourceFile)) {
                File::copy($sourceFile, $destFile);
            }
        }
    }

    /**
     * Update version in config file
     */
    private function updateVersionConfig(string $version): void
    {
        $configPath = config_path('cms.php');
        
        if (File::exists($configPath)) {
            $content = File::get($configPath);
            $content = preg_replace(
                "/'version'\s*=>\s*'[^']*'/",
                "'version' => '{$version}'",
                $content
            );
            File::put($configPath, $content);
        }
    }
}
