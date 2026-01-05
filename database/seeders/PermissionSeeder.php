<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Page permissions
            ['name' => 'View Pages', 'slug' => 'view_pages', 'category' => 'pages'],
            ['name' => 'Create Page', 'slug' => 'create_page', 'category' => 'pages'],
            ['name' => 'Edit Page', 'slug' => 'edit_page', 'category' => 'pages'],
            ['name' => 'Delete Page', 'slug' => 'delete_page', 'category' => 'pages'],
            ['name' => 'Publish Page', 'slug' => 'publish_page', 'category' => 'pages'],
            
            // Theme permissions
            ['name' => 'View Themes', 'slug' => 'view_themes', 'category' => 'themes'],
            ['name' => 'Manage Themes', 'slug' => 'manage_themes', 'category' => 'themes'],
            ['name' => 'Activate Theme', 'slug' => 'activate_theme', 'category' => 'themes'],
            
            // Plugin permissions
            ['name' => 'View Plugins', 'slug' => 'view_plugins', 'category' => 'plugins'],
            ['name' => 'Manage Plugins', 'slug' => 'manage_plugins', 'category' => 'plugins'],
            ['name' => 'Install Plugin', 'slug' => 'install_plugin', 'category' => 'plugins'],
            ['name' => 'Activate Plugin', 'slug' => 'activate_plugin', 'category' => 'plugins'],
            
            // User & Role permissions
            ['name' => 'View Users', 'slug' => 'view_users', 'category' => 'users'],
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'category' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'category' => 'users'],
            
            // Settings permissions
            ['name' => 'View Settings', 'slug' => 'view_settings', 'category' => 'settings'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'category' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
