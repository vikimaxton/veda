<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        // Cannot modify super-admin role unless you are super-admin
        if ($role->slug === 'super-admin' && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasPermission('manage_roles');
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Cannot delete system roles
        if (in_array($role->slug, ['super-admin', 'admin', 'mentor', 'student'])) {
            return false;
        }

        return $user->hasPermission('manage_roles');
    }
}
