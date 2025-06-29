<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Traits\TranslatableColumn;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;

class ServiceResource extends Resource
{


    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 4;

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

    public static function getPluralModelLabel(): string
    {
        return __('filament.Service.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.Service.label');
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();

        if ($user->role === 'secretary') {
            return parent::getEloquentQuery()
                ->where('workspace_id', $user->workspace_id);
        }

        return parent::getEloquentQuery();
    }
    public static function getLabel(): ?string
    {
        return __('filament.Service.label');
    }


    public static function form(Form $form): Form
    {
        $languages = config('app.supported_locales', ['ar', 'en']);

        $predefinedCategoryKeys = ['hot', 'cold', 'sweets'];


        return $form
            ->schema([
                Section::make(__('filament.Service.form.Section'))
                    ->schema([
                        Repeater::make('category_translations')
                            ->label(__('filament.Service.form.category_translations.label'))
                            ->addActionLabel(__('filament.Service.form.addActionLabel'))
                            ->schema([
                                Select::make('locale')
                                    ->label(__('filament.Service.form.locale'))
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('predefined')
                                    ->label(__('filament.Service.form.category_translations.predefined.label'))
                                    ->options(function () use ($predefinedCategoryKeys) {
                                        return collect($predefinedCategoryKeys)
                                            ->mapWithKeys(fn($key) => [$key => __('filament.Service.form.Categories.' . $key)]);
                                    })
                                    ->searchable()
                                    ->placeholder(__('filament.Service.form.category_translations.predefined.placeholder'))
                                    ->columnSpan(1),

                                TextInput::make('custom')
                                    ->label(__('filament.Service.form.category_translations.custom'))
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ])
                            ->default([
                                ['locale' => 'en', 'predefined' => null],
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),


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
                    ]),

                // Forms\Components\Select::make('category')
                //     ->label('التصنيف')
                //     ->options([
                //         'مشروبات ساخنة' => 'مشروبات ساخنة',
                //         'مشروبات باردة' => 'مشروبات باردة',
                //         'حلويات' => 'حلويات',
                //     ])
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $currentLocale = auth()->user()?->current_locale;
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.' . $currentLocale)->label(__('filament.Service.table.name')),
                Tables\Columns\TextColumn::make('category.' . $currentLocale)->label(__('filament.Service.table.category')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('category');
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
