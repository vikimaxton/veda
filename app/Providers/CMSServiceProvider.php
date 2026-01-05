<?php

namespace App\Providers;

use App\CMS\Kernel;
use App\CMS\Managers\ThemeManager;
use App\CMS\Managers\PluginManager;
use App\CMS\Managers\BlockManager;
use Illuminate\Support\ServiceProvider;

class CMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register managers as singletons
        $this->app->singleton(ThemeManager::class);
        $this->app->singleton(PluginManager::class);
        $this->app->singleton(BlockManager::class);
        
        // Register CMS Kernel
        $this->app->singleton(Kernel::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Boot CMS Kernel
        $kernel = $this->app->make(Kernel::class);
        $kernel->boot();

        // Load CMS routes
        $this->loadRoutesFrom(base_path('routes/cms.php'));

        // Merge CMS config
        $this->mergeConfigFrom(
            base_path('config/cms.php'), 'cms'
        );

        // Publish CMS config
        $this->publishes([
            base_path('config/cms.php') => config_path('cms.php'),
        ], 'cms-config');
    }
}
