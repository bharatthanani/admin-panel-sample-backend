<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function createAccount(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:6',
        ]);

        $data = $request->except(['confirm_password', 'role']); 
        $data['password'] = Hash::make($data['password']);
        $data['status']   = 1;

        $user = User::create($data);

        // ── Always assign 'user' role on public registration ──
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully!',
            'token'   => $token,
            'data'    => [
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'email'      => $user->email,
                'role'       => 'user',
                'permissions'=> $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    public function getUsers()
    {
        $user = User::with('roles')
            ->where('id', '!=', auth()->id())
            ->orderBy('id', 'DESC')
            ->paginate(10);
        // $user = User::orderBy('id','DESC')->get();

        return response()->json([
            'message' => 'User fatech',
            'users' => $user
        ]);
    }

   public function getVendors()
    {
        $user = auth()->user();

        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'vendor');
        })->orderBy('id', 'DESC');

        if ($user->hasRole('vendor')) {
            $query->where('id', $user->id);
        }

        $vendors = $query->get();

        return response()->json([
            'message' => 'Vendor fetch',
            'vendors' => $vendors
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully!']);

    }

    public function addUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:5',
            'role'       => 'required|string|in:admin,vendor,user', 
        ]);

        $data = $request->except(['confirm_password']);
        $data['password'] = Hash::make($request->password);
        $data['status']   = $request->status ?? 1;

        if ($request->hasFile('profile_picture')) {
            $file     = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('documents/profile'), $filename);
            $data['profile_picture'] = $filename;
        }

        $user = User::create($data);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'data'    => [
                ...$user->toArray(),
                'role' => $request->role,
            ],
        ]);
    }

    public function updateUser($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name'  => 'sometimes|required|string|max:255',
            'email'      => 'sometimes|required|email|unique:users,email,' . $id,
            'password'   => 'sometimes|nullable|min:6',
            'role'       => 'sometimes|required|string|in:admin,vendor,user',
        ]);

        $data = $request->except(['confirm_password', 'password']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && file_exists(public_path('documents/profile/' . $user->profile_picture))) {
                unlink(public_path('documents/profile/' . $user->profile_picture));
            }
            $file     = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('documents/profile'), $filename);
            $data['profile_picture'] = $filename;
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]); 
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'data'    => [
                ...$user->fresh()->toArray(),
                'role' => $user->getRoleNames()->first() ?? 'user',
            ],
        ]);
    }

}
