<!-- <x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow text-center">
            <div class="text-lg font-bold text-gray-800 dark:text-white">
                📩 عدد الرسائل غير المقروءة:
            </div>
            <div class="text-4xl font-extrabold text-primary-600 mt-2">
                {{ $unreadCount }}
            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget> -->

<div
    x-data
    x-init="
        Echo.private(`secretary.{{ auth()->id() }}`)
            .listen('.message.sent', (e) => {
                window.livewire.dispatchEvent('message-received');
            });
    ">
    <x-filament::card>
        <div class="text-lg font-bold text-primary">
            📩 {{ $unreadCount }} رسالة جديدة

        </div>
    </x-filament::card>
</div>