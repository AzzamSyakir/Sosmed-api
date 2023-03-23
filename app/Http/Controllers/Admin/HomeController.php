<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;


class HomeController extends Controller
{
    public function StoreUser(Request $request)
    {
        try {
            $data = $request->validate([
                'username' => 'required|string',
                'no_hp' => 'required|integer',
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            ]);
    
            $data['password'] = Hash::make($data['password']);
    
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
    

    public function DeleteUser(User $user){
        try{  
            $user->delete();
            return response()->json([
                'message' => 'Berhasil Hapus user'
            ]);
        }
      catch (\Throwable $th) {
        return response()->json([
            'message' => 'Gagal Hapus user',
            'error' => $th->getMessage()
        ], 500);
    }
    }
    public function listUser(){
        try{
            $user = User::get();
            return response()->json([
                $user
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal ambil user',
                'error' => $th->getMessage()
            ], 500);
        }
}

}
