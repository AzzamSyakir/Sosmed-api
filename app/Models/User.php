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

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function hasSentFriendRequestTo(User $user)
    {
        return $this->sendFriendRequestTo($user)->where('friend_id', $user->id)->exists();
    }
    public function friends()
    {
        return $this->hasMany(Friend::class);
    }
    public function acceptFriendRequest(User $user, $status)
    {
        $friendship = Friend::where('friend_id', $user->id)->update([
            'status' => $status
        ]);
        if(!$friendship){
            return false;
        }
        return true;
    }
    public function rejectFriendRequest(User $user)
    {
        $friendship = $this->friendRequests()->where('user_id', $user->id)->first();
        dd($user->friendRequests());
            if (!$friendship) {
            return false;
        }
    
        $friendship->delete();
        $user->deleteFriendRequest($this);
    
        return true;
    }
    public function deleteFriendRequest(User $user)
    {
        $friendship = $this->friendRequests()->where('user_id', $user->id)->first();
    
        if (!$friendship) {
            return false;
        }
    
        $friendship->delete();
    
        return true;
    }
    public function sendFriendRequestTo(User $user)
{
    $friendRequest = new Friend([
        'user_id' => $this->id,
        'friend_id' => $user->id,
        'status' => 'pending',
    ]);
    
    $friendRequest->save();
    
    return $friendRequest;
}
public function friendRequestsSent()
{
    return $this->belongsToMany(User::class, 'friend_requests', 'user_id', 'friend_id')
        ->wherePivot('status', 'pending')
        ->withPivot('status')
        ->withTimestamps();
}

public function isFriendWith(User $user)
{
    return Friend::where('friend_id', $user->id)->
    where('status', 'accept')->
    exists();
}

}
