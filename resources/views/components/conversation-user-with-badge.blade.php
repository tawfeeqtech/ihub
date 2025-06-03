<span>
    {{ $conversation->user->name }}
    @livewire('conversation-unread-badge', ['conversation' => $conversation], key($conversation->id))
</span>