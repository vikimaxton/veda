<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index(): Response
    {
        $pages = Page::with('creator')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return Inertia::render('admin/Pages', [
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): Response
    {
        return Inertia::render('admin/PageEditor', [
            'page' => null,
            'templates' => ['home', 'landing', 'full-width', 'blank'],
        ]);
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(string $id): Response
    {
        $page = Page::with('creator')->findOrFail($id);

        return Inertia::render('admin/PageEditor', [
            'page' => $page,
            'templates' => ['home', 'landing', 'full-width', 'blank'],
        ]);
    }
}
