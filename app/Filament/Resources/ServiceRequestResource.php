<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Helpers\FilamentAccess;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ServiceRequestStatusUpdated;
use App\ServiceRequestsStatus;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?int $navigationSort = 7;

    public static function getPluralModelLabel(): string
    {
        return __('filament.ServiceRequest.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.ServiceRequest.label');
    }

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

    public static function canAccess(): bool
    {
        return FilamentAccess::isSecretary();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();

        return static::getModel()::whereHas('booking', function ($query) use ($user) {
            $query->where('workspace_id', $user->workspace_id);
        })->count();
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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),

                Forms\Components\Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->options([
                        'seat_change' => __('filament.ServiceRequest.types.seat_change'),
                        'cafe_request' => __('filament.ServiceRequest.types.cafe_request'),
                    ])
                    ->required(),

                Forms\Components\Textarea::make('details'),

                Forms\Components\Select::make('status')
                    ->options(collect(ServiceRequestsStatus::cases())->mapWithKeys(
                        fn($case) => [$case->value => $case->label()]
                    )->toArray())
                    ->required(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label(__('filament.table.username')),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament.ServiceRequest.table.request_type'))
                    ->formatStateUsing(fn(?string $state) => $state ? __("filament.ServiceRequest.types.{$state}") : '-'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.table.status'))
                    ->formatStateUsing(fn(?ServiceRequestsStatus $state) => $state?->label() ?? '-')
                    ->badge()
                    ->color(fn(?ServiceRequestsStatus $state) => $state?->color() ?? 'gray'),

            ])
            ->actions([
                EditAction::make()
                    ->visible(fn() => auth()->user()->role !== 'secretary'),

                DeleteAction::make()
                    ->visible(fn() => auth()->user()->role !== 'secretary'),

                self::makeStatusAction('in_progress', ServiceRequestsStatus::InProgress, 'warning', ['pending']),
                self::makeStatusAction('complete', ServiceRequestsStatus::Completed, 'success', ['pending', 'in_progress']),
                self::makeStatusAction('reject', ServiceRequestsStatus::Rejected, 'danger', ['pending']),

                // Action::make('in_progress')
                //     ->label(__('filament.ServiceRequest.table.status.in_progress'))
                //     ->icon('heroicon-o-arrow-path')
                //     ->color('warning')
                //     ->visible(
                //         fn($record) =>
                //         auth()->user()->role === 'secretary' &&
                //             $record->status === ServiceRequestsStatus::Pending
                //     )
                //     ->requiresConfirmation()
                //     ->action(function ($record) {
                //         $record->status = ServiceRequestsStatus::InProgress;
                //         $record->save();

                //         Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                //     }),

                // Action::make('complete')
                //     ->label(__('filament.ServiceRequest.table.status.complete'))
                //     ->icon('heroicon-o-check-circle')
                //     ->color('success')
                //     ->visible(
                //         fn($record) =>
                //         auth()->user()->role === 'secretary' &&
                //             in_array($record->status, ['pending', 'in_progress'])
                //     )
                //     ->requiresConfirmation()
                //     ->action(function ($record) {
                //         $record->status = ServiceRequestsStatus::Completed;
                //         $record->save();

                //         Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                //     }),

                // Action::make('reject')
                //     ->label(__('filament.ServiceRequest.table.status.reject'))
                //     ->icon('heroicon-o-x-circle')
                //     ->color('danger')
                //     ->visible(
                //         fn($record) =>
                //         auth()->user()->role === 'secretary' && $record->status = ServiceRequestsStatus::Pending
                //     )
                //     ->requiresConfirmation()
                //     ->action(function ($record) {
                //         $record->status = ServiceRequestsStatus::Rejected;
                //         $record->save();

                //         Notification::send($record->user, new ServiceRequestStatusUpdated($record));
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()->role !== 'secretary'),
                ]),
            ]);
    }

    protected static function makeStatusAction(string $name, ServiceRequestsStatus $status, string $color, array $visibleWhen): Action
    {
        return Action::make($name)
            ->label(__('filament.ServiceRequest.table.status.' . $name))
            ->icon(match ($name) {
                'in_progress' => 'heroicon-o-arrow-path',
                'complete' => 'heroicon-o-check-circle',
                'reject' => 'heroicon-o-x-circle',
                default => 'heroicon-o-question-mark-circle',
            })
            ->color($color)
            ->visible(
                fn($record) =>
                auth()->user()->role === 'secretary'
                    && in_array($record->status->value ?? $record->status, $visibleWhen)
            )
            ->requiresConfirmation()
            ->action(function ($record) use ($status) {
                $record->status = $status;
                $record->save();
                Notification::send($record->user, new ServiceRequestStatusUpdated($record));
            });
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
