<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use Inertia\Inertia;
use Inertia\Response;

class ThemeController extends Controller
{
    /**
     * Display a listing of themes.
     */
    public function index(): Response
    {
        $themes = Theme::all();
        
        return Inertia::render('admin/Themes', [
            'themes' => $themes,
        ]);
    }
}
