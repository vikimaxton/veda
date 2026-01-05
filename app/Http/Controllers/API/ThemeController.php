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
}
