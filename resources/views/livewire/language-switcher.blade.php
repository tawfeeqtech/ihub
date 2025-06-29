<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="text-sm px-4 py-2 border rounded bg-white hover:bg-gray-100">
        🌐 {{ $currentLocale === 'ar' ? 'العربية' : 'English' }}
    </button>

    <div x-show="open" @click.away="open = false" class="absolute mt-2 bg-white border rounded shadow w-32 z-50">
        <button wire:click="switchLanguage('en')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            English
        </button>
        <button wire:click="switchLanguage('ar')" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
            العربية
        </button>
    </div>
</div>

{{-- <div class="language-switcher flex items-center space-x-2">
    @foreach ($availableLocales as $locale)
        <button 
            wire:click="switchLanguage('{{ $locale }}')"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="px-3 py-1 rounded {{ $currentLocale === $locale ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700' }} hover:bg-primary-700 hover:text-white transition-colors"
        >
            {{ strtoupper($locale) }}
        </button>
    @endforeach

    <script>
        console.log('language-switcher:');
        document.addEventListener('livewire:init', function () {
             console.log('livewire:init');
            Livewire.on('language-switched', function (data) {
                console.log('Language switched to:', data[0]);
            });
        });
    </script>
</div> --}}

