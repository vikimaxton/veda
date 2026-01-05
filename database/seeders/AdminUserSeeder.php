<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@cms.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Super Admin role
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdmin->syncRoles([$superAdminRole->id]);
        }

        $this->command->info('Super Admin created: admin@cms.local / admin123');

        // Create regular Admin user
        $admin = User::firstOrCreate(
            ['email' => 'editor@cms.local'],
            [
                'name' => 'Content Editor',
                'password' => Hash::make('editor123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Admin role
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $admin->syncRoles([$adminRole->id]);
        }

        $this->command->info('Admin created: editor@cms.local / editor123');

        // Create Mentor user
        $mentor = User::firstOrCreate(
            ['email' => 'mentor@cms.local'],
            [
                'name' => 'Content Mentor',
                'password' => Hash::make('mentor123'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Mentor role
        $mentorRole = Role::where('slug', 'mentor')->first();
        if ($mentorRole) {
            $mentor->syncRoles([$mentorRole->id]);
        }

        $this->command->info('Mentor created: mentor@cms.local / mentor123');
    }
}
