<?php

namespace App\CMS;

use App\CMS\Managers\ThemeManager;
use App\CMS\Managers\PluginManager;
use App\CMS\Managers\BlockManager;
use Illuminate\Support\Facades\Event;

class Kernel
{
    protected ThemeManager $themeManager;
    protected PluginManager $pluginManager;
    protected BlockManager $blockManager;
    protected bool $booted = false;

    public function __construct(
        ThemeManager $themeManager,
        PluginManager $pluginManager,
        BlockManager $blockManager
    ) {
        $this->themeManager = $themeManager;
        $this->pluginManager = $pluginManager;
        $this->blockManager = $blockManager;
    }

    /**
     * Boot the CMS kernel.
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        try {
            // Discover and activate themes
            $this->themeManager->discover();
            $this->themeManager->activateDefault();

            // Discover and load plugins
            $this->pluginManager->discover();
            $this->pluginManager->loadActive();

            // Register core blocks
            $this->blockManager->registerCoreBlocks();

            // Fire CMS booted event
            Event::dispatch('cms.booted');

            $this->booted = true;
        } catch (\Exception $e) {
            // Silently fail during migrations or when tables don't exist
            logger()->warning('CMS Kernel boot failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the theme manager.
     */
    public function themes(): ThemeManager
    {
        return $this->themeManager;
    }

    /**
     * Get the plugin manager.
     */
    public function plugins(): PluginManager
    {
        return $this->pluginManager;
    }

    /**
     * Get the block manager.
     */
    public function blocks(): BlockManager
    {
        return $this->blockManager;
    }

    /**
     * Check if CMS is booted.
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }
}
