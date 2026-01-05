<?php

namespace Plugins\HelloWorld;

class HelloWorldService
{
    /**
     * Get a greeting message.
     */
    public function getGreeting(string $name = 'World'): string
    {
        return "Hello, {$name}! This is from the Hello World plugin.";
    }

    /**
     * Get plugin statistics.
     */
    public function getStats(): array
    {
        return [
            'total_greetings' => rand(100, 1000),
            'active_users' => rand(10, 100),
            'version' => '1.0.0',
        ];
    }
}
