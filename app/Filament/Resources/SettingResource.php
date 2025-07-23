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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?int $navigationSort = 10;

    public static function getPluralModelLabel(): string
    {
        return __('filament.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.settings');
    }

    public static function getLabel(): ?string
    {
        return __('filament.setting');
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

    public static function canCreate(): bool
    {
        $used = Setting::pluck('key')->toArray();
        $all = ['about', 'terms'];
        return count(array_diff($all, $used)) > 0;
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
                    ])->required()
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'هذا النوع من الإعدادات موجود بالفعل.',
                    ]),

                Forms\Components\Repeater::make('value')
                    ->label('المحتوى متعدد اللغات')
                    ->addActionLabel('إضافة عنصر')
                    ->schema([
                        Forms\Components\Repeater::make('key_translations')
                            ->label('العنوان (النص الكبير الغامق)')
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('اللغة')
                                    ->options(array_combine($languages, $languages))
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('value')
                                    ->label('النص')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->default([['locale' => 'ar', 'value' => '']])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),

                        Forms\Components\Repeater::make('value_translations')
                            ->label('الوصف (النص الصغير)')
                            ->schema([
                                Forms\Components\Select::make('locale')
                                    ->label('اللغة')
                                    ->options(array_combine($languages, $languages))
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('value')
                                    ->label('النص')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->default([['locale' => 'ar', 'value' => '']])
                            ->columns(2)
                            ->grid(2)
                            ->columnSpan('full'),
                    ])
                    ->default([['key_translations' => [['locale' => 'ar', 'value' => '']], 'value_translations' => [['locale' => 'ar', 'value' => '']]]])
                    ->columns(1)
                    ->grid(1)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('نوع المحتوى')
                    ->formatStateUsing(function ($state) {
                        return __('filament.' . $state);
                    })->badge()
                    ->color(fn ($state) => $state === 'about' ? 'info' : 'warning')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('translated_name')
                ->label('الوصف')
                ->formatStateUsing(fn ($state) => Str::limit($state, 100, '...'))
                ->html()
                ->wrap()
                ->description(fn ($record) => 'عدد العناصر: ' . $record->item_count),
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
