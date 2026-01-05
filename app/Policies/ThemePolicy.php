<?php

namespace App\Policies;

use App\Models\Theme;
use App\Models\User;

class ThemePolicy
{
    /**
     * Determine if the user can view any themes.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_themes');
    }

    /**
     * Determine if the user can view the theme.
     */
    public function view(User $user, Theme $theme): bool
    {
        return $user->hasPermission('view_themes');
    }

    /**
     * Determine if the user can activate a theme.
     */
    public function activate(User $user, Theme $theme): bool
    {
        return $user->hasPermission('activate_theme');
    }

    /**
     * Determine if the user can manage themes.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_themes');
    }
}
