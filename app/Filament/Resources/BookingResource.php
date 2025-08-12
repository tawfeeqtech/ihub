<?php

namespace App\Filament\Resources;

use App\BookingStatus;
use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Helpers\FilamentAccess;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

use Filament\Tables\Columns\ImageColumn;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return FilamentAccess::isSecretary();
    }
    public static function getPluralModelLabel(): string
    {
        return __('filament.booking.sidebar.label'); // مثال: "الحجوزات"
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.booking.sidebar.label');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('workspace_id', auth()->user()->workspace_id)->count();

        // return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): string | array | null
    {
        $count = static::getNavigationBadge();

        return $count > 0 ? 'primary' : 'gray';
    }


    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery();

    //     if (Auth::user()->role === 'secretary') {
    //         return $query->where('workspace_id', Auth::user()->workspace_id);
    //     }

    //     return $query;
    // }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('seat_number')
                    ->label(__("filament.BookingResource.form.seat_number"))
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\TextInput::make('wifi_username')
                    ->label(__("filament.BookingResource.form.wifi_username"))
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\TextInput::make('wifi_password')
                    ->label(__("filament.BookingResource.form.wifi_password"))
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => __("filament.BookingResource.form.status.pending"),
                        'confirmed' => __("filament.BookingResource.form.status.confirmed"),
                        'cancelled' => __("filament.BookingResource.form.status.cancelled"),
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label(__('filament.table.username')),
                Tables\Columns\TextColumn::make('workspace.name.' . $currentLocale)->label(__('filament.booking.table.workspace')),
                Tables\Columns\TextColumn::make('package.name')->label(__('filament.booking.table.package')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.table.status'))
                    ->formatStateUsing(fn(?BookingStatus $state) => $state?->label() ?? '-')
                    ->badge()
                    ->color(fn(?BookingStatus $state) => $state?->color() ?? 'gray')

                // ImageColumn::make('payment_attachment')
                //     ->disk('public')
                //     ->directory('payment_attachments')
                //     ->label('إيصال الدفع'),

            ])
            ->filters([
                //
            ])
            ->recordUrl(fn($record) => $record->status !== 'confirmed'
                ? static::getUrl('edit', ['record' => $record])
                : null)
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) => $record->status !== 'confirmed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
