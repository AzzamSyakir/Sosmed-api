<?php
namespace App\Http\Controllers\Friend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Friend;
use App\Models\FriendRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function sendRequest(Request $request)
    {
        $authUser = Auth::user();
    
        $friendId = $request->input('friend_id');
        $friend = User::find($friendId);

        if (!$friend) {
            return response()->json(['error' => 'Friend not found.'], 404);
        }
    
        if ($authUser->hasSentFriendRequestTo($friend)) {
            return response()->json(['error' => 'Friend request already sent.'], 400);
        }
    
        if ($authUser->id === $friend->id) {
            return response()->json(['error' => 'You cannot send friend request to yourself.'], 400);
        }
    
        $authUser->sendFriendRequestTo($friend);
    
        return response()->json(['success' => 'Friend request sent.'], 200);
    }
    
    public function showFriends()
    {
        $user = Auth::user();
        $friends = $user->friends;
    
        return response()->json(['friends' => $friends]);
    }
    public function SpecificFriends($friendId)
    {
        $user = Auth::user();
        $friends = Friend::where('friend_id', $friendId)
                         ->where('user_id', $user->id)
                         ->get();

        return response()->json(['friends' => $friends]);
    }
    public function respondRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
            'status' => 'required|in:accept,reject',
        ]);
    
        if (Auth::check()) {
            $authUser = Auth::user();
        } else {
            // handle the case where the user is not authenticated
        }
    
        try {
            $friend = User::findOrFail($request->friend_id);
        } catch (ModelNotFoundException $e) {
            $message = 'user does not exist.';
            $status = 'failure';
            return response()->json([
                'message' => $message,
                'status' => $status,
            ]);
        }
    
        $friendRequest = Friend::where('friend_id', $authUser->id)
        ->where('user_id', $request->friend_id)
        ->first();
        if($friendRequest == null){
        return response()->json([
            'message' => 'tidak ada request friend yang di temukan']);
        }
        if (!$friendRequest || $friendRequest->status != 'pending') {
            $message = 'Friend request has already been accepted or rejected.';
            $status = 'failure';
        } else {
            if ($request->status == 'accept') {
                $friendship = $friend->acceptFriendRequest($authUser, $request->status);
                if ($friendship) {
                    $message = 'Friend request has been accepted.';
                    $status = 'success';
                } else {
                    $message = 'Failed to accept friend request. The friend request may have been accepted or rejected already.';
                    $status = 'failure';
                }
            } elseif ($request->status == 'reject') {
                $result = $authUser->rejectFriendRequest($friend);
                if ($result) {
                    $message = 'Friend request has been rejected.';
                    $status = 'success';
                } else {
                    $message = 'Failed to reject friend request. The friend request may have been accepted or rejected already.';
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
