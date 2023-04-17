<?php

namespace App\Http\Controllers\ChangeAccount;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function changeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|string|',
            'password' => 'required|string'
        ]);
        $token = $request->user()->token()->revoke();
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
    
}
