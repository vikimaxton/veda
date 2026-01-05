<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ThemeController as AdminThemeController;
use App\Http\Controllers\Admin\PluginController as AdminPluginController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Pages Management
    Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
    Route::get('/pages/{id}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    
    // Users & Roles
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    
    // Themes
    Route::get('/themes', [AdminThemeController::class, 'index'])->name('themes.index');
    
    // Plugins
    Route::get('/plugins', [AdminPluginController::class, 'index'])->name('plugins.index');
    
    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('admin.settings.update');
});
