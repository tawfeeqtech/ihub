<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection; // لاستخدام Collection
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // For image URLs
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    public Conversation $conversation;
    public array $messages = [];
    public string $newMessageBody = '';

    // protected $listeners = [];
    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        Auth::user()->markMessagesAsRead($conversation->id);
        $this->loadMessages();
        // $this->listeners["echo-private:conversations.{$this->conversation->id},.message.sent"] = 'handleIncomingMessage';
    }

    public function loadMessages()
    {
        $this->messages = $this->conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'sender' => [
                        'id' => $message->sender->id ?? null,
                        'name' => $message->sender->name ?? 'غير معروف',
                    ],
                    'created_at' => $message->created_at?->toDateTimeString(),
                    'attachment' => $message->attachment,
                ];
            })->toArray();
    }

    public function getListeners()
    {
        return [
            "echo-private:conversations.{$this->conversation->id},.message.sent" => 'handleIncomingMessage',
        ];
    }

    // لمعالجة الرسائل الواردة من Pusher (التي يرسلها المستخدم من الموبايل)
    public function handleIncomingMessage($eventData)
    {
        // تأكد من المسار الصحيح للـ ID والـ sender_id
        $messageSenderId = $eventData['event']['sender']['id'] ?? null;
        $currentAuthId = Auth::id();
        // Log::info('messageSenderId: ' . $messageSenderId);
        if ($messageSenderId == $currentAuthId) {
            return;
        }

        if (collect($this->messages)->contains('id', $eventData['id'])) {
            return;
        }
        $newMessage = new Message([
            'id' => $eventData['id'],
            'conversation_id' => $this->conversation->id,
            'sender_id' => $eventData['sender']['id'],
            'body' => $eventData['body'],
            'attachment' => $eventData['attachment'] ?? null,
            'created_at' => \Carbon\Carbon::parse($eventData['created_at']),
        ]);
        $newMessageArray = $newMessage->toArray();
        $newMessageArray['sender'] = $eventData['sender'];

        $this->messages[] = $newMessageArray;
        Auth::user()->markMessagesAsRead($this->conversation->id);
        // $this->dispatch('scroll-chat-to-bottom');
        $this->js(<<<'JS'
            window.dispatchEvent(new CustomEvent('scroll-chat-to-bottom'));
        JS);

        // $this->js(<<<"JS"
        //     window.dispatchEvent(new CustomEvent('message-received-for-conversation', {
        //         detail: { conversationId: {$this->conversation->id} }
        //     }));
        // JS);
        // Log::info('message-received-for-conversation: ' . $this->conversation->id);

        // $this->dispatch('messageReceivedForConversation', ['conversationId' => $this->conversation->id])->to('conversation-unread-badge');
    }

    // لإرسال رسالة من السكرتير
    public function sendMessage()
    {
        $this->validate([
            'newMessageBody' => 'required|string|max:1000',
        ]);

        $secretary = Auth::user();
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $secretary->id,
            'receiver_id' => $this->conversation->user_id,
            'body' => $this->newMessageBody,
            // 'attachment' => ... // منطق رفع المرفقات إذا أردت
        ]);

        $message->load('sender');
        $this->messages[] = [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'sender' => [
                'id' => $message->sender->id ?? null,
                'name' => $message->sender->name ?? 'غير معروف',
            ],
            'created_at' => $message->created_at?->toDateTimeString(),
            'attachment' => $message->attachment ?? null,
        ];

        broadcast(new MessageSent($message));

        $this->newMessageBody = '';
        // $this->dispatch('scroll-chat-to-bottom');
        $this->js(<<<'JS'
            window.dispatchEvent(new CustomEvent('scroll-chat-to-bottom'));
        JS);
    }

    // دالة render لعرض واجهة المستخدم
    public function render()
    {
        return view('livewire.chat-interface');
    }

    // #[On('incoming-message')]
    // public function addIncomingMessage($message)
    // {
    //     if (!collect($this->messages)->contains('id', $message['id'])) {
    //         $this->messages[] = $message;
    //         $this->dispatch('scroll-chat-to-bottom');
    //     }
    // }
}
