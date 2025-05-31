<?php

namespace App\Filament\Resources\ConversationResource\Pages;

use App\Filament\Resources\ConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // protected function registerScripts(): array
    // {
    //     return [
    //         <<<JS
    //             Echo.private("conversations." + {{ auth()->id() }})
    //                 .listen(".message.sent", (e) => {
    //                     console.log("📥 رسالة جديدة وصلت:", e);
    //                     window.dispatchEvent(new CustomEvent('new-message-received', { detail: e }));
    //                 });
    //         JS,
    //     ];
    // }
}
