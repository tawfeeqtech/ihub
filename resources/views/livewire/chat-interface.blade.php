<div>
    <div id="chat-messages-container" data-conversation-id="{{ $conversation->id }}"
        class="chat-messages overflow-auto h-[600px]"
        style="height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        @forelse ($messages as $message)
        @php
        $isSenderSecretary = $message['sender_id'] === auth()->id();
        @endphp
        <div class="message-container {{ $isSenderSecretary ? 'sent' : 'received' }}">
            <div class="message-bubble">
                <p class="sender-name">{{ $message['sender']['name'] ?? 'مستخدم غير معروف' }}</p>
                <p class="message-body">{{ $message['body'] }}</p>
                @if (isset($message['attachment']) && $message['attachment'] !== null)
                @php
                $isImage = Str::endsWith(strtolower($message['attachment']), [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp',
                '.webp',
                ]);
                @endphp
                <a href="{{ asset($message['attachment']) }}" target="_blank"
                    style="display: block; margin-top: 5px;"><img src="{{ asset($message['attachment']) }}"
                        alt="مرفق"
                        style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 5px; display: block;"></a>
                @endif
                @if (isset($message['created_at']))
                <p class="message-time">{{ \Carbon\Carbon::parse($message['created_at'])->format('h:i A') }}</p>
                @endif
            </div>
        </div>
        @empty
        <p style="text-align: center; color: #888;">لا توجد رسائل في هذه المحادثة حتى الآن.</p>
        @endforelse
    </div>

    <form wire:submit.prevent="sendMessage" style="display: flex; gap: 10px;" id="chat-form">
        <textarea wire:model.defer="newMessageBody" wire:keydown.enter.prevent="sendMessage" placeholder="اكتب رسالتك هنا..."
            style="flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;" rows="2"></textarea>
        <button type="submit" class="filament-button filament-button-size-md filament-button-color-primary">
            إرسال
        </button>
    </form>

    <style>
        .message-container {
            display: flex;
            margin-bottom: 10px;
        }

        .message-container.sent {
            justify-content: flex-end;
            /* رسائل السكرتير على اليمين */
        }

        .message-container.received {
            justify-content: flex-start;
            /* رسائل المستخدم على اليسار */
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
            word-wrap: break-word;
        }

        .message-container.sent .message-bubble {
            background-color: #dcf8c6;
            /* لون رسائل السكرتير (المرسلة) */
            color: #333;
        }

        .message-container.received .message-bubble {
            background-color: #f1f0f0;
            /* لون رسائل المستخدم (المستلمة) */
            color: #333;
        }

        .sender-name {
            font-weight: bold;
            font-size: 0.9em;
            margin-bottom: 3px;
        }

        .message-body {
            margin-bottom: 5px;
            white-space: pre-wrap;
            /* للحفاظ على الأسطر الجديدة */
        }

        .message-time {
            font-size: 0.75em;
            color: #777;
            text-align: right;
        }
    </style>

    @push("scripts")
    <script>
        function scrollToBottom() {
            const container = document.querySelector('.chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
                console.log("🔽 Scrolled to bottom. scrollTop:", container.scrollTop);
            } else {
                console.warn("❌ chat-messages container not found");
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            console.log("DOMContentLoaded", Livewire);
            scrollToBottom();
            window.addEventListener('scroll-chat-to-bottom', () => {
                console.log("📩 scroll-chat-to-bottom event received");
                requestAnimationFrame(() => {
                    const container = document.querySelector('.chat-messages');
                    if (container) {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                        console.log("🔽 Smooth scroll done to:", container.scrollHeight);
                    }
                });
            });
        });
        
        
        // هذا المستمع يضمن التمرير لأسفل عند تحديث Livewire لأي سبب
        // document.addEventListener('livewire:navigated', () => {
        //     console.log("livewire:navigated", Livewire);

        //     const container = document.querySelector('.chat-messages');
        //     if (container) {
        //         container.scrollTop = container.scrollHeight;
        //     }
        // });

        // مستمع لحدث التمرير لأسفل الذي يتم إرساله من Livewire
        // Livewire.on('scroll-chat-to-bottom', () => {
        //     console.log("scroll-chat-to-bottom", Livewire);

        //     const container = document.querySelector('.chat-messages');
        //     if (container) {
        //         container.scrollTop = container.scrollHeight;
        //     }
        // });

        // *** التعديل الرئيسي هنا: انتظر حتى يكون Livewire جاهزاً ***
        // document.addEventListener('livewire:initialized', () => {
        //     console.log("livewire:initialized", Livewire);

        //     const conversationId = document.getElementById('chat-messages-container')?.dataset?.conversationId;
        //     console.log("Livewire Initialized. conversationId:", conversationId);

        //     if (conversationId) {
        //         console.log("livewire:initialized conversationId", Livewire);

        //         window.Echo.private(`conversations.${conversationId}`)
        //             .listen('message.sent', (e) => {
        //                 console.log("livewire:initialized conversationId message.sent", Livewire);

        //                 console.log('📥 وصلت رسالة جديدة:', e);
        //                 Livewire.emit(`echo-private:conversations.${conversationId},message.sent`, e);
        //             });
        //     } else {
        //         console.warn("❌ Livewire Initialized, but conversationId not found in DOM.");
        //     }
        // });
    </script>
    @endpush
</div>