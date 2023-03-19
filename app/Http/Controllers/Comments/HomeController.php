<?php

namespace App\Http\Controllers\Comments;

use App\Models\Post;
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
        $comment->user_id = auth()->user()->id;

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

}
