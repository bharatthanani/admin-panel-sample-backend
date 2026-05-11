<?php
// app/Http/Controllers/Api/Admin/RolePermissionController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
  
    public function getRoles()
    {
        $roles = Role::with('permissions')->get()->map(fn($role) => [
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'users_count' => User::role($role->name)->count(),
        ]);

        return response()->json(['success' => true, 'roles' => $roles]);
    }

    
    public function getPermissions()
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);

        return response()->json(['success' => true, 'permissions' => $permissions]);
    }

    
    public function createRole(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['success' => true, 'message' => 'Role created!', 'role' => $role]);
    }

    
    public function updateRoleOLd(Request $request, $id)
    {
        $role = Role::findOrFail($id);

       
        if (in_array($role->name, ['admin', 'vendor', 'user']) && $request->name !== $role->name) {
            return response()->json(['success' => false, 'error' => 'Cannot rename core roles.'], 403);
        }

        if ($request->has('name')) {
            $role->update(['name' => $request->name]);
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json(['success' => true, 'message' => 'Role updated!']);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        
        if (
            $request->has('name') &&
            in_array($role->name, ['admin', 'vendor', 'user']) &&
            $request->name !== $role->name
        ) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot rename core roles.'
            ], 403);
        }

        if ($request->has('name')) {
            $role->update(['name' => $request->name]);
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role updated!'
        ]);
    }

    
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);

        if (in_array($role->name, ['admin', 'vendor', 'user'])) {
            return response()->json(['success' => false, 'error' => 'Cannot delete core roles.'], 403);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted!']);
    }

   
    public function getUsers()
    {
        $users = User::with('roles')->get()->map(fn($u) => [
            'id'         => $u->id,
            'name'       => "{$u->first_name} {$u->last_name}",
            'email'      => $u->email,
            'role'       => $u->roles->first()?->name ?? 'user',
            'status'     => $u->status,
            'created_at' => $u->created_at,
        ]);

        return response()->json(['success' => true, 'users' => $users]);
    }

   
    public function assignRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);   

        return response()->json([
            'success' => true,
            'message' => "Role '{$request->role}' assigned to {$user->first_name}.",
        ]);
    }

    
    public function myPermissions(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success'     => true,
            'role'        => $user->roles->first()?->name ?? 'user',
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }
}