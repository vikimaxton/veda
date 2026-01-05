<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin gets all permissions
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::all()->pluck('id')->toArray();
            $superAdmin->syncPermissions($allPermissions);
        }

        // Admin gets most permissions except system-level
        $admin = Role::where('slug', 'admin')->first();
        if ($admin) {
            $adminPermissions = Permission::whereIn('category', [
                'pages', 'themes', 'plugins', 'users', 'settings'
            ])->pluck('id')->toArray();
            $admin->syncPermissions($adminPermissions);
        }

        // Mentor gets page management permissions
        $mentor = Role::where('slug', 'mentor')->first();
        if ($mentor) {
            $mentorPermissions = Permission::where('category', 'pages')
                ->pluck('id')->toArray();
            $mentor->syncPermissions($mentorPermissions);
        }

        // Student gets read-only permissions
        $student = Role::where('slug', 'student')->first();
        if ($student) {
            $studentPermissions = Permission::whereIn('slug', [
                'view_pages'
            ])->pluck('id')->toArray();
            $student->syncPermissions($studentPermissions);
        }
    }
}
