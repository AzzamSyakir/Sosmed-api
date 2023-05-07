<?php

namespace App\Listeners;

use App\Events\MessageCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class MessageCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  MessageCreated  $event
     * @return void
     */
    public function handle(MessageCreated $event)
    {
        $message = $event->message;
        $senderId = $message->sender_id;
        $receiverId = $message->receiver_id;

        // Cek apakah pesan cocok dengan user yang sedang login
        if (Auth::id() == $senderId || Auth::id() == $receiverId) {

            // Dekripsi pesan jika is_encrypted bernilai true
            if ($message->is_encrypted) {
                $message->message = decrypt($message->message);
            }

            // Broadcast pesan ke channel 'messages' dengan event 'message.created'
            event(new MessageCreated($message));
        }
    }
}
