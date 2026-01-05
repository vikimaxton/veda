<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Plugin;
use App\Models\User;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): Response
    {
        $stats = [
            'total_pages' => Page::count(),
            'published_pages' => Page::where('status', 'published')->count(),
            'active_plugins' => Plugin::where('is_active', true)->count(),
            'total_users' => User::count(),
        ];

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
