<?php
namespace App\Http\Controllers\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageCreated;
use App\Models\User;
use App\Models\conversation;
use App\Models\Message;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
class HomeController extends Controller
{
    public function sendMessage(Request $request)
    {
        $senderId = Auth::id();
        $receiverId = $request->input('receiver_id');
        $messageContent = $request->input('message');
        $isEncrypted = $request->input('is_encrypted', true);
    
        // Validate the input
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|numeric',
            'message' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Check if sender is friend with the receiver
        $sender = User::findOrFail($senderId);
        $receiver = User::findOrFail($receiverId);
    
        if (!$sender->isFriendWith($receiver)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus berteman dengan pengguna ini untuk dapat mengirim pesan.'
            ], 403);
        }
    
        // Check if there is an existing conversation
        $conversation = Conversation::where(function ($query) use ($senderId, $receiverId) {
            $query->where('user_id_1', $senderId)
                ->where('user_id_2', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('user_id_1', $receiverId)
                ->where('user_id_2', $senderId);
        })->first();
    
        // If there is no existing conversation, create a new one
        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id_1' => $senderId,
                'user_id_2' => $receiverId,
                'conversation_start_time' => now(),
                'message_content' => $messageContent
            ]);
        }
    
        // Encrypt the message if isEncrypted is true
        if ($isEncrypted) {
            $messageContent = encrypt($messageContent);
        }
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => true,
                'useTLS' => true,
            ]
        );
        $pusher->trigger('message', 'message send', [
            'message' => $messageContent,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId
        ]);
        
    
        // Create a new message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'message' => $messageContent,
            'message_type' => 'text',
            'message_status' => 'sent',
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'encrypted' => $isEncrypted
        ]);
    
        return response()->json([
            'message' => 'Pesan berhasil dikirim.',
        ]);
    }
    public function getMessage($senderId)
    {
        $authUser = Auth::user();
        if (!$authUser) {
            return response()->json(["error" => "Anda belum login"]);
        }
        $receiverId = $authUser->id;
    
        // Ambil pesan dari database
        $messages = Message::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->orderBy('created_at')
        ->select('message', 'encrypted')
        ->get();
    
        if ($messages->isEmpty()) {
            return response()->json(["error" => "Tidak ada pesan yang ditemukan"]);
        }
    
        // Dekripsi pesan jika is_encrypted bernilai true
        foreach ($messages as $message) {
            if ($message->encrypted) {
                $message->message = decrypt($message->message);
            }
            unset($message->encrypted);
        }
    
        return response()->json([
            'messages' => $messages
        ]);
    }
    
    
}

