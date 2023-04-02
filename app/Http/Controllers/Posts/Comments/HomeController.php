<?php

namespace App\Http\Controllers\Posts\Comments;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;


class HomeController extends Controller
{
    //
    public function storeComment(Request $request, $postId)
{
    try {
        $validatedData = $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $post = Post::findOrFail($postId);
        $comment = new Comment;
        $comment->comment = $validatedData['comment'];
        if(auth()->check()) {
            $comment->user_id = auth()->user()->id;
            $comment->username = auth()->user()->username;
        } else {
        return response()->json([
            'message' => 'belum login',
            ], 201);
        }
        $post->comments()->save($comment);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment
        ], 201);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Gagal Tambah comment',
            'error' => $th->getMessage(),
        ], 500);
    }
}

public function GetCommentByUser($postId)
{
    try {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json([
                'message' => 'Postingan tidak ditemukan'
            ], 404);
        }

        $comments = Comment::where('post_id', $postId)->with('post')->get();

        return response()->json([
            'comments' => $comments
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Gagal ambil comment',
            'error' => $th->getMessage()
        ], 500);
    }
}

    public function GetAllComments(Request $request)
    {
        try {
            $comments = Comment::with('user')->get();
            $username = $comments->map(function($comment) {
                return $comment->user->username;
            })->toArray();
            return response()->json([
                'comments' => $comments,
                'username' => $username
            ]);
            if($comment == null){
                return response()->json([
                    'message' => 'belum ada komentar'        
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Gagal ambil comment',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    

}



