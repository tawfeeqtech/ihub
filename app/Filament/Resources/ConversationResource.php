<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Models\Conversation;
use App\Models\Message; // استيراد موديل Message
use App\Models\User; // استيراد موديل User
use App\Events\MessageSent; // استيراد الحدث MessageSent
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action; // استيراد Action
use Filament\Forms\Components\Textarea; // استيراد Textarea
use Illuminate\Support\Facades\Auth; // للحصول على السكرتير الحالي
use Filament\Notifications\Notification; // لإظهار إشعار

class ConversationResource extends Resource
{
    protected static ?int $unreadCountCache = null;


    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('filament.Conversation.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.Conversation.label');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'secretary') {
            return $query->where('secretary_id', Auth::user()->id);
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('secretary_id', auth()->user()->id)->count();

        // return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getNavigationBadge();

        return $count > 0 ? 'primary' : 'gray';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('filament.table.id')),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.table.username'))
                    ->html()
                    ->formatStateUsing(function ($state, Conversation $record) {
                        return view('components.conversation-user-with-badge', ['conversation' => $record,])->render();
                    }),






            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_chat')
                    ->label(__('filament.Conversation.table.show_conversation'))
                    ->url(fn(Conversation $record): string => static::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('id')->label('رقم المحادثة'),
    //             Tables\Columns\TextColumn::make('user.name')
    //                 ->label('اسم المستخدم')
    //                 ->extraAttributes([
    //                     'x-data' => '{}',
    //                     'x-init' => 'window.addEventListener("new-message-received", () => {$store.messages.incrementUnread();});',
    //                 ])
    //                 ->formatStateUsing(function ($state, Conversation $record) {
    //                     $count = $record->getUnreadMessagesCountForAuth();
    //                     return $state . ($count > 0 ? " 🔴 ($count)" : '');
    //                 })


    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\Action::make('view_chat')
    //                 ->label('عرض المحادثة')
    //                 ->url(fn(Conversation $record): string => static::getUrl('view', ['record' => $record])),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view' => Pages\ViewConversationChat::route('/{record}'),
        ];
    }
}
