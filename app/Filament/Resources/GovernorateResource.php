<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use App\Helpers\FilamentAccess;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?int $navigationSort = 8;

    public static function getPluralModelLabel(): string
    {
        return __('filament.governorates');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.governorates');
    }
    public static function getLabel(): ?string
    {
        return __('filament.governorate');
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
    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }
    public static function form(Form $form): Form
    {
        $languages = config('app.supported_locales', ['ar', 'en']);

        return $form
            ->schema([

                Repeater::make('name_translations')
                    ->label(__('filament.Service.form.name_translations.lable'))
                    ->addActionLabel(__('filament.Service.form.addActionLabel'))
                    ->schema([
                        Select::make('locale')
                            ->label(__('filament.Service.form.locale'))
                            ->options(fn() => $languages)
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('value')
                            ->label(__('filament.Service.form.name_translations.value'))
                            ->required()->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->default([
                        ['locale' => 'en', 'value' => ''],
                    ])
                    ->columns(2)
                    ->grid(2)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.' . $currentLocale)
                    ->label(__('filament.governorate'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('regions_count')
                    ->label(__('filament.numberRegions'))
                    ->counts('regions'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit' => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }
}
