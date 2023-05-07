<?php
namespace App\Http\Controllers\Friend;

use App\Models\Friendship;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FollowRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //follow and followers
    public function follow(Request $request)
    {
        $authUser = Auth::user();
        $receiverId = $request->input('receiver_id');
    
        try {
            $user = User::findOrFail($receiverId);
            $authUser = User::find($authUser->id);
        } catch (ModelNotFoundException $e) {
            $message = 'user does not exist.';
            $status = 'failure';
            return response()->json([
                'message' => $message,
                'status' => $status,
            ]);
        }
    
        if ($authUser->hasSentFollowRequestTo($user)) {
            // Hapus follow request dari tabel follow_requests
            FollowRequest::where('sender_id', $authUser->id)
                ->where('receiver_id', $user->id)
                ->delete();
            
            // Mengembalikan response
            $message = 'berhasil unfollow';
            $status = 'success';
            return response()->json([
                'message' => $message,
                'status' => $status,
            ]);
        }
        
    
        if ($authUser->id === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself.'], 400);
        }
        $authUser->Follow($authUser, $user);
    
        return response()->json(['success' => "You are now following {$user->username}"], 200);
    }
    
    public function ShowFollowers()
    {
        $user = Auth::user();
        $followers = $user->follower;
    
        return response()->json(['followers' => $followers]);
    }
    public function RespondRequest(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:users,id',
            'status' => 'required|in:accept,reject',
        ]);
    
        if (Auth::check()) {
            $authUser = Auth::user();
       } else {
            // handle the case where the user is not authenticated
        }    
        try {
            $user = User::findOrFail($request->sender_id);
            $authUser = User::find($authUser->id);
        } catch (ModelNotFoundException $e) {
            $message = 'user does not exist.';
            $status = 'failure';
            return response()->json([
                'message' => $message,
                'status' => $status,
            ]);
        }

        $FollowRequest = FollowRequest::where('receiver_id', $authUser->id)
        ->where('sender_id', $request->sender_id)
        ->first();
    
        if($FollowRequest == null){
            return response()->json([
                'message' => 'tidak ada request follow yang di temukan'
            ]);
        }
    
        if (!$FollowRequest || $FollowRequest->status != 'pending') {
            $message = 'follow request has already been accepted or rejected.';
            $status = 'failure';
        } else {
            if ($request->status == 'accept') {
                $friendship = $authUser->acceptFollowRequest($authUser,$user,$request->status);
                if ($friendship) {
                    $message = 'follow request has been accepted.';
                    $status = 'success';
                } else {
                    $message = 'Failed to accept follow request. The follow request may have been accepted or rejected already.';
                    $status = 'failure';
                }
            } elseif ($request->status == 'reject') {
                $result = $authUser->rejectFollowRequest($user);
                if ($result) {
                    $message = 'follow request has been rejected.';
                    $status = 'success';
                } else {
                    $message = 'Failed to reject follow request. The follow request may have been accepted or rejected already.';
                    $status = 'failure';
                }
            } else {
                $message = 'Invalid request status.';
                $status = 'failure';
            }
        }
    
        return response()->json([
            'message' => $message,
            'status' => $status,
        ]);
    }    
}
