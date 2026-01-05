<?php

namespace App\CMS\Contracts;

interface ThemeInterface
{
    /**
     * Get the theme name.
     */
    public function getName(): string;

    /**
     * Get the theme slug.
     */
    public function getSlug(): string;

    /**
     * Get the theme version.
     */
    public function getVersion(): string;

    /**
     * Get the theme configuration.
     */
    public function getConfig(): array;

    /**
     * Get available templates.
     */
    public function getTemplates(): array;

    /**
     * Check if theme supports a feature.
     */
    public function supports(string $feature): bool;
}
