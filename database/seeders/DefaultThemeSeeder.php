<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class DefaultThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Theme::updateOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Default Theme',
                'version' => '1.0.0',
                'description' => 'A clean, modern default theme for the CMS',
                'author' => 'CMS Team',
                'config' => [
                    'templates' => [
                        'home' => [
                            'name' => 'Home Page',
                            'component' => 'templates/home.tsx',
                            'supports' => ['blocks', 'seo'],
                        ],
                        'landing' => [
                            'name' => 'Landing Page',
                            'component' => 'templates/landing.tsx',
                            'supports' => ['blocks', 'seo'],
                        ],
                        'full-width' => [
                            'name' => 'Full Width',
                            'component' => 'templates/full-width.tsx',
                            'supports' => ['blocks'],
                        ],
                    ],
                    'settings' => [
                        'colors' => [
                            'primary' => '#3b82f6',
                            'secondary' => '#8b5cf6',
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );
    }
}
