<?php

namespace App\CMS\Managers;

use App\Models\Theme;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ThemeManager
{
    protected array $themes = [];
    protected ?Theme $activeTheme = null;

    /**
     * Discover all themes from the themes directory.
     */
    public function discover(): void
    {
        $themePath = config('cms.theme_path', base_path('themes'));

        if (!File::exists($themePath)) {
            File::makeDirectory($themePath, 0755, true);
            return;
        }

        $directories = File::directories($themePath);

        foreach ($directories as $directory) {
            $themeJsonPath = $directory . '/theme.json';

            if (File::exists($themeJsonPath)) {
                $config = json_decode(File::get($themeJsonPath), true);

                if ($config && isset($config['slug'])) {
                    $this->themes[$config['slug']] = $config;
                    
                    // Sync to database (skip if tables don't exist)
                    try {
                        Theme::updateOrCreate(
                            ['slug' => $config['slug']],
                            [
                                'name' => $config['name'] ?? $config['slug'],
                                'version' => $config['version'] ?? '1.0.0',
                                'description' => $config['description'] ?? null,
                                'author' => $config['author'] ?? null,
                                'config' => $config,
                            ]
                        );
                    } catch (\Exception $e) {
                        // Skip database sync if tables don't exist yet
                    }
                }
            }
        }
    }

    /**
     * Get all discovered themes.
     */
    public function all(): array
    {
        return $this->themes;
    }

    /**
     * Get a theme by slug.
     */
    public function get(string $slug): ?array
    {
        return $this->themes[$slug] ?? null;
    }

    /**
     * Activate a theme.
     */
    public function activate(string $slug): bool
    {
        $theme = Theme::where('slug', $slug)->first();

        if (!$theme) {
            return false;
        }

        // Deactivate all other themes
        Theme::where('is_active', true)->update(['is_active' => false]);

        // Activate the selected theme
        $theme->update(['is_active' => true]);

        // Clear theme cache
        Cache::forget('cms.active_theme');

        $this->activeTheme = $theme;

        return true;
    }

    /**
     * Get the active theme.
     */
    public function getActive(): ?Theme
    {
        if ($this->activeTheme) {
            return $this->activeTheme;
        }

        $this->activeTheme = Cache::remember('cms.active_theme', 3600, function () {
            return Theme::active()->first();
        });

        return $this->activeTheme;
    }

    /**
     * Activate the default theme if no theme is active.
     */
    public function activateDefault(): void
    {
        if (!$this->getActive()) {
            $defaultSlug = config('cms.default_theme', 'default');
            $this->activate($defaultSlug);
        }
    }

    /**
     * Get theme path.
     */
    public function getThemePath(string $slug): string
    {
        return config('cms.theme_path', base_path('themes')) . '/' . $slug;
    }

    /**
     * Check if a theme exists.
     */
    public function exists(string $slug): bool
    {
        return isset($this->themes[$slug]);
    }
}
