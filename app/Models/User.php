<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'no_hp',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function friend()
    {
        return $this->hasMany(Friendship::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function hasSentFollowRequestTo(User $user)
    {
        return FollowRequest::where('receiver_id', $user->id)->exists();
    }
    public function follower()
    {
        return $this->hasMany(Follower::class)
                    ->where('user_id', $this->id);
    }
   
    public function following()
{
    return $this->belongsToMany(User::class, 'follow_requests', 'sender_id', 'receiver_id')
                ->where('status', 'accept')
                ->withTimestamps();
}
public function posts()
{
    return $this->hasMany(Post::class);
}


    public function acceptFollowRequest(User $user_receiver, User $user_sender, $status)
    {
        $follower = Follower::where('follower_id', $user_sender->id)->first();
        $followRequest = FollowRequest::where('receiver_id', $user_receiver->id)->where('sender_id', $user_sender->id)->first();
    
        if (!$followRequest) {
            return response()->json(['message' => 'The follow request was not found'], 404);
        }
        $followRequest->status = $status;
        $followRequest->save();
    
        $followerRequest = Follower::create([
            'user_id' => $user_receiver->id,
            'follower_id' => $user_sender->id
        ]);
    
        if (!$followerRequest) {
            return response()->json(['message' => 'Failed to create follower'], 500);
        }
    
        $followerRequest->save();
    
        // Cek apakah kedua user saling mengikuti, jika ya maka tambahkan keduanya sebagai teman
        $isFriend = $this->makeFriends($user_sender, $user_receiver);
        if (!$isFriend) {
            return response()->json(['message' => 'Failed to create friend'], 500);
        }
    
        return response()->json(['message' => 'Follow request accepted successfully'], 200);
    }
    
public function makeFriends(User $user1, User $user2)
{
    if(!$user1 || !$user2) {
        return false; // Jika salah satu user null, maka langsung return false
    }

    $follower1 = Follower::where('user_id', $user1->id)->where('follower_id', $user2->id)->first();
    $follower2 = Follower::where('user_id', $user2->id)->where('follower_id', $user1->id)->first();

    if($follower1 && $follower2) {
        $friend1 = Friendship::create([
            'user_id' => $user1->id,
            'friend_id' => $user2->id
        ]);

        $friend2 = Friendship::create([
            'user_id' => $user2->id,
            'friend_id' => $user1->id
        ]);

        if($friend1 && $friend2) {
            $user1->friend()->save($friend1);
            $user2->friend()->save($friend2);
            return true;
        }
    }

   return false;
}


    public function rejectFollowRequest(User $user)
    {
        $follower = $this->followRequests()->where('user_id', $user->id)->first();
            if (!$follower) {
            return false;
        }
    
        $follower->delete();
        $user->deletefollowRequest($this);
    
        return true;
    }
    public function deleteFollowRequest(User $user)
    {
        $follower = $this->followRequests()->where('user_id', $user->id)->first();
    
        if (!$follower) {
            return false;
        }
    
        $follower->delete();
    
        return true;
    }
    // respond to follow request 
public function Follow(User $sender_id, User $receiver_id)
{
    $follower = new Follower([
        'follower_id' => $sender_id->id,
        'user_id' => $receiver_id->id,
    ]);
    
    $follower->save();
     // Cek apakah kedua user saling mengikuti, jika ya maka tambahkan keduanya sebagai teman
     $isFriend = $this->makeFriends($sender_id, $receiver_id);
     if (!$isFriend) {
         return response()->json(['message' => 'Failed to create friend'], 500);
     }
    return $follower;
}
public function isFriendWith(User $user)
{
    $friendship = Friendship::where([
        ['user_id', '=', $this->id],
        ['friend_id', '=', $user->id]
    ])->orWhere([
        ['friend_id', '=', $this->id],
        ['user_id', '=', $user->id]
    ])->first();

    return $friendship ? true : false;
}

public function conversations1()
{
    return $this->hasMany(Conversation::class, 'user_id_1');
}
public function conversations2()
{
    return $this->hasMany(Conversation::class, 'user_id_2');
}
}
