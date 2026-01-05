<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): Response
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return Inertia::render('admin/Settings', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_title' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_logo' => 'nullable|string',
            'logo_size' => 'nullable|string',
            'timezone' => 'nullable|string',
            'default_meta_title' => 'nullable|string|max:255',
            'default_meta_description' => 'nullable|string',
            'cache_ttl' => 'nullable|string',
            'debug_mode' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
