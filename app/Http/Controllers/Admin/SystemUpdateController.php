<?php

namespace App\Http\Controllers\Admin;

use App\CMS\Services\UpdateService;
use App\Http\Controllers\Controller;
use App\Models\CmsBackup;
use App\Models\CmsUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemUpdateController extends Controller
{
    public function __construct(
        protected UpdateService $updateService
    ) {}

    /**
     * Upload and apply CMS update
     */
    public function upload(Request $request): JsonResponse
    {
        // Check permission
        if (!Auth::user()->can('manage_system')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'update_zip' => 'required|file|mimes:zip|max:102400', // 100MB max for CMS updates
            'version' => 'required|string|max:50',
            'changelog' => 'nullable|string',
        ]);

        $file = $request->file('update_zip');
        $version = $request->input('version');
        $changelog = $request->input('changelog');

        $fileUploadService = app(\App\CMS\Services\FileUploadService::class);

        // Validate ZIP file
        $errors = $fileUploadService->validateZipFile($file);
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Invalid update ZIP file',
                'errors' => $errors,
            ], 422);
        }

        try {
            // Save uploaded file temporarily
            $tempZipPath = storage_path('app/temp/cms_update_' . \Illuminate\Support\Str::random(10) . '.zip');
            $file->move(dirname($tempZipPath), basename($tempZipPath));

            // Apply update
            $result = $this->updateService->applyUpdate(
                $tempZipPath,
                $version,
                Auth::id(),
                $changelog
            );

            // Cleanup temp ZIP
            $fileUploadService->cleanupTemp($tempZipPath);

            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'update' => $result['update'],
                    'backup' => $result['backup'],
                ], 200);
            } else {
                return response()->json([
                    'message' => $result['message'],
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('CMS update upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Update upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get update history
     */
    public function history(): JsonResponse
    {
        if (!Auth::user()->can('manage_system')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $history = $this->updateService->getUpdateHistory();

        return response()->json([
            'data' => $history,
        ]);
    }

    /**
     * Rollback to a previous backup
     */
    public function rollback(string $backupId): JsonResponse
    {
        if (!Auth::user()->can('manage_system')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $success = $this->updateService->rollback($backupId);

        if ($success) {
            return response()->json([
                'message' => 'CMS rolled back successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'Rollback failed',
            ], 500);
        }
    }

    /**
     * Get available backups
     */
    public function backups(): JsonResponse
    {
        if (!Auth::user()->can('manage_system')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $backups = $this->updateService->getAvailableBackups();

        return response()->json([
            'data' => $backups,
        ]);
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(string $backupId): JsonResponse
    {
        if (!Auth::user()->can('manage_system')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $backup = CmsBackup::findOrFail($backupId);

            // Delete backup file
            if (\Illuminate\Support\Facades\File::exists($backup->backup_path)) {
                \Illuminate\Support\Facades\File::delete($backup->backup_path);
            }

            // Delete backup record
            $backup->delete();

            return response()->json([
                'message' => 'Backup deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Backup deletion failed', [
                'backup_id' => $backupId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to delete backup',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
