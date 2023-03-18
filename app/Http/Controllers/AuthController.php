<?php

namespace App\Http\Controllers;


use App\Models\admin;
use App\Models\product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;


class AuthController extends Controller
{
    public function LoginUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|',
            'password' => 'required|string'
        ]);

        $credentials = request(['username', 'password']);

        $username = $credentials['username'];
        $password = $credentials['password'];

        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'username tidak terdaftar'
            ], 401);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        
        if (!$tokenResult) {
            return response()->json([
                'message' => 'Gagal membuat token'
            ], 500);
        }
        $token->save();
        
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
        
    }
    
    public function LogoutUser(Request $request){
        $user = $request->user();
        $token = $request->user()->token()->revoke();
        return response()->json([
            'user' => $user,
            'message' => 'Successfully logged out'
        ]);
    }
    
}