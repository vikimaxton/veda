<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display all settings.
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->groupBy('group');

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Get settings by group.
     */
    public function group(string $group): JsonResponse
    {
        $settings = Setting::where('group', $group)->get();

        return response()->json([
            'data' => $settings,
        ]);
    }

    /**
     * Update multiple settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['required'],
            'settings.*.group' => ['nullable', 'string'],
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::set(
                $setting['key'],
                $setting['value'],
                $setting['group'] ?? 'general'
            );
        }

        return response()->json([
            'message' => 'Settings updated successfully',
        ]);
    }

    /**
     * Update a single setting.
     */
    public function updateSingle(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'value' => ['required'],
            'group' => ['nullable', 'string'],
        ]);

        Setting::set(
            $key,
            $validated['value'],
            $validated['group'] ?? 'general'
        );

        return response()->json([
            'message' => 'Setting updated successfully',
        ]);
    }
}
