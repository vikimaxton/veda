<?php

namespace App\CMS\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Exception;

class FileUploadService
{
    /**
     * Validate uploaded ZIP file
     */
    public function validateZipFile(UploadedFile $file): array
    {
        $errors = [];

        // Check file extension
        if ($file->getClientOriginalExtension() !== 'zip') {
            $errors[] = 'File must be a ZIP archive';
        }

        // Check MIME type
        $allowedMimes = ['application/zip', 'application/x-zip-compressed', 'application/x-zip'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Invalid file type. Only ZIP files are allowed';
        }

        // Check file size (50MB max)
        $maxSize = config('cms.uploads.max_size', 52428800);
        if ($file->getSize() > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of ' . $this->formatBytes($maxSize);
        }

        // Check if file is actually a valid ZIP
        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            $errors[] = 'File is not a valid ZIP archive';
        } else {
            $zip->close();
        }

        return $errors;
    }

    /**
     * Extract ZIP file to destination
     */
    public function extractZip(string $zipPath, string $destination): bool
    {
        try {
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath) !== true) {
                throw new Exception('Failed to open ZIP file');
            }

            // Create destination directory if it doesn't exist
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            // Extract
            $result = $zip->extractTo($destination);
            $zip->close();

            if (!$result) {
                throw new Exception('Failed to extract ZIP file');
            }

            return true;
        } catch (Exception $e) {
            Log::error('ZIP extraction failed', [
                'zip_path' => $zipPath,
                'destination' => $destination,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate plugin structure
     */
    public function validatePluginStructure(string $path): array
    {
        $errors = [];

        // Check for plugin.json
        $pluginJsonPath = $path . '/plugin.json';
        if (!File::exists($pluginJsonPath)) {
            $errors[] = 'plugin.json not found in ZIP root';
            return $errors;
        }

        // Validate plugin.json content
        try {
            $json = json_decode(File::get($pluginJsonPath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'plugin.json is not valid JSON';
                return $errors;
            }

            // Check required fields
            $requiredFields = ['name', 'slug', 'version', 'author'];
            foreach ($requiredFields as $field) {
                if (!isset($json[$field]) || empty($json[$field])) {
                    $errors[] = "plugin.json missing required field: {$field}";
                }
            }

            // Validate slug format (lowercase, alphanumeric, hyphens only)
            if (isset($json['slug']) && !preg_match('/^[a-z0-9-]+$/', $json['slug'])) {
                $errors[] = 'Plugin slug must contain only lowercase letters, numbers, and hyphens';
            }

        } catch (Exception $e) {
            $errors[] = 'Failed to read plugin.json: ' . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Validate theme structure
     */
    public function validateThemeStructure(string $path): array
    {
        $errors = [];

        // Check for theme.json
        $themeJsonPath = $path . '/theme.json';
        if (!File::exists($themeJsonPath)) {
            $errors[] = 'theme.json not found in ZIP root';
            return $errors;
        }

        // Validate theme.json content
        try {
            $json = json_decode(File::get($themeJsonPath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'theme.json is not valid JSON';
                return $errors;
            }

            // Check required fields
            $requiredFields = ['name', 'slug', 'version', 'author'];
            foreach ($requiredFields as $field) {
                if (!isset($json[$field]) || empty($json[$field])) {
                    $errors[] = "theme.json missing required field: {$field}";
                }
            }

            // Validate slug format
            if (isset($json['slug']) && !preg_match('/^[a-z0-9-]+$/', $json['slug'])) {
                $errors[] = 'Theme slug must contain only lowercase letters, numbers, and hyphens';
            }

        } catch (Exception $e) {
            $errors[] = 'Failed to read theme.json: ' . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Validate CMS update structure
     */
    public function validateCmsStructure(string $path): array
    {
        $errors = [];

        // Check for essential CMS directories
        $requiredDirs = ['app', 'config', 'database', 'public', 'resources', 'routes'];
        foreach ($requiredDirs as $dir) {
            if (!File::isDirectory($path . '/' . $dir)) {
                $errors[] = "Missing required directory: {$dir}";
            }
        }

        // Check for composer.json
        if (!File::exists($path . '/composer.json')) {
            $errors[] = 'composer.json not found - this does not appear to be a valid CMS package';
        }

        return $errors;
    }

    /**
     * Clean up temporary files
     */
    public function cleanupTemp(string $path): bool
    {
        try {
            if (File::exists($path)) {
                if (File::isDirectory($path)) {
                    File::deleteDirectory($path);
                } else {
                    File::delete($path);
                }
            }
            return true;
        } catch (Exception $e) {
            Log::error('Failed to cleanup temp files', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
