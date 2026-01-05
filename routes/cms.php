<?php

use App\Http\Controllers\API\PageController;
use App\Http\Controllers\API\ThemeController;
use App\Http\Controllers\API\PluginController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register CMS-specific API routes for your application.
| These routes are loaded by the CMSServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::prefix('api/cms')->middleware(['web', 'auth'])->group(function () {
    
    // Page Management
    Route::apiResource('pages', PageController::class);
    Route::post('pages/{page}/publish', [PageController::class, 'publish']);
    Route::post('pages/{page}/unpublish', [PageController::class, 'unpublish']);
    Route::get('pages/{page}/preview', [PageController::class, 'preview']);
    Route::get('pages/{page}/versions', [PageController::class, 'versions']);
    Route::post('pages/{page}/restore/{version}', [PageController::class, 'restore']);
    
    // Theme Management
    Route::get('themes', [ThemeController::class, 'index']);
    Route::get('themes/{slug}', [ThemeController::class, 'show']);
    Route::post('themes/{slug}/activate', [ThemeController::class, 'activate']);
    Route::get('themes/{slug}/preview', [ThemeController::class, 'preview']);
    Route::post('themes/upload', [ThemeController::class, 'upload']);
    
    // Plugin Management
    Route::get('plugins', [PluginController::class, 'index']);
    Route::get('plugins/{slug}', [PluginController::class, 'show']);
    Route::post('plugins/{slug}/install', [PluginController::class, 'install']);
    Route::post('plugins/{slug}/activate', [PluginController::class, 'activate']);
    Route::post('plugins/{slug}/deactivate', [PluginController::class, 'deactivate']);
    Route::delete('plugins/{slug}', [PluginController::class, 'uninstall']);
    Route::post('plugins/upload', [PluginController::class, 'upload']);
    
    // Role & Permission Management
    Route::apiResource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::get('permissions/categories', [PermissionController::class, 'categories']);
    
    // Settings Management
    Route::get('settings', [SettingController::class, 'index']);
    Route::get('settings/{group}', [SettingController::class, 'group']);
    Route::put('settings', [SettingController::class, 'update']);
    Route::put('settings/{key}', [SettingController::class, 'updateSingle']);
    
    // System Updates
    Route::prefix('system')->group(function () {
        Route::post('updates/upload', [\App\Http\Controllers\Admin\SystemUpdateController::class, 'upload']);
        Route::get('updates/history', [\App\Http\Controllers\Admin\SystemUpdateController::class, 'history']);
        Route::post('updates/rollback/{backupId}', [\App\Http\Controllers\Admin\SystemUpdateController::class, 'rollback']);
        Route::get('backups', [\App\Http\Controllers\Admin\SystemUpdateController::class, 'backups']);
        Route::delete('backups/{backupId}', [\App\Http\Controllers\Admin\SystemUpdateController::class, 'deleteBackup']);
    });
    
    // Block Registry
    Route::get('blocks', function () {
        return response()->json(app(\App\CMS\Managers\BlockManager::class)->all());
    });
});
