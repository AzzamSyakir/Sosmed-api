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
        if(!$post){
            return response()->json([
                'message' => 'post tidak ditemukan',
                'error' => $th->getMessage(),
            ], 500);
        }

        $like = new Like;
        $like->user_id = auth()->user()->id;

        $post->like()->save($like);

        return response()->json([
            'message' => 'like succesfully',
            'comment' => $like
        ], 201);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Gagal Tambah like',
            'error' => $th->getMessage(),
        ], 500);
    }
}
      
}
