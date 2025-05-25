<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Helpers\FilamentAccess;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\App;

class SettingResource extends Resource
{

    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canAccess(): bool
    {
        return FilamentAccess::isAdmin();
    }

    public static function form(Form $form): Form
    {

        $languages = config('app.supported_locales', ['ar', 'en']);

        return $form
            ->schema([
                Forms\Components\Select::make('key')
                    ->label('نوع المحتوى')
                    ->options([
                        'about' => 'حول التطبيق',
                        'terms' => 'الشروط والأحكام',
                    ])
                    ->required(),

                Forms\Components\Repeater::make('value_translations')
                    ->label('المحتوى متعدد اللغات')
                    ->addActionLabel('إضافة لغة')
                    ->schema([
                        Forms\Components\Select::make('locale')
                            ->label('اللغة')
                            ->options($languages)
                            ->required()->columnSpan('full'),

                        Forms\Components\Textarea::make('value')
                            ->label('المحتوى')
                            ->columnSpan('full')
                            ->required(),
                    ])
                    ->default([
                        ['locale' => 'ar', 'value' => ''],
                    ])
                    ->columns(2)->grid(2)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('title'),

                TextColumn::make('value')
                    ->label('description')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) {
                            return $state;
                        }
                        return collect($state)
                            ->map(fn($val, $locale) => strtoupper($locale) . ": " . $val)
                            ->implode(' | ');
                    })->wrap()
                    ->limit(80),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
