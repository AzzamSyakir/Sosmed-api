<?php

namespace App\Http\Controllers\Posts\Like;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class HomeController extends Controller
{
 //like posts
 public function LikePost($postId){
    try {
        $post = Post::findOrFail($postId);

        if (!$post) {
            return response()->json([
                'message' => 'Post tidak ditemukan',
            ], 404);
        }

        $userId = auth()->user()->id;

        // Check if the user has already liked the post
        $existingLike = $post->like()->where('user_id', $userId)->first();
        if ($existingLike) {
            $existingLike->delete(); // hapus catatan like yang sudah ada
            return response()->json([
                'message' => 'Anda sudah menyukai postingan ini sebelumnya, like sebelumnya telah dihapus.',
            ], 200);
        }
        

        // Create a new like record
        $like = new Like;
        $like->user_id = $userId;

        $post->like()->save($like);

        return response()->json([
            'message' => 'Berhasil menyukai postingan',
            'like' => $like
        ], 201);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Gagal menyukai postingan',
            'error' => $th->getMessage(),
        ], 500);
    }
}

      
}
