<?php

namespace App\Http\Controllers\API;

use App\CMS\Managers\PluginManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\PluginResource;
use App\Models\Plugin;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PluginController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        protected PluginManager $pluginManager
    ) {}

    /**
     * Display a listing of plugins.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Plugin::class);

        $plugins = Plugin::all();

        return response()->json([
            'data' => PluginResource::collection($plugins),
        ]);
    }

    /**
     * Display the specified plugin.
     */
    public function show(string $slug): JsonResponse
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        
        $this->authorize('view', $plugin);

        return response()->json([
            'data' => new PluginResource($plugin),
        ]);
    }

    /**
     * Install a plugin.
     */
    public function install(string $slug): JsonResponse
    {
        $this->authorize('install', Plugin::class);

        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return response()->json([
                'message' => 'Plugin not found',
            ], 404);
        }

        if ($plugin->is_installed) {
            return response()->json([
                'message' => 'Plugin is already installed',
            ], 422);
        }

        $plugin->update(['is_installed' => true]);

        return response()->json([
            'message' => 'Plugin installed successfully',
            'data' => new PluginResource($plugin->fresh()),
        ]);
    }

    /**
     * Activate a plugin.
     */
    public function activate(string $slug): JsonResponse
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        
        // TODO: Re-enable after policies are created
        // $this->authorize('activate', $plugin);

        // For now, mark as active directly
        $plugin->update(['is_active' => true]);

        return response()->json([
            'message' => 'Plugin activated successfully',
            'data' => new PluginResource($plugin->fresh()),
        ]);
    }

    /**
     * Deactivate a plugin.
     */
    public function deactivate(string $slug): JsonResponse
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        
        // TODO: Re-enable after policies are created
        // $this->authorize('deactivate', $plugin);

        // For now, mark as inactive directly
        $plugin->update(['is_active' => false]);

        return response()->json([
            'message' => 'Plugin deactivated successfully',
            'data' => new PluginResource($plugin->fresh()),
        ]);
    }

    /**
     * Uninstall a plugin.
     */
    public function uninstall(string $slug): JsonResponse
    {
        $plugin = Plugin::where('slug', $slug)->firstOrFail();
        
        $this->authorize('delete', $plugin);

        // Deactivate first if active
        if ($plugin->is_active) {
            $this->pluginManager->deactivate($slug);
        }

        // Delete plugin record
        $plugin->delete();

        return response()->json([
            'message' => 'Plugin uninstalled successfully',
        ]);
    }

    /**
     * Upload a new plugin ZIP file
     */
    public function upload(): JsonResponse
    {
        $this->authorize('create', Plugin::class);

        request()->validate([
            'plugin_zip' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $file = request()->file('plugin_zip');
        $fileUploadService = app(\App\CMS\Services\FileUploadService::class);

        // Validate ZIP file
        $errors = $fileUploadService->validateZipFile($file);
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Invalid plugin ZIP file',
                'errors' => $errors,
            ], 422);
        }

        try {
            // Extract to temporary directory
            $tempPath = storage_path('app/temp/plugin_upload_' . \Illuminate\Support\Str::random(10));
            $zipPath = $file->getRealPath();

            if (!$fileUploadService->extractZip($zipPath, $tempPath)) {
                throw new \Exception('Failed to extract ZIP file');
            }

            // Validate plugin structure
            $errors = $fileUploadService->validatePluginStructure($tempPath);
            if (!empty($errors)) {
                $fileUploadService->cleanupTemp($tempPath);
                return response()->json([
                    'message' => 'Invalid plugin structure',
                    'errors' => $errors,
                ], 422);
            }

            // Read plugin.json
            $pluginJson = json_decode(\Illuminate\Support\Facades\File::get($tempPath . '/plugin.json'), true);
            $slug = $pluginJson['slug'];

            // Check if plugin already exists
            if (Plugin::where('slug', $slug)->exists()) {
                $fileUploadService->cleanupTemp($tempPath);
                return response()->json([
                    'message' => 'Plugin already exists',
                    'errors' => ['A plugin with this slug already exists. Please uninstall it first.'],
                ], 422);
            }

            // Move plugin to plugins directory
            $pluginPath = base_path('plugins/' . $slug);
            if (\Illuminate\Support\Facades\File::exists($pluginPath)) {
                \Illuminate\Support\Facades\File::deleteDirectory($pluginPath);
            }
            \Illuminate\Support\Facades\File::moveDirectory($tempPath, $pluginPath);

            // Discover and register plugin
            $this->pluginManager->discover();

            // Get the newly created plugin
            $plugin = Plugin::where('slug', $slug)->first();

            return response()->json([
                'message' => 'Plugin uploaded successfully',
                'data' => new PluginResource($plugin),
            ], 201);

        } catch (\Exception $e) {
            // Cleanup on error
            if (isset($tempPath)) {
                $fileUploadService->cleanupTemp($tempPath);
            }

            \Illuminate\Support\Facades\Log::error('Plugin upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Plugin upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
