<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Closure;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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


    public static function form(Form $form): Form
    {
        $languages = config('app.supported_locales', ['ar', 'en']);

        $predefinedCategories = [
            'en' => ['hot drinks', 'cold drinks', 'sweets'],
            'ar' => ['مشروبات ساخنة', 'مشروبات باردة', 'حلويات'],
        ];

        return $form
            ->schema([
                Section::make('معلومات عامة')
                    ->schema([
                        Repeater::make('category_translations')
                            ->label('التصنيف متعدد اللغات')
                            ->addActionLabel('إضافة لغة أخرى')
                            ->schema([
                                Select::make('locale')
                                    ->label('اللغة')
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('predefined')
                                    ->label('اختيار تصنيف جاهز')
                                    ->options(function (Get $get) use ($predefinedCategories) {
                                        $locale = $get('locale') ?? 'en';
                                        return collect($predefinedCategories[$locale] ?? [])->mapWithKeys(fn($item) => [$item => $item]);
                                    })
                                    ->searchable()
                                    ->placeholder('اختر من التصنيفات الجاهزة')
                                    ->columnSpan(1),

                                TextInput::make('custom')
                                    ->label('أو أدخل تصنيف جديد')
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
                            ->label('الاسم متعدد اللغات')
                            ->addActionLabel('إضافة لغة أخرى')
                            ->schema([
                                Select::make('locale')
                                    ->label('اللغة')
                                    ->options(fn() => $languages)
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('value')
                                    ->label('الاسم')
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
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')->label('التصنيف'),
                Tables\Columns\TextColumn::make('name')->label('اسم الخدمة'),
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
