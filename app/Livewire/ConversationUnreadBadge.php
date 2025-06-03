<?php

namespace App\Livewire;

use App\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class ConversationUnreadBadge extends Component
{
    public Conversation $conversation;
    public int $count; // أضف هذه الخاصية
    public function mount()
    {
        $this->count = $this->conversation->getUnreadMessagesCountForAuth();
    }

    #[On('echo-private:conversations.{conversation.id},message.sent')]
    public function updateUnreadCount()
    {
        $this->count = $this->conversation->getUnreadMessagesCountForAuth();
        Log::info('Livewire updated unread count for conversation ' . $this->conversation->id . ': ' . $this->count);
    }
    public function render()
    {
        // $count = $this->conversation->getUnreadMessagesCountForAuth();
        // Log::info('this->conversation->getUnreadMessagesCountForAuth: ' . $count);
        // $conversationIdsFromBackend = Conversation::pluck('id')->toArray();

        return view('livewire.conversation-unread-badge');
    }
}
