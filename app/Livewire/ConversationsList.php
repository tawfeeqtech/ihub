<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ConversationsList extends Component
{
    public $conversationIds = [];

    public function mount()
    {
        $this->conversationIds = Auth::user()->conversations->pluck('id')->toArray();
    }
    public function render()
    {
        return view('livewire.conversations-list');
    }
}
