<?php

namespace App\CMS\Managers;

use App\Models\Plugin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Event;
use Exception;

class PluginManager
{
    protected array $plugins = [];
    protected array $loadedPlugins = [];

    /**
     * Discover all plugins from the plugins directory.
     */
    public function discover(): void
    {
        $pluginPath = config('cms.plugin_path', base_path('plugins'));

        if (!File::exists($pluginPath)) {
            File::makeDirectory($pluginPath, 0755, true);
            return;
        }

        $directories = File::directories($pluginPath);

        foreach ($directories as $directory) {
            $pluginJsonPath = $directory . '/plugin.json';

            if (File::exists($pluginJsonPath)) {
                $config = json_decode(File::get($pluginJsonPath), true);

                if ($config && isset($config['slug'])) {
                    $this->plugins[$config['slug']] = $config;
                    
                    // Sync to database (skip if tables don't exist)
                    try {
                        Plugin::updateOrCreate(
                            ['slug' => $config['slug']],
                            [
                                'name' => $config['name'] ?? $config['slug'],
                                'version' => $config['version'] ?? '1.0.0',
                                'description' => $config['description'] ?? null,
                                'author' => $config['author'] ?? null,
                                'config' => $config,
                                'is_installed' => true,
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
     * Load all active plugins.
     */
    public function loadActive(): void
    {
        $activePlugins = Plugin::active()->get();

        foreach ($activePlugins as $plugin) {
            $this->load($plugin->slug);
        }
    }

    /**
     * Load a specific plugin.
     */
    public function load(string $slug): bool
    {
        if (isset($this->loadedPlugins[$slug])) {
            return true;
        }

        try {
            $pluginPath = $this->getPluginPath($slug);
            $serviceProviderPath = $pluginPath . '/' . $this->getServiceProviderName($slug);

            if (File::exists($serviceProviderPath)) {
                require_once $serviceProviderPath;
                
                $className = $this->getServiceProviderClass($slug);
                
                if (class_exists($className)) {
                    $provider = app()->register($className);
                    $this->loadedPlugins[$slug] = $provider;
                    
                    // Register plugin hooks
                    $this->registerHooks($slug);
                    
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            // Log error but don't crash the system
            logger()->error("Failed to load plugin {$slug}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register plugin hooks.
     */
    protected function registerHooks(string $slug): void
    {
        $plugin = Plugin::where('slug', $slug)->first();
        
        if (!$plugin) {
            return;
        }

        $hooks = $plugin->getHooks();

        foreach ($hooks as $event => $handler) {
            try {
                Event::listen($event, $handler);
            } catch (Exception $e) {
                logger()->error("Failed to register hook {$event} for plugin {$slug}: " . $e->getMessage());
            }
        }
    }

    /**
     * Activate a plugin.
     */
    public function activate(string $slug): bool
    {
        $plugin = Plugin::where('slug', $slug)->where('is_installed', true)->first();

        if (!$plugin) {
            return false;
        }

        $plugin->update(['is_active' => true]);

        // Load the plugin immediately
        return $this->load($slug);
    }

    /**
     * Deactivate a plugin.
     */
    public function deactivate(string $slug): bool
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return false;
        }

        $plugin->update(['is_active' => false]);

        // Remove from loaded plugins
        unset($this->loadedPlugins[$slug]);

        return true;
    }

    /**
     * Get plugin path.
     */
    public function getPluginPath(string $slug): string
    {
        return config('cms.plugin_path', base_path('plugins')) . '/' . $slug;
    }

    /**
     * Get service provider file name.
     */
    protected function getServiceProviderName(string $slug): string
    {
        $studly = str_replace('-', '', ucwords($slug, '-'));
        return $studly . 'ServiceProvider.php';
    }

    /**
     * Get service provider class name.
     */
    protected function getServiceProviderClass(string $slug): string
    {
        $studly = str_replace('-', '', ucwords($slug, '-'));
        return "Plugins\\{$studly}\\{$studly}ServiceProvider";
    }

    /**
     * Get all discovered plugins.
     */
    public function all(): array
    {
        return $this->plugins;
    }

    /**
     * Check if a plugin is loaded.
     */
    public function isLoaded(string $slug): bool
    {
        return isset($this->loadedPlugins[$slug]);
    }
}
