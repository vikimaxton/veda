<?php

namespace Plugins\HelloWorld;

use Illuminate\Support\ServiceProvider;

class HelloWorldServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register plugin services
        $this->app->singleton(HelloWorldService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load plugin routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        // Load plugin views (if using Blade)
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hello-world');

        // Publish plugin assets
        $this->publishes([
            __DIR__ . '/public' => public_path('plugins/hello-world'),
        ], 'hello-world-assets');

        // Register plugin hooks
        $this->registerHooks();
    }

    /**
     * Register plugin hooks.
     */
    protected function registerHooks(): void
    {
        // Hook into CMS booted event
        \Event::listen('cms.booted', function () {
            \Log::info('Hello World plugin: CMS has booted!');
        });

        // Hook into page created event
        \Event::listen('page.created', function ($page) {
            \Log::info('Hello World plugin: Page created - ' . $page->title);
        });
    }
}
