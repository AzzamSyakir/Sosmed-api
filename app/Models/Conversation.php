<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id_1',
        'user_id_2',
        'conversation_start_time',
        'conversation_end_time',
        'message_content',
        'message_type',
        'message_id',
        'message_status',
        'receiver_id',
        'sender_id',
        'conversation_status'
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }
    protected $table = 'Conversations';
}
