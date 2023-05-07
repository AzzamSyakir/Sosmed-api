<?php

namespace App\Http\Controllers\Users;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Image;


class HomeController extends Controller
{
    public function userProfile($username)
    {
        // Cari pengguna dengan username yang diberikan
        $user = User::where('username', $username)->firstOrFail();
        // Ambil postingan terbaru dari pengguna
        $posts = $user->posts()->count();
    
        // Hitung jumlah pengikut dan pengguna yang diikuti oleh pengguna
        $follower_count = $user->follower()->count();
        
        $following_count = $user->following()->count();
    
        // Kembalikan data dalam format JSON
        return response()->json([
            'user' => $user,
            'posts' => $posts,
            'follower_count' => $follower_count,
            'following_count' => $following_count,
        ]);
    }
    

    public function searchUserByName(Request $request, $query)
    {
        $users = User::where('username', 'LIKE', '%'.$query.'%')
            ->orWhere('email', 'LIKE', '%'.$query.'%')
            ->get();
    
        return response()->json($users);
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
