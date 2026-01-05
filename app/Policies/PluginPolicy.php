<?php

namespace App\Policies;

use App\Models\Plugin;
use App\Models\User;

class PluginPolicy
{
    /**
     * Determine if the user can view any plugins.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_plugins');
    }

    /**
     * Determine if the user can view the plugin.
     */
    public function view(User $user, Plugin $plugin): bool
    {
        return $user->hasPermission('view_plugins');
    }

    /**
     * Determine if the user can install plugins.
     */
    public function install(User $user): bool
    {
        return $user->hasPermission('install_plugin');
    }

    /**
     * Determine if the user can activate a plugin.
     */
    public function activate(User $user, Plugin $plugin): bool
    {
        return $user->hasPermission('activate_plugin');
    }

    /**
     * Determine if the user can deactivate a plugin.
     */
    public function deactivate(User $user, Plugin $plugin): bool
    {
        return $user->hasPermission('activate_plugin');
    }

    /**
     * Determine if the user can uninstall a plugin.
     */
    public function uninstall(User $user, Plugin $plugin): bool
    {
        return $user->hasPermission('manage_plugins');
    }

    /**
     * Determine if the user can manage plugins.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermission('manage_plugins');
    }
}
