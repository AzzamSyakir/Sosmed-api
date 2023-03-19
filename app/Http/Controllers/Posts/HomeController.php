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
                $validatedData = $request->validate([
                    'media' => 'required|file|image|max:2048',
                    'caption' => 'required|string|max:255',
                ]);
        
                $path = $validatedData['media']->store('public');
                $url = Storage::url($path);
        
                $post = new Post;
                $post->media = $url;
                $post->caption = $validatedData['caption'];
                $post->user_id = auth()->user()->id;
                $post->save();
        
                return response()->json([
                    'message' => 'Post created successfully',
                    'post' => $post
                ], 201);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => 'Gagal Tambah post',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }
    
}

