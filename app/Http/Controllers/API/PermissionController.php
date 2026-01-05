<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all();

        return response()->json([
            'data' => PermissionResource::collection($permissions),
        ]);
    }

    /**
     * Get permissions grouped by category.
     */
    public function categories(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('category');

        $grouped = $permissions->map(function ($perms, $category) {
            return [
                'category' => $category,
                'permissions' => PermissionResource::collection($perms),
            ];
        })->values();

        return response()->json([
            'data' => $grouped,
        ]);
    }
}
