<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model; // ← هذا مش موجود عندك غالبًا

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ServiceRequestStatusUpdated;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->role === 'secretary') {
            $query->whereHas('booking', function ($q) use ($user) {
                $q->where('workspace_id', $user->workspace_id);
            });
        }

        return $query;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role !== 'secretary';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role !== 'secretary';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role !== 'secretary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')->relationship('user', 'name')->required(),
                Forms\Components\Select::make('booking_id')->relationship('booking', 'id')->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'seat_change' => 'Seat Change',
                        'cafe_request' => 'Cafe Request',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('details'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('المستخدم'),
                Tables\Columns\TextColumn::make('type')->label('نوع الطلب'),
                Tables\Columns\TextColumn::make('status')->label('الحالة'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn() => auth()->user()->role !== 'secretary'),

                DeleteAction::make()
                    ->visible(fn() => auth()->user()->role !== 'secretary'),

                Action::make('in_progress')
                    ->label('جارٍ التنفيذ')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'secretary' &&
                            $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'in_progress';
                        $record->save();

                        Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                    }),

                Action::make('complete')
                    ->label('تم التنفيذ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'secretary' &&
                            in_array($record->status, ['pending', 'in_progress'])
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'completed';
                        $record->save();

                        Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                    }),

                Action::make('reject')
                    ->label('رفض الطلب')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn($record) =>
                        auth()->user()->role === 'secretary' &&
                            $record->status === 'pending'
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->status = 'rejected';
                        $record->save();

                        Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->role !== 'secretary'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
