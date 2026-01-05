<?php

namespace App\CMS\Contracts;

interface PluginInterface
{
    /**
     * Get the plugin name.
     */
    public function getName(): string;

    /**
     * Get the plugin slug.
     */
    public function getSlug(): string;

    /**
     * Get the plugin version.
     */
    public function getVersion(): string;

    /**
     * Boot the plugin.
     */
    public function boot(): void;

    /**
     * Register plugin services.
     */
    public function register(): void;
}
