<?php

namespace App\Http\Controllers\Message;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Routing\Controller;


class HomeController extends Controller
{
    public function SendMessage(Request $request)
{
    $senderId = Auth::id();
    $receiverId = $request->input('receiver_id');
    $message = $request->input('message');
    $isEncrypted = $request->input('is_encrypted', false);

    // Enkripsi pesan jika is_encrypted bernilai true
    $encryptedMessage = $message;
    $encryptedMessage = encrypt($message);

    // Simpan pesan ke database
    $chat = new Chat([
        'sender_id' => $senderId,
        'receiver_id' => $receiverId,
        'message' => $encryptedMessage,
        'is_encrypted' => true
    ]);

    $chat->save();

    return response()->json([
        'user' =>$senderId,
        'message' => 'Pesan berhasil dikirim',
        'chat' => $message
    ]);
}

public function getMessage($senderId)
{
    $receiverId = Auth::id();

    // Ambil pesan dari database
    $messages = Chat::where(function($query) use ($senderId, $receiverId) {
        $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
    })->orWhere(function($query) use ($senderId, $receiverId) {
        $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
    })->orderBy('created_at')->get();

    // Dekripsi pesan jika is_encrypted bernilai true
    foreach ($messages as $message) {
        if ($message->is_encrypted) {
            $message->message = decrypt($message->message);
        }
    }
    

    return response()->json([
        'messages' => $messages
    ]);
}

    }

