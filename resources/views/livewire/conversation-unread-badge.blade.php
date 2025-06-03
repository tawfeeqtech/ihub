<span wire:poll.5s>
    {{ $conversation->user->name }}
    @if ($count > 0)
        🔴 ({{ $count }})
    @endif
</span>

@push("scripts")
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log({{ $count }});
            
        });
    </script>
@endpush