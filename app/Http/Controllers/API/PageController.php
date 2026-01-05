<?php

namespace App\Http\Controllers\API;

use App\CMS\Services\PageService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Models\PageVersion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PageController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected PageService $pageService
    ) {}

    /**
     * Display a listing of pages.
     */
    public function index(Request $request): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('viewAny', Page::class);

        $query = Page::with(['creator', 'parent']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $pages = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => PageResource::collection($pages),
            'meta' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'total' => $pages->total(),
            ],
        ]);
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('create', Page::class);

        $page = $this->pageService->createPage(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Page created successfully',
            'data' => new PageResource($page->load('creator')),
        ], 201);
    }

    /**
     * Display the specified page.
     */
    public function show(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('view', $page);

        return response()->json([
            'data' => new PageResource($page->load(['creator', 'parent', 'children'])),
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(UpdatePageRequest $request, Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('update', $page);

        // Validate hierarchy if parent_id is being changed
        if ($request->has('parent_id')) {
            if (!$this->pageService->validateHierarchy($page, $request->parent_id)) {
                return response()->json([
                    'message' => 'Invalid page hierarchy. Circular reference detected.',
                    'errors' => ['parent_id' => ['Cannot create circular reference']],
                ], 422);
            }
        }

        $page = $this->pageService->updatePage(
            $page,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Page updated successfully',
            'data' => new PageResource($page->load('creator')),
        ]);
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('delete', $page);

        try {
            $this->pageService->deletePage($page, request()->user());

            return response()->json([
                'message' => 'Page deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Publish a page.
     */
    public function publish(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('publish', $page);

        $page = $this->pageService->publishPage($page, request()->user());

        return response()->json([
            'message' => 'Page published successfully',
            'data' => new PageResource($page),
        ]);
    }

    /**
     * Unpublish a page.
     */
    public function unpublish(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('publish', $page);

        $page = $this->pageService->unpublishPage($page, request()->user());

        return response()->json([
            'message' => 'Page unpublished successfully',
            'data' => new PageResource($page),
        ]);
    }

    /**
     * Get page preview.
     */
    public function preview(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('view', $page);

        return response()->json([
            'data' => new PageResource($page->load(['creator', 'parent'])),
            'preview_url' => config('app.url') . '/' . $page->slug . '?preview=true',
        ]);
    }

    /**
     * Get page versions.
     */
    public function versions(Page $page): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('view', $page);

        $versions = $page->versions()
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $versions->map(function ($version) {
                return [
                    'id' => $version->id,
                    'content_snapshot' => $version->content_snapshot,
                    'created_by' => $version->creator->name,
                    'created_at' => $version->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Restore a page to a specific version.
     */
    public function restore(Page $page, PageVersion $version): JsonResponse
    {
        // TODO: Re-enable after policies are created
        // $this->authorize('update', $page);

        if ($version->page_id !== $page->id) {
            return response()->json([
                'message' => 'Version does not belong to this page',
            ], 422);
        }

        $page = $this->pageService->restoreVersion($page, $version, request()->user());

        return response()->json([
            'message' => 'Page restored to previous version successfully',
            'data' => new PageResource($page),
        ]);
    }
}
