<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine if the user can view any pages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_pages');
    }

    /**
     * Determine if the user can view the page.
     */
    public function view(User $user, Page $page): bool
    {
        // Can view if has permission and (page is published OR user created it OR user can edit)
        if (!$user->hasPermission('view_pages')) {
            return false;
        }

        return $page->isPublished() 
            || $page->created_by === $user->id 
            || $user->hasPermission('edit_page');
    }

    /**
     * Determine if the user can create pages.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_page');
    }

    /**
     * Determine if the user can update the page.
     */
    public function update(User $user, Page $page): bool
    {
        // Can update if has edit permission OR is the creator
        return $user->hasPermission('edit_page') 
            || ($page->created_by === $user->id && $user->hasPermission('create_page'));
    }

    /**
     * Determine if the user can delete the page.
     */
    public function delete(User $user, Page $page): bool
    {
        return $user->hasPermission('delete_page');
    }

    /**
     * Determine if the user can publish the page.
     */
    public function publish(User $user, Page $page): bool
    {
        return $user->hasPermission('publish_page');
    }

    /**
     * Determine if the user can restore the page.
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->hasPermission('delete_page');
    }

    /**
     * Determine if the user can permanently delete the page.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $user->hasRole('super-admin');
    }
}
