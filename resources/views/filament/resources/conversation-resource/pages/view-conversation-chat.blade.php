<x-filament-panels::page>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewire('chat-interface', ['conversation' => $record])
</x-filament-panels::page>