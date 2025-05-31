<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UnreadMessagesWidget extends Widget
{
    protected static string $view = 'filament.widgets.unread-messages-widget';

    public $unreadCount;

    public function mount()
    {
        $this->unreadCount = auth()->user()->getAllUnreadMessagesCount();
    }

    public function poll()
    {
        $this->unreadCount = auth()->user()->getAllUnreadMessagesCount();
    }
}
