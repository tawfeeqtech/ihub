<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord; // سنستخدم ViewRecord كقاعدة
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Conversation;

class ViewConversationChat extends ViewRecord
{
    protected static string $resource = ConversationResource::class;
    public Model | int | string | null $record;

    // هنا سنقوم بتغيير الـ View الافتراضي لعرض مكون Livewire الخاص بنا
    protected static string $view = 'filament.resources.conversation-resource.pages.view-conversation-chat';

    // يمكنك إضافة actions هنا إذا أردت (مثل زر للعودة أو إغلاق المحادثة)
    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\EditAction::make(), // مثال
    //     ];
    // }


    // public function mount($record): void
    // {
    //     parent::mount($record);

    //     if (auth()->user()->role === 'secretary') {
    //         $this->record->messages()
    //             ->where('sender_id', $this->record->user_id)
    //             ->whereNull('read_at')
    //             ->update(['read_at' => Carbon::now()]);
    //     }
    // }
}
