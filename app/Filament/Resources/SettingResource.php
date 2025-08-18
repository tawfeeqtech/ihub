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
                    ->label(__('filament.WorkspaceResource.form.description_translations.title'))
                    ->options([
                        'about' => __("filament.SettingResource.about"),
                        'terms' => __("filament.SettingResource.terms"),
                    ])->required()
                    ->unique(ignoreRecord: true)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                       if ($state === 'terms') {
                            $set('value.contacts', null);
                            $set('value.links', null);
                        }
                    })
                    ->validationMessages([
                        'unique' => __("filament.SettingResource.unique"),
                    ]),

                Forms\Components\Section::make(__("filament.SettingResource.Basicinformation"))
                    ->schema([
                        Forms\Components\Repeater::make('value.info')
                            ->label(__("filament.SettingResource.Maincontent"))
                            ->addActionLabel(__("filament.SettingResource.Additem"))
                            ->schema([
                                Forms\Components\Repeater::make('key_translations')
                                    ->label(__("filament.WorkspaceResource.form.location_translations.title"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('value')
                                            ->label(__("filament.SettingResource.Text"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                                Forms\Components\Repeater::make('value_translations')
                                    ->label(__("filament.WorkspaceResource.form.description_translations.title"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\Textarea::make('value')
                                            ->label(__("filament.SettingResource.Text"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                            ])
                            ->default([['key_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']], 'value_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']]]])
                            ->columns(1)
                            ->grid(1)
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Section::make(__("filament.SettingResource.contactinformation"))
                    ->schema([
                        Forms\Components\Repeater::make('value.contacts')
                            ->label(__("filament.SettingResource.contactinformation"))
                            ->addActionLabel(__("filament.SettingResource.Addcontactinformation"))
                            ->schema([
                                Forms\Components\Repeater::make('key_translations')
                                    ->label(__("filament.WorkspaceResource.form.location_translations.title"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('value')
                                            ->label(__("filament.SettingResource.Text"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                                Forms\Components\Repeater::make('value_translations')
                                    ->label(__("filament.WorkspaceResource.form.description_translations.title"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\Textarea::make('value')
                                            ->label(__("filament.SettingResource.Text"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                            ])
                            ->default([['key_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']], 'value_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']]]])
                            ->columns(1)
                            ->grid(1)
                            ->columnSpan('full'),
                    ])
                    ->hidden(fn ($get) => $get('key') === 'terms'),

                    Forms\Components\Section::make(__("filament.SettingResource.Socialmedialinks"))
                    ->schema([
                        Forms\Components\Repeater::make('value.links')
                            ->label(__("filament.SettingResource.Communicationlinks"))
                            ->addActionLabel(__("filament.SettingResource.Addalink"))
                            ->schema([
                                Forms\Components\Repeater::make('key_translations')
                                    ->label(__("filament.SettingResource.Linkname"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('value')
                                            ->label(__("filament.SettingResource.Text"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                                Forms\Components\Repeater::make('value_translations')
                                    ->label(__("filament.SettingResource.Communicationlink"))
                                    ->schema([
                                        Forms\Components\Select::make('locale')
                                            ->label(__("filament.Service.form.locale"))
                                            ->options(array_combine($languages, $languages))
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('value')
                                            ->label(__("filament.SettingResource.Link"))
                                            ->required()
                                            ->columnSpan(1),
                                    ])
                                    ->default([['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']])
                                    ->columns(2)
                                    ->grid(2)
                                    ->columnSpan('full'),
                            ])
                            ->default([['key_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']], 'value_translations' => [['locale' => 'ar', 'value' => ''], ['locale' => 'en', 'value' => '']]]])
                            ->columns(1)
                            ->grid(1)
                            ->columnSpan('full'),
                    ])
                    ->hidden(fn ($get) => $get('key') === 'terms'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('filament.SettingResource.form.key.title'))
                    ->formatStateUsing(function ($state) {
                        return __('filament.' . $state);
                    })->badge()
                    ->color(fn ($state) => $state === 'about' ? 'info' : 'warning')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('filament.WorkspaceResource.form.description_translations.title'))
                    ->formatStateUsing(fn ($state) => Str::limit($state, 100, '...'))
                    ->html()
                    ->wrap()
                    ->description(fn ($record) => __('filament.SettingResource.Numberofitems').': ' . $record->item_count . ($record->contacts ? ', '. __('filament.SettingResource.contactinformation') .': ' . count($record->contacts) : '') . ($record->links ? ', '.__('filament.SettingResource.links').': ' . count($record->links) : '')),
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
