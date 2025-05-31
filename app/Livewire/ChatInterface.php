<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection; // لاستخدام Collection
use Illuminate\Support\Facades\Storage; // For image URLs
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    // protected $listeners = ['refreshUnreadCount' => '$refresh'];


    public Conversation $conversation;
    public array $messages = [];
    public string $newMessageBody = '';

    // يتم استدعاؤها عند تحميل المكون لأول مرة
    public function mount(Conversation $conversation)
    {
        $this->conversation = $conversation;
        // تعيين الرسائل كمقروءة إذا كانت موجهة للمستخدم الحالي
        Auth::user()->markMessagesAsRead($conversation->id);

        $this->loadMessages();
    }

    // لتحميل الرسائل من قاعدة البيانات
    public function loadMessages()
    {
        // $this->messages = $this->conversation->messages()
        //     ->with('sender') // تحميل بيانات المرسل مع كل رسالة
        //     ->orderBy('created_at', 'asc') // ترتيب الرسائل حسب تاريخ الإنشاء
        //     ->get()->toArray();

        $this->messages = $this->conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'sender' => [
                        'id' => $message->sender->id ?? null,
                        'name' => $message->sender->name ?? 'غير معروف',
                    ],
                    'created_at' => $message->created_at?->toDateTimeString(),
                    'attachment' => $message->attachment,
                ];
            })->toArray(); // ✅ هذا يحل المشكلة
    }

    // للاستماع لأحداث Pusher
    public function getListeners()
    {
        return [
            // استمع للحدث 'message.sent' على القناة الخاصة لهذه المحادثة
            "echo-private:conversations.{$this->conversation->id},.message.sent" => 'handleIncomingMessage',
        ];
    }

    // لمعالجة الرسائل الواردة من Pusher (التي يرسلها المستخدم من الموبايل)
    public function handleIncomingMessage($eventData)
    {
        // تحقق إذا كانت الرسالة بالفعل موجودة لتجنب الازدواجية في حالة إعادة تشغيل المكون أو أي مشكلة
        if (collect($this->messages)->contains('id', $eventData['id'])) {
            return;
        }

        // $eventData هي البيانات التي أرسلتها مع broadcastWith() في MessageSent
        // إنشاء كائن رسالة جديد من البيانات المستلمة
        $newMessage = new Message([
            'id' => $eventData['id'], // استخدم id من الحدث لضمان التوافق
            'conversation_id' => $this->conversation->id,
            'sender_id' => $eventData['sender']['id'],
            'body' => $eventData['body'],
            'attachment' => $eventData['attachment'] ?? null, // إذا كنت ترسل المرفقات
            'created_at' => \Carbon\Carbon::parse($eventData['created_at']), // تحويل السلسلة إلى تاريخ
        ]);
        // تعيين علاقة المرسل يدويًا بناءً على بيانات الحدث
        // $newMessage->setRelation('sender', (object) $eventData['sender']);
        $newMessageArray = $newMessage->toArray();
        $newMessageArray['sender'] = $eventData['sender'];
        Auth::user()->markMessagesAsRead($this->conversation->id);
        // إضافة الرسالة الجديدة إلى قائمة الرسائل
        $this->messages[] = $newMessageArray;
        // (اختياري) يمكنك إضافة JavaScript لتمرير الشاشة لأسفل تلقائيًا
        $this->dispatch('scroll-chat-to-bottom');
    }

    // لإرسال رسالة من السكرتير
    public function sendMessage()
    {
        $this->validate([
            'newMessageBody' => 'required|string',
        ]);

        $secretary = Auth::user();

        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $secretary->id,
            'body' => $this->newMessageBody,
            // 'attachment' => ... // منطق رفع المرفقات إذا أردت
        ]);

        $message->load('sender'); // تحميل بيانات السكرتير

        // بث الحدث (ليستقبله المستخدم في الموبايل)
        broadcast(new MessageSent($message))->toOthers();

        // إضافة الرسالة إلى الواجهة مباشرة (بدون انتظار Pusher)
        // $this->messages->push($message);
        // $this->messages[] = $message->toArray();

        $this->newMessageBody = ''; // تفريغ حقل الإدخال

        // (اختياري) يمكنك إضافة JavaScript لتمرير الشاشة لأسفل تلقائيًا
        $this->dispatch('scroll-chat-to-bottom');
    }

    // دالة render لعرض واجهة المستخدم
    public function render()
    {
        return view('livewire.chat-interface');
    }

    #[On('incoming-message')]
    public function addIncomingMessage($message)
    {
        if (!collect($this->messages)->contains('id', $message['id'])) {
            $this->messages[] = $message;
            $this->dispatch('scroll-chat-to-bottom');
        }
    }
}
