<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Facades\Hash;
use App\Helpers\FilamentAccess;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;
    public static function getPluralModelLabel(): string
    {
        return __('filament.users');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.users');
    }
    public static function getLabel(): ?string
    {
        return __('filament.user');
    }

    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                Forms\Components\TextInput::make('name')->required()->label(__('filament.UserResource.form.name.label')),
                Forms\Components\TextInput::make('phone')->required()->label(__('filament.UserResource.form.phone.label')),
                Forms\Components\TextInput::make('email')->nullable()->label(__('filament.UserResource.form.email.label')),
                Forms\Components\Select::make('role')
                    ->options([
                        'user' => 'User',
                        'secretary' => 'Secretary',
                        'admin' => 'Admin',
                    ])->reactive()
                    ->required()->label(__('filament.UserResource.form.role.label')),
                Forms\Components\TextInput::make('specialty')->label(__('filament.UserResource.form.specialty.label'))->nullable()
                    ->visible(fn($livewire) => ($livewire->data['role'] ?? '') === 'user' || request()->input('role') === 'user'),

                Forms\Components\Select::make('workspace_id')
                    ->relationship('workspace', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name)

                    ->required(fn($livewire) => $livewire->data['role'] ?? '' === 'secretary' || request()->input('role') === 'secretary')
                    ->visible(fn($livewire) => ($livewire->data['role'] ?? '') === 'secretary' || request()->input('role') === 'secretary')
                    ->label(__('filament.UserResource.form.relationshipWorkspace.label'))
                    ->searchable(),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->label(__('filament.UserResource.form.password.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('filament.UserResource.form.name.label')),
                Tables\Columns\TextColumn::make('phone')->label(__('filament.UserResource.form.phone.label')),
                Tables\Columns\TextColumn::make('email')->label(__('filament.UserResource.form.email.label')),
                Tables\Columns\TextColumn::make('role')->label(__('filament.UserResource.form.role.label')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
