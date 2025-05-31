<div>
    {{-- هنا ممكن تضع UI للمحادثات أو تتركه فارغاً إذا تم العرض عبر Filament --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach($conversationIds as $id)
            window.Echo.private("conversations.{{ $id }}")
                .listen("MessageSent", (e) => {
                    console.log("New message in conversation {{ $id }}", e);
                    Livewire.dispatch("refreshUnreadCount");
                });
            @endforeach
        });
    </script>
</div>