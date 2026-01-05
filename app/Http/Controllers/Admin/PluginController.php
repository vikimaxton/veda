<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Inertia\Inertia;
use Inertia\Response;

class PluginController extends Controller
{
    /**
     * Display a listing of plugins.
     */
    public function index(): Response
    {
        $plugins = Plugin::all();
        
        return Inertia::render('admin/Plugins', [
            'plugins' => $plugins,
        ]);
    }
}
