<?php

namespace App\CMS\Managers;

class BlockManager
{
    protected array $blocks = [];

    /**
     * Register core blocks.
     */
    public function registerCoreBlocks(): void
    {
        $this->register('heading', [
            'name' => 'Heading',
            'category' => 'text',
            'icon' => 'heading',
            'attributes' => [
                'level' => ['type' => 'number', 'default' => 2],
                'content' => ['type' => 'string', 'default' => ''],
            ],
        ]);

        $this->register('paragraph', [
            'name' => 'Paragraph',
            'category' => 'text',
            'icon' => 'paragraph',
            'attributes' => [
                'content' => ['type' => 'string', 'default' => ''],
            ],
        ]);

        $this->register('image', [
            'name' => 'Image',
            'category' => 'media',
            'icon' => 'image',
            'attributes' => [
                'url' => ['type' => 'string', 'default' => ''],
                'alt' => ['type' => 'string', 'default' => ''],
                'caption' => ['type' => 'string', 'default' => ''],
            ],
        ]);

        $this->register('button', [
            'name' => 'Button',
            'category' => 'common',
            'icon' => 'square',
            'attributes' => [
                'text' => ['type' => 'string', 'default' => 'Click me'],
                'url' => ['type' => 'string', 'default' => '#'],
                'variant' => ['type' => 'string', 'default' => 'primary'],
            ],
        ]);

        $this->register('spacer', [
            'name' => 'Spacer',
            'category' => 'layout',
            'icon' => 'separator-horizontal',
            'attributes' => [
                'height' => ['type' => 'number', 'default' => 40],
            ],
        ]);
    }

    /**
     * Register a new block type.
     */
    public function register(string $type, array $config): void
    {
        $this->blocks[$type] = $config;
    }

    /**
     * Get a block configuration.
     */
    public function get(string $type): ?array
    {
        return $this->blocks[$type] ?? null;
    }

    /**
     * Get all registered blocks.
     */
    public function all(): array
    {
        return $this->blocks;
    }

    /**
     * Get blocks by category.
     */
    public function byCategory(string $category): array
    {
        return array_filter($this->blocks, function ($block) use ($category) {
            return ($block['category'] ?? '') === $category;
        });
    }

    /**
     * Check if a block type exists.
     */
    public function exists(string $type): bool
    {
        return isset($this->blocks[$type]);
    }

    /**
     * Unregister a block type.
     */
    public function unregister(string $type): void
    {
        unset($this->blocks[$type]);
    }
}
