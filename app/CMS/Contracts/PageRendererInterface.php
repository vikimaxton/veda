<?php

namespace App\CMS\Contracts;

use App\Models\Page;

interface PageRendererInterface
{
    /**
     * Render a page with the active theme.
     */
    public function render(Page $page): string;

    /**
     * Get the active theme.
     */
    public function getActiveTheme(): ?ThemeInterface;
}
