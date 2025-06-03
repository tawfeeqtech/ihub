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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
        // return static::getModel()::count();
        // $count = auth()->user()?->getAllUnreadMessagesCount();
        // return $count > 0 ? (string) $count : null;
        // static::$unreadCountCache = auth()->user()?->getAllUnreadMessagesCount();
        // return static::$unreadCountCache > 0 ? (string) static::$unreadCountCache : null;

        $count = auth()->user()?->getAllUnreadMessagesCount() ?? 0;
        return $count > 0 ? (string) $count : null;
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
                Tables\Columns\TextColumn::make('id')->label('رقم المحادثة'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم المستخدم')
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
                    ->label('عرض المحادثة')
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
