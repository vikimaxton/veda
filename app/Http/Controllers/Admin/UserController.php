<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): Response
    {
        $users = User::with('roles')->paginate(20);
        $roles = Role::all();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
