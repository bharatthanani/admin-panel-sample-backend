<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
  
    public function login(Request $request)
    {
        $request->validate(['email'=>'required|email','password'=>'required']);

        if (!Auth::attempt($request->only('email','password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'success'     => true,
            'token'       => $token,
            'message' => 'Login successful',
            'user'        => [
                'id'          => $user->id,
                'first_name'  => $user->first_name,
                'last_name'   => $user->last_name,
                'email'       => $user->email,
                'role'        => $user->roles->first()?->name ?? 'user',
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
