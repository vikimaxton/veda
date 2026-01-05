<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Theme Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where themes are stored.
    |
    */
    'theme_path' => base_path('themes'),

    /*
    |--------------------------------------------------------------------------
    | CMS Plugin Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where plugins are stored.
    |
    */
    'plugin_path' => base_path('plugins'),

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | This value determines which theme should be activated by default.
    |
    */
    'default_theme' => env('CMS_DEFAULT_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching for themes and plugins.
    |
    */
    'cache_enabled' => env('CMS_CACHE_ENABLED', true),
    'cache_ttl' => env('CMS_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Enable or disable audit logging for CMS actions.
    |
    */
    'audit_enabled' => env('CMS_AUDIT_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Page Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for pages.
    |
    */
    'page' => [
        'default_template' => 'default',
        'default_status' => 'draft',
        'auto_slug' => true,
        'versioning_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration.
    |
    */
    'security' => [
        'plugin_sandboxing' => env('CMS_PLUGIN_SANDBOXING', false),
        'theme_isolation' => env('CMS_THEME_ISOLATION', true),
    ],
];
