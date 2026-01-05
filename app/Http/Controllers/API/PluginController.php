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
        
        $this->authorize('uninstall', $plugin);

        if ($plugin->is_active) {
            return response()->json([
                'message' => 'Plugin must be deactivated before uninstalling',
            ], 422);
        }

        $plugin->update(['is_installed' => false]);

        return response()->json([
            'message' => 'Plugin uninstalled successfully',
        ]);
    }
}
