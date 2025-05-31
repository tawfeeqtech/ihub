<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (Auth::user()->role === 'secretary') {
            return $query->where('workspace_id', Auth::user()->workspace_id);
        }

        return $query;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('seat_number')
                    ->label('رقم المقعد')
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\TextInput::make('wifi_username')
                    ->label('اسم مستخدم الوايفاي')
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\TextInput::make('wifi_password')
                    ->label('كلمة مرور الوايفاي')
                    ->required(fn($record) => $record->status === 'pending'),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'بانتظار التأكيد',
                        'confirmed' => 'تم التأكيد',
                        'cancelled' => 'تم الإلغاء',
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('workspace.name'),
                Tables\Columns\TextColumn::make('package.name'),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('status'),
                // ImageColumn::make('payment_attachment')
                //     ->disk('public')
                //     ->directory('payment_attachments')
                //     ->label('إيصال الدفع'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
