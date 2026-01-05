<?php

use App\Models\Role;
use App\Models\Permission;
use App\Models\Theme;
use App\Models\Plugin;
use App\Models\User;
use App\CMS\Kernel;
use App\CMS\Managers\BlockManager;

echo "=== CMS System Test ===\n\n";

// Test 1: Database
echo "1. Database Test:\n";
echo "   - Roles: " . Role::count() . "\n";
echo "   - Permissions: " . Permission::count() . "\n";
echo "   - Themes: " . Theme::count() . "\n";
echo "   - Plugins: " . Plugin::count() . "\n";
echo "   - Users: " . User::count() . "\n";

// Test 2: CMS Kernel
echo "\n2. CMS Kernel Test:\n";
$kernel = app(Kernel::class);
echo "   - Kernel booted: " . ($kernel->isBooted() ? 'Yes' : 'No') . "\n";

// Test 3: Block Manager
echo "\n3. Block Manager Test:\n";
$blockManager = app(BlockManager::class);
$blocks = $blockManager->all();
echo "   - Registered blocks: " . count($blocks) . "\n";
echo "   - Block types: " . implode(', ', array_keys($blocks)) . "\n";

// Test 4: Active Theme
echo "\n4. Theme System Test:\n";
$activeTheme = Theme::where('is_active', true)->first();
if ($activeTheme) {
    echo "   - Active theme: " . $activeTheme->name . "\n";
    echo "   - Theme slug: " . $activeTheme->slug . "\n";
    echo "   - Theme version: " . $activeTheme->version . "\n";
}

// Test 5: User Permissions
echo "\n5. User Permission Test:\n";
$user = User::first();
if ($user) {
    echo "   - Test user: " . $user->name . " (" . $user->email . ")\n";
    echo "   - Has create_page: " . ($user->hasPermission('create_page') ? 'Yes' : 'No') . "\n";
    echo "   - Has manage_plugins: " . ($user->hasPermission('manage_plugins') ? 'Yes' : 'No') . "\n";
}

// Test 6: Roles
echo "\n6. Roles Test:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "   - {$role->name} ({$role->slug}): " . $role->permissions->count() . " permissions\n";
}

// Test 7: Plugin Discovery
echo "\n7. Plugin Discovery Test:\n";
$plugins = Plugin::all();
foreach ($plugins as $plugin) {
    echo "   - {$plugin->name}: " . ($plugin->is_active ? 'Active' : 'Inactive') . "\n";
}

echo "\n=== All Tests Complete ===\n";
