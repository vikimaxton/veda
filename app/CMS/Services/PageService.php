<?php

namespace App\CMS\Services;

use App\Models\Page;
use App\Models\PageVersion;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class PageService
{
    /**
     * Create a new page.
     */
    public function createPage(array $data, User $user): Page
    {
        // Auto-generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        $data['created_by'] = $user->id;
        $data['status'] = $data['status'] ?? config('cms.page.default_status', 'draft');

        $page = Page::create($data);

        // Create initial version
        if (config('cms.page.versioning_enabled', true)) {
            $this->createVersion($page, $user);
        }

        // Log audit
        if (config('cms.audit_enabled', true)) {
            AuditLog::log('page.created', 'Page', $page->id, null, $page->toArray());
        }

        return $page;
    }

    /**
     * Update a page.
     */
    public function updatePage(Page $page, array $data, User $user): Page
    {
        $oldValues = $page->toArray();

        $page->update($data);

        // Create version snapshot
        if (config('cms.page.versioning_enabled', true)) {
            $this->createVersion($page, $user);
        }

        // Log audit
        if (config('cms.audit_enabled', true)) {
            AuditLog::log('page.updated', 'Page', $page->id, $oldValues, $page->toArray());
        }

        return $page->fresh();
    }

    /**
     * Delete a page.
     */
    public function deletePage(Page $page, User $user): bool
    {
        // Check if page has children
        if ($page->hasChildren()) {
            throw new \Exception('Cannot delete page with children. Delete or reassign child pages first.');
        }

        $pageData = $page->toArray();

        $deleted = $page->delete();

        // Log audit
        if ($deleted && config('cms.audit_enabled', true)) {
            AuditLog::log('page.deleted', 'Page', $page->id, $pageData, null);
        }

        return $deleted;
    }

    /**
     * Publish a page.
     */
    public function publishPage(Page $page, User $user): Page
    {
        $page->update(['status' => 'published']);

        // Log audit
        if (config('cms.audit_enabled', true)) {
            AuditLog::log('page.published', 'Page', $page->id);
        }

        return $page->fresh();
    }

    /**
     * Unpublish a page.
     */
    public function unpublishPage(Page $page, User $user): Page
    {
        $page->update(['status' => 'draft']);

        // Log audit
        if (config('cms.audit_enabled', true)) {
            AuditLog::log('page.unpublished', 'Page', $page->id);
        }

        return $page->fresh();
    }

    /**
     * Create a version snapshot of a page.
     */
    protected function createVersion(Page $page, User $user): PageVersion
    {
        return PageVersion::create([
            'page_id' => $page->id,
            'content_snapshot' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'template' => $page->template,
                'content_schema' => $page->content_schema,
                'seo_meta' => $page->seo_meta,
                'status' => $page->status,
            ],
            'created_by' => $user->id,
        ]);
    }

    /**
     * Restore a page to a specific version.
     */
    public function restoreVersion(Page $page, PageVersion $version, User $user): Page
    {
        $snapshot = $version->content_snapshot;

        $page->update([
            'title' => $snapshot['title'] ?? $page->title,
            'content_schema' => $snapshot['content_schema'] ?? $page->content_schema,
            'seo_meta' => $snapshot['seo_meta'] ?? $page->seo_meta,
        ]);

        // Create new version after restore
        $this->createVersion($page, $user);

        // Log audit
        if (config('cms.audit_enabled', true)) {
            AuditLog::log('page.restored', 'Page', $page->id, null, ['version_id' => $version->id]);
        }

        return $page->fresh();
    }

    /**
     * Generate a unique slug from a title.
     */
    protected function generateSlug(string $title, int $attempt = 0): string
    {
        $slug = Str::slug($title);

        if ($attempt > 0) {
            $slug .= '-' . $attempt;
        }

        // Check if slug exists
        if (Page::where('slug', $slug)->exists()) {
            return $this->generateSlug($title, $attempt + 1);
        }

        return $slug;
    }

    /**
     * Validate page hierarchy (prevent circular references).
     */
    public function validateHierarchy(Page $page, ?string $parentId): bool
    {
        if (!$parentId) {
            return true;
        }

        // Can't be its own parent
        if ($page->id === $parentId) {
            return false;
        }

        // Check if parent is a descendant
        $parent = Page::find($parentId);
        while ($parent) {
            if ($parent->id === $page->id) {
                return false;
            }
            $parent = $parent->parent;
        }

        return true;
    }
}
