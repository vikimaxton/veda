<?php

namespace App\Http\Controllers\API;

use App\CMS\Managers\ThemeManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ThemeController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected ThemeManager $themeManager
    ) {}

    /**
     * Display a listing of themes.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Theme::class);

        $themes = Theme::all();

        return response()->json([
            'data' => ThemeResource::collection($themes),
        ]);
    }

    /**
     * Display the specified theme.
     */
    public function show(string $slug): JsonResponse
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        
        $this->authorize('view', $theme);

        return response()->json([
            'data' => new ThemeResource($theme),
        ]);
    }

    /**
     * Activate a theme.
     */
    public function activate(string $slug): JsonResponse
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        
        // TODO: Re-enable after policies are created
        // $this->authorize('activate', $theme);

        // Deactivate all themes first
        Theme::query()->update(['is_active' => false]);
        
        // Activate the selected theme
        $theme->update(['is_active' => true]);

        return response()->json([
            'message' => 'Theme activated successfully',
            'data' => new ThemeResource($theme->fresh()),
        ]);
    }

    /**
     * Get theme preview information.
     */
    public function preview(string $slug): JsonResponse
    {
        $theme = Theme::where('slug', $slug)->firstOrFail();
        
        $this->authorize('view', $theme);

        $themePath = $this->themeManager->getThemePath($slug);

        return response()->json([
            'data' => new ThemeResource($theme),
            'screenshot' => file_exists($themePath . '/screenshot.png') 
                ? asset('themes/' . $slug . '/screenshot.png') 
                : null,
        ]);
    }

    /**
     * Upload a new theme ZIP file
     */
    public function upload(): JsonResponse
    {
        $this->authorize('create', Theme::class);

        request()->validate([
            'theme_zip' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $file = request()->file('theme_zip');
        $fileUploadService = app(\App\CMS\Services\FileUploadService::class);

        // Validate ZIP file
        $errors = $fileUploadService->validateZipFile($file);
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Invalid theme ZIP file',
                'errors' => $errors,
            ], 422);
        }

        try {
            // Extract to temporary directory
            $tempPath = storage_path('app/temp/theme_upload_' . \Illuminate\Support\Str::random(10));
            $zipPath = $file->getRealPath();

            if (!$fileUploadService->extractZip($zipPath, $tempPath)) {
                throw new \Exception('Failed to extract ZIP file');
            }

            // Validate theme structure
            $errors = $fileUploadService->validateThemeStructure($tempPath);
            if (!empty($errors)) {
                $fileUploadService->cleanupTemp($tempPath);
                return response()->json([
                    'message' => 'Invalid theme structure',
                    'errors' => $errors,
                ], 422);
            }

            // Read theme.json
            $themeJson = json_decode(\Illuminate\Support\Facades\File::get($tempPath . '/theme.json'), true);
            $slug = $themeJson['slug'];

            // Check if theme already exists
            if (Theme::where('slug', $slug)->exists()) {
                $fileUploadService->cleanupTemp($tempPath);
                return response()->json([
                    'message' => 'Theme already exists',
                    'errors' => ['A theme with this slug already exists. Please delete it first.'],
                ], 422);
            }

            // Move theme to themes directory
            $themePath = base_path('themes/' . $slug);
            if (\Illuminate\Support\Facades\File::exists($themePath)) {
                \Illuminate\Support\Facades\File::deleteDirectory($themePath);
            }
            \Illuminate\Support\Facades\File::moveDirectory($tempPath, $themePath);

            // Discover and register theme
            $this->themeManager->discover();

            // Get the newly created theme
            $theme = Theme::where('slug', $slug)->first();

            return response()->json([
                'message' => 'Theme uploaded successfully',
                'data' => new ThemeResource($theme),
            ], 201);

        } catch (\Exception $e) {
            // Cleanup on error
            if (isset($tempPath)) {
                $fileUploadService->cleanupTemp($tempPath);
            }

            \Illuminate\Support\Facades\Log::error('Theme upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Theme upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
