<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Helpers\FilamentAccess;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?int $navigationSort = 9;
    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }
    public static function getPluralModelLabel(): string
    {
        return __('filament.regions');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.regions');
    }
    public static function getLabel(): ?string
    {
        return __('filament.region');
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
        $languages = config('app.supported_locales', ['ar', 'en']);

        return $form
            ->schema([
                Forms\Components\Select::make('governorate_id')
                    ->label(__('filament.governorate'))
                    ->relationship('governorate', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name) // هنا السحر
                    ->required(),
                // Forms\Components\TextInput::make('name')
                //     ->label(__('filament.region'))
                //     ->required()
                //     ->maxLength(255),

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
            ->query(Region::with('governorate'))
            ->columns([
                // Tables\Columns\TextColumn::make('name')
                //     ->label(__('filament.region'))
                //     ->searchable(),

                // Tables\Columns\TextColumn::make('governorate.name')
                //     ->label(__('filament.governorate'))
                //     ->searchable(),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('filament.region'))
                    ->getStateUsing(fn($record) => $record->translated_name)
                    ->searchable(query: function ($query, $search) {
                        $locale = app()->getLocale();
                        return $query->orWhereJsonContains('name->' . $locale, $search);
                    }),


                Tables\Columns\TextColumn::make('governorate_translated_name')
                    ->label(__('filament.governorate'))
                    ->searchable(query: function ($query, $search) {
                        // نبحث داخل JSON في الحقل name
                        $locale = app()->getLocale();
                        return $query->orWhereJsonContains('governorate.name->' . $locale, $search);
                    })
                    ->getStateUsing(function ($record) {
                        return $record->governorate?->translated_name;
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate')
                    ->label(__('filament.governorate'))
                    ->relationship('governorate', 'name')->getOptionLabelFromRecordUsing(fn($record) => $record->translated_name) // هنا السحر
                ,
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegion::route('/create'),
            'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
}
