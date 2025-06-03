<?php

namespace App\Traits;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;

trait HasUnreadMessages
{
    // ضعه داخل User model أو Trait
    public function getAllUnreadMessagesCount(): int
    {
        $auth = auth()->user();

        if (!$auth) {
            return 0;
        }

        return Message::whereNull('read_at')
            ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
            ->where(function ($query) use ($auth) {
                $this->isMessageForCurrentUser($query, $auth);
            })
            ->count();
    }


    // public function getUnreadMessagesCount(): int
    // {
    //     $auth = Auth::user();
    //     if (!$auth) {
    //         return 0;
    //     }

    //     return Message::whereNull('read_at')
    //         ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
    //         ->where(function ($query) use ($auth) {
    //             $this->isMessageForCurrentUser($query, $auth);
    //         })
    //         ->count();
    // }

    public function markMessagesAsRead(int $conversationId): void
    {
        $auth = Auth::user();
        // if (!$auth) {
        //     return;
        // }
        // Message::whereNull('read_at')
        //     ->join('conversations', 'messages.conversation_id', '=', 'conversations.id')
        //     ->where(function ($query) use ($auth) {
        //         $this->isMessageForCurrentUser($query, $auth);
        //     })
        //     ->update(['read_at' => now()]);
        Message::whereNull('read_at')
            ->where('conversation_id', $conversationId) // ⬅️ حدد المحادثة الحالية فقط
            ->where('sender_id', '!=', $auth->id)       // فقط الرسائل من الطرف الآخر
            ->update(['read_at' => now()]);
    }

    protected function isMessageForCurrentUser($query, $auth)
    {
        if ($auth->role === 'secretary') {
            $query->where('conversations.secretary_id', $auth->id)
                ->whereColumn('messages.sender_id', '!=', 'conversations.secretary_id');
        } else {
            $query->where('conversations.user_id', $auth->id)
                ->whereColumn('messages.sender_id', '!=', 'conversations.user_id');
        }
    }
}
