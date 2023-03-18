<?php

namespace App\Http\Controllers\Posts;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class HomeController extends Controller
{
    //store posts 
  
        public function StorePosts(Request $request)
        {
            try {
                $data = $request->validate([
                    'media' => 'required|mimes:jpeg,jpg,png', // validasi hanya untuk file gambar
                    'caption' => 'required|string',
                    'comment' => 'required|string',
                    'user_id' => 'required|exists:users,id',
                ]);
    
                $path = $request->file('media')->store('public/media');
                $url = Storage::url($path);
    
                $posts = Post::create([
                    'media' => $url,
                    'caption' => $data['caption'],
                    'comment' => $data['comment'],
                    'user_id' => $data['user_id'],
                ]);
    
                return response()->json([
                    'message' => 'Berhasil buat posts',
                    'data' => $posts,
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => 'Gagal Tambah post',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }
    
}

