<?php
namespace App\Http\Controllers\Message;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageCreated;
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
    
        // Check if sender is friend with the receiver
        try {
            $sender = User::findOrFail($senderId);
            $receiver = User::findOrFail($receiverId);
        } catch (ModelNotFoundException $e) {
            $message = 'user does not exist.';
            $status = 'failure';
            return response()->json([
                'message' => $message,
                'status' => $status,
            ]);
        }
        $friendship = $sender->isFriendWith($receiver);
        if (!$friendship) {
            return response()->json([
                'message' => 'anda harus berteman dahulu'
            ], 403);
        }
        // Jalankan event MessageCreate
        event(new MessageCreated($message));
        // Enkripsi pesan jika is_encrypted bernilai true
        if ($isEncrypted) {
            $encryptedMessage = encrypt($message);
        } else {
            $encryptedMessage = $message;
        }
    
        // Simpan pesan ke database
        $chat = new Chat([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $encryptedMessage,
            'is_encrypted' => $isEncrypted
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

