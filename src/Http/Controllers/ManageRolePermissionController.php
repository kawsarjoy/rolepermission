<?php

namespace KawsarJoy\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use KawsarJoy\RolePermission\Models\Role;
use KawsarJoy\RolePermission\Models\Permission;

class ManageRolePermissionController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rolepermission::manageRolePermission')->with([
            'users' => User::all(),
            'roles' => Role::all(),
            'permissions' => Permission::all()
        ]);
    }

    public function saveRole(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'bail|required|string|unique:roles',
            'description' => 'bail|required|string'
        ]);

        Role::create($validatedData);

        return redirect()->route('manageRolePermission');
    }

    public function savePermission(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'bail|required|string|unique:permissions',
            'description' => 'bail|required|string'
        ]);

        Permission::create($validatedData);

        return redirect()->route('manageRolePermission');
    }

    public function addRole(Request $request)
    {
        $validatedData = $request->validate([
            'user' => 'bail|required|integer',
            'roles' => 'bail|required|array',
            'roles.*' => 'bail|required|integer'
        ]);

        User::find($validatedData['user'])->roles()->sync($validatedData['roles']);

        return redirect()->route('manageRolePermission');
    }

    public function addPermission(Request $request)
    {
        $validatedData = $request->validate([
            'role' => 'bail|required|integer',
            'permissions' => 'bail|required|array',
            'permissions.*' => 'bail|required|integer'
        ]);

        Role::find($validatedData['role'])->permissions()->sync($validatedData['permissions']);

        return redirect()->route('manageRolePermission');
    }

}
