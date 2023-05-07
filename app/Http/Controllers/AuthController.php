<?php

namespace App\Http\Controllers;



use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


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
    public function RegisterUser (Request $request)
    {
        try {
            $data = $request->validate([
                'username' => 'required|string',
                'no_hp' => 'required|integer',
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            ]);
    
            $data['password'] = Hash::make($data['password']);
        
        // tambahkan kode untuk menyimpan foto profil default
        if (!$request->hasFile('photo')) {
            $defaultPhotoPath = 'users-avatar/avatar.png';
            $data['profile_picture'] = Storage::url($defaultPhotoPath);
        } else {
            // simpan gambar ke storage
            $path = $request->file('photo')->store('public/users-avatar');
            $data['profile_picture'] = Storage::url($path);
        }
            $user = User::create($data);

            return response()->json([
                'message' => 'Berhasil tambah user',
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal Tambah user',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    
}